<?php

namespace App\ORM;

class Car extends \Tina4\ORM
{
    // Define primary key
    public $primaryKey = "id";

    // Define the properties that will map to the database columns
    public $id;
    public $brandName;  // maps to 'brand_name' column in the database
    public $year;       // maps to 'year' column in the database

    // Optionally, you can define the table name explicitly
    public $tableName = "car";  // Defaults to "car" because of the class name
}
