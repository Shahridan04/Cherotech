<?php
include 'db_connect.php';

// Fetch data for gender distribution
$genderQuery = "SELECT gender, COUNT(*) as count FROM members GROUP BY gender";
$genderResult = $conn->query($genderQuery);

$genders = [];
$genderCounts = [];

while ($row = $genderResult->fetch_assoc()) {
    $genders[] = $row['gender'];
    $genderCounts[] = $row['count'];
}

// Fetch data for membership types
$membershipQuery = "SELECT membership_type, COUNT(*) as count FROM members GROUP BY membership_type";
$membershipResult = $conn->query($membershipQuery);

$membershipTypes = [];
$membershipCounts = [];

while ($row = $membershipResult->fetch_assoc()) {
    $membershipTypes[] = $row['membership_type'];
    $membershipCounts[] = $row['count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .chart-container {
            width: 80%; 
            margin: auto;
            margin-bottom: 20px;
        }
        canvas {
            max-height: 300px; /* Limit height of the charts */
        }
    </style>
</head>
<body>
    <h1>Analytics/Reports</h1>

    <div class="chart-container">
        <!-- Pie Chart for Gender Distribution -->
        <h3>Gender Distribution</h3>
        <canvas id="genderChart"></canvas>
    </div>

    <div class="chart-container">
        <!-- Bar Chart for Membership Types -->
        <h3>Membership Types</h3>
        <canvas id="membershipChart"></canvas>
    </div>

    <script>
        // Data for Gender Distribution Chart
        const genderData = {
            labels: <?php echo json_encode($genders); ?>,
            datasets: [{
                label: 'Gender Distribution',
                data: <?php echo json_encode($genderCounts); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
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
            labels: <?php echo json_encode($membershipTypes); ?>,
            datasets: [{
                label: 'Membership Types',
                data: <?php echo json_encode($membershipCounts); ?>,
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
    </script>
</body>
</html>
