<?php

class Feedback implements JsonSerializable{

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

    function JsonSerialize(){
		echo json_encode(
			[
            "id" => $this->id,
            "success" => $this->success,
            "text" => $this->text
			],
			JSON_PRETTY_PRINT
		);
    }
}