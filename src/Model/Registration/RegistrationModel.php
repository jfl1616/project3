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
        $duration = new \DateTime();
        $duration->modify("+30 minutes");
        if(!$this->activationTokenModel->add($username, $duration)){
            $this->setError($this->activationTokenModel->errorToString());
            return false;
        }

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