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
    <link rel="stylesheet" href="style admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>RTB ADMIN</h2>
            </div>
            <ul>
                <li><a href="dashboard.php?page=overview" class="<?php echo $page == 'overview' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</a></li>
                <li><a href="dashboard.php?page=members" class="<?php echo $page == 'members' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Members Management</a></li>
                <li><a href="dashboard.php?page=approve_members" class="<?php echo $page == 'approve_members' ? 'active' : ''; ?>"><i class="fas fa-check-circle"></i> Approve Members</a></li>
            </ul>
            <div class="logout-button-container">
                <a href="logout.php" class="logout-button">Log Out</a>
            </div>
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
                default:
                    include 'overview.php';
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>
