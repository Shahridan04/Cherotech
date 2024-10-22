<?php
// Default page
$page = isset($_GET['page']) ? $_GET['page'] : 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTB Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>RTB ADMIN</h2>
            </div>
            <ul>
                <li><a href="dashboard.php?page=overview">Dashboard Overview</a></li>
                <li><a href="dashboard.php?page=members">Members Management</a></li>
                <li><a href="dashboard.php?page=approve_members">Approve Members</a></li>
                <li><a href="dashboard.php?page=reports">Analytics/Reports</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            // Dynamically include content based on the selected section
            switch ($page) {
                case 'members':
                    include 'members.php';
                    break;
                case 'approve_members':
                    include 'approve_members.php';
                    break;
                case 'reports':
                    include 'reports.php';
                    break;
                default:
                    include 'overview.php';
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>
