<?php


namespace Bolzen\Src\Controller\Login;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    private $accountModel;

    public function __construct()
    {
        parent::__construct();
        $this->accountModel = new AccountModel();
    }

    public function login(Request $request){

        if($request->isMethod("POST")){
            $username = $request->get("username", "");
            $password = $request->get("password", "");

            if(!$this->accountModel->login($username, $password)){
                $response = array("status"=>400, "msg"=> $this->accountModel->errorToString());
            }
            else{
                $response = array("status"=>200, "Successfully!");
            }
            return $this->jsonResponse($response);
        }
        else{
            return $this->render($request);
        }
    }

    public function isActivated(){

    }
}