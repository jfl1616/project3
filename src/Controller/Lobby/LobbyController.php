<?php


namespace Bolzen\Src\Controller\Lobby;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\Game\GameModel;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Model\Profile\ProfileModel;
use Bolzen\Src\Model\UserLastActivity\UserLastActivityModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Bolzen\Src\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LobbyController extends Controller
{
    use AuthenticationTrait;
    use ResponseTrait;

    private $lobbyModel, $accountModel, $profileModel, $userLastActivityModel, $gameModel;

    public function __construct()
    {
        parent::__construct();
        $this->lobbyModel = new LobbyModel();
        $this->accountModel = new AccountModel();
        $this->profileModel = new ProfileModel();
        $this->userLastActivityModel = new UserLastActivityModel();
        $this->gameModel = new GameModel();
    }
    /*
     * Render the string template that passes the strings to the twig context.
     */
    public function lobby(Request $request){
        $this->authenticatedUserOnly($request);
        $content["username"] = $this->accountModel->getUsername();
        $content["firstname"] = $this->profileModel->getFirstName($content["username"]);
        $content["lastname"] = $this->profileModel->getLastName($content["username"]);
        $content["gameId"] = "1"; // Default value for the public chat-room

        //Check if "reload" session variables and sets to true, then change it to false.
        if(isset($_SESSION["reload"]) && $_SESSION["reload"] === true){
            $_SESSION["reload"] = false;
        }

        return $this->render($request, $content);
    }
    /*
     * Return the automatic updates from a server
     * with a JSON that comes the information of
     * all messages for public chat-room.
     */
    public function stream(Request $request){
        $this->authenticatedUserOnly($request);
        $messages = $this->lobbyModel->listMsg($request->get("gameId")); //Retrieve all messages for only public chat-room.

        $response = new StreamedResponse();

        $response->setCallback(function() use ($messages){
            echo 'data: ' . json_encode($messages) . "\n\n";
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
     * with a JSON that comes the information of
     * all online players.
     */
    public function getOnlineUser(Request $request){
        $this->authenticatedUserOnly($request);
        $users = $this->lobbyModel->listOnlineUser(); // Retrieve all online registered users

        $response = new StreamedResponse();

        $response->setCallback(function() use ($users){
            echo 'data: ' . json_encode($users) . "\n\n";
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
     * with a JSON that comes the information of
     * incoming request.
     */
    public function getIncomingChallenge(Request $request){
        $this->authenticatedUserOnly($request);
        $users = $this->gameModel->incoming();

        $response = new StreamedResponse();

        $response->setCallback(function() use ($users){
            echo 'data: ' . json_encode($users) . "\n\n";
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
     * with a JSON that comes the information of
     * incoming request.
     */
    public function startGame(Request $request){
        $this->authenticatedUserOnly($request);
        $gameStart = $this->gameModel->startGame();

        $response = new StreamedResponse();

        $response->setCallback(function() use ($gameStart){
            echo 'data: ' . json_encode($gameStart) . "\n\n";
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
     * Redirect to the lobby page
     */
    public function redirectToLobby(){
        $this->lobbyModel->redirectToLobbyWithToken();
    }

    /*
     * Redirect to the login/registration page
     */
    public function logout(Request $request){
        $this->authenticatedUserOnly($request);
        $this->accountModel->logout();
    }

    /*
     * Pass the message and game id to the add() function in Lobby Model class
     * and insert the message data into the database.
     */
    public function addMessage(Request $request){
        $this->authenticatedUserOnly($request);
        $message = $request->get("message");
        $gameId = $request->get("gameId");

        if(!$this->lobbyModel->add($message, $gameId)){
            $this->setResponse($this->lobbyModel->errorToString());
        }
        else{
            $this->setResponse("Successfully to submit the message.", 200);
        }
        return $this->jsonResponse($this->response);
    }

    /*
     * Pass the username to the updateActivity() function in UserLastActivityModel class
     * and update the timestamp on the database.
     */
    public function updateUserLastActivity(Request $request){
        $this->authenticatedUserOnly($request);
        $username = $this->accountModel->getUsername();

        if(!$this->userLastActivityModel->updateActivity($username)){
            $this->setResponse($this->userLastActivityModel->errorToString());
        }
        else{
            $this->setResponse("Update the timestamp successfully", 200);
        }
        return $this->jsonResponse($this->response);
    }
    /*
     * Pass the opponent id to the insert() function in Game Model class
     * and insert into the database to create an incoming challenge for the opponent.
     */
    public function sendChallenge(Request $request){
        $this->authenticatedUserOnly($request);
        $opponentId = $request->get("opponentId");

        if(!$this->gameModel->insert($opponentId)){
            $this->setResponse($this->gameModel->errorToString());
        }
        else{
            $this->setResponse("The request has been sent!", 200);
        }
        return $this->jsonResponse($this->response);
    }
    /*
     * Pass the challenge id and response to updateGameId() function. The response contains
     * the string value "accept" or "reject".
     */
    public function responseChallenge(Request $request){
        $this->authenticatedUserOnly($request);
        $response = $request->get("response");
        $challengeId = $request->get("challengeId");

        if(!$this->gameModel->updateGameId($response, $challengeId)){
            $this->setResponse($this->gameModel->errorToString());
        }
        else{
            $this->setResponse("Successfully updating game row.", 200);
        }
        return $this->jsonResponse($this->response);
    }
}