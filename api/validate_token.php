<?php
//headers required
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required files
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

// get posted data
class JwtValidate{

    private $key;

    function __construct($key)
    {
        $this->key=$key;
    }

    public function jwt_validate($jwt)
    {
            if(isset($jwt)){
       
                // if decode succeed, show user details
                try {
                    // decode jwt
                    $decoded = JWT::decode($jwt, $this->key, array('HS256'));
           
                    // set response code
                    http_response_code(200);
           
                    // show user details
                    echo json_encode(array(
                        "message" => "Access granted.",
                        "data" => $decoded->data
                    ));
                    return array("result"=>true,"data"=>$decoded->data);
           
                }
                catch (Exception $e){
       
                    // set response code
                    http_response_code(401);
               
                    // tell the user access denied  & show error message
                    echo json_encode(array(
                        "message" => "Access denied.",
                        "error" => $e->getMessage()
                    ));

                    return array("result"=>false,"email"=>NULL);
                }
           
                // catch will be here
            }
            else{
 
                // set response code
                http_response_code(401);
             
                // tell the user access denied
                echo json_encode(array("message" => "Access denied."));
                return array("result"=>false,"email"=>NULL);
            }
        }
    }


?>