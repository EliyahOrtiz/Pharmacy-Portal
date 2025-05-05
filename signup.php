<?php
require_once 'PharmacyDatabase.php';

$db = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $contactInfo = $_POST['contact_info'] ?? '';
    $userType = $_POST['user_type'] ?? 'patient';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($username) || empty($contactInfo) || empty($userType) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Add the user to the database
            $db->addUser($username, $contactInfo, $userType, $password);
            header("Location: login.php?message=Account created successfully. Please log in.");
            exit();
        } catch (Exception $e) {
            $error = "Error creating account: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Create an Account</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="signup.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="contact_info">Contact Info:</label>
        <input type="text" id="contact_info" name="contact_info" required><br>
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type">
            <option value="patient">Patient</option>
            <option value="pharmacist">Pharmacist</option>
            <option value="admin">Admin</option>
        </select><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in here</a>.</p>
</body>
</html>