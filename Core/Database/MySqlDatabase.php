<?php

namespace Core\Database;


use \PDO;

class MySqlDatabase extends Database
{
    private $db_name;
    private $db_user;
    private $db_pwd;
    private $db_host;
    private $pdo;

    public function __construct($db_name, $db_user, $db_pwd, $db_host)
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pwd = $db_pwd;
        $this->db_host = $db_host;
    }

    private function getPDO()
    {
        if ($this->pdo === null) {
            $pdo = new PDO('mysql:dbname=' . $this->db_name . ';host=' . $this->db_host, $this->db_user, $this->db_pwd , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        }
        return $this->pdo;
    }

    /**
     * query
     *
     * @param  String $statement la requette sql
     * @param  array $attributes
     * @param  String $class_name   exemple: App\Model\Entity\CoursesEntity
     * @param  bool $one : true => fetch (un seul enregestrement). false => fetchAll
     * @return array d'objet  exemple : App\Model\Entity\CoursesEntity
     */
    public function query($statement, $class_name = null, $one = false) : array
    {
        try {
            $req = $this->getPDO()->query($statement);
            if (
                strpos($statement, 'UPDATE') === 0 ||
                strpos($statement, 'INSERT') === 0 ||
                strpos($statement, 'DELETE') === 0
            ) {

                return $req;

            }
            if ($class_name === null) {
                $req->setFetchMode(PDO::FETCH_OBJ);
            } else {
                $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
            }
            if ($one) {
                $data = $req->fetch();
            } else {
                $data = $req->fetchAll();
            }
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * prepare
     *
     * @param  String $statement la requette sql
     * @param  array $attributes
     * @param  String $class_name   exemple: App\Model\Entity\CoursesEntity
     * @param  bool $one : true => fetch (un seul enregestrement). false => fetchAll
     * @return array|object    exemple : App\Model\Entity\CoursesEntity
     */
    public function prepare($statement, $attributes, $class_name = null, $one = false)
    {
        $req = $this->getPDO()->prepare($statement);
        //TODO chick $attributes[0]
        $res = $req->execute($attributes);
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ) {
            return $res;
        }
        if ($class_name === null) {
            $req->setFetchMode(PDO::FETCH_OBJ);
        } else {
           $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
        }
        if ($one) {
            $data = $req->fetch();
        } else {
            $data = $req->fetchAll();
        }
       
        return $data;
    }

    public function lastInsertId()
    {
        return $this->getPDO()->lastInsertId();
    }
}