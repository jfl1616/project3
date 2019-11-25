<?php


namespace Bolzen\Src\Model\Profile;


use Bolzen\Core\Model\Model;

class ProfileModel extends Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "Profile";
    }

    public function add(string $username, string $email, string $firstName, string $lastName, string $country):bool {
        $username = trim($username);
        $email = trim($email);
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $country = trim($country);

        if(empty($username) || empty($email) || empty($firstName) || empty($lastName) || empty($country)){
            $this->setError("All parameters are required.");
            return false;
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setError("Please enter a valid email address");
            return false;
        }

        //disable it for temporarily.
//        if($this->hasEmail($email)){
//            return false;
//        }

        $columns = "username,email,firstname,lastname,country";
        $bindings = array($username, $email, $firstName, $lastName, $country);
        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert profile.");
            return false;
        }
        return true;
    }
    public function hasEmail(string $email): bool{
        $where = "email = ?";
        $bindings = array($email);
        if($this->database->select($this->table, "email", $where, $bindings)->rowCount() > 0 ){
            $this->setError("Oops! Email is already taken. Click 'Forget my password' to recover the username or password.");
            return true;
        }
        return false;
    }
    public function getEmail(string $username):string
    {
        $result = $this->get($username);
        return empty($result) ? "" : $result['email'];
    }

    public function getFirstName(string $username):string{
        $result = $this->get($username);
        return empty($result) ? "": $result['firstname'];
    }
    public function getLastName(string $username):string{
        $result = $this->get($username);
        return empty($result) ? "": $result['lastname'];
    }

    public function getUsername(string $email):string{
        if(empty($email)){
            $this->setError("email cannot be empty");
            return "";
        }
        $columns = "username";
        $where = "email = ?";
        $bindings = array($email);
        $result = $this->database->select($this->table, $columns, $where, $bindings);
        if($result->rowCount() === 0) return "";
        else{
            $result = $result->fetch();
            return $result["username"];
        }
    }

    public function get(string $username):array{
        if (empty($username)) {
            $this->setError("username cannot be empty");
            return array();
        }
        $columns = "*";
        $where = "username = ?";
        $bindings = array($username);

        $result = $this->database->select($this->table,$columns, $where, $bindings);

        return $result->rowCount() === 0 ? array() : $result->fetch();

    }

    public function listUser():array {
        $column = "username, firstname, lastname";
        $result = $this->database->select($this->table, $column);
        return $result->rowCount() === 0 ? array() : $result->fetchAll();
    }
}