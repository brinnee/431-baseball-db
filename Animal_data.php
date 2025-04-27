<?php
class AnimalData {
    private $animal = "";
    private $feeding_time = array('HOUR' => 0, 'MIN' => 0);
    private $check_up = array('MONTH' => 1, 'DAY' => 1, 'YEAR' => 2000);

    public function __construct($animal = "", $feeding_hour = 0, $feeding_min = 0, $month = 1, $day = 1, $year = 1000) {
        $this->animal = $animal;
        $this->feeding_time = array('HOUR' => $feeding_hour, 'MINS' => $feeding_min);
        $this->check_up = array('MONTH' => $month, 'DAY' => $day, 'YEAR' => $year);
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
            if ($property === 'animal') {
                $this->$property = htmlspecialchars(trim($value));
            } else if ($property === 'feeding_time' && is_array($value)) {
                $this->$property = [
                    'HOUR' => max(0, (int)($value['HOUR'] ?? 0)),
                    'MIN'  => max(0, (int)($value['MIN'] ?? 0))
                ];
            } else if ($property === 'check_up' && is_array($value)) {
                $this->$property = [
                    'MONTH' => (int)($value['MONTH'] ?? 1),
                    'DAY'   => (int)($value['DAY'] ?? 1),
                    'YEAR'  => (int)($value['YEAR'] ?? 2000)
                ];
            } else {
                $this->$property = $value;
            }
        } else {
            throw new Exception("Property '$property' cannot be set.");
        }
    }

    public function getFormattedFeedingTime() {
        return "{$this->feeding_time['HOUR']}:" . str_pad($this->feeding_time['MIN'], 2, '0', STR_PAD_LEFT);
    }

    public function getFormattedCheckUp() {
        return "{$this->check_up['MONTH']}/{$this->check_up['DAY']}/{$this->check_up['YEAR']}";
    }
}
?>
