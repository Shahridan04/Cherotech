<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form submission to add a member
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $membership_type = $_POST['membership_type'];

    // Insert the new member with status set to 'approved'
    $sql = "INSERT INTO members (name, email, phone_number, gender, age, membership_type, status) 
            VALUES ('$name', '$email', '$phone_number', '$gender', $age, '$membership_type', 'approved')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>New member added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<h1>Members Management</h1>

<!-- Add New Member Form -->
<h2>Add New Member</h2>
<form method="POST" action="">
    <label for="name">Name:</label>
    <input type="text" name="name" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <label for="phone_number">Phone Number:</label>
    <input type="text" name="phone_number" required><br><br>

    <label for="gender">Gender:</label>
    <select name="gender">
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select><br><br>

    <label for="age">Age:</label>
    <input type="number" name="age" required><br><br>

    <label for="membership_type">Membership Type:</label>
    <select name="membership_type">
        <option value="Monthly">Monthly</option>
        <option value="3-Month">3-Month</option>
        <option value="Yearly">Yearly</option>
    </select><br><br>

    <button type="submit">Add Member</button>
</form>

<hr>

<!-- List of Approved Members -->
<h2>Approved Members</h2>
<?php
$sql = "SELECT * FROM members WHERE status = 'approved'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Membership Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . $row["name"] . "</td>
                <td>" . $row["email"] . "</td>
                <td>" . $row["phone_number"] . "</td>
                <td>" . $row["gender"] . "</td>
                <td>" . $row["age"] . "</td>
                <td>" . $row["membership_type"] . "</td>
                <td>" . $row["status"] . "</td>
                <td>
                    <a href='edit_member.php?id=" . $row["id"] . "'>Edit</a> | 
                    <a href='delete_member.php?id=" . $row["id"] . "' onclick=\"return confirm('Are you sure you want to delete this member?');\">Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No approved members found.</p>";
}

?>

<hr>

<!-- List of Pending Members -->
<h2>Pending Members</h2>
<?php
// No need to close the connection before this part
$sql_pending = "SELECT * FROM members WHERE status = 'pending'";
$result_pending = $conn->query($sql_pending);

if ($result_pending->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Membership Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>";
    while($row_pending = $result_pending->fetch_assoc()) {
        echo "<tr>
                <td>" . $row_pending["id"] . "</td>
                <td>" . $row_pending["name"] . "</td>
                <td>" . $row_pending["email"] . "</td>
                <td>" . $row_pending["phone_number"] . "</td>
                <td>" . $row_pending["gender"] . "</td>
                <td>" . $row_pending["age"] . "</td>
                <td>" . $row_pending["membership_type"] . "</td>
                <td>" . $row_pending["status"] . "</td>
                <td>
                    <a href='approve_member.php?id=" . $row_pending["id"] . "'>Approve</a> | 
                    <a href='delete_member.php?id=" . $row_pending["id"] . "' onclick=\"return confirm('Are you sure you want to delete this member?');\">Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pending members found.</p>";
}

// Close the connection at the end
$conn->close();
?>
