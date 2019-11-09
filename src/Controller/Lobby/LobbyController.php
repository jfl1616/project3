<?php


namespace Bolzen\Src\Controller\Lobby;


use Bolzen\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LobbyController extends Controller
{
    public function lobby(Request $request){
        return $this->render($request);
    }
}