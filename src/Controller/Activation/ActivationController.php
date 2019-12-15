<?php


namespace Bolzen\Src\Controller\Activation;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\ActivationToken\ActivationTokenModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Bolzen\Src\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;

class ActivationController extends Controller
{
    use ResponseTrait;
    use AuthenticationTrait;

    private $activationTokenModel;

    public function __construct()
    {
        parent::__construct();
        $this->activationTokenModel = new ActivationTokenModel();
    }

    /*
     * Validate the token through URL Get Parameter
     * The account will be activate and redirect to the lobby page
     * as long as token is valid; otherwise, render the error message
     * and pass to the twig context.
    */
    public function activate(Request $request){
        $token = $request->get("token", '');

        if ($token=="default") {
            $content['error'] = "Please activate your account";
        }
        elseif (!$this->activationTokenModel->activate($token)) {
            if ($this->activationTokenModel->hasError()) {
                $content['error'] = $this->activationTokenModel->errorToString();
            } else {
                $content['error'] = "An unknown error prevented us from continue";
            }
        } else {
            $acc = new AccountModel();
            $acc->redirectToLobby();
        }
        return $this->render($request, $content);
    }
    /*
     * Resend the email with the different token
     */
    public function resend(Request $request){
        $this->CSRFProtection($request);
        $email = $request->get("email", "");
        $reCAPTCHA = $request->get("g-recaptcha-response", "");

        if(!$this->activationTokenModel->resend($reCAPTCHA, $email, $this->twig)){
            $this->setResponse($this->activationTokenModel->errorToString());
        }
        else{
            $this->setResponse("We sent you an activation code. Check your email and click on the link to verify.", 200);
        }
        return $this->jsonResponse($this->response);
    }
}