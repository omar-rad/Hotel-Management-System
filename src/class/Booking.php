<?php

class Booking {

    private $confirmation, $email, $number, $cin, $cout, $guests, $price, $ccn;

    function __construct($row) {
        $this->confirmation = $row[0];
        $this->email = $row[1];
        $this->number = intval($row[2]);
        $this->cin = $row[3];
        $this->cout = $row[4];
        $this->guests = $row[5];
        $this->price = floatval($row[6]);
        $this->ccn = $row[7];
    }

    function getConfirmation() {
        return $this->confirmation;
    }

    function getEmail() {
        return $this->email;
    }

    function getNumber() {
        return $this->number;
    }

    function getCin() {
        return $this->cin;
    }

    function getCout() {
        return $this->cout;
    }

    function getGuests() {
        return $this->guests;
    }

    function getPrice() {
        return $this->price;
    }

    function getCcn() {
        return $this->ccn;
    }

}
