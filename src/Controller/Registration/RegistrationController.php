<?php


namespace Bolzen\Src\Controller\Registration;


use Bolzen\Core\Controller\Controller;
use Bolzen\Src\Model\Registration\RegistrationModel;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    private $registrationModel;

    public function __construct()
    {
        parent::__construct();
        $this->registrationModel = new RegistrationModel();
    }

    public function register(Request $request){
        $firstName = $request->get("first_name", '');
        $lastName = $request->get("last_name", '');
        $username = $request->get("username", '');
        $password = $request->get("password", '');
        $confirm = $request->get("confirm", '');
        $email = $request->get("email", '');
        $country = $request->get("country", '');

        if(!$this->registrationModel->register($username,$password,$firstName,$lastName,$email,$confirm,$country,$this->twig )){
            $response['status'] = 400;
            $response['msg'] = $this->registrationModel->errorToString();
        } else {
            $response['status'] = 200;
            $response['msg'] = "We sent you an activation code. Check your email and click on the link to verify.";
        }
        return $this->jsonResponse($response);
    }
}