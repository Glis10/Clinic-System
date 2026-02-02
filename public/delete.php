<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0 && $type) {
    if ($type == 'patient') {
        // Check if patient has appointments
        $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE patient_id = $id");
        $result = mysqli_fetch_assoc($check);
        
        if ($result['count'] > 0) {
            // Patient has appointments, cannot delete
            echo "<script>alert('Cannot delete patient with existing appointments!'); window.location.href='patients.php';</script>";
            exit;
        }
        
        mysqli_query($conn, "DELETE FROM patients WHERE id = $id");
        redirect('patients.php');
        
    } elseif ($type == 'doctor') {
        // Check if doctor has appointments
        $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = $id");
        $result = mysqli_fetch_assoc($check);
        
        if ($result['count'] > 0) {
            // Doctor has appointments, cannot delete
            echo "<script>alert('Cannot delete doctor with existing appointments!'); window.location.href='doctors.php';</script>";
            exit;
        }
        
        mysqli_query($conn, "DELETE FROM doctors WHERE id = $id");
        redirect('doctors.php');
        
    } elseif ($type == 'appointment') {
        mysqli_query($conn, "DELETE FROM appointments WHERE id = $id");
        redirect('appointments.php');
    }
}

redirect('dashboard.php');
?>
