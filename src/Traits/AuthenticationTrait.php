<?php


namespace Bolzen\Src\Traits;


use Bolzen\Src\Model\Account\AccountModel;
use Bolzen\Src\Model\CSRF\CSRFModel;
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
}