<?php


namespace Bolzen\Src\Traits;


use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\CSRF\CSRFModel;
use Bolzen\Src\Model\Game\GameModel;
use Symfony\Component\HttpFoundation\Request;

trait AuthenticationTrait
{
    public function CSRFProtection(Request $request){
        $CSRFModel = new CSRFModel();
        $CSRFModel->CSRFProtection($request);
    }
    public function authenticatedUserOnly(Request $request){
        $this->CSRFProtection($request);
        $accountModel = new AccountModel();
        $accountModel->checkPermission();
        $accountModel->redirectToActivationPage();

    }
    public function GameIdProtection(Request $request){
        $gameId = $request->get("gameId", "");
        $gameModel = new GameModel();

        if(!$gameModel->hasGameId($gameId)){
            $accountModel = new AccountModel();
            $accountModel->redirectToLobby();
        }
    }
}