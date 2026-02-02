<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (isset($_GET['doctor_id']) && isset($_GET['date'])) {
    $doctor_id = intval($_GET['doctor_id']);
    $date = sanitize($conn, $_GET['date']);
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
    
    $slots = getAvailableTimeSlots($conn, $doctor_id, $date);
    
    // Format slots for display
    $formatted_slots = [];
    foreach ($slots as $slot) {
        $formatted_slots[] = [
            'value' => $slot,
            'display' => formatTime($slot)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'slots' => $formatted_slots,
        'count' => count($formatted_slots)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
}
?>
