<?php

class Feedback implements JsonSerializable{

    private $data;
    private $success;
    private $text;

    function __construct($data, $success, $text) {
        $this->data = $data;
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
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    function JsonSerialize(){
		echo json_encode(
			[
            "data" => $this->data,
            "success" => $this->success,
            "text" => $this->text
			],
			JSON_PRETTY_PRINT
		);
    }
}