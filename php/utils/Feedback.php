<?php

class Feedback implements JsonSerializable{

    private $data;
    private $success;
    private $message;

    function __construct($data, $success, $message) {
        $this->data = $data;
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the value of success
     */ 
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    function JsonSerialize(){
        return 
            [
            "data" => $this->data,
            "success" => $this->success,
            "message" => $this->message
            ];
    }
}