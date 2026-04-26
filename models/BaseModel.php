<?php
class BaseModel {

    protected $connect;

    public function __construct(){
    
  

       $this->connect = new mysqli("localhost", "root", "", "database");
        if ($this->connect->connect_error) {
            die("Connection failed: " . $this->connect->connect_error);
        }
    }

    // Optional: helper for executing queries safely
    protected function execute($stmt){
        if(!$stmt->execute()){
            die("Query Error: " . $stmt->error);
        }
        return $stmt->get_result();
    }
}
