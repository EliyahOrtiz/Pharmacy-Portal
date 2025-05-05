<html>
<head>
    <title>View Inventory</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Medication Inventory</h1>

    <?php 
    
    $message = $_GET['message'] ?? '';
    if (!empty($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    
    <table>
        <thead>
            <tr>
                <th>Medication Name</th>
                <th>Dosage</th>
                <th>Manufacturer</th>
                <th>Quantity Available</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($inventory)): ?>
                <tr><td colspan="4">No inventory data available.</td></tr>
            <?php else: ?>
                <?php foreach ($inventory as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['medicationName']); ?></td>
                    <td><?php echo htmlspecialchars($item['dosage']); ?></td>
                    <td><?php echo htmlspecialchars($item['manufacturer']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantityAvailable']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

   
    <div>
        <a href="?action=home">Back to Home</a>
        <a href="?action=addPrescription">Add Prescription</a>
        <a href="?action=viewPrescriptions">View Prescriptions</a>
    </div>
</body>
</html>
