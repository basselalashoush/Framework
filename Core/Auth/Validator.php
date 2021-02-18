<?php


namespace Core\Auth;


class Validator
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    private function getField($field)
    {
        if (!isset($this->data[$field])) {
            return null;
        }
        return $this->data[$field];
    }

    public function isAlphanumerique($field, $errorMsg)
    {
        if (!preg_match('/^[a-zA-Z0-9_@.]+$/', $this->getField($field))) {
            $this->errors[$field] = $errorMsg;
        }
    }

    public function isEmpty($field, $errorMsg)
    {
        if (empty($this->getField($field))) {
            $this->errors[$field] = $errorMsg;
        }
    }
    /**
     * vérifier si le psudo soit le mail déjà utilisés 
     */
    public function isUniq($field, $notUniq, $errorMsg)
    {
        if ($notUniq) {
            $this->errors[$this->getField($field)] = $errorMsg;
        }
    }

    /**
     * vérifier si le mail est valide  
     */
    public function isEmail($field, $errorMsg)
    {
        if (!filter_var($this->getField($field), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $errorMsg;
        }
    }
    /**
     * vérifier si les deux mots de passes sont identique 
     */
    public function isConfirmed($field, $errorMsg)
    {
        if (empty($this->getField($field)) || $this->getField($field) != $this->getField($field . '_confirm')) {
            $this->errors[$field] = $errorMsg;
        }
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * vérifier les données inserets par l'utilisateur
     */
    public function verify()
    {
        foreach ($_POST as $k => $v) {
            if(empty($v)){
                $this->errors[$k] = "veuillez entrer votre $k";

            }
            if($k === 'user_email'){
                $this->isEmail($k,'veuillez entrer une adresse email valide');
            }
            if($k === 'user_pseudo'){
                $this->isAlphanumerique($k,'votre pseudo n\'est pas valide(alphanumérique)');
            }
            if($k === 'user_password'){
                $this->isConfirmed($k,'merci de rentrer un mot de passe valide');
            }

        }
    }
}