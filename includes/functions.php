<?php
// Helper functions

function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

function checkAppointmentOverlap($conn, $doctor_id, $appointment_date, $appointment_time, $exclude_id = null) {
    $query = "SELECT * FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?";
    
    if ($exclude_id) {
        $query .= " AND id != ?";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    
    if ($exclude_id) {
        mysqli_stmt_bind_param($stmt, "issi", $doctor_id, $appointment_date, $appointment_time, $exclude_id);
    } else {
        mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $appointment_date, $appointment_time);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}

function getAvailableTimeSlots($conn, $doctor_id, $date) {
    // Define working hours (9 AM to 5 PM)
    $slots = [];
    for ($hour = 9; $hour < 17; $hour++) {
        $time = sprintf("%02d:00:00", $hour);
        $slots[] = $time;
    }
    
    // Get booked slots
    $query = "SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $doctor_id, $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $booked = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $booked[] = $row['appointment_time'];
    }
    
    // Filter available slots
    $available = array_diff($slots, $booked);
    return array_values($available);
}

function formatTime($time) {
    return date("g:i A", strtotime($time));
}

function formatDate($date) {
    return date("M d, Y", strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'success') {
    return "<div class='alert alert-$type'>$message</div>";
}
?>
