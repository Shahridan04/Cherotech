<?php
include 'db_connect.php';

// Fetch summary data with optimized queries
$summaryQuery = "
    SELECT
        (SELECT COUNT(*) FROM files) as totalFiles,
        (SELECT COUNT(*) FROM files WHERE gender = 'Male') as totalMale,
        (SELECT COUNT(*) FROM files WHERE gender = 'Female') as totalFemale,
        (SELECT COUNT(*) FROM files WHERE status = 'Pending') as totalPending,
        (SELECT COUNT(*) FROM files WHERE membership = 'Monthly') as totalMonthly,
        (SELECT COUNT(*) FROM files WHERE membership = '3-Month') as total3Month,
        (SELECT COUNT(*) FROM files WHERE membership = 'Yearly') as totalYearly
";
$summaryResult = $conn->query($summaryQuery);
$summaryData = $summaryResult->fetch_assoc();

$totalFiles = $summaryData['totalFiles'];
$totalMale = $summaryData['totalMale'];
$totalFemale = $summaryData['totalFemale'];
$totalPending = $summaryData['totalPending'];
$totalMonthly = $summaryData['totalMonthly'];
$total3Month = $summaryData['total3Month'];
$totalYearly = $summaryData['totalYearly'];
$totalRevenue = ($totalMonthly * 130) + ($total3Month * 360) + ($totalYearly * 1260);

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

// Fetch monthly registration data
$monthlyRegistrationQuery = "
    SELECT DATE_FORMAT(registration_date, '%Y-%m') AS month, COUNT(*) as count
    FROM files
    GROUP BY month
    ORDER BY month ASC
";
$monthlyRegistrationResult = $conn->query($monthlyRegistrationQuery);

$monthlyRegistrations = [];
while ($row = $monthlyRegistrationResult->fetch_assoc()) {
    $monthlyRegistrations[$row['month']] = $row['count'];
}
$registrationLabels = array_keys($monthlyRegistrations);
$registrationCounts = array_values($monthlyRegistrations);

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
            padding: 0px;
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
            width: 100%;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 0px;
            margin-left: 5px;
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

        .pending-approval {
            background: #fff3cd; /* Soft yellow */
        }

        .pending-approval .badge {
            background: red;
            color: #fff;
            border-radius: 50%;
            padding: 5px 10px;
            font-weight: bold;
        }

        .charts-row {
            display: flex;
            justify-content: space-between;
        }

        .chart-container {
            width: 48%;
            margin-bottom: 10px;
        }

        canvas {
            max-height: 400px;
            height: 400px;
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

        h4 {
            margin-top: 10px;
            color: #333;
            padding: 10px;
            background-color: #e9ecef; /* Light gray background */
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        h3, {
            margin-top: 30px;
            color: #333;
            padding: 10px;
            background-color: #e9ecef; /* Light gray background */
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design Improvements */
        @media (max-width: 768px) {
            .totals {
                flex-direction: column;
                margin-bottom: 10px;
            }

            .total-box {
                margin-bottom: 20px;
            }

            .charts-row {
                flex-direction: column;
            }

            .chart-container {
                width: 100%;
            }
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
                    <p><?php echo $totalPending; ?> </p>
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

            <div class="charts-row">
                <div class="chart-container">
                    <h4>Membership Types</h4>
                    <canvas id="membershipChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4>Monthly Registrations</h4>
                    <canvas id="monthlyRegistrationChart"></canvas>
                </div>
            </div>
            <div class="charts-row">
                <div class="chart-container">
                    <h4>Gender Distribution</h4>
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4>Age Distribution</h4>
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gender Distribution Chart
        const genderData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [<?php echo $totalMale; ?>, <?php echo $totalFemale; ?>],
                backgroundColor: ['#FF6384', '#36A2EB'],
            }]
        };
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: genderData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let percentage = ((context.raw / <?php echo $totalFiles; ?>) * 100).toFixed(2);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Membership Types Chart
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

        // Age Distribution Chart
        const ageData = {
            labels: <?php echo json_encode($ageLabels); ?>,
            datasets: [{
                label: 'Age Distribution',
                data: <?php echo json_encode($ageCounts); ?>,
                backgroundColor: '#36A2EB',
            }]
        };
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

        // Monthly Registration Chart
        const registrationData = {
            labels: <?php echo json_encode($registrationLabels); ?>,
            datasets: [{
                label: 'Monthly Registrations',
                data: <?php echo json_encode($registrationCounts); ?>,
                backgroundColor: '#FFCE56',
                borderColor: '#FF6384',
                fill: false,
                tension: 0.1
            }]
        };
        const registrationCtx = document.getElementById('monthlyRegistrationChart').getContext('2d');
        const monthlyRegistrationChart = new Chart(registrationCtx, {
            type: 'line',
            data: registrationData,
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
