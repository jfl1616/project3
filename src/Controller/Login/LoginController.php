<?php


namespace Bolzen\Src\Controller\Login;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\Lobby\LobbyModel;
use Bolzen\Src\Model\UserLastActivity\UserLastActivityModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Bolzen\Src\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    use ResponseTrait;
    use AuthenticationTrait;

    private $accountModel, $userLastActivity;

    public function __construct()
    {
        parent::__construct();
        $this->accountModel = new AccountModel();
        $this->userLastActivity = new UserLastActivityModel();
    }


    public function login(Request $request){

        if($request->isMethod("POST")){
            $this->CSRFProtection($request);

            $username = $request->get("username", "");
            $password = $request->get("password", "");

            if(!$this->accountModel->login($username, $password)){
                $this->setResponse($this->accountModel->errorToString());
            }
            else{
                if(!$this->userLastActivity->updateActivity($username)){
                    $this->setResponse($this->userLastActivity->errorToString());
                }
                else{
                    $this->setResponse("Successfully", 200);
                    $lobby = new LobbyModel();
                    $this->response['url'] = $lobby->lobbyPath();
                }
            }
            return $this->jsonResponse($this->response);
        }
        else{
            return $this->render($request);
        }
    }

}