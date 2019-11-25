<?php


namespace Bolzen\Src\Controller\Lobby;


use Bolzen\Core\Controller\Controller;
use Bolzen\Core\User\User;
use Bolzen\Src\Model\Account\AccountModel;
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

    private $lobbyModel, $accountModel, $profileModel, $userLastActivityModel;

    public function __construct()
    {
        parent::__construct();
        $this->lobbyModel = new LobbyModel();
        $this->accountModel = new AccountModel();
        $this->profileModel = new ProfileModel();
        $this->userLastActivityModel = new UserLastActivityModel();
    }

    public function lobby(Request $request){
        $this->authenticatedUserOnly($request);
        $content["username"] = $this->accountModel->getUsername();
        $content["firstname"] = $this->profileModel->getFirstName($content["username"]);
        $content["lastname"] = $this->profileModel->getLastName($content["username"]);

        return $this->render($request, $content);
    }
    public function stream(Request $request){
        $this->authenticatedUserOnly($request);
        $messages = $this->lobbyModel->listMsg("1"); //Retrieve all messages for only Public Chat.

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
    public function redirectToLobby(){
        $this->lobbyModel->redirectToLobbyWithToken();
    }

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
}