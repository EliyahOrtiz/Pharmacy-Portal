CREATE DATABASE pharmarcy_portal_db;
USE pharmarcy_portal_db;

CREATE TABLE Users
(
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    userName VARCHAR(45) NOT NULL UNIQUE,
    contactinfo VARCHAR(255) NOT NULL UNIQUE,
    userType ENUM('pharmacist', 'patient') NOT NULL
);

CREATE TABLE Medications
(
    medications_id INT NOT NULL UNIQUE AUTO_INCREMENT,
    medicationName VARCHAR(100) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    manufacturer VARCHAR(200),
    PRIMARY KEY (medications_id)
);

CREATE TABLE Prescriptions
(
    prescriptions_id INT NOT NULL UNIQUE AUTO_INCREMENT,
    user_id INT NOT NULL,
    medications_id INT NOT NULL,
    prescribedDate DATETIME NOT NULL,
    dosageInstructions VARCHAR(200),
    quantity INT NOT NULL,
    refillCount INT DEFAULT 0,
    PRIMARY KEY (prescriptions_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (medications_id) REFERENCES Medications(medications_id)
);

CREATE TABLE Inventory
(
    inventory_id INT NOT NULL UNIQUE AUTO_INCREMENT,
    medications_id INT NOT NULL,
    quantityAvailable INT NOT NULL,
    lastUpdated DATETIME NOT NULL,
    PRIMARY KEY (inventory_id),
    FOREIGN KEY (medications_id) REFERENCES Medications(medications_id)
);

CREATE TABLE Sales
(
    sale_id INT NOT NULL UNIQUE AUTO_INCREMENT,
    prescription_id INT NOT NULL,
    saleDate DATETIME NOT NULL,
    quantitySold INT NOT NULL,
    saleAmount DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (sale_id),
    FOREIGN KEY (prescription_id) REFERENCES Prescriptions(prescriptions_id)
);

DELIMITER $$

CREATE PROCEDURE AddOrUpdateUser
(
    IN user_id INT,
    IN userName VARCHAR(45),
    IN contactInfo VARCHAR(200),
    IN userType ENUM('pharmacist', 'patient')
)
BEGIN
    IF user_id IS NULL THEN
        INSERT INTO Users (userName, contactInfo, userType) 
        VALUES (userName, contactInfo, userType);
    ELSE
        UPDATE Users
        SET userName = userName, contactInfo = contactInfo, userType = userType
        WHERE user_id = userId;
    END IF;
END $$

DELIMITER $$

CREATE PROCEDURE ProcessSale(
    IN prescription_id INT,
    IN quantitySold INT
)
BEGIN
    DECLARE inventoryQty INT;
    DECLARE medicationPrice DECIMAL(10, 2);

    SELECT quantityAvailable INTO inventoryQty 
    FROM Inventory 
    WHERE medications_id = (SELECT medications_id FROM Prescriptions WHERE prescriptions_id = prescription_id);
    
    IF inventoryQty >= quantitySold THEN
        UPDATE Inventory 
        SET quantityAvailable = quantityAvailable - quantitySold, lastUpdated = NOW()
        WHERE medications_id = (SELECT medications_id FROM Prescriptions WHERE prescriptions_id = prescription_id);

        SELECT 10.00 INTO medicationPrice; -- Replace with actual price logic if needed
        
        INSERT INTO Sales (prescription_id, saleDate, quantitySold, saleAmount) 
        VALUES (prescriptionId, NOW(), quantitySold, medicationPrice * quantitySold);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Not enough stock available';
    END IF;
END $$

DELIMITER $$

CREATE VIEW MedicationInventoryView AS
SELECT 
    m.medicationName,
    m.dosage,
    m.manufacturer,
    i.quantityAvailable
FROM 
    Medications m
JOIN 
    Inventory i ON m.medications_id = i.medications_id;

DELIMITER $$

CREATE TRIGGER AfterPrescriptionInsert
AFTER INSERT ON Prescriptions
FOR EACH ROW
BEGIN
    UPDATE Inventory 
    SET quantityAvailable = quantityAvailable - NEW.quantity,
        lastUpdated = NOW()
    WHERE medications_id = NEW.medications_id;

    IF (SELECT quantityAvailable FROM Inventory WHERE medications_id = NEW.medications_id) < 10 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Low stock warning';
    END IF;
END $$

DELIMITER ;

INSERT INTO Users (userName, contactInfo, userType)
VALUES('Johnny_Smith', 'example@gmail.com', 'patient'),
('Jasmine_Rodriguez', 'jasmine@outlook.com', 'pharmacist'),
('Mike_Ortiz', 'mike@yahoo.com', 'pharmacist');



INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES
('Benadryl', '200mg', 'Merck'),
('Ibuprofen', '500mg', 'Pfizer'),
('Amoxicillin', '300mg', 'Johnson & Johnson');

INSERT INTO Inventory (medications_id, quantityAvailable, lastUpdated) VALUES
(1, 150, NOW()),
(2, 25, NOW()),
(3, 75, NOW());

INSERT INTO Prescriptions (user_id, medications_id, prescribedDate, dosageInstructions, quantity, refillCount) VALUES
(1, 1, NOW(), 'Take one tablet every 6 hours', 10, 1),
(3, 2, NOW(), 'Take two tablets daily', 5, 0),
(1, 3, NOW(), 'One capsule after meals', 7, 2);


CALL ProcessSale(1, 5);
CALL ProcessSale(2, 2);
CALL ProcessSale(3, 3);

ALTER TABLE Users ADD COLUMN password VARCHAR(255) NOT NULL;


SELECT m.medicationName, m.dosage, m.manufacturer, i.quantityAvailable
FROM Inventory i
JOIN Medications m ON i.medications_id = m.medications_id;