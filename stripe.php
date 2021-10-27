<?php
//headers required
header("Access-Control-Allow-Origin: http://localhost/email%20service%20provider/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Origin, Authorization, X-Requested-With");


require 'vendor/autoload.php';


// Files required
include_once 'api/objects/merchant.php';
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


$number = $get_data->number;
$cvc = $get_data->cvc;
$amount = $get_data->amount;


$stripe =  new \Stripe\StripeClient("sk_test_51Jp7U4CFI5NpalIIngo3coA8kupctqevxQuaHeHcnm8AjBfJEyiljJ9fT88DNqNcVdxfNBdS9Ioeclya4Z0vhVPK003fjiTOng");

$token = $stripe->tokens->create([
    'card' => [
    //   'number' => '4242424242424242',
    // 'exp_month' => 10,
    //   'exp_year' => 2022,
    //   'cvc' => '314',
    'number' => $number,
      'exp_month' => 10,
      'exp_year' => 2022,
      'cvc' => $cvc
    ],
  ]);


$customer=$stripe->customers->create([
    
    'description' => 'This Merchant',
    'source'=>$token->id
  ]);

 $charges=$stripe->charges->create([
    'amount' => $amount,
    'currency' => 'usd',
    'customer' => $customer->id,
    'description' => 'Amount Recharge'
  ]);

// call Recharge balance function
$get_result = $merchant->recharge_balance($number, $amount, $cvc, $jwt["data"]->name, $jwt["data"]->email);
if (!$get_result == true)
  {
    http_response_code(200);
    echo json_encode(array("message" => "Balance recharge Successful!."));
  }

  else
  {
    http_response_code(400);
    echo json_encode(array("message" => " UnSuccessful Transaction!."));
  }


?>