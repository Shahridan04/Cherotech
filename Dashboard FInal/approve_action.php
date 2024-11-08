<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Approve member by updating the status
    $sql = "UPDATE files SET status = 'approved' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Member approved successfully!";
        header("Location: approve_members.php"); // Redirect back to the approval page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
