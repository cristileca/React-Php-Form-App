<?php

class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbname;

    private $conn;

    public function __construct()
    {

        $this->host = 'localhost';
        $this->user = 'root';
        $this->pass = '';
        $this->dbname = 'form_submission';

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            exit("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
