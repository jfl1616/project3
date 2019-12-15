<?php


namespace Bolzen\Src\Model\Game;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Board\BoardModel;
use Bolzen\Src\Model\Chip\ChipModel;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Model\Profile\ProfileModel;
use Bolzen\Src\Model\UserLastActivity\UserLastActivityModel;
use Symfony\Component\HttpFoundation\Request;

class GameModel extends Model
{
    private $table, $boardModel, $chipModel, $userLastActivityModel, $profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->table = "Game";
        $this->boardModel = new BoardModel();
        $this->chipModel = new ChipModel();
        $this->userLastActivityModel = new UserLastActivityModel();
        $this->profileModel = new ProfileModel();
    }

    public function insert($opponentId): bool{
        if(empty($opponentId)){
            $this->setError("Opponent ID cannot be empty.");
            return false;
        }
        $player1 = $this->user->getUserName();
        $player2 = $this->userLastActivityModel->getUsername($opponentId);
        $challengeId = $this->accessControl->generateRandomToken();

        $columns = "player1, player2, challengeId";
        $bindings = array($player1, $player2, $challengeId);

        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert the Game table at this time");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during inserting the Game table");
            return false;
        }
        return true;
    }

    public function incoming(): array{
        $columns = "gameId, player1, challengeId";
        $where = "player2 = ?";
        $bindings = array($this->user->getUserName());
        $result = $this->database->select($this->table, $columns, $where, $bindings);
        $incoming = array();

        if($result->rowCount() > 0){
            while($rows = $result->fetch()){
                //Make sure, this game is still pending and has not started game yet and the challenger's last activity must be less than 5 minutes
                if(is_null($rows["gameId"]) && $this->userLastActivityModel->isTimestampValidate($rows["player1"])){
                    $challengerName = $this->profileModel->getFirstName($rows["player1"]) . " " . $this->profileModel->getLastName($rows["player1"]);
                    $incoming[] = [
                        'player1' => $challengerName,
                        'challengeId' => $rows["challengeId"]
                    ];
                }
            }
        }
        return $incoming;
    }

    public function startGame(): array{
        $columns = "gameId, player1, player2, winner";
        $where = "player1 = ? OR player2 = ?";
        $bindings = array($this->user->getUserName(), $this->user->getUserName());
        $result = $this->database->select($this->table, $columns, $where, $bindings);
        $startGame = array();

        if($result->rowCount() > 0){
            while($rows = $result->fetch()){
                //Make sure, game ID is available in order to start the game, both players' last activity must be less than 5 minutes, and the winner column must be empty.
                if(!is_null($rows["gameId"]) && $this->userLastActivityModel->isTimestampValidate($rows["player1"])
                    && $this->userLastActivityModel->isTimestampValidate($rows["player2"]) && is_null($rows["winner"])){
                    //Assign the opponent's name
                    if($this->user->getUserName() === $rows["player1"]){
                        $challengerName = $this->profileModel->getFirstName($rows["player2"]) . " " . $this->profileModel->getLastName($rows["player2"]);
                    }
                    else{
                        $challengerName = $this->profileModel->getFirstName($rows["player1"]) . " " . $this->profileModel->getLastName($rows["player1"]);
                    }
                    $startGame[] = [
                        "challengerName" => $challengerName,
                        "url" => $rows["gameId"] . "/FourConnect"
                    ];
                }
            }
        }
        return $startGame;
    }

    public function updateGameId(string $response, string $challengeId): bool {
        if(empty($response) || empty($challengeId)){
            $this->setError("Response or FourConnect ID cannot be empty");
            return false;
        }
        // The opponent has rejected the challenger's request
        if($response == "reject"){
            if(!$this->deleteGame($challengeId)){
                return false;
            }
        }
        // The opponent has accepted the challenger's request
        else if($response == "accept"){
            $where="challengeId = ?";
            $set = "gameId";
            $gameId = $this->accessControl->generateRandomToken();
            $bindings = array($gameId, $challengeId);

            if(!$this->database->update($this->table, $set, $where, $bindings)){
                $this->setError("Unable to update the game row.");
                return false;
            }
            if(!$this->save()){
                $this->setError("Unable to save the transaction during updating the game row.");
                return false;
            }
            //Set the default turn for challenger only
            if(!$this->boardModel->insert($this->getChallengerUsername($this->user->getUserName()), $gameId)){
                $this->setError($this->boardModel->errorToString());
                return false;
            }
            //Set the default Red color for challenger
            if(!$this->chipModel->insert($gameId)){
                $this->setError($this->chipModel->errorToString());
                return false;
            }
        }
        return true;
    }

    public function updateWinner(string $result, string $gameId): bool{
        //If there is an existed result of the game, then it's unnecessary to update the row.
        if(!$this->hasWinner($gameId)){
            $where="gameId = ?";
            $set = "winner";
            $bindings = array($result, $gameId);

            if(!$this->database->update($this->table, $set, $where, $bindings)){
                $this->setError("Unable to update the winner row.");
                return false;
            }
            if(!$this->save()){
                $this->setError("Unable to save the transaction during updating the winner row.");
                return false;
            }
        }
        return true;
    }

    public function deleteGame($challengeId): bool{
        if(empty($challengeId)){
            $this->setError("Challenge ID cannot be empty");
            return false;
        }
        $where = "challengeId = ?";
        $bindings = array($challengeId);
        if(!$this->database->delete($this->table, $where, $bindings)){
            $this->setError("Unable to delete the game row at this time.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Cannot save the transaction during deleting the game row.");
            return false;
        }
        return true;
    }

    public function hasGameId($gameId): bool{
        $where = "gameId = ?";
        $bindings = array($gameId);
        return $this->database->select($this->table, "gameId", $where, $bindings)->rowCount() > 0;
    }

    public function hasWinner($gameId): bool{
        $where = "gameId = ?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table, "winner", $where, $bindings);
        $result = $result->fetch();

        //Return false if the winner column is currently null.
        if(is_null($result["winner"])){
            return false;
        }
        return true;
    }

    public function getWinner($gameId): string{
        $where = "gameId = ?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table, "winner", $where, $bindings);
        $result = $result->fetch();
        return $result["winner"];
    }

    public function getChallengerUsername(string $username){
        $result = $this->get($username);
        return $result["player1"] != $this->user->getUserName() ? $result["player1"] : $result["player2"];
    }

    public function fetchPlayerMove($chipId): array{
        return [
            "username" => $this->boardModel->getUsername($chipId),
            "playerName" => $this->profileModel->getFirstName($this->boardModel->getUsername($chipId)) . " " . $this->profileModel->getLastName($this->boardModel->getUsername($chipId)),
            "location" => $this->boardModel->getLocation($chipId),
            "chipColor" => $this->chipModel->getChipColor($chipId)
        ];
    }

    public function fetchGameStatus($gameId, Request $request): array{
        // Make sure the winner row is not null.
        if($this->hasWinner($gameId)){
            $result = $this->getWinner($gameId);
            //Check if the result of game is tie.
            if($result === "tie"){
                  $status = "It is a tie, and you will return to the lobby page.";
            }
            //$result refers to the winner's username
            else{
                $playerName = $this->profileModel->getFirstName($result) . " " . $this->profileModel->getLastName($result);
                $status = $playerName . " wins the game! You will return to the lobby page.";
            }
            $lobbyModel = new LobbyModel();
            return [
                "status" => $status,
                "url" => $request->getSchemeAndHttpHost() . "/portfolio/project3/" . $lobbyModel->lobbyPath()
            ];
        }
        return array();
    }

    /*
     * Default setting for Chip Color
     * Challenger - RED
     * Opponent - BLUE
     */
    public function getColor(string $username){
        $result = $this->get($username);
        if($result["player1"] === $this->user->getUserName()){
            return "red";
        }
        else if($result["player2"] === $this->user->getUserName()){
            return "blue";
        }
        return "";
    }

    public function get(string $username): array{
        if (empty($username)) {
            $this->setError("username cannot be empty");
            return array();
        }
        $columns = "*";
        $where = "player1 = ? OR player2 = ?";
        $bindings = array($username, $username);

        $result = $this->database->select($this->table,$columns, $where, $bindings);

        return $result->rowCount() === 0 ? array() : $result->fetch();
    }
}