<?php

namespace Core\Session;

class Session
{
    static $instance;

    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function __construct()
    {
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
       
    }

    /**
     * @param string|null $type
     * @param string $message
     */
    public function setFlash(?string $type, string $message): void
    {
        $type = isset($type) ? $type : 'success';
        $flash = "<div class='alert alert-{$type}'>" . $message . '</div>';
        $_SESSION['flash'][$type] = $flash;
    }

    /**
     * @return bool
     */
    public function hasFlashes(): bool
    {
        return isset($_SESSION['flash']);
    }

    /**
     *
     * @return mixed
     */
    public function getFlashes()
    {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * @param $key
     * @param $value
     */
    public function write($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function read($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * @param $key
     */
    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function authorized()
    {
        if (($this->read('user'))) {
            if ($this->read('user')->user_role === '0' || $this->read('user')->user_role === '1') {
                return true;
            }
                return false;
        }
    }
    
    public function isAdmin()
    {
        if (($this->read('user'))) {
            if ($this->read('user')->user_role === '0') {
                return true;
            }
            return false;
        }
    }
}