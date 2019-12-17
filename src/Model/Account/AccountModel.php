<?php


namespace Bolzen\Src\Model\Account;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Model\Profile\ProfileModel;
use Bolzen\Src\Model\UserLastActivity\UserLastActivityModel;

class AccountModel extends Model
{
    private $table, $profileModel, $userLastActivityModel;

    public function __construct()
    {
        parent::__construct();
        $this->table = "account";
        $this->profileModel = new ProfileModel();
        $this->userLastActivityModel = new UserLastActivityModel();
    }

    /*
     * Return the current CSRF token from AccessControlInterface Interface.
     */
    public function getCSRFToken(): string{
        return $this->accessControl->getCSRFToken();
    }
    /*
     * Return the verified account's username from Model class.
     */
    public function getUsername(): string {
        return $this->user->getUserName();
    }

    /*
     * Validate the username and password and call add() function in UserInterface Interface to
     * add a new user to the account table as long as there is no errors.
     */
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

    /*
     * Return true as long as the username is valid based on the requirements
     */
    public function isValidUsername(string $username){
        if (strlen($username) < 3 || strlen($username > 17) || !ctype_alnum($username)){
            $this->setError("Usernames are required to be Alphanumeric, and between 3-16 characters long");
            return false;
        }
        return true;
    }
    /*
     * Return true as long as if the username and password are correct.
     */
    public function login(string $username, string $password):bool{
        if(!$this->accessControl->authenticate($username, $password, true)){
            $this->setError("Invalid username or password");
            return false;
        }
        if(!$this->userLastActivityModel->updateActivity($username)){
            $this->setError($this->userLastActivityModel->errorToString());
            return false;
        }
        return true;
    }
    /*
     * Check if user is anonymous and will be directed to the login/registration page by default.
     */
    public function checkPermission(){
        if($this->user->isAnonymous()){
                $this->logout();
        }
    }
    /*
     * Destroy the session and redirect to the login page.
     */
    public function logout(){
        $this->session->clear();
        $this->accessControl->redirect("login");
    }
    /*
     * Redirect to the lobby page with a CSRF Token, which is more secured for the CSRF attack.
     */
    public function redirectToLobby()
    {
        $lobby = new LobbyModel();
        $lobby->redirectToLobbyWithToken();
    }
    /*
     * Make sure the account has been verified; otherwise, redirect the user to the activation page.
     */
    public function redirectToActivationPage(){
        if(!$this->user->isVerified()){
            $this->accessControl->redirect('activation');
        }
    }
}