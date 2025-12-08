-- Create the database
CREATE DATABASE IF NOT EXISTS dolphin_crm;
USE dolphin_crm;

-- 1. Users Table
CREATE TABLE Users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    firstname VARCHAR(50) NOT NULL, 
    lastname VARCHAR(50) NOT NULL, 
    password VARCHAR(255) NOT NULL, 
    email VARCHAR(100) NOT NULL UNIQUE, 
    role VARCHAR(20) NOT NULL, -- 'Admin' or 'Member'
    created_at DATETIME NOT NULL 
);

-- 2. Contacts Table
CREATE TABLE Contacts (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    title VARCHAR(10), 
    firstname VARCHAR(50) NOT NULL, 
    lastname VARCHAR(50) NOT NULL, 
    email VARCHAR(100) NOT NULL, 
    telephone VARCHAR(20), 
    company VARCHAR(100), 
    type VARCHAR(20) NOT NULL, -- 'Sales Lead' or 'Support'
    assigned_to INT NOT NULL, 
    created_by INT NOT NULL, 
    created_at DATETIME NOT NULL, 
    updated_at DATETIME NOT NULL, 

    FOREIGN KEY (assigned_to) REFERENCES Users(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES Users(id) ON DELETE RESTRICT
);

-- 3. Notes Table
CREATE TABLE Notes (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    contact_id INT NOT NULL, 
    comment TEXT NOT NULL, 
    created_by INT NOT NULL, 
    created_at DATETIME NOT NULL, 

    FOREIGN KEY (contact_id) REFERENCES Contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES Users(id) ON DELETE RESTRICT
);

-- Initial Administrator User 
INSERT INTO Users (firstname, lastname, password, email, role, created_at) VALUES
('Admin', 'User', '$2y$10$m5R6AZIbQ.08d631qE7wkuJxOh/WOBjOHxKrLwiZG04QbyZgoOBA6', 'admin@project2.com', 'Admin', NOW()); 