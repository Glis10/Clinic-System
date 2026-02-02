<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Patients - Clinic Management';

// Get all patients
$patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY name");

include '../includes/header.php';
?>

<div class="page-content">
    <div class="page-header">
        <div>
            <h2>👥 Patients Management</h2>
            <p>Manage all patient records</p>
        </div>
        <a href="add.php?type=patient" class="btn btn-primary">+ Add New Patient</a>
    </div>

    <div class="data-section">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($patients) > 0): ?>
                    <?php while($patient = mysqli_fetch_assoc($patients)): ?>
                    <tr>
                        <td><?php echo $patient['id']; ?></td>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                        <td class="actions">
                            <a href="edit.php?type=patient&id=<?php echo $patient['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?type=patient&id=<?php echo $patient['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this patient?');" 
                               class="btn-delete">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">No patients found. Add your first patient!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
