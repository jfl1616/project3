<?php


namespace Bolzen\Src\Traits;


trait ResponseTrait
{
    protected $response = array("status"=>400);

    protected function setResponse(string $msg, int $status=400){
        $this->response = array("status"=>$status, "msg"=>$msg);
    }
}