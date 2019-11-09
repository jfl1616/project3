<?php


namespace Bolzen\Src\Model\ActivationToken;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Email\EmailModel;
use Bolzen\Src\Model\Profile\ProfileModel;
use Bolzen\Src\Model\Registration\RegistrationModel;

class ActivationTokenModel extends Model
{
    private $table,$token,$emailModel,$profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->table = "ActivationToken";
        $this->emailModel = new EmailModel();
        $this->profileModel = new ProfileModel();
    }

    public function add(string $username, \DateTime $duration):bool {
        if(empty($username) || empty($duration)){
            $this->setError("Username and duration are required.");
            return false;
        }
        if(!$this->user->hasUser($username)){
            $this->setError("Invalid Username");
            return false;
        }
        if($duration < new \DateTime()){
            $this->setError("Duration cannot be past.");
            return false;
        }

        $duration = $duration->format('Y-m-d H:i:s');
        $column = "username,token,duration";
        $this->token = $this->accessControl->generateRandomToken();
        $bindings=array($username, $this->token, $duration);

        if(!$this->database->insert($this->table, $column, $bindings)){
            $this->setError("Unable to insert the activation token.");
            return false;
        }
        return true;
    }


    public function send(string $email, string $username, string $firstName, \Twig_Environment $twig):bool{
        if(!$this->hasToken($this->token)){
            return false;
        }
        //Create an email to send the user to activate the account.
        $subject = "Activate your account";
        $link = $this->accessControl->getPath(). "activation/" . $username."/". $this->getToken();

        $template = $twig->loadTemplate("Email/activationEmail.php");
        $message = $template->render(array("url"=>$link, "name"=>$firstName));


        if(!$this->emailModel->send($subject, $message,$email)){
            $this->setError($this->emailModel->errorToString());
            return false;
        }
        return true;

    }

    public function resend(string $username, string $token, \Twig_Environment $twig)
    {
        if(!$this->hasToken($token)){
            return false;
        }
        if(!$this->isUserTokenValid($username, $token)){
            return false;
        }

        //Remove the token
        if(!$this->deleteToken($token)){
            return false;
        }

        //Set the random token and expiration datetime
        $duration = new \DateTime();
        $duration->modify("+30 minutes");

        //Insert the new token
        if(!$this->add($username, $duration)){
            return false;
        }

        $email = $this->profileModel->getEmail($username);
        $firstName = $this->profileModel->getFirstName($username);

        if(!$this->send($email, $username, $firstName, $twig)){
            return false;
        }

        if (!$this->save()) {
            $this->setError("Unable to save.");
            return false;
        }

        return true;
    }

    public function activate(string $username, $token):bool{
        if(!$this->hasToken($token)){
            return false;
        }
        if(!$this->user->hasUser($username)){
            $this->setError("Invalid username");
            return false;
        }
        if(!$this->isUserTokenValid($username, $token)){
            return false;
        }

        $where = "username = ? AND token = ?";
        $bindings =  array($username, $token);
        $tokenRecord = $this->database->select($this->table, "duration", $where, $bindings);
        $tokenRecord = $tokenRecord->fetch();
        $duration = new \DateTime($tokenRecord['duration']);
        $current = new \DateTime();

        if ($duration < $current) {
            $this->setError("The token has expired. Please click below button to resend a new token.");
            return false;
        }

        if (!$this->database->delete($this->table, $where, $bindings)) {
            $this->setError("An error occurred while marking your token as used");
            return false;
        }

        // go ahead to update User table activated = true? correct
        if(!$this->user->makeAccountVerified($username)){
            $this->setError("Unable to update the activated value at this time.");
            return false;
        }

        if (!$this->save()) {
            $this->setError("Unable to save.");
            return false;
        }
        return true;
    }

    public function getToken(){
        return $this->token;
    }

    public function hasToken(string $token):bool {
        $where = "token=?";
        $bindings = array($token);
        if($this->database->select($this->table, "token", $where, $bindings)->rowCount() === 0){
            $this->setError("Invalid token");
            return false;
        }
        return true;
    }

    public function deleteToken($token){
        if(!$this->hasToken($token)){
            return false;
        }
        $where= "token=?";
        $bindings=array($token);
        if(!$this->database->delete($this->table, $where, $bindings)){
            $this->setError("Unable to delete token.");
            return false;
        }
        return true;
    }

    public function isUserTokenValid(string $username, $token){
        $where = "username = ? AND token = ?";
        $bindings =  array($username, $token);
        $tokenRecord = $this->database->select($this->table, "username, token", $where, $bindings);
        if($tokenRecord->rowCount() === 0){
            $this->setError("Username and token does not exist.");
            return false;
        }
        return true;
    }

}