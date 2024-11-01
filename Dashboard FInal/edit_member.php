<?php
include 'db_connect.php';

// Get member ID from the URL
$id = $_GET['id'];

// Fetch member data based on ID
$sql = "SELECT * FROM files WHERE id = $id";
$result = $conn->query($sql);
$member = $result->fetch_assoc();

// Handle the form submission to update the member's details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $membership_type = $_POST['membership_type'];

    // Update query
    $sql_update = "UPDATE files 
                   SET name='$name', email='$email', phone='$phone_number', gender='$gender', age=$age, membership='$membership_type' 
                   WHERE id=$id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<p style='color:green;'>Member updated successfully!</p>";
        // Redirect to members page after successful update
        header('Location: members.php'); 
        exit();
    } else {
        echo "<p style='color:red;'>Error updating member: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #e68900;
        }

        a {
            text-decoration: none;
        }

        a button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Member</h1>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" value="<?php echo $member['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo $member['email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone" value="<?php echo $member['phone']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender">
                    <option value="Male" <?php if ($member['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($member['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($member['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" name="age" value="<?php echo $member['age']; ?>" required>
            </div>

            <div class="form-group">
                <label for="membership_type">Membership Type:</label>
                <select name="membership_type">
                    <option value="Monthly" <?php if ($member['membership'] == 'Monthly') echo 'selected'; ?>>Monthly</option>
                    <option value="3-Month" <?php if ($member['membership'] == '3-Month') echo 'selected'; ?>>3-Month</option>
                    <option value="Yearly" <?php if ($member['membership'] == 'Yearly') echo 'selected'; ?>>Yearly</option>
                </select>
            </div>

            <button type="submit">Update Member</button>
        </form>

        <br>
        <!-- Back button to go to the Members list -->
        <a href="members.php" style="text-decoration:none;">
            <button>Back to Members</button>
        </a>
    </div>
</body>
</html>