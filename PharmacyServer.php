<?php

require_once 'PharmacyDatabase.php';
require_once 'Security.php';

class PharmacyPortal {
    private $db;

    public function __construct() {
        $this->db = new PharmacyDatabase();
    }

    public function getDb() {
        return $this->db;
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'home'; // Retrieve action from the request or default to 'home'
        $allowedActions = ['addPrescription', 'viewPrescriptions', 'viewInventory', 'viewUsers', 'home'];

        if (!in_array($action, $allowedActions)) {
            $action = 'home';
        }

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            // Fallback to home if the method does not exist
            $this->home();
        }
    }

    // Home page
    private function home() {
        include 'templates/home.php';
    }

    // Add prescription logic
    private function addPrescription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientUserName = $_POST['userName'] ?? '';
            $medicationId = $_POST['medications_id'] ?? '';
            $dosageInstructions = $_POST['dosageInstructions'] ?? '';
            $quantity = $_POST['quantity'] ?? '';

            if (empty($patientUserName) || empty($medicationId) || empty($dosageInstructions) || empty($quantity)) {
                echo "Please fill in all fields.";
                return;
            }

            try {
                $this->db->addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity);
                header("Location:?action=viewPrescriptions&message=Prescription Added");
                exit();
            } catch (Exception $e) {
                echo "Error adding prescription: " . $e->getMessage();
            }
        } else {
            include 'templates/addPrescription.php';
        }
    }

    // View prescriptions logic
    private function viewPrescriptions() {
        $prescriptions = $this->db->getAllPrescriptions();
        $message = $_GET['message'] ?? '';
        include 'templates/viewPrescriptions.php';
    }

    // View inventory logic
    private function viewInventory() {
        $inventory = $this->db->getMedicationInventoryView(); 
        include 'templates/viewInventory.php'; 
    }
    // View users logic
    private function viewUsers() {
        $users = $this->getAllUsers();
        $message = $_GET['message'] ?? '';
        include 'templates/viewUsers.php';
    }

    // Add user logic
    private function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userName = $_POST['user_name'] ?? '';
            $contactinfo = $_POST['contact_info'] ?? '';
            $userType = $_POST['user_type'] ?? 'patient';

            if (empty($userName) || empty($contactinfo) || empty($userType)) {
                echo "Please fill in all fields.";
                return;
            }

            try {
                $this->db->addUser($userName, $contactinfo, $userType, 'defaultPassword');
                header("Location:?action=viewUsers&message=User Added");
                exit();
            } catch (Exception $e) {
                echo "Error adding user: " . $e->getMessage();
            }
        } else {
            include 'templates/addUser.php';
        }
    }

    public function getAllUsers() {
        $query = "SELECT * FROM Users";
        $result = $this->db->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Initialize the PharmacyPortal class
$portal = new PharmacyPortal();

// Pass the database connection to the Security class
$security = new Security($portal->getDb()->connection);

if (!$security->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Handle the request
$portal->handleRequest();
?>