<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Clinic Management'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo">🏥 Clinic Manager</h1>
            <?php if (isLoggedIn()): ?>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="patients.php">Patients</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li><a href="appointments.php">Appointments</a></li>
                <li><a href="search.php">Search</a></li>
                <li class="user-info">
                    <span>👤 <?php echo htmlspecialchars(getUsername()); ?></span>
                </li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container main-content">
