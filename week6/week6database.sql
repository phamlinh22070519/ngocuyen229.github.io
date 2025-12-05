CREATE DATABASE user_account;

USE user_account;

CREATE TABLE table1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL
);
