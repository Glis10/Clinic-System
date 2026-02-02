<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Dashboard - Clinic Management';

// Get statistics
$total_patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients"))['count'];
$total_doctors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM doctors"))['count'];
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments"))['count'];
$today_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()"))['count'];

// Get today's appointments
$today_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name, d.specialty 
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id 
                LEFT JOIN doctors d ON a.doctor_id = d.id 
                WHERE a.appointment_date = CURDATE()
                ORDER BY a.appointment_time ASC";
$today_appointments_result = mysqli_query($conn, $today_query);

// Get upcoming appointments
$upcoming_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name, d.specialty 
                   FROM appointments a 
                   LEFT JOIN patients p ON a.patient_id = p.id 
                   LEFT JOIN doctors d ON a.doctor_id = d.id 
                   WHERE a.appointment_date > CURDATE()
                   ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                   LIMIT 5";
$upcoming_appointments = mysqli_query($conn, $upcoming_query);

include '../includes/header.php';
?>

<div class="dashboard">
    <div class="welcome-banner">
        <h2>Welcome back, <?php echo htmlspecialchars(getUsername()); ?>! 👋</h2>
        <p>Here's what's happening in your clinic today</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card stat-patients">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $total_patients; ?></h3>
                <p>Total Patients</p>
            </div>
        </div>
        <div class="stat-card stat-doctors">
            <div class="stat-icon">👨‍⚕️</div>
            <div class="stat-info">
                <h3><?php echo $total_doctors; ?></h3>
                <p>Total Doctors</p>
            </div>
        </div>
        <div class="stat-card stat-appointments">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h3><?php echo $total_appointments; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>
        <div class="stat-card stat-today">
            <div class="stat-icon">⏰</div>
            <div class="stat-info">
                <h3><?php echo $today_appointments; ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-section">
            <div class="section-header">
                <h3>📋 Today's Appointments</h3>
                <a href="appointments.php" class="btn btn-sm">View All</a>
            </div>
            <div class="appointments-list">
                <?php if (mysqli_num_rows($today_appointments_result) > 0): ?>
                    <?php while($apt = mysqli_fetch_assoc($today_appointments_result)): ?>
                    <div class="appointment-card">
                        <div class="appointment-time">
                            <span class="time"><?php echo formatTime($apt['appointment_time']); ?></span>
                        </div>
                        <div class="appointment-details">
                            <h4><?php echo htmlspecialchars($apt['patient_name']); ?></h4>
                            <p>Dr. <?php echo htmlspecialchars($apt['doctor_name']); ?> - <?php echo htmlspecialchars($apt['specialty']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">No appointments scheduled for today</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-section">
            <div class="section-header">
                <h3>🔜 Upcoming Appointments</h3>
                <a href="appointments.php" class="btn btn-sm">View All</a>
            </div>
            <div class="appointments-list">
                <?php if (mysqli_num_rows($upcoming_appointments) > 0): ?>
                    <?php while($apt = mysqli_fetch_assoc($upcoming_appointments)): ?>
                    <div class="appointment-card">
                        <div class="appointment-time">
                            <span class="date"><?php echo formatDate($apt['appointment_date']); ?></span>
                            <span class="time"><?php echo formatTime($apt['appointment_time']); ?></span>
                        </div>
                        <div class="appointment-details">
                            <h4><?php echo htmlspecialchars($apt['patient_name']); ?></h4>
                            <p>Dr. <?php echo htmlspecialchars($apt['doctor_name']); ?> - <?php echo htmlspecialchars($apt['specialty']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-data">No upcoming appointments</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
            <a href="add.php?type=appointment" class="action-btn">
                <span class="action-icon">📅</span>
                <span>New Appointment</span>
            </a>
            <a href="add.php?type=patient" class="action-btn">
                <span class="action-icon">👤</span>
                <span>Add Patient</span>
            </a>
            <a href="add.php?type=doctor" class="action-btn">
                <span class="action-icon">⚕️</span>
                <span>Add Doctor</span>
            </a>
            <a href="search.php" class="action-btn">
                <span class="action-icon">🔍</span>
                <span>Search</span>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
