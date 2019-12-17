<?php


namespace Bolzen\Src\Traits;


use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\CSRF\CSRFModel;
use Bolzen\Src\Model\Game\GameModel;
use Symfony\Component\HttpFoundation\Request;

trait AuthenticationTrait
{
    /*
     * Pass the request to the function in CSRFModel class to validate the CSRF Token
     */
    public function CSRFProtection(Request $request){
        $CSRFModel = new CSRFModel();
        $CSRFModel->CSRFProtection($request);
    }
    /*
     * Verified and registered account is required; otherwise, redirected to
     * the login/registration or activation page either.
     */
    public function authenticatedUserOnly(Request $request){
        $this->CSRFProtection($request);
        $accountModel = new AccountModel();
        $accountModel->checkPermission();
        $accountModel->redirectToActivationPage();
    }
    /*
     * Redirect to the lobby if game id is invalid.
     */
    public function GameIdProtection(Request $request){
        $gameId = $request->get("gameId", "");
        $gameModel = new GameModel();

        if(!$gameModel->hasGameId($gameId)){
            $accountModel = new AccountModel();
            $accountModel->redirectToLobby();
        }
    }
}