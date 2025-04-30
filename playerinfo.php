<?php
require_once 'playerstats.php';

class Player {
    private $playerID;
    private $name;
    private $dob;
    private $position;
    private $team_id;
    private $statistics; // to hold player stats

    public function __construct($playerID, $name, $dob, $position, $team_id, PlayerStatistics $stats = null) {
        $this->playerID = $playerID;
        $this->name = $name;
        $this->dob = $dob;
        $this->position = $position;
        $this->team_id = $team_id;
        $this->statistics = $stats ?: new PlayerStatistics();
    }

    // accessor methods
    public function getAge() {
        if (!$this->dob) return null;
        $dob = new DateTime($this->dob);
        $now = new DateTime();
        return $now->diff($dob)->y;
    }

    public function getStatistics() {
        return $this->statistics;
    }

    public function setStatistics(PlayerStatistics $stats) {
        $this->statistics = $stats;
    }
}
?>
