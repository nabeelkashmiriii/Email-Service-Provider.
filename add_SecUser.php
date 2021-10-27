<?php
// headers required
header("Access-Control-Allow-Origin: http://localhost/email%20service%20provider/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Files required
include_once 'api/objects/merchant.php';
include_once 'api/config/db.php';
include_once 'api/objects/merchant.php';
include_once 'api/validate_token.php';
// require_once 'sendEmail.php';

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


$name = $get_data->name;
$email = $get_data->email;
$password = $get_data->password;
$check_listing = $get_data->check_listing;
$billing_info = $get_data->billing_info;
$send_email = $get_data->send_email;


if($jwt['result'])
{
    $get_result= $merchant->add_SecondryUser($name,$email,$password,$check_listing,$billing_info,$send_email,$jwt["data"]->id);
    if ($get_result==true)
    {
      http_response_code(200);
      echo json_encode(array("message" => "Add Secondry User Successful!."));
    }
  
    else
    {
      http_response_code(400);
      echo json_encode(array("message" => "Add Secondry User UnSuccessful!."));
    }
}
else
{
  http_response_code(500);
  echo json_encode(array("message" => "Bad Request"));

}








?>