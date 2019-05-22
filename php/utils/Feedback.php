<?php

class Feedback {

    private $id;
    private $success;
    private $text;

    function __construct($id, $success, $text) {
        $this->id = $id;
        $this->success = $success;
        $this->text = $text;
    }

    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get the value of success
     */ 
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }
}