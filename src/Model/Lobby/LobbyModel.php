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
    /*
     * Redirect to the lobby page
     */
    public function redirectToLobbyWithToken(){
        $this->accessControl->redirect($this->lobbyPath());
    }
    /*
     * Return the URL for the lobby path that comes with the CSRF Token that protects the CSRF attack.
     */
    public function lobbyPath()
    {
        return $this->accessControl->getCSRFToken()."/lobby";
    }
    /*
     * Insert the data into ChatMessage database regarding the message.
     */
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
    /*
     * Return an array that comes with all registered and online users based on
     * the timestamp that is under 5 minutes; otherwise, return an empty array
     * if there isn't any online users.
     */
    public function listOnlineUser(): array{
        $user = [];
        //Retrieve an array from ProfileModel class and loop through all registered users.
        foreach($this->profileModel->listUser() as $item){
            // Insert into an array as long as user's timestamp is under 5 minutes and different user, instead of this user.
            if($this->userLastActivityModel->isTimestampValidate($item["username"]) && $this->user->getUserName() != $item["username"]){
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
    /*
     * Return an array that comes with all messages with the specific game id and
     * user's last activity (timestamp) must be under 5 minutes. Otherwise, it returns
     * an empty array.
     */
    public function listMsg(string $gameId):array {
        $where = "gameId=?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table, "*", $where, $bindings);
        $messages = array();

        if($result->rowCount() > 0){
            while($rows = $result->fetch()){

                // Insert into an array as long as user's timestamp is under 5 minutes.
                if($this->userLastActivityModel->isTimestampValidate($rows["username"])){
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