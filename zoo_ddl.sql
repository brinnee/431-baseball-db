DROP DATABASE IF EXISTS zoo_db;
CREATE DATABASE IF NOT EXISTS zoo_db;
DROP USER IF EXISTS 'zoo_username'@'localhost';
GRANT
    SELECT,
    INSERT,
    DELETE,
    UPDATE,
    EXECUTE ON zoo_db.* TO 'csuf_username'@'localhost';

USE zoo_db;

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

INSERT INTO Employee VALUES (2,'Rigby','Test','nowhere','here','there','somehwere',84651);


CREATE TABLE Animals (
    animalID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    care_takerID INT UNSIGNED NOT NULL,
    exhibit VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED DEFAULT 1,
    weight INT UNSIGNED,
    feeding_time DATETIME,
    food_type VARCHAR(250),
    CONSTRAINT fk_caretakerID FOREIGN KEY (care_takerID) REFERENCES Employee(employeeID) ON DELETE CASCADE
);

CREATE TABLE Roles (
    ID INT AUTO_INCREMENT primary key,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO Roles (ID, role_name) VALUES
(1, Manager),
(2, Visitor),
(3, Employee);

INSERT INTO accounts (username, password, role_name) VALUES
('owner1', 'pass123', 'owner'),
('manager1', 'pass123', 'manager'),
('employee1', 'pass123', 'employee'),
('customer1', 'pass123', 'customer');

--manager has full access(read/write)
CREATE USER 'manager'@'localhost' IDENTIFIED BY 'secureManagerPassword';
GRANT SELECT, INSERT, UPDATE ON zoo_db.Employee TO 'manager'@'localhost';
FLUSH PRIVILEGES

--employee (read and write animal data)
CREATE USER 'employee'@'localhost' IDENTIFIED BY 'secureEmployeePassword';
GRANT SELECT, INSERT, UPDATE ON zoo_db.Animals TO 'employee'@'localhost';
FLUSH PRIVILEGES;

--customer (buy tickets)
