<?php

namespace App\DB;

use App\Resource\Category;

class Mysql extends PDO {

    public function __construct($host, $db, $user, $pass) {

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->connection = new \PDO($dsn, $user, $pass, $options);

        $this->categories = array_map(
            fn($data) => new Category($data, $this),
            $this->getRows("categories")
        );

    }

}





