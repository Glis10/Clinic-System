<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = 'Edit ' . ucfirst($type);

// Fetch existing data
if ($type == 'patient') {
    $result = mysqli_query($conn, "SELECT * FROM patients WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
} elseif ($type == 'doctor') {
    $result = mysqli_query($conn, "SELECT * FROM doctors WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
} elseif ($type == 'appointment') {
    $result = mysqli_query($conn, "SELECT * FROM appointments WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
}

if (!$data) {
    redirect('dashboard.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($type == 'patient') {
        $name = sanitize($conn, $_POST['name']);
        $phone = sanitize($conn, $_POST['phone']);
        
        $query = "UPDATE patients SET name = ?, phone = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $phone, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            redirect('patients.php');
        }
    } 
    elseif ($type == 'doctor') {
        $name = sanitize($conn, $_POST['name']);
        $specialty = sanitize($conn, $_POST['specialty']);
        
        $query = "UPDATE doctors SET name = ?, specialty = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $specialty, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            redirect('doctors.php');
        }
    } 
    elseif ($type == 'appointment') {
        $patient_id = intval($_POST['patient_id']);
        $doctor_id = intval($_POST['doctor_id']);
        $appointment_date = sanitize($conn, $_POST['appointment_date']);
        $appointment_time = sanitize($conn, $_POST['appointment_time']);
        
        // Check for overlapping appointments (excluding current appointment)
        if (checkAppointmentOverlap($conn, $doctor_id, $appointment_date, $appointment_time, $id)) {
            $error = "This time slot is already booked for the selected doctor!";
        } else {
            $query = "UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, appointment_time = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iissi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                redirect('appointments.php');
            }
        }
    }
}

// Get data for dropdowns
$patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY name");
$doctors = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name");

include '../includes/header.php';
?>

<div class="form-container">
    <h2>Edit <?php echo ucfirst($type); ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="data-form">
        <?php if ($type == 'patient'): ?>
            <div class="form-group">
                <label for="name">Patient Name: <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number: <span class="required">*</span></label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone']); ?>" required>
            </div>
            
        <?php elseif ($type == 'doctor'): ?>
            <div class="form-group">
                <label for="name">Doctor Name: <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="specialty">Specialty: <span class="required">*</span></label>
                <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($data['specialty']); ?>" required>
            </div>
            
        <?php elseif ($type == 'appointment'): ?>
            <div class="form-group">
                <label for="patient_id">Select Patient: <span class="required">*</span></label>
                <select id="patient_id" name="patient_id" required>
                    <option value="">-- Choose Patient --</option>
                    <?php while($patient = mysqli_fetch_assoc($patients)): ?>
                        <option value="<?php echo $patient['id']; ?>" 
                                <?php echo ($patient['id'] == $data['patient_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($patient['name']); ?> (<?php echo htmlspecialchars($patient['phone']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="doctor_id">Select Doctor: <span class="required">*</span></label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">-- Choose Doctor --</option>
                    <?php while($doctor = mysqli_fetch_assoc($doctors)): ?>
                        <option value="<?php echo $doctor['id']; ?>" 
                                <?php echo ($doctor['id'] == $data['doctor_id']) ? 'selected' : ''; ?>
                                data-specialty="<?php echo htmlspecialchars($doctor['specialty']); ?>">
                            <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialty']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="appointment_date">Date: <span class="required">*</span></label>
                <input type="date" id="appointment_date" name="appointment_date" 
                       value="<?php echo $data['appointment_date']; ?>" 
                       min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="appointment_time">Time: <span class="required">*</span></label>
                <select id="appointment_time" name="appointment_time" required>
                    <option value="<?php echo $data['appointment_time']; ?>" selected>
                        <?php echo formatTime($data['appointment_time']); ?> (Current)
                    </option>
                </select>
                <div id="loading-slots" style="display:none; color: #666; margin-top: 5px;">
                    ⏳ Loading available slots...
                </div>
            </div>
            
            <input type="hidden" id="current_appointment_id" value="<?php echo $id; ?>">
        <?php endif; ?>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">✅ Update</button>
            <a href="<?php echo ($type == 'patient' ? 'patients.php' : ($type == 'doctor' ? 'doctors.php' : 'appointments.php')); ?>" class="btn btn-secondary">❌ Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
