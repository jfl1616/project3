<?php


namespace Bolzen\Src\Model\Game;


use Bolzen\Core\Model\Model;

class Game extends Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "Game";
    }

    public function insert(): boolean{

    }

    public function incoming(): array{

    }

}