<?php


namespace Core\Auth;

use Core\Session\Session;

trait TraitAuth
{
    /**
     * hash un password 
     */
    public function hashPassword($password){
        return  password_hash($password,PASSWORD_BCRYPT);
    }
    /**
     * return un random token alphnomirique
     */
    public function random($length)
    {
        $str = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPMLKJHGFDSQWXCVBN";
        return utf8_encode(substr(str_shuffle(str_repeat($str, $length)), 0, $length));
    }
    /**
     * return un random token nomirique
     */
    public function randomNumber($length)
    {
        $str = "0123456789";
        return utf8_encode(substr(str_shuffle(str_repeat($str, $length)), 0, $length));
    }

   
}