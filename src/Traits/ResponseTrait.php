<?php


namespace Bolzen\Src\Traits;


trait ResponseTrait
{
    protected $response = array("status"=>400);

    /*
     * Set up a custom response that comes with the message and status.
     */
    protected function setResponse(string $msg, int $status=400){
        $this->response = array("status"=>$status, "msg"=>$msg);
    }
}