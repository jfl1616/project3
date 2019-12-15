<?php


namespace Bolzen\Src\Model\UserLastActivity;


use Bolzen\Core\Model\Model;

class UserLastActivityModel extends Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "UserLastActivity";
    }

    public function updateActivity(string $username): bool {

        if($this->hasUserLastActivity($username)){
            if(!$this->updateUserLastActivity($username)){
                return false;
            }
        }
        else{
            if(!$this->insertUserLastActivity($username)){
                return false;
            }
        }
        return true;
    }

    public function hasUserLastActivity(string $username):bool{
        $column = "username";
        $where = "username=?";
        $bindings = array($username);

        if($this->database->select($this->table, $column, $where, $bindings)->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function updateUserLastActivity(string $username): bool{
        $where ="username = ?";
        $set = "timestamp";
        $timestamp = new \DateTime();
        $timestamp = $timestamp->format("Y-m-d H:i:s");
        $bindings = array($timestamp, $username);

        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to update the last activity.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save during updating the last activity.");
            return false;
        }
        return true;
    }

    public function insertUserLastActivity(string $username): bool{
        $activitykey = $this->accessControl->generateRandomToken();
        $timestamp = new \DateTime();
        $timestamp = $timestamp->format("Y-m-d H:i:s");

        $columns = "username,timestamp,activitykey";
        $bindings = array($username, $timestamp, $activitykey);

        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert the user last activity");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the user last activity");
            return false;
        }
        return true;
    }

    public function deleteUserLastActivity(string $username):bool{
        if(!$this->hasUserLastActivity($username)){
            $this->setError("There is no such existed user last activity row");
            return false;
        }
        $where = "username=?";
        $bindings = array($username);
        if(!$this->database->delete($this->table, $where, $bindings)){
            $this->setError("Unable to delete the last activity");
            return false;
        }
        return true;
    }

    public function isTimestampValidate($username): bool{
        $lastUserActivity = new \DateTime($this->getTimestamp($username));
        $currentTime = new \DateTime();
        $currentTime = $currentTime->modify("-5 minutes");
        return $lastUserActivity > $currentTime ? true : false;
    }

    public function getTimestamp(string $username): string{
        $result = $this->get($username);
        return empty($result) ? "" : $result['timestamp'];
    }

    public function getActivityKey(string $username): string{
        $result = $this->get($username);
        return empty($result) ? "" : $result["activitykey"];
    }

    public function getUsername(string $activitykey): string{
        $result = $this->getUserLastActivityInfo($activitykey);
        return empty($result) ? "" : $result["username"];
    }

    public function getUserLastActivityInfo(string $activitykey): array{
        if(empty($activitykey)){
            $this->setError("Activity key cannot be empty");
            return array();
        }
        $columns = "*";
        $where = "activitykey = ?";
        $bindings = array($activitykey);

        $result = $this->database->select($this->table, $columns, $where, $bindings);

        return $result->rowCount() === 0 ? array() : $result->fetch();
    }

    public function get(string $username): array{
        if(empty($username)){
            $this->setError("Username cannot be empty");
            return array();
        }
        $columns = "*";
        $where = "username = ?";
        $bindings = array($username);

        $result = $this->database->select($this->table, $columns, $where, $bindings);

        return $result->rowCount() === 0 ? array() : $result->fetch();
    }
}