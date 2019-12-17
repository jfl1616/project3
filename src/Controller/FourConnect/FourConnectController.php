<?php


namespace Bolzen\Src\Controller\FourConnect;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\Board\BoardModel;
use Bolzen\Src\Model\Chip\ChipModel;
use Bolzen\Src\Model\Game\GameModel;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Bolzen\Src\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FourConnectController extends Controller
{
    use ResponseTrait;
    use AuthenticationTrait;

    private $accountModel, $lobbyModel, $boardModel, $chipModel, $gameModel;

    public function  __construct()
    {
        parent::__construct();
        $this->accountModel = new AccountModel();
        $this->lobbyModel = new LobbyModel();
        $this->boardModel = new BoardModel();
        $this->chipModel = new ChipModel();
        $this->gameModel = new GameModel();
    }

    /*
     * Render the string template that passes the strings to the twig context.
     */
    public function FourConnect(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);

        $content["username"] = $this->accountModel->getUsername();
        $content["gameId"] = $request->get("gameId");
        $content["chipColor"] = $this->gameModel->getColor($content["gameId"]);

        //Prevent the cheating when the user attempts to reload the page.
        if(!isset($_SESSION["reload"]) || $_SESSION["reload"] === false){
            $_SESSION['reload'] = true;
        }
        else if($_SESSION["reload"] === true){
            $_SESSION['reload'] = false;
            $winner = $this->gameModel->getChallengerUsername($content["gameId"]);
            $this->gameModel->updateWinner($winner, $content["gameId"]);
        }

        //Enable the resetGame function that allows the user to make a request to reset the game.
        if(!isset($_SESSION["resetGame"]) || $_SESSION["resetGame"] === false){
            $this->gameModel->updateResetGame($content["gameId"]); /* Set the resetGame column to null */
            $_SESSION["resetGame"] = true;
        }

        return $this->render($request, $content);
    }

    /*
     * Pass the game ID and location to the function in Board Model class
     * that updates the location.
     */
    public function click(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);

        $gameId = $request->get("gameId");
        $location = $request->get("location");


        if(!$this->boardModel->updateLocation($gameId, $location)){
            $this->setResponse($this->boardModel->errorToString());
        }
        else{
            $this->setResponse("Successfully - update the location", 200);
        }
        return $this->jsonResponse($this->response);
    }
    /*
     * Pass the game id and opponent's username to requestedResetGame() function in Game Model class to
     * insert the data into the database. It is useful to notify another player that opponent would like to
     * reset the game.
     */
    public function requestedResetGame(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);

        $gameId = $request->get("gameId");
        $opponent = $this->gameModel->getChallengerUsername($gameId);

        if(!$this->gameModel->updateResetGame($gameId, $opponent)){
            $this->setResponse($this->gameModel->errorToString());
        }
        else{
            $this->setResponse("The request has been sent!", 200);
        }
        return $this->jsonResponse($this->response);
    }

    /*
     * Return the automatic updates from a server
     * with a JSON object that comes the
     * with information of username, location, and chip color.
     */
    public function playerMove(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);
        $playerMove = $this->gameModel->fetchPlayerMove($request->get("gameId"));

        $response = new StreamedResponse();

        $response->setCallback(function() use ($playerMove){
            echo 'data: ' . json_encode($playerMove) . "\n\n";
            echo 'retry: 1000\n\n';
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set("X-Accel-Buffering", "no");
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }

    /*
     * Return the automatic updates from a server
     * with a JSON object that comes
     * with the result of the game.
     */
    public function gameStatus(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);
        $gameStatus = $this->gameModel->fetchGameStatus($request->get("gameId"), $request);

        $response = new StreamedResponse();

        $response->setCallback(function() use ($gameStatus){
            echo 'data: ' . json_encode($gameStatus) . "\n\n";
            echo 'retry: 1000\n\n';
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set("X-Accel-Buffering", "no");
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }

    /*
     * Pass the chip id to the function in Chip Model class
     * and switch the different chip color
     */
    public function switchChipColor(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);
        if(!$this->chipModel->updateChipColor($request->get("chipId"))){
            $this->setResponse($this->chipModel->errorToString());
        }
        else{
            $this->setResponse("Successfully switching the different chip color", 200);
        }
        return $this->jsonResponse($this->response);
    }
    /*
     * Pass the result and game id to the function in Game Model class
     * and update the winner column in the database. As noted, result variable
     * may contain the player's username or "tie".
     */
    public function updateGameResult(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);
        if(!$this->gameModel->updateWinner($request->get("result"), $request->get("gameId"))){
            $this->setResponse($this->gameModel->errorToString());
        }
        else{
            $this->setResponse("Successfully updating the result of game", 200);
        }
        return $this->jsonResponse($this->response);
    }
    /*
     * Return the automatic updates from a server
     * with a JSON object that comes with the
     * incoming request for the reset game.
     */
    public function checkResetGame(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);

        $resetGame = $this->gameModel->incomingResetGame($request->get("gameId"), $this->accountModel->getUsername());

        $response = new StreamedResponse();

        $response->setCallback(function() use ($resetGame){
            echo 'data: ' . json_encode($resetGame) . "\n\n";
            echo 'retry: 1000\n\n';
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set("X-Accel-Buffering", "no");
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }
    /*
     * Return the automatic updates from a server
     * with a JSON that comes the approval to start over
     * the game.
     */
    public function reloadResetGame(Request $request){
        $this->authenticatedUserOnly($request);
        $this->GameIdProtection($request);

        $resetGame = $this->gameModel->reload($request->get("gameId"));

        $response = new StreamedResponse();

        $response->setCallback(function() use ($resetGame){
            echo 'data: ' . json_encode($resetGame) . "\n\n";
            echo 'retry: 1000\n\n';
            ob_flush();
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set("X-Accel-Buffering", "no");
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }
    /*
     * Pass the game id and response to the updateResetGame() function
     * in Game Model to update the resetGame column in the database. This
     * function has occurred when the opponent responds to another opponent's
     * request to reset the game by approving or declining.
     */
    public function responseResetGame(Request $request){
        $this->authenticatedUserOnly($request);
        $response = $request->get("response");
        $gameId = $request->get("gameId");

        if(!$this->gameModel->updateResetGame($gameId, $response)){
            $this->setResponse($this->gameModel->errorToString());
        }
        else{
            $this->setResponse("Successfully updating the resetGame column.", 200);
        }
        return $this->jsonResponse($this->response);
    }
}