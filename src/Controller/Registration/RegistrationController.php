<?php


namespace Bolzen\Src\Controller\Registration;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Registration\RegistrationModel;
use Bolzen\Src\Traits\AuthenticationTrait;
use Bolzen\Src\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    use ResponseTrait;
    use AuthenticationTrait;

    private $registrationModel;

    public function __construct()
    {
        parent::__construct();
        $this->registrationModel = new RegistrationModel();
    }

    public function register(Request $request){
        $this->CSRFProtection($request);

        $firstName = $request->get("first_name", '');
        $lastName = $request->get("last_name", '');
        $username = $request->get("username", '');
        $password = $request->get("password", '');
        $confirm = $request->get("confirm", '');
        $email = $request->get("email", '');
        $country = $request->get("country", '');

        if(!$this->registrationModel->register($username,$password,$firstName,$lastName,$email,$confirm,$country,$this->twig )){
            $this->setResponse($this->registrationModel->errorToString());
        } else {
            $this->setResponse("We sent you an activation code. Check your email and click on the link to verify.", 200);
        }
        return $this->jsonResponse($this->response);
    }
}