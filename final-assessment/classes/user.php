<?php

    class Person
    {
        public $name;
        private $email;
        protected $role = "User";

        public function __construct($name, $email)
        {
            $this->name = $name;
            $this->email = $email;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function showDetails()
        {
            return "User Name: " . $this->name
                . "\nRole: " . $this->role;
        }
    }

    class User extends Person
    {
        public function showDetails()
        {
            echo "User Name: " . $this->name . "<br>";
            echo "Role: " . $this->role . "<br>";
        }
    }
