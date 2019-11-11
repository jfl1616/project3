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
        $this->token = hash('sha256', $username . $this->accessControl->generateRandomToken());
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
        $link = $this->accessControl->getPath(). "activation/" . $this->getToken();

        $template = $twig->loadTemplate("Email/activationEmail.php");
        $message = $template->render(array("url"=>$link, "name"=>$firstName));


        if(!$this->emailModel->send($subject, $message,$email)){
            $this->setError($this->emailModel->errorToString());
            return false;
        }
        return true;

    }

    public function resend(string $reCAPTCHA, string $email, \Twig_Environment $twig): bool{
        $secretKey = "6Lco-MEUAAAAAKXTp1dj_tPeiOck0mfSqGPDi0va";

        // Verify the reCAPTCHA response
        if(!empty($recaptcha)){
            $this->setError("Please check on the reCAPTCHA box.");
            return false;
        }

        // Verify the reCAPTCHA response
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey.
            '&response='.$_POST['g-recaptcha-response']);

        // Decode json data
        $responseData = json_decode($verifyResponse);

        // If reCAPTCHA response is valid
        if(!$responseData->success){
            $this->setError("Robot vertification failed, please try again.");
            return false;
        }

        if(!$this->profileModel->hasEmail($email)){
            $this->setError("The email address you entered does not exist in our system.");
            return false;
        }

        $username = $this->profileModel->getUsername($email);
        $firstName = $this->profileModel->getFirstName($username);
        $token = $this->getToken($username);

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

        if(!$this->send($email, $username, $firstName, $twig)){
            return false;
        }

        if (!$this->save()) {
            $this->setError("Unable to save.");
            return false;
        }

        return true;
    }

    public function activate(string $token):bool{
        if(!$this->hasToken($token)){
            return false;
        }

        $where = "token = ?";
        $bindings =  array($token);
        $tokenRecord = $this->database->select($this->table, "duration, username", $where, $bindings);
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

        if(!$this->user->makeAccountVerified($tokenRecord['username'])){
            $this->setError("Unable to update the activated value at this time.");
            return false;
        }

        if (!$this->save()) {
            $this->setError("Unable to save.");
            return false;
        }
        return true;
    }

    public function getToken($username=""){
        if(!empty($this->token)){
            return $this->token;
        }
        else{
            $where = "username=?";
            $bindings = array($username);
            $columns = "token";
            $result = $this->database->select($this->table, $columns, $where, $bindings);
            if($result->rowCount() === 0) return "";
            else{
                $result = $result->fetch();
                $this->token = $result["token"];
            }
        }
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

}