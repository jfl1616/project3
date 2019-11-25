<?php


namespace Bolzen\Src\Model\Lobby;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Profile\ProfileModel;
use Bolzen\Src\Model\UserLastActivity\UserLastActivityModel;

class LobbyModel extends Model
{
    private $table, $profileModel, $userLastActivityModel;

    public function __construct()
    {
        parent::__construct();
        $this->table = "ChatMessage";
        $this->profileModel = new ProfileModel();
        $this->userLastActivityModel = new UserLastActivityModel();
    }

    public function redirectToLobbyWithToken(){
        $this->accessControl->redirect($this->lobbyPath());
    }

    public function lobbyPath()
    {
        return $this->accessControl->getCSRFToken()."/lobby";
    }

    public function add(string $message, string $gameId): bool {
        if(empty($message) || empty($gameId)){
            $this->setError("Username, Message, and GameId parameters are required.");
            return false;
        }
        $username = $this->user->getUserName();
        $timestamp = new \DateTime();
        $timestamp = $timestamp->format('Y-m-d H:i:s');
        $columns = "message, timestamp, username, gameId";
        $bindings = array($message, $timestamp, $username, $gameId);

        if(!$this->database->insert($this->table,$columns, $bindings)){
            $this->setError("Unable to insert the message");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save.");
            return false;
        }
        return true;
    }
    public function listOnlineUser(): array{
        $user = [];
        //Retrieve an array from ProfileModel class and loop through all registered users.
        foreach($this->profileModel->listUser() as $item){
            // Insert into an array as long as user's timestamp is under 5 minutes and different user, instead of this user.
            $lastUserActivity = new \DateTime($this->userLastActivityModel->getTimestamp($item["username"]));
            $currentTime = new \DateTime();
            $currentTime = $currentTime->modify("-5 minutes");
            if($lastUserActivity > $currentTime && $this->user->getUserName() != $item["username"]){
                $user[] = [
                    "username" => $item["username"],
                    "firstname" => $item["firstname"],
                    "lastname" => $item["lastname"],
                    "activitykey" => $this->userLastActivityModel->getActivityKey($item["username"])
                ];
            }
        }
        return $user;
    }

    public function listMsg(string $gameId):array {
        $where = "gameId=?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table, "*", $where, $bindings);
        $messages = array();

        if($result->rowCount() > 0){
            while($rows = $result->fetch()){

                // Insert into an array as long as user's timestamp is under 5 minutes.
                $lastUserActivity = new \DateTime($this->userLastActivityModel->getTimestamp($rows["username"]));
                $currentTime = new \DateTime();
                $currentTime = $currentTime->modify("-5 minutes");

                if($lastUserActivity > $currentTime){
                    $messages[] = [
                        'message' => $rows["message"],
                        'timestamp' => $rows["timestamp"],
                        'username' => $rows["username"],
                        'gameId' => $rows["gameId"],
                        'firstname' => $this->profileModel->getFirstName($rows["username"]),
                        'lastname' => $this->profileModel->getLastName($rows["username"])
                    ];
                }
            }
        }
        return $messages;
    }
}