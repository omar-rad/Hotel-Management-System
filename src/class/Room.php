<?php

class Room {

    private $number, $floor, $type, $capacity, $price,
            $wifi, $breakfast, $smoking, $description, $dir, $available;

    function __construct($row) {
        $this->number = intval($row[0]);
        $this->floor = intval($row[1]);
        $this->type = ucfirst($row[2]);
        $this->capacity = intval($row[3]);
        $this->price = floatval($row[4]);
        $this->wifi = intval($row[5]) ? true : false;
        $this->breakfast = intval($row[6]) ? true : false;
        $this->smoking = intval($row[7]) ? true : false;
        $this->description = $row[8];
        $this->dir = './img/' . $row[9] . '/';
        $this->available = intval($row[10]);
    }

    function getNumber() {
        return $this->number;
    }

    function getFloor() {
        return $this->floor;
    }

    function getType() {
        return $this->type;
    }

    function getCapacity() {
        return $this->capacity;
    }

    function getPrice() {
        return $this->price;
    }

    function hasWifi() {
        return $this->wifi;
    }

    function hasBreakfast() {
        return $this->breakfast;
    }

    function hasSmoking() {
        return $this->smoking;
    }

    function getDescription() {
        return $this->description;
    }

    function getDir() {
        return $this->dir;
    }

    function isAvailable() {
        return $this->available;
    }

}
