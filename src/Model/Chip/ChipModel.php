<?php


namespace Bolzen\Src\Model\Chip;


use Bolzen\Core\Model\Model;
use Bolzen\Src\Model\Game\GameModel;

class ChipModel extends Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = "Chip";
    }

    public function insert(string $chipId): bool{
        if(empty($chipId)){
            $this->setError("Chip ID cannot be empty");
            return false;
        }
        $chipColor = "red"; // Default color for the challenger
        $columns = "chipId, chipColor";
        $bindings = array($chipId, $chipColor);

        if(!$this->database->insert($this->table, $columns, $bindings)){
            $this->setError("Unable to insert the Chip table at this time.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during inserting the Chip table.");
            return false;
        }
        return true;
    }
    public function updateChipColor(string $chipId): bool {
        if(empty($chipId)){
            $this->setError("Chip ID cannot be empty");
            return false;
        }
        // Switch the different chip color
        $chipColor = $this->getChipColor($chipId) === "red" ? "blue" : "red";

        $where = "chipId = ?";
        $set = "chipColor";
        $bindings = array($chipColor, $chipId);

        if(!$this->database->update($this->table, $set, $where, $bindings)){
            $this->setError("Unable to update the Chip Color.");
            return false;
        }
        if(!$this->save()){
            $this->setError("Unable to save the transaction during updating the Chip Color.");
            return false;
        }
        return true;
    }

    public function getChipColor(string $chipId): string{
        $result = $this->get($chipId);
        return empty($result) ? "" : $result["chipColor"];
    }

    public function get(string $chipId): array{
        if (empty($chipId)) {
            $this->setError("username cannot be empty");
            return array();
        }
        $columns = "*";
        $where = "chipId = ?";
        $bindings = array($chipId);

        $result = $this->database->select($this->table,$columns, $where, $bindings);

        return $result->rowCount() === 0 ? array() : $result->fetch();
    }

}