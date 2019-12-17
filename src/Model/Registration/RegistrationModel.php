<?php


namespace Bolzen\Src\Model\Registration;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\ActivationToken\ActivationTokenModel;
use Bolzen\Src\Model\Profile\ProfileModel;


class RegistrationModel extends Model
{
    private $accountModel, $activationTokenModel, $profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->accountModel = new AccountModel();
        $this->activationTokenModel = new ActivationTokenModel();
        $this->profileModel = new ProfileModel();
    }
    /*
     * Create an account and store the data into the Account and Profile as long as there is no errors
     */
    public function register(string $username, string $password, string $firstName, string $lastName, string $email, string $confirm, string $country, \Twig_Environment $twig){
        if(!$this->checkPwd($password, $confirm)){
            return false;
        }
        if(!$this->accountModel->add($username, $password)){
            $this->setError($this->accountModel->errorToString());
            return false;
        }
        if(!$this->profileModel->add($username, $email,$firstName,$lastName,$country)){
            $this->setError($this->profileModel->errorToString());
            return false;
        }
        //Set the expiration in the next 30 minutes for the activation token.
        $duration = new \DateTime();
        $duration->modify("+30 minutes");
        if(!$this->activationTokenModel->add($username, $duration)){
            $this->setError($this->activationTokenModel->errorToString());
            return false;
        }
        //Call the function to send an email
        if(!$this->activationTokenModel->send($email,$username,$firstName, $twig)){
            $this->setError($this->activationTokenModel->errorToString());
            return false;
        }

        if(!$this->save()){
            $this->setError("An error has occurred that prevents us.");
            return false;
        }
        return true;
    }
    /*
     * Return true if password has passed the validation; otherwise,
     * return false.
     */
    private function checkPwd(string $pwd, string $confirmPwd){
        $pwd = trim($pwd);
        $confirmPwd = trim($confirmPwd);
        if(empty($confirmPwd) || empty($confirmPwd)){
            $this->setError("Password and confirm password is required.");
            return false;
        }
        if($confirmPwd !== $pwd){
            $this->setError("Password and confirm password must be same.");
            return false;
        }
        return true;
    }
}