<?php
include 'db_connect.php';

// Get the member ID from the URL
$id = $_GET['id'];

// SQL to delete the member
$sql = "DELETE FROM files WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<p>Member deleted successfully!</p>";
    header('Location: members.php'); // Redirect back to members page
} else {
    echo "Error deleting member: " . $conn->error;
}

$conn->close();
?>