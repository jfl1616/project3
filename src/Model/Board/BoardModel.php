<?php


namespace Bolzen\Src\Model\Board;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Chip\ChipModel;
use Bolzen\Src\Model\Game\GameModel;

class BoardModel extends Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "Board";
    }

    public function insert(string $username, string $chipId): bool{
        if(empty($username) || empty($chipId)){
            $this->setError("Username or Chip ID cannot be empty");
            return false;
        }

        $columns = "username, chipId";
        $bindings = array($username, $chipId);

        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert Board table at this time");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during inserting the Board row.");
            return false;
        }
        return true;
    }
    public function updateLocation(string $chipId, string $location): bool {
        if(strlen($location) === 0){
            $this->setError("Location cannot be empty.");
            return false;
        }

        $username = $this->user->getUserName();
        if(!$this->isUserTurns($username)){
            $this->setError("It's not your turn yet.");
            return false;
        }

        $gameModel = new GameModel();
        $chipModel = new ChipModel();

        $playerColor = $gameModel->getColor($username);
        $currentColor = $chipModel->getChipColor($chipId);

        // Verify the player's color before updating the location
        if(!strcmp($playerColor, $currentColor) == 0){
            $this->setError("It is not your turn yet. ");
            return false;
        }

        /*
         * Prevent the bug when the user clicks the repeat location, and it will not update the location column
         * due to the same value. Just set the location column to null as long as if it exists.
         */
        if($this->hasLocation($chipId)){
            if(!$this->setLocationNull($chipId)){
                return false;
            }
        }

        $where = "chipId = ?";
        $set = "location";
        $bindings = array($location, $chipId);

        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to update the location at this time");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during updating the location.");
            return false;
        }

        if(!$this->updateUserTurns($chipId, $username)){
            return false;
        }
        return true;
    }

    public function setLocationNull($chipId): bool {
        $where = "chipId = ?";
        $set = "location";
        $bindings = array("", $chipId);
        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to set the null for location.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during setting the null for location.");
            return false;
        }
        return true;
    }

    public function hasLocation(string $chipId): bool {
        $where = "chipId = ?";
        $bindings = array($chipId);
        if($this->database->select($this->table, "location", $where, $bindings)->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function deleteBoard($chipId): bool {
        if(empty($chipId)){
            $this->setError("Chip ID cannot be empty");
            return false;
        }
        $where = "chipId = ?";
        $bindings = array($chipId);
        if(!$this->database->delete($this->table, $where, $bindings)){
            $this->setError("Unable to delete the Board row");
            return false;
        }
        if(!$this->save()){
            $this->setError("Cannot save the transaction during deleting the Board row.");
            return false;
        }
        return true;
    }

    //Return true if the user's turn to make a move.
    public function isUserTurns($username): bool{
        $result = $this->get($username);
        return empty($result) ? false : true;
    }

    public function updateUserTurns(string $chipId, string $username): bool{
        if(empty($chipId)|| empty($username)){
            $this->setError("Username or GameID cannot be empty.");
            return false;
        }
        $gameModel = new GameModel();
        $where = "chipId = ?";
        $set = "username";
        $bindings = array($gameModel->getChallengerUsername($username), $chipId);

        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to update the username at this time.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during updating the username");
            return false;
        }
        return true;
    }

    public function getLocation(string $chipId): ?string{
        if(empty($chipId)){
            $this->setError("Chip ID cannot be empty.");
            return "";
        }
        $columns = "location";
        $where = "chipId = ?";
        $bindings = array($chipId);
        $result = $this->database->select($this->table, $columns, $where,$bindings);
        if($result->rowCount() > 0){
            $result = $result->fetch();
            return $result["location"];
        }
        else{
            return "";
        }
    }

    public function getUsername(string $chipId): string{
        if(empty($chipId)){
            $this->setError("Username cannot be empty.");
            return "";
        }
        $columns = "username";
        $where = "chipId = ?";
        $bindings = array($chipId);
        $result = $this->database->select($this->table, $columns, $where,$bindings);
        if($result->rowCount() === 0) return "";
        else{
            $result = $result->fetch();
            return $result["username"];
        }
    }



    public function get(string $username): array{
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
}