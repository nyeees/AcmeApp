<?php

class Customer{
    public $id;
    public $firstname;
    public $lastname;
    public $objNum; //Para saber la posicion de cada cliente creado
    public static $count = 0;//Para saber el numero de clientes creados
    


    function __construct(int $id, String $firstname, String $lastname) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        self::$count++; 
        $this->objNum = Customer::getCount();
    }
    
    function getObjNum() {
        return $this->objNum;
    }

    static function getCount() {
        return self::$count;
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
    public $objNum; //Para saber la posicion de cada producto creado
    public static $count = 0;
    
    
    
    function __construct(int $id, String $name, float $cost) {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
        self::$count++;
        $this->objNum = Product::getCount();
    }

    function getObjNum() {
        return $this->objNum;
    }

    static function getCount() {
        return self::$count;
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
    public $objNum; //Para saber la posicion de cada orden creada
    public static $count = 0;
    
    function __construct(int $id, int $idCustomer, String $idProducts) { //Debido a que hay varios idProductos lo cogeremos como String
        $this->id = $id;
        $this->idCustomer = $idCustomer;
        $this->idProducts = $idProducts;
        self::$count++;
        $this->objNum = Order::getCount();
    }
    
    function getObjNum() {
        return $this->objNum;
    }

    static function getCount() {
        return self::$count;
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


