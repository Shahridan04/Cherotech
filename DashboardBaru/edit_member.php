<?php
include 'db_connect.php';

// Get member ID from the URL
$id = $_GET['id'];

// Fetch member data based on ID
$sql = "SELECT * FROM members WHERE id = $id";
$result = $conn->query($sql);
$member = $result->fetch_assoc();

// Handle the form submission to update the member's details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $membership_type = $_POST['membership_type'];

    // Update query
    $sql_update = "UPDATE members 
                   SET name='$name', email='$email', phone_number='$phone_number', gender='$gender', age=$age, membership_type='$membership_type' 
                   WHERE id=$id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<p style='color:green;'>Member updated successfully!</p>";
        // Redirect to members page after successful update
        header('Location: dashboard.php?page=members'); 
        exit();
    } else {
        echo "<p style='color:red;'>Error updating member: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<h1>Edit Member</h1>

<form method="POST" action="">
    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo $member['name']; ?>" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $member['email']; ?>" required><br><br>

    <label for="phone_number">Phone Number:</label>
    <input type="text" name="phone_number" value="<?php echo $member['phone_number']; ?>" required><br><br>

    <label for="gender">Gender:</label>
    <select name="gender">
        <option value="Male" <?php if ($member['gender'] == 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if ($member['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if ($member['gender'] == 'Other') echo 'selected'; ?>>Other</option>
    </select><br><br>

    <label for="age">Age:</label>
    <input type="number" name="age" value="<?php echo $member['age']; ?>" required><br><br>

    <label for="membership_type">Membership Type:</label>
    <select name="membership_type">
        <option value="Monthly" <?php if ($member['membership_type'] == 'Monthly') echo 'selected'; ?>>Monthly</option>
        <option value="3-Month" <?php if ($member['membership_type'] == '3-Month') echo 'selected'; ?>>3-Month</option>
        <option value="Yearly" <?php if ($member['membership_type'] == 'Yearly') echo 'selected'; ?>>Yearly</option>
    </select><br><br>

    <button type="submit">Update Member</button>
</form>

<br>
<!-- Back button to go to the Members list -->
<a href="dashboard.php?page=members" style="text-decoration:none;">
    <button>Back to Members</button>
</a>
