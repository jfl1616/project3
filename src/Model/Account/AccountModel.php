<?php


namespace Bolzen\Src\Model\Account;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Model\Profile\ProfileModel;
use Twig\Profiler\Profile;

class AccountModel extends Model
{
    private $table, $profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->table = "account";
        $this->profileModel = new ProfileModel();
    }

    public function getCSRFToken(): string{
        return $this->accessControl->getCSRFToken();
    }

    public function getUsername(): string {
        return $this->user->getUserName();
    }

    public function add(string $username, string $password):bool {
        if(empty($username) || empty($password)){
            $this->setError("Username or Password cannot be empty.");
            return false;
        }
        if(!$this->isValidUsername($username)){
            return false;
        }
        if($this->user->hasUser($username)){
            $this->setError("Username is already taken.");
            return false;
        }
        if(strlen($password) < 3){
            $this->setError("Password is too short.");
            return false;
        }

        if(!$this->user->add($username, $password)) {
            $this->setError("Unfortunately, we cannot insert the account at this time.");
            return false;
        }
        return true;
    }

    public function isValidUsername(string $username){
        if (strlen($username) < 3 || strlen($username > 17) || !ctype_alnum($username)){
            $this->setError("Usernames are required to be Alphanumeric, and between 3-16 characters long");
            return false;
        }
        return true;
    }
    public function login(string $username, string $password):bool{
        if(!$this->accessControl->authenticate($username, $password, true)){
            $this->setError("Invalid username or password");
            return false;
        }
        return true;
    }
    public function checkPermission(){
        if($this->user->isAnonymous()){
                $this->logout();
        }
    }
    public function logout(){
        $this->session->clear();
        $this->accessControl->redirect("login");
    }

    public function redirectToLobby()
    {
        $lobby = new LobbyModel();
        $lobby->redirectToLobbyWithToken();
    }
    public function redirectToActivationPage(){
        if(!$this->user->isVerified()){
            $this->accessControl->redirect('activation');
        }
    }
}