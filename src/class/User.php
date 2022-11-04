<?php

class User {

    private $email, $fName, $lName, $phone, $birth;

    function __construct($row) {
        $this->email = $row[0];
        $this->fName = $row[1];
        $this->lName = $row[2];
        $this->phone = $row[4];
        $this->birth = $row[5];
    }

    function getEmail() {
        return $this->email;
    }

    function getFName() {
        return $this->fName;
    }

    function getLName() {
        return $this->lName;
    }

    function getPhone() {
        return $this->phone;
    }

    function getBirth() {
        return $this->birth;
    }

}
