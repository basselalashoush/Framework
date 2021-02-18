<?php

namespace Core\Model;


use Core\Database\Database;


class Table
{
    protected $table;
    protected $db;
    /**
     * @param $db Core\Database\Database
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        if (is_null($this->table)) {
            $parts = explode('\\', get_class($this));
            $className = end($parts);
            $this->table = strtolower(str_replace('Table', '', $className));
        }
    }
    /**
     * get the table name
     * @return tableName string
     */
    private function getTable():string
    {
        if ($this->table === null) {
            $className = explode('\\', get_called_class());
            $this->table = strtolower(str_replace('Table', '', end($className)));
        }
        return $this->table;
    }
    /**
     * récupérer le ID de la table
     * @return id string
     */
    private function getIdTable() :string
    {
        $table = $this->getTable();
        if ($table === "categories") {
            $idTable = "category_id";
        } else {
            $idTable = strtolower(substr($table, 0, -1)) . "_id";
        }

        return $idTable;
    }

    /**
     * @param array $attributes
     * @return array de \App\Model\Entity\
     */
    public function all( $attributes = null):array
    {
        $statement = "SELECT * from " . $this->getTable();
        if ($attributes){
            $keys = array_keys($attributes);
            $fields = '`' . implode('`=? AND `', $keys) . '`';
            $fields .= '=?';
            $values = array_values($attributes);
            $statement .= " WHERE $fields";
            return $this->query($statement,$values);
        }
        return $this->query($statement);
    }

    public function isUniq($field, $check_field)
    {
        $statement = "SELECT $field FROM " . $this->getTable() . " WHERE $field =?";
        return $this->query($statement, [$check_field], true);
    }

    /**
     * get by id
     * @param $id
     
     */
    public function find($id)
    {
        $idTable = $this->getIdTable();
        $statement = "SELECT * FROM " . $this->getTable() . " WHERE $idTable = ?";
        return $this->query($statement, [$id], true);
    }
    /**
     * get by properties
     * @param $inputs properties 
     * @param $table  tableName|null
     *  
     */
    public function findBy($inputs, $table = null)
    {
        
        if($table === null){
            $table = $this->getTable();
        }
        $values = array_values($inputs);
        $keys = array_keys($inputs);
        $fields = '`' . implode('`=? AND `', $keys) . '`';
        $statement = "SELECT * FROM " . $table . " WHERE $fields = ?";
        return $this->query($statement, $values, true);
    }

    public function add(array $inputs)
    {
        $keys = array_keys($inputs);
        $fields = '`' . implode('`,`', $keys) . '`';
        $placeholder = substr(str_repeat('?,', count($keys)), 0, -1);
        $values = array_values($inputs);
            $statement = "INSERT INTO " . $this->getTable() . " ($fields) VALUES ($placeholder)";
            return $this->query($statement, $values);
    }

    public function update(array $inputs)
    {
        $values = array_values($inputs);
        unset($inputs[$this->getIdTable()]);
        $keys = array_keys($inputs);
        $fields = '`' . implode('`=?, `', $keys) . '`';
        $fields .= '=?';
        
            $statement = "UPDATE " . $this->getTable() . " SET  $fields WHERE " . $this->getIdTable() . "= ?";
   
           return $this->query($statement, $values);


    }
    public function delete($inputs){
        $values = array_values($inputs);
        $keys = array_keys($inputs);
        $fields = '`' . implode('`=? AND `', $keys) . '`';
        $fields .= '=?';
        $statement = "DELETE FROM ".$this->getTable() ." WHERE $fields ";
        return $this->query($statement, $values);
    }
   
        /**
     * query
     *
     * @param  String $statement la requette sql
     * @param  array|null  $attributes
     * @param  bool $one : true => fetch (un seul enregestrement). false => fetchAll
     * @return array d'objet  exemple : App\Model\Entity\CoursesEntity
     */
    public function query($statement, $attributes = null, $one = false)
    {
        if ($attributes) {
            return $this->db->prepare($statement, $attributes, str_replace('Table', 'Entity', get_called_class()), $one);
        } else {
            return $this->db->query($statement, str_replace('Table', 'Entity', get_called_class()), $one);
        }

    }


    public function lastInsertId(){
        return $this->db->lastInsertId();
    }

    public function extract($key, $value){
        $records = $this->all();
        $return = [];
        foreach($records as $v){
            $return[$v->$key] = $v->$value;
        }
        return $return;
    }
}