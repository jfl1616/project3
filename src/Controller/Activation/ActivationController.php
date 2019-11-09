<?php


namespace Bolzen\Src\Controller\Activation;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\ActivationToken\ActivationTokenModel;
use Symfony\Component\HttpFoundation\Request;

class ActivationController extends Controller
{
    private $activationTokenModel;

    public function __construct()
    {
        parent::__construct();
        $this->activationTokenModel = new ActivationTokenModel();
    }

    public function activate(Request $request){
        $username = $request->get("username", '');
        $token = $request->get("token", '');

        if (!$this->activationTokenModel->activate($username, $token)) {
            if ($this->activationTokenModel->hasError()) {
                $content['error'] = $this->activationTokenModel->errorToString();
            } else {
                $content['error'] = "An unknown error prevented us from continue";
            }

            $content['username'] = $username;
            $content['userToken'] = $token;
        } else {
            //otherwise the activation was success so we will just redirect to lobby
            $acc = new AccountModel();
            $acc->redirectToLobby();
        }
        return $this->render($request, $content);
    }
    public function resend(Request $request){
        $username = $request->get('username', '');
        $token = $request->get('userToken', '');

        //call resend here
        if(!$this->activationTokenModel->resend($username, $token, $this->twig )){
            $response = array("status"=>400, "msg"=> $this->activationTokenModel->errorToString());
        }
        else{
            $response = array("status"=>200, "msg"=> "We sent you an activation code. Check your email and click on the link to verify");
        }
        return $this->jsonResponse($response);
    }
}