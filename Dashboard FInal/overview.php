<?php
include 'db_connect.php';

// Fetch total number of files (members)
$totalFilesQuery = "SELECT COUNT(*) as total FROM files"; 
$totalFilesResult = $conn->query($totalFilesQuery);
$totalFiles = $totalFilesResult->fetch_assoc()['total'];

// Fetch total number of male and female members
$genderQuery = "SELECT gender, COUNT(*) as count FROM files GROUP BY gender";
$genderResult = $conn->query($genderQuery);

$genderCounts = [];
while ($row = $genderResult->fetch_assoc()) {
    $genderCounts[$row['gender']] = $row['count'];
}
$totalMale = $genderCounts['Male'] ?? 0;
$totalFemale = $genderCounts['Female'] ?? 0;

// Fetch total number of pending approvals
$totalPendingQuery = "SELECT COUNT(*) as total FROM files WHERE status = 'Pending'";
$totalPendingResult = $conn->query($totalPendingQuery);

if ($totalPendingResult) {
    $totalPending = $totalPendingResult->fetch_assoc()['total'];
} else {
    $totalPending = 0; // Default to 0 if the query fails
}

// Fetch membership counts and calculate revenue
$membershipQuery = "SELECT membership, COUNT(*) as count FROM files GROUP BY membership";
$membershipResult = $conn->query($membershipQuery);

$membershipCounts = [];
$totalRevenue = 0;
while ($row = $membershipResult->fetch_assoc()) {
    $membershipCounts[$row['membership']] = $row['count'];
    switch ($row['membership']) {
        case 'Monthly':
            $totalRevenue += $row['count'] * 130;
            break;
        case '3-Month':
            $totalRevenue += $row['count'] * 360;
            break;
        case 'Yearly':
            $totalRevenue += $row['count'] * 1260;
            break;
    }
}
$totalMonthly = $membershipCounts['Monthly'] ?? 0;
$total3Month = $membershipCounts['3-Month'] ?? 0;
$totalYearly = $membershipCounts['Yearly'] ?? 0;

// Fetch age distribution (grouping into ranges)
$ageQuery = "
    SELECT 
        CASE 
            WHEN age < 20 THEN 'Under 20'
            WHEN age BETWEEN 20 AND 29 THEN '20-29'
            WHEN age BETWEEN 30 AND 39 THEN '30-39'
            WHEN age BETWEEN 40 AND 49 THEN '40-49'
            WHEN age >= 50 THEN '50 and above'
        END AS age_group,
        COUNT(*) as count 
    FROM files 
    GROUP BY age_group
";
$ageResult = $conn->query($ageQuery);

if (!$ageResult) {
    die("Error executing age query: " . $conn->error);
}

$ageDistribution = [];
while ($row = $ageResult->fetch_assoc()) {
    $ageDistribution[$row['age_group']] = $row['count'];
}
$ageLabels = array_keys($ageDistribution);
$ageCounts = array_values($ageDistribution);

// Fetch recent files (new members)
$recentFilesQuery = "SELECT name, email, phone, gender, membership FROM files ORDER BY registration_date DESC LIMIT 10";
$recentFilesResult = $conn->query($recentFilesQuery);

if (!$recentFilesResult) {
    die("Error executing query: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
        }

        h1 {
            color: #333;
        }

        .overview-container {
            display: flex;
            flex: 1;
            overflow: auto;
        }

        .summary-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }

        .graph-container {
            display: flex;
            flex-direction: column;
            width: 100%; /* Full width for the graph container */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-left: 20px; /* Added margin to the left for centering */
        }

        .totals {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            width: 100%;
        }

        .total-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0 , 0, 0, 0.1);
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }

        .total-box h3 {
            margin: 0 0 10px;
            color: #555;
        }

        .total-box p {
            font-size: 24px;
            color: #ff9800;
        }

        /* Change color for pending approvals */
        .pending-approval {
            background: #ffcccc; /* Soft red */
        }

        .chart-container {
            width: 100%; 
            margin-bottom: 20px;
        }

        canvas {
            max-height: 400px; /* Increased max height for charts */
            height: 400px; /* Set a fixed height for better visibility */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        h2 {
            margin-top: 30px;
            color: #333;
        }

        .charts-row {
            display: flex;
            justify-content: space-between;
        }

        .chart-container {
            width: 48%; /* Adjust width for side-by-side display */
        }
    </style>
</head>
<body>
    <header>
        <h1>Dashboard Overview</h1>
    </header>
    <div class="overview-container">
        <div class="summary-container">
            <div class="totals">
                <div class="total-box">
                    <h3>Total Members</h3>
                    <p><?php echo $totalFiles; ?></p>
                </div>
                <div class="total-box">
                    <h3>Male</h3>
                    <p><?php echo $totalMale; ?></p>
                </div>
                <div class="total-box">
                    <h3>Female</h3>
                    <p><?php echo $totalFemale; ?></p>
                </div>
                <div class="total-box <?php echo $totalPending > 0 ? 'pending-approval' : ''; ?>">
                    <h3>Pending Approval</h3>
                    <p><?php echo $totalPending; ?></p>
                </div>
                <div class="total-box">
                    <h3>Total Revenue</h3>
                    <p>RM <?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>

            <h2>New Members</h2>
            <table class="recent-files-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Membership</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($file = $recentFilesResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                            <td><?php echo htmlspecialchars($file['email']); ?></td>
                            <td><?php echo htmlspecialchars($file['phone']); ?></td>
                            <td><?php echo htmlspecialchars($file['gender']); ?></td>
                            <td><?php echo htmlspecialchars($file['membership']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="graph-container">
            <h3>Charts</h3>
            <div class="charts-row">
                <div class="chart-container">
                    <h4>Gender Distribution</h4>
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4>Membership Types</h4>
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>

            <div class="charts-row">
                <div class="chart-container">
                    <h4>Age Distribution</h4>
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data for Gender Distribution Chart
        const genderData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [<?php echo $totalMale; ?>, <?php echo $totalFemale; ?>],
                backgroundColor: ['#FF6384', '#36A2EB'],
            }]
        };

        // Create Pie Chart for Gender Distribution
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: genderData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Data for Membership Types Chart
        const membershipData = {
            labels: ['Monthly', '3-Month', 'Yearly'],
            datasets: [{
                label: 'Membership Types',
                data: [<?php echo $totalMonthly; ?>, <?php echo $total3Month; ?>, <?php echo $totalYearly; ?>],
                backgroundColor: ['#4BC0C0', '#FF6384', '#FFCE56'],
                borderColor: '#000000',
                borderWidth: 1,
            }]
        };

        // Create Bar Chart for Membership Types
        const membershipCtx = document.getElementById('membershipChart').getContext('2d');
        const membershipChart = new Chart(membershipCtx, {
            type: 'bar',
            data: membershipData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Data for Age Distribution Chart
        const ageData = {
            labels: <?php echo json_encode($ageLabels); ?>,
            datasets: [{
                label: 'Age Distribution',
                data: <?php echo json_encode($ageCounts); ?>,
                backgroundColor: '#36A2EB',
            }]
        };

        // Create Bar Chart for Age Distribution
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        const ageChart = new Chart(ageCtx, {
            type: 'bar',
            data: ageData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
