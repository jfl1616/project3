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
    /*
     * Insert the user's profile data into the Profile database.
     */
    public function add(string $username, string $email, string $firstName, string $lastName, string $country):bool {
        $username = trim($username);
        $email = trim($email);
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $country = trim($country);

        //All parameters cannot be empty or null.
        if(empty($username) || empty($email) || empty($firstName) || empty($lastName) || empty($country)){
            $this->setError("All parameters are required.");
            return false;
        }
        //A valid email address is required.
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setError("Please enter a valid email address");
            return false;
        }
        //Duplicate email is not allowed.
        if($this->hasEmail($email)){
            return false;
        }

        $columns = "username,email,firstname,lastname,country";
        $bindings = array($username, $email, $firstName, $lastName, $country);
        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert profile.");
            return false;
        }
        return true;
    }
    /*
     * Return true if email is already existed in the database; otherwise,
     * return false.
     */
    public function hasEmail(string $email): bool{
        $where = "email = ?";
        $bindings = array($email);
        if($this->database->select($this->table, "email", $where, $bindings)->rowCount() > 0 ){
            $this->setError("Oops! Email is already taken. Click 'Forget my password' to recover the username or password.");
            return true;
        }
        return false;
    }
    /*
     * Return the user's email address.
     */
    public function getEmail(string $username):string
    {
        $result = $this->get($username);
        return empty($result) ? "" : $result['email'];
    }
    /*
     * Return the user's first name
     */
    public function getFirstName(string $username):string{
        $result = $this->get($username);
        return empty($result) ? "": $result['firstname'];
    }
    /*
     * Return the user's last name.
     */
    public function getLastName(string $username):string{
        $result = $this->get($username);
        return empty($result) ? "": $result['lastname'];
    }
    /*
     * Return the user's username based on the email address
     * if it has been found in the database.
     */
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
    /*
     * Return an array with all columns.
     */
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