<?php


namespace Bolzen\Src\Model\CSRF;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Account\AccountModel;
use Symfony\Component\HttpFoundation\Request;

class CSRFModel extends Model
{
    /*
     * Pass the token to the isValidCSRFToken() function in AccessControlInterface
     * Interface. If token is invalid, then the session will be destroyed
     * and direct to the login/registration page.
     */
    public function CSRFProtection(Request $request){
        $token = $request->get("token", "");
        if(!$this->accessControl->isValidCSRFToken($token)){
            $acc = new AccountModel();
            $acc->logout();
        }
    }
}