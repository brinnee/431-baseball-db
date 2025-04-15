DROP DATABASE IF EXISTS zoo__db;
CREATE DATABASE IF NOT EXISTS zoo_db;
DROP USER IF EXISTS 'zoo_username'@'localhost';
GRANT
    SELECT,
    INSERT,
    DELETE,
    UPDATE,
    EXECUTE ON zoo_db.* TO 'csuf_username'@'localhost';

USE zoo__db;

CREATE TABLE Employee (
    employeeID INT UNSIGNED AUTO_INCREMENT,
    Fname VARCHAR(100) NOT NULL,
    Lname VARCHAR(150) NOT NULL,
    Street VARCHAR(250),
    City VARCHAR(100),
    State VARCHAR(100),
    Country VARCHAR(100),
    ZipCode VARCHAR(10) NULL,
    PRIMARY KEY (employeeID),
    CONSTRAINT valid_zipcode CHECK (ZipCode REGEXP '^(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?$') 
);

CREATE TABLE Animals (
    animalID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exhibit VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED DEFAULT 1,
    weight INT UNSIGNED,
    feeding_time TIME,
    food_type VARCHAR(250),
    CONSTRAINT fk_caretakerID FOREIGN KEY (employeeID) REFERENCES Employee(employeeID) ON DELETE CASCADE
);

CREATE TABLE Roles (
    ID INT AUTO_INCREMENT primary key,
    role_name VARCHAR(50) UNIQUE NOT NULL
);


--manager has full access(read/write)
CREATE USER 'manager'@'localhost' IDENTIFIED BY 'secureManagerPassword';
GRANT SELECT, INSERT, UPDATE ON zoo_db.Employee TO 'manager'@'localhost';
FLUSH PRIVILEGES

--employee (read and write animal data)
CREATE USER 'employee'@'localhost' IDENTIFIED BY 'secureEmployeePassword';
GRANT SELECT, INSERT, UPDATE ON zoo_db.Animals TO 'employee'@'localhost';
FLUSH PRIVILEGES;

--customer (buy tickets)
