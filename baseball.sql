-- Drop and create database
DROP DATABASE IF EXISTS baseball_db;
CREATE DATABASE IF NOT EXISTS baseball_db;

-- Drop and grant user
DROP USER IF EXISTS 'baseball_username'@'localhost';
GRANT SELECT, INSERT, DELETE, UPDATE, EXECUTE ON baseball_db.* TO 'csuf_username'@'localhost';

USE baseball_db;

CREATE TABLE TEAM (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(150) NOT NULL
);


CREATE TABLE Players (
    playerID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED DEFAULT 1,
    position VARCHAR(150) NOT NULL,
    dob DATETIME, --date of birth
    team_id INT UNSIGNED NOT NULL,
    Street VARCHAR(250),
    City VARCHAR(100),
    State VARCHAR(100),
    Country VARCHAR(100),
    ZipCode VARCHAR(10),
    CONSTRAINT valid_zipcode CHECK (ZipCode REGEXP '^(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?$'),
    CONSTRAINT fk_team_id FOREIGN KEY (team_id) REFERENCES TEAM(ID) ON DELETE CASCADE
);


CREATE TABLE Statistics (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Player INT UNSIGNED NOT NULL,
    Games_played INT UNSIGNED NOT NULL,
    Plate_appearance TINYINT(2) UNSIGNED DEFAULT 0,
    Runs_Scored TINYINT(3) UNSIGNED DEFAULT 0,
    Hits TINYINT(3) UNSIGNED DEFAULT 0,
    Home_runs TINYINT(3) UNSIGNED DEFAULT 0,
    CONSTRAINT fk_player FOREIGN KEY (Player) REFERENCES Players(playerID) ON DELETE CASCADE
);


CREATE TABLE Matches (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    home_team INT UNSIGNED NOT NULL,
    away_team INT UNSIGNED NOT NULL,
    home_score TINYINT UNSIGNED DEFAULT 0,
    away_score TINYINT UNSIGNED DEFAULT 0,
    match_date DATE,
    CONSTRAINT fk_home FOREIGN KEY (home_team) REFERENCES TEAM(ID),
    CONSTRAINT fk_away FOREIGN KEY (away_team) REFERENCES TEAM(ID)
);

CREATE TABLE Roles (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);


CREATE TABLE Users (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES Roles(ID) ON DELETE CASCADE
);

-- insert roles
INSERT INTO Roles (ID, role_name) VALUES
(1, 'manager'),
(2, 'coach'),
(3, 'player'),
(4, 'visitor');

-- manager
CREATE USER IF NOT EXISTS 'manager'@'localhost' IDENTIFIED BY 'secureManagerPassword';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Players TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Statistics TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.TEAM TO 'manager'@'localhost';
FLUSH PRIVILEGES;

