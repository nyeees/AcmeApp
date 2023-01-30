<?php

class Customer{
    public $id;
    public $firstname;
    public $lastname;
    public $gastoProductos;


    function __construct(int $id, String $firstname, String $lastname) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
    
    function setGastoProductos($gastoProductos){
        $this->gastoProductosProductos=$gastoProductos;
    }
    
    function getId() {
        return $this->id;
    
    }

    function getFirstname() {
        return $this->firstname;
    }

    function getLastname() {
        return $this->lastname;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    function setLastname($lastname) {
        $this->lastname = $lastname;
    }


}

class Product {
    public $id;
    public $name;
    public $cost;
 
    function __construct(int $id, String $name, float $cost) {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
    }

    function getId(){
        return $this->id;
    }
    function getName() {
        return $this->name;
    }

    function getCost() {
        return $this->cost;
    }

    function setId($id){
        $this-> id=$id;
    }
    function setName($name) {
        $this->name = $name;
    }

    function setCost($cost) {
        $this->cost = $cost;
    }


}

class Order{
    public $id;
    public $idCustomer;
    public $idProducts;
 
    function __construct(int $id, int $idCustomer, String $idProducts) { //Debido a que hay varios idProductos lo cogeremos como String
        $this->id = $id;
        $this->idCustomer = $idCustomer;
        $this->idProducts = $idProducts;
    }
    
    function getId() {
        return $this->id;
    }

    function getIdCustomer() {
    return $this->idCustomer;
    }

    function getIdProducts() {
        return $this->idProducts;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdCostumer($idCustomer) {
        $this->idCostumer = $idCustomer;
    }

    function setIdProducts($idProducts) {
        $this->idProducts = $idProducts;
    }
}


