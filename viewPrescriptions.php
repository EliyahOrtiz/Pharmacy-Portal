<?php
require_once 'PharmacyDatabase.php';


$db = new PharmacyDatabase();


$prescriptions = $db->getAllPrescriptions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescriptions</title>
    <style>
      
    </style>
</head>
<body>
    <h1>All Prescriptions</h1>

    
    <?php if (isset($_GET['message'])): ?>
        <div class="message">
            <p style="background-color: #e0f7fa; color: #00796b; padding: 10px; border-radius: 5px;">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </p>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Prescription ID</th>
            <th>User ID</th>
            <th>Medication ID</th>
            <th>Medication Name</th>
            <th>Dosage Instructions</th>
            <th>Quantity</th>
        </tr>
        <?php if (empty($prescriptions)): ?>
            <tr>
                <td colspan="6" class="no-data">No prescriptions available for viewing.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($prescriptions as $prescription): ?>
         <tr>
        <td><?php echo htmlspecialchars($prescription['prescriptions_id']); ?></td>
        <td><?php echo htmlspecialchars($prescription['user_id']); ?></td>
          <td><?php echo htmlspecialchars($prescription['medication_id']); ?></td>
          <td><?php echo htmlspecialchars($prescription['medicationName']); ?></td>
          <td><?php echo htmlspecialchars($prescription['dosageInstructions']); ?></td>
          <td><?php echo htmlspecialchars($prescription['quantity']); ?></td>
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

    
    <p><a href="?action=home">Back to Home</a></p>
</body>
</html>
