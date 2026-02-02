<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Search Appointments';

$search_results = [];
$search_performed = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['search'])) {
    $search_performed = true;
    $search_type = isset($_POST['search_type']) ? $_POST['search_type'] : $_GET['search_type'];
    $search_value = isset($_POST['search_value']) ? sanitize($conn, $_POST['search_value']) : sanitize($conn, $_GET['search_value']);
    
    if ($search_type == 'date') {
        $query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone, 
                  d.name as doctor_name, d.specialty 
                  FROM appointments a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  WHERE a.appointment_date = ?
                  ORDER BY a.appointment_time";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $search_value);
        
    } elseif ($search_type == 'doctor') {
        $search_value = "%$search_value%";
        $query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone, 
                  d.name as doctor_name, d.specialty 
                  FROM appointments a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  WHERE d.name LIKE ?
                  ORDER BY a.appointment_date DESC, a.appointment_time";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $search_value);
        
    } elseif ($search_type == 'patient') {
        $search_value = "%$search_value%";
        $query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone, 
                  d.name as doctor_name, d.specialty 
                  FROM appointments a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  WHERE p.name LIKE ?
                  ORDER BY a.appointment_date DESC, a.appointment_time";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $search_value);
    }
    
    if (isset($stmt)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    }
}

include '../includes/header.php';
?>

<div class="search-container">
    <h2>🔍 Search Appointments</h2>
    
    <form method="POST" class="search-form">
        <div class="search-grid">
            <div class="form-group">
                <label for="search_type">Search By:</label>
                <select id="search_type" name="search_type" required>
                    <option value="date" <?php echo (isset($search_type) && $search_type == 'date') ? 'selected' : ''; ?>>Date</option>
                    <option value="doctor" <?php echo (isset($search_type) && $search_type == 'doctor') ? 'selected' : ''; ?>>Doctor Name</option>
                    <option value="patient" <?php echo (isset($search_type) && $search_type == 'patient') ? 'selected' : ''; ?>>Patient Name</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="search_value">Search Value:</label>
                <input type="text" id="search_value" name="search_value" 
                       value="<?php echo isset($search_value) ? htmlspecialchars(str_replace('%', '', $search_value)) : ''; ?>" 
                       placeholder="Enter search term..." required>
                <small id="search_hint" class="hint-text">Enter doctor name</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">🔍 Search</button>
                <a href="search.php" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>
    
    <?php if ($search_performed): ?>
        <div class="search-results">
            <h3>Search Results (<?php echo count($search_results); ?> found)</h3>
            
            <?php if (count($search_results) > 0): ?>
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
                        <?php foreach ($search_results as $apt): ?>
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
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-results">
                    <p>No appointments found matching your search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
