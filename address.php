<?php
class Address {
    private $fname;
    private $lname;
    private $street;
    private $city;
    private $state;
    private $country;
    private $zip;

    public function __construct($fname = "", $lname = "", $street = "", $city = "", $state = "", $country = "", $zip = "") {
        $this->fname = $fname;
        $this->lname = $lname;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->zip = $zip;
    }
    //changed to __get
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

   //changed to __set
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    public function __toString() {
        return "$this->fname, $this->lname, $this->street, $this->city, $this->state, $this->country, $this->zip";
    }

    public function toTSV(){
       return implode("\t", [$this->fname,$this->lname,$this->street, $this->city, $this->state, $this->country, $this->zip]);
    }
    

    public function fromTSV($tsvString) {
        list($this->fname,$this->lname,$this->street, $this->city, $this->state, $this->country, $this->zip) = explode("\t", $tsvString);
        return $this;
    }
    
    //returns "last,First"
    public function getFormattedName() {
        return $this->lname . ", " . $this->fname;
    }

    //return formatted address
    public function getFullAddress() {
        return "{$this->street}<br/>{$this->city}, {$this->state} {$this->zip}<br/>{$this->country}";
    }
}


?>