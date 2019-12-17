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
    /*
     * Insert the row into Game database in order to prepare for the incoming requested challenge
     * player1 - proponent
     * player2 - opponent
     */
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
    /*
     * Return an array that comes with the requested challenges for these opponents; otherwise,
     * it will return an empty array.
     */
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

    /*
     * Return an array with any game that is ready to get started for both players (proponent and opponent).
     * Otherwise, it will return an empty array.
     */
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
    /*
     * Return an array with the specific game id and opponent that has a request
     * to start over the game.
     */
    public function incomingResetGame($gameId, $opponent): array{
        $columns = "resetGame";
        $where = "gameId = ? AND resetGame = ?";
        $bindings = array($gameId, $opponent);
        $result = $this->database->select($this->table, $columns, $where, $bindings);
        $resetGame = array();

        if($result->rowCount() > 0){
            $anotherOpponent = $this->getChallengerUsername($gameId);
            $playerName = $this->profileModel->getFirstName($anotherOpponent) . " " . $this->profileModel->getLastName($anotherOpponent);
            $resetGame[] = [
                "msg" => $playerName . " requests to start over the game."
            ];
        }
        return $resetGame;
    }
    /*
     * Return an array with any game that is eligible to reload the page
     * after the opponent has been approved the request.
     */
    public function reload($gameId): array{
        $where = "gameId = ? AND resetGame = ?";
        $resetGame = "reload";
        $bindings = array($gameId, $resetGame);
        $result = $this->database->select($this->table, "resetGame", $where, $bindings);
        $reload = array();

        if($result->rowCount() > 0){
            //Create the session to give an approval once that allows the user to refresh the page.
            if(!isset($_SESSION["resetGame"]) || $_SESSION["resetGame"]){
                $_SESSION["resetGame"] = false;
                $_SESSION["reload"] = false; //approval to refresh the page

                $reload[] = [
                    "eligibility" => "approval",
                    "msg" => "Game will be start over after you clicked the below button."
                ];
            }
        }
        return $reload;
    }
    /*
     * Update the resetGame column based the opponent's username or string "reload" either.
     */
    public function updateResetGame(string $gameId, string $opponent = null){
        if(empty($gameId)){
            $this->setError("Game ID cannot be empty.");
        }
        $where="gameId = ?";
        $set = "resetGame";
        $bindings = array($opponent, $gameId);

        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to update the resetGame column.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during updating the resetGame column.");
            return false;
        }
        return true;
    }
    /*
     * Update the game id column regarding the opponent's response to the requested
     * challenge.
     */
    public function updateGameId(string $response, string $challengeId): bool {
        if(empty($response) || empty($challengeId)){
            $this->setError("Response or FourConnect ID cannot be empty.");
            return false;
        }
        // The opponent has rejected the proponent's request
        if($response == "reject"){
            if(!$this->deleteGame($challengeId)){
                return false;
            }
        }
        // The opponent has accepted the proponent's request
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
            //Set the default turn for proponent only
            if(!$this->boardModel->insert($this->getChallengerUsername($gameId), $gameId)){
                $this->setError($this->boardModel->errorToString());
                return false;
            }
            //Set the default Red color for proponent
            if(!$this->chipModel->insert($gameId)){
                $this->setError($this->chipModel->errorToString());
                return false;
            }
        }
        return true;
    }
    /*
     * Update the winner column based on the result of the game.
     */
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
    /*
     * Remove the row from Game database based on the specific challenge id.
     */
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
    /*
     * Return true if game id is existed in database; otherwise, return false if it isn't.
     */
    public function hasGameId($gameId): bool{
        $where = "gameId = ?";
        $bindings = array($gameId);
        return $this->database->select($this->table, "gameId", $where, $bindings)->rowCount() > 0;
    }
    /*
     * Return true if winner column is not null; otherwise, return false.
     */
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
    /*
     * Return the result of the game such as "tie" or the player's username
     */
    public function getWinner($gameId): string{
        $where = "gameId = ?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table, "winner", $where, $bindings);
        $result = $result->fetch();
        return $result["winner"];
    }
    /*
     * Return the player's opponent with an username
     */
    public function getChallengerUsername(string $gameId){
        if (empty($gameId)) {
            $this->setError("Game id cannot be empty");
            return "";
        }
        $columns = "player1, player2";
        $where = "gameId = ?";
        $bindings = array($gameId);
        $result = $this->database->select($this->table,$columns, $where, $bindings);
        if($result->rowCount() > 0){
            $result = $result->fetch();
        return $result["player1"] != $this->user->getUserName() ? $result["player1"] : $result["player2"];

        }
        return "";
    }
    /*
     * Return an array that comes with the player's move
     */
    public function fetchPlayerMove($chipId): array{
        return [
            "username" => $this->boardModel->getUsername($chipId),
            "playerName" => $this->profileModel->getFirstName($this->boardModel->getUsername($chipId)) . " " . $this->profileModel->getLastName($this->boardModel->getUsername($chipId)),
            "location" => $this->boardModel->getLocation($chipId),
            "chipColor" => $this->chipModel->getChipColor($chipId),
            "chipId" => $chipId
        ];
    }
    /*
     * Return an array that comes the status of the game; otherwise,
     * return the empty array if winner column is null.
     */
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
     * Return the player's default chip color
     * Proponent - "red"
     * Opponent - "blue"
     */
    public function getColor(string $chipId){
        if (empty($chipId)) {
            $this->setError("Chip id cannot be empty");
            return "";
        }
        $columns = "player1, player2";
        $where = "gameId = ?";
        $bindings = array($chipId);
        $result = $this->database->select($this->table,$columns, $where, $bindings);
        if($result->rowCount() > 0){
            $result = $result->fetch();
            if($result["player1"] === $this->user->getUserName()){
                return "red";
            }
            else{
                return "blue";
            }
        }
    }
    /*
     * Return an array with all columns.
     */
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