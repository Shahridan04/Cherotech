<?php
include 'db_connect.php';

// Fetch total number of users
$totalUsersQuery = "SELECT COUNT(*) AS total FROM members";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];

// Fetch total number of male users
$maleUsersQuery = "SELECT COUNT(*) AS total FROM members WHERE gender = 'Male'";
$maleUsersResult = $conn->query($maleUsersQuery);
$totalMales = $maleUsersResult->fetch_assoc()['total'];

// Fetch total number of female users
$femaleUsersQuery = "SELECT COUNT(*) AS total FROM members WHERE gender = 'Female'";
$femaleUsersResult = $conn->query($femaleUsersQuery);
$totalFemales = $femaleUsersResult->fetch_assoc()['total'];

// Fetch the most recent 5 users
$recentUsersQuery = "SELECT name, email, gender, membership_type FROM members ORDER BY id DESC LIMIT 5";
$recentUsersResult = $conn->query($recentUsersQuery);
?>

<h1>Dashboard Overview</h1>

<!-- Display Total Stats -->
<div class="stats">
    <h2>Total Users: <?php echo $totalUsers; ?></h2>
    <h2>Total Males: <?php echo $totalMales; ?></h2>
    <h2>Total Females: <?php echo $totalFemales; ?></h2>
</div>

<!-- Display Recent Users -->
<h3>Recent Users</h3>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Membership Type</th>
    </tr>
    <?php
    if ($recentUsersResult->num_rows > 0) {
        while ($row = $recentUsersResult->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["gender"] . "</td>
                    <td>" . $row["membership_type"] . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No recent users found.</td></tr>";
    }
    ?>
</table>

<?php
$conn->close();
?>
