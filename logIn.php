<?php


//headers required
header("Access-Control-Allow-Origin: http://localhost/email%20service%20provider/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once 'api/config/db.php';
include_once 'api/objects/merchant.php';

// get database connection
$database = new Database();
$db = $database->get_connection();
 
// instantiate merchant object
$merchant = new Merchant($db);


// get posted data
$data = json_decode(file_get_contents("php://input", true));
 
// get property values
$merchant->email = $data->email;
// $merchant->password = $data->password;
$email = $data->email;


$email_exists = $merchant->emailExists($email);

// generate json web token
include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// check if email exists and if password is correct

if($email_exists && $data->password==$merchant->password){
 
    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $merchant->id,
           "name" => $merchant->name,
           "email" => $merchant->email,
        //    "password" => $merchant->password
       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt
            )
        );
        
 
}
 
// login failed
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
?>