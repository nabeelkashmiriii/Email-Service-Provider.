<?php
// headers required
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
$data = json_decode(file_get_contents("php://input"));

// Set Property values
$merchant->name = $data->name;
$merchant->email = $data->email;
$merchant->password = $data->password;

// Create Merchant
if(
    !empty($merchant->name) &&
    !empty($merchant->email) &&
    !empty($merchant->password) &&
    $merchant->create($merchant->name, $merchant->email, $merchant->password)
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "Merchant Account created."));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create Merchant Account."));
}
?>