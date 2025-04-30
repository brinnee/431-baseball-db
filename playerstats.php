<?php
class PlayerStatistics {
    private $playerID = 0;
    private $games_played = 0;
    private $plate_appearances = 0;
    private $runs_scored = 0;
    private $hits = 0;
    private $home_runs = 0;

    public function __construct($playerID = 0, $games = 0, $appearances = 0, $runs = 0, $hits = 0, $hr = 0) {
        $this->playerID = (int)$playerID;
        $this->games_played = (int)$games;
        $this->plate_appearances = (int)$appearances;
        $this->runs_scored = (int)$runs;
        $this->hits = (int)$hits;
        $this->home_runs = (int)$hr;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new Exception("Property '$property' does not exist");
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = max(0, (int)$value); // ensure non-negative integers
        } else {
            throw new Exception("Property '$property' cannot be set");
        }
    }

    public function battingAverage() {
        if ($this->plate_appearances === 0) return 0.0;
        return round($this->hits / $this->plate_appearances, 3);
    }

    public function sluggingPercentage() {
        if ($this->plate_appearances === 0) return 0.0;
        return round(($this->hits + $this->home_runs) / $this->plate_appearances, 3);
    }

    public function toArray() {
        return [
            'playerID' => $this->playerID,
            'games_played' => $this->games_played,
            'plate_appearances' => $this->plate_appearances,
            'runs_scored' => $this->runs_scored,
            'hits' => $this->hits,
            'home_runs' => $this->home_runs
        ];
    }
}
?>
