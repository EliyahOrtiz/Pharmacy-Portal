<html>
<head>
    <title>Add Prescription</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function validateForm() {
            const userName = document.forms["addPrescriptionForm"]["userName"].value;
            const medicationsId = document.forms["addPrescriptionForm"]["medications_id"].value;
            const dosageInstructions = document.forms["addPrescriptionForm"]["dosageInstructions"].value;
            const quantity = document.forms["addPrescriptionForm"]["quantity"].value;

            if (!userName || !medicationsId || !dosageInstructions || !quantity) {
                alert("All fields must be filled out.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h1>Add Your Prescription</h1>
    <form name="addPrescriptionForm" method="POST" action="PharmacyServer.php?action=addPrescription" onsubmit="return validateForm()">
        Patient Username: <input type="text" name="userName" /><br>
        Medication ID: <input type="number" name="medications_id" /><br>
        Dosage Instructions: <textarea name="dosageInstructions"></textarea><br>
        Quantity: <input type="number" name="quantity" /><br>
        <button type="submit">Save</button>
    </form>
    <a href="PharmacyServer.php">Back to Home</a>
</body>
</html>
