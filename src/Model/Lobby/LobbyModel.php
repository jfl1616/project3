<?php


namespace Bolzen\Src\Model\Lobby;


use Bolzen\Core\Model\Model;

class LobbyModel extends Model
{
    public function redirectToLobbyWithToken(){
        $this->accessControl->redirect($this->lobbyPath());
    }

    public function lobbyPath()
    {
        return $this->accessControl->getCSRFToken()."/lobby";
    }
}