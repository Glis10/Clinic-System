<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Doctors - Clinic Management';

// Get all doctors
$doctors = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name");

include '../includes/header.php';
?>

<div class="page-content">
    <div class="page-header">
        <div>
            <h2>👨‍⚕️ Doctors Management</h2>
            <p>Manage all doctor profiles</p>
        </div>
        <a href="add.php?type=doctor" class="btn btn-primary">+ Add New Doctor</a>
    </div>

    <div class="data-section">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Doctor Name</th>
                    <th>Specialty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($doctors) > 0): ?>
                    <?php while($doctor = mysqli_fetch_assoc($doctors)): ?>
                    <tr>
                        <td><?php echo $doctor['id']; ?></td>
                        <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                        <td><span class="specialty-badge"><?php echo htmlspecialchars($doctor['specialty']); ?></span></td>
                        <td class="actions">
                            <a href="edit.php?type=doctor&id=<?php echo $doctor['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?type=doctor&id=<?php echo $doctor['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this doctor?');" 
                               class="btn-delete">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">No doctors found. Add your first doctor!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
