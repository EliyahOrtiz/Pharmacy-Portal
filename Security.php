<?php

class Security {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

   
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT user_id, password FROM Users WHERE userName = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
           
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $username;
            return true;
        } else {
            return false;
        }
    }

   
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
    }

    
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }
}
?>
