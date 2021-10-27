<?php
require 'vendor/autoload.php';
use \Mailjet\Resources;

// Class for Merchant
class Merchant{
    // db conn & table name
    private $conn;
    private $table_name = "merchant";

    // object properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $token;
    public $credit;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }



    // Method to create New Merchant
function create($name, $email, $password){
 
    // insert query
    $query = "INSERT INTO $this->table_name (name, email, password) 
    VALUES ('$name', '$email', '$password')";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
    
 
    
 
    // execute the query, also check if query was successful
    if($stmt->execute()){
        return true;
    }
 
    return false;
}



// check  the email exist in the database
function emailExists($email){
 
    // query to check if merchant email exists
    $query = "SELECT id, name, email, password
    FROM $this->table_name
    WHERE email = '$email' 
    LIMIT 0,1 " ;

    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    if($stmt->execute()){
       // get record  values
       $stmt=$stmt->get_result();
        $row = $stmt->fetch_assoc();
 
        // assign values to object properties
        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->email = $row['email'];
        $this->password = $row['password'];

        // return true because email exists in the database
        return true;
    }
    // return false if email does not exist in the database
    return false;
}



// Method to send Email
public function send_mail($from, $to, $from_name, $to_name, $cc, $cc_name, $bcc, $bcc_name, $subject, $description, $html_text, $email)
{
    
    $mj = new \Mailjet\Client('85b1a0ada271782a2c56583cf820c424','8d714b826e2d204e004984926e6f3c60',true,['version' => 'v3.1']);
    $body = [
        'Messages' => [
          [
            'From' => [
              'Email' => $from,
              'Name' => $from_name
            ],
            'To' => [
              [
                'Email' => $to,
                'Name' => $to_name
              ]
            ],
            'Cc' => [
              [
                'Email' => $cc,
                'Name' => $cc_name
              ]
            ],
            'Bcc' => [
              [
                'Email' => $bcc,
                'Name' => $bcc_name
              ]
            ],
            'Subject' => $subject,
            'TextPart' => $description,
            'HTMLPart' => $html_text,
            'CustomID' => "AppGettingStartedTest"
          ]
        ]
      ];
      $response = $mj->post(Resources::$Email, ['body' => $body]);
      if($response->success()==false)
        {
            $query = "SELECT * FROM merchant WHERE email='$email'";
            $result = $this->conn->query($query);
            $result=$result->fetch_assoc();
            $merchant_id=$result['id'];
            $credit=$result['credit']-0.0489;

            $query = "UPDATE merchant SET credit='$credit' WHERE email='$email' ";
            $result = $this->conn->query($query);

            $query = "INSERT INTO transactions (balance, merchant_id) VALUES ('-0.0489', '$merchant_id')";
            $result = $this->conn->query($query);
            $query = "INSERT INTO request (merchant_id,from_email,to_email,cc,bcc,subject,body) VALUES ('$merchant_id','$from','$to','$cc','$bcc','$subject','$description.\n$html_text')";
            var_dump($query);
            $result = $this->conn->query($query);
            var_dump($result);
            return true;
        }

        return false;
        $conn->close();
}

// add secondry Users
public function add_SecondryUser($name, $email, $password, $check_listing, $billing_info, $send_email,$merchant_id){
    $query = "INSERT INTO secondary_user (name, email, password, check_listing, billing_info, send_email, merchant_id)
    values ('$name', '$email', '$password', '$check_listing','$billing_info', '$send_email', '$merchant_id')";
    $result = $this->conn->query($query);

    if($result==true)
    {
      return $result;
    }
    else
    {
      return false;
    }
    $conn->close();
}


// Recharge balance
public function recharge_balance($number, $amount, $cvc, $name, $email){
  $query="select * from merchant where email='$email'";
  $result = $this->conn->query($query);
  $result=$result->fetch_assoc();
  $merchant_id=$result['id'];
  $credit=$result['credit']+$amount;

  $query= "INSERT INTO transactions (Balance, merchant_id, Cr_Db) values('$amount', '$merchant_id', '1')";
  $result = $this->conn->query($query);

  $query = "UPDATE merchant SET credit='$credit' WHERE email='$email' ";
  $result = $this->conn->query($query);

}


}



?>
