<?php
class Reservation{

    // database connection and table name
    private $conn;
    private $table_name = "reservation";

    // object properties
    public $id;
    public $requestid;
    public $computerid;
    public $imageid;
    public $managementnodeid;
    public $remoteIP;
    public $lastcheck;
    public $pw;
    public $connectIP;
    public $connectport;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    function getReservation(){

        // select all query
        $query = "";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    function getAllReservations(){
      // select all query
      $query = "";

      // prepare query statement
      $stmt = $this->conn->prepare($query);

      // execute query
      $stmt->execute();

      return $stmt;
    }
}
