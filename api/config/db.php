<?php
// used to get DataBase Connection
class Database{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $db_name = "email_service";
    public $conn = null;


    // get connection
    public function __construct()
    {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($conn->connect_error) {
            die("connection failed: " . $conn->connect_error);
        } else {
            // echo "connected";
            $this->connection = $conn;
        }
    }

    // get connection reference
    public function get_connection()
    {
        return $this->connection;
    }
    public function close_connection()
    {
        $this->connection->close();
    }
}

?>