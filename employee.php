<?php

class Employee
{
    public $id;
    public $name;
    public $age;
    public $address;
    public $tax;
    public $salary;

    public function __construct($name, $age, $address, $tax, $salary)
    {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
        $this->salary = $salary;
        $this->tax = $tax;
    }

    public function __get($prop)
    {
        return $this->prop;
    }
    
    public function calcSalary()
    {
        return $this->salary - ($this->salary * $this->tax / 100);
    }

}