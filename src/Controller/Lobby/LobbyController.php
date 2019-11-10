<?php


namespace Bolzen\Src\Controller\Lobby;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Request;

class LobbyController extends Controller
{
    use AuthenticationTrait;

    private $lobbyModel;

    public function __construct()
    {
        parent::__construct();
        $this->lobbyModel = new LobbyModel();
    }

    public function lobby(Request $request){
        $this->authenticatedUserOnly($request);
        return $this->render($request);
    }
    public function redirectToLobby(){
        $this->lobbyModel->redirectToLobbyWithToken();
    }
}