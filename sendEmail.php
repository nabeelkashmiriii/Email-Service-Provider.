<?php

//headers required
header("Access-Control-Allow-Origin: http://localhost/email%20service%20provider/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Origin, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'api/config/core.php';
include_once 'api/config/db.php';
include_once 'api/objects/merchant.php';
include_once 'api/validate_token.php';

// prepare database connection
$db = new Database();
$connection = $db->get_connection();
// prepare merchant object
$merchant = new Merchant($connection);

// get posted data
$get_data = json_decode(file_get_contents("php://input"));
$get_token = getallheaders();

$jwt_token = new JwtValidate($key);
$jwt = $get_token["Authorization"];
$jwt = $jwt_token->jwt_validate($jwt);
 
// set data values
$from = $get_data->from;
$to = $get_data->to;
$from_name = $get_data->from_name;
$to_name = $get_data->to_name;
$cc = $get_data->cc;
$cc_name = $get_data->cc_name;
$bcc = $get_data->bcc;
$bcc_name = $get_data->bcc_name;
$subject = $get_data->subject;
$text_part = $get_data->description;
$html_part = $get_data->html_text;



if($jwt['result'])
{
  $get_result = $merchant->send_mail($from, $to, $from_name, $to_name, $cc, $cc_name, $bcc, $bcc_name, $html_part, $html_part, $subject, $jwt["data"]->email);

  if ($get_result == true)
  {
    http_response_code(200);
    echo json_encode(array("message" => "Email Sent Successful!."));
  }

  else
  {
    http_response_code(400);
    echo json_encode(array("message" => "Email Sent UnSuccessful!."));
  }
}

else
{
  http_response_code(500);
  echo json_encode(array("message" => "Bad Request"));

}


?>
