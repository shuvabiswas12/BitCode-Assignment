<?php
/**
 * Created by VsCode
 * User: Shuva Biswas
 * Date: 10/08/2022
 * Time: 2:17 PM
 */

namespace App\Model;
use PDO, PDOException;

class Database
{
    protected $dbconnection;

    private function __construct()
    {
        try {
            $this->dbconnection = new PDO("mysql:host=localhost;dbname=assignment", "root", "");
            $this->dbconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getDbInstance()
    {
        return $this->dbconnection;
    }

    public function retrieve($query)
    {
        $statement = $this->dbconnection->prepare($query);
        $statement->execute();
        $data = $statement->fetch();
        return $data;
    }

    public function delete_Update_Store($query)
    {
        $statement = $this->dbconnection->prepare($query);
        $statement->execute();
        return true;
    }


}

