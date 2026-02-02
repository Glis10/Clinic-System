<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Appointments - Clinic Management';

// Get all appointments with patient and doctor details
$appointments_query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone, 
                       d.name as doctor_name, d.specialty 
                       FROM appointments a 
                       LEFT JOIN patients p ON a.patient_id = p.id 
                       LEFT JOIN doctors d ON a.doctor_id = d.id 
                       ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = mysqli_query($conn, $appointments_query);

include '../includes/header.php';
?>

<div class="page-content">
    <div class="page-header">
        <div>
            <h2>📅 Appointments Management</h2>
            <p>View and manage all appointments</p>
        </div>
        <a href="add.php?type=appointment" class="btn btn-primary">+ Schedule New Appointment</a>
    </div>

    <div class="data-section">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Doctor</th>
                    <th>Specialty</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($appointments) > 0): ?>
                    <?php while($apt = mysqli_fetch_assoc($appointments)): ?>
                    <tr>
                        <td><?php echo $apt['id']; ?></td>
                        <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($apt['patient_phone']); ?></td>
                        <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                        <td><span class="specialty-badge"><?php echo htmlspecialchars($apt['specialty']); ?></span></td>
                        <td><?php echo formatDate($apt['appointment_date']); ?></td>
                        <td><strong><?php echo formatTime($apt['appointment_time']); ?></strong></td>
                        <td class="actions">
                            <a href="edit.php?type=appointment&id=<?php echo $apt['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?type=appointment&id=<?php echo $apt['id']; ?>" 
                               onclick="return confirm('Are you sure you want to cancel this appointment?');" 
                               class="btn-delete">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-data">No appointments found. Schedule your first appointment!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
