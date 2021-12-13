<?php

namespace App\Model;

use Exception;
use mysqli;

class DbConnectionManager
{

    private $connectionParam;

    /**
     * @var mysqli
     */
    private $db;

    /**
     * DbConnectionManager constructor.
     * @param null $appConfig
     * @throws Exception
     */
    public function __construct($appConfig = null)
    {
        $this->connectionParam = $appConfig;
        $this->db = $this->connect();

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            $this->install();
            $this->db = $this->connect();
        }
        if (mysqli_connect_errno()) {
            throw new Exception(sprintf("Connect failed: %s\n", mysqli_connect_error()));
        }
    }

    /**
     * @return mysqli
     */
    private function connect()
    {
        $this->db = new mysqli(
            $this->connectionParam['host'],
            $this->connectionParam['user'],
            $this->connectionParam['password'],
            $this->connectionParam['dbname']
        );
        return $this->db;
    }

    /**
     * Install te database if doesn't exist
     *
     * @throws Exception
     */
    private function install()
    {
        $output = [];
        $conn = new mysqli($this->connectionParam['host'], $this->connectionParam['user'], $this->connectionParam['password']);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        $sql = "CREATE DATABASE " . $this->connectionParam['dbname'];
        if ($conn->query($sql) === TRUE) {
            echo "Database created successfully";
        } else {
            $output[] = "<br />Error creating database: " . $conn->error;
        }

        $conn = $this->connect();
        $conn->store_result();
        $sql = file_get_contents('../data/schema.sql');
        if (mysqli_multi_query($conn, $sql)) {
            $output[] = "<br />SQL installation script is executed successfully";
        } else {
            throw new Exception("Error of database setting up: " . $conn->error);
        }
        $conn->close();
    }

    /**
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->db;
    }
}