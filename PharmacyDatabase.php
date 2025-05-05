<?php

class PharmacyDatabase {
    private $host = "localhost";
    private $username = "root";
    private $password = "Spiderman:nowayhome1"; 
    private $database = "pharmarcy_portal_db";
    private $port = "3306";
    public $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    
    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity) {
       
        $stmt = $this->connection->prepare(
            "SELECT user_id FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patient_id);
        $stmt->fetch();
        $stmt->close();
    
        if ($patient_id) {
           
            $stmt = $this->connection->prepare(
                "INSERT INTO Prescriptions (user_id, medications_id, prescribedDate, dosageInstructions, quantity) 
                 VALUES (?, ?, NOW(), ?, ?)"
            );
            $stmt->bind_param("iisi", $patient_id, $medicationId, $dosageInstructions, $quantity);
    
            if ($stmt->execute()) {
                echo "Prescription added successfully";
            } else {
                throw new Exception("Failed to add prescription: " . $this->connection->error);
            }
    
            $stmt->close();
        } else {
            throw new Exception("Failed to add prescription: Patient not found");
        }
    }

   
    public function getAllPrescriptions() {
        $query = "
           SELECT p.prescriptions_id, p.user_id, p.medications_id AS medication_id, m.medicationName, p.dosageInstructions, p.quantity
           FROM Prescriptions p
           JOIN Medications m ON p.medications_id = m.medications_id
        ";
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

   
    public function getMedicationInventoryView() {
        $query = "
            SELECT m.medicationName, m.dosage, m.manufacturer, i.quantityAvailable
            FROM Inventory i
            JOIN Medications m ON i.medications_id = m.medications_id
        ";
        $result = $this->connection->query($query);
    
        if ($result === false) {
            die("Query Error: " . $this->connection->error); 
    
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        if (empty($data)) {
            die("No data returned from the query. Check your database tables."); 
        }
    
        return $data;
    }
    public function addUser($userName, $contactInfo, $userType, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
        $stmt = $this->connection->prepare(
            "INSERT INTO Users (userName, contactInfo, userType, password) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $userName, $contactInfo, $userType, $hashedPassword);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            throw new Exception("Failed to add user: " . $this->connection->error);
        }
    }

    
    public function getUserDetails($userId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM Users WHERE userId = ?"
        );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
}

}
        
