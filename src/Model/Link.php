<?php

namespace App\Model;

use Exception;
use mysqli;

class Link
{
    /**
     * @var mysqli
     */
    private $db;

    /**
     * Link constructor.
     * @param $dbConnection
     * @throws Exception
     */
    public function __construct($dbConnection)
    {
        if ($dbConnection instanceof mysqli) {
            $this->db = $dbConnection;
        } else {
            throw new Exception('Connection injected should be of Mysqli object');
        }
    }

    /**
     * looking for a short code in the database
     *
     * @param $short
     * @return array|mixed|null
     */
    public function findLink($short)
    {
        $query = "SELECT link FROM links WHERE short LIKE '%s'";
        $query = sprintf($query, $this->db->real_escape_string($short));
        if ($result = $this->db->query($query)) {
            $row = $result->fetch_assoc();
        } else {
            die($this->db->error);
        }
        if($row) {
            return $row['link'];
        }
        return $row;
    }

    /**
     * inserting the short code to the database
     *
     * @param $link
     * @param $short
     * @return bool
     */
    public function addLink($link, $short)
    {
        $query = "INSERT INTO links (link, short, valid_until) VALUES ('%s', '%s', null)";
        $query = sprintf($query, $this->db->real_escape_string($link), $this->db->real_escape_string($short));
        if ($this->db->query($query)) {
            return true;
        } else {
            die($this->db->error);
        }
    }
}