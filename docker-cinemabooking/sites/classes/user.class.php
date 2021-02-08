<?php
    class User{
        public $id;
        public $username;
        public $password;

        public function __construct($id, $username, $password)
        {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
        }

        public function logincheck($username, $password){
            if($this->username != $username){
                return false;
            }
            else{
                if($this->password != hash("sha256", $username.$password)){
                    return false;
                }
                else{
                    return true;
                }
            }
        }
    }
?>