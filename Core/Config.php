<?php

namespace Core;

class Config
{
    private $settings = [];
    private static $_instance; // l'instance
    
    /**
     * permet de returner une unique instance 
     * @param $file c'est le chemin de fichier qui contient l'identification pour le base de données
     * 
     */
    public static function getInstance($file){
        if(is_null(self::$_instance)){
            self::$_instance = new Config($file);
        }
        return self::$_instance;
    }
    
    public function __construct($file)
    {
        //$this->id = uniqid();
        $this->settings = require $file;
    }
    /** permet de récupérer les clés 
     * @param $key 
     * @return le clé 
     */
    public function getKey($key)
    {
        if(!isset($this->settings[$key])){
            return null;
        }
        return $this->settings[$key];
    }
}