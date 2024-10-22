<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memberId = $_POST['id'];

    // Update the member's status to "Approved"
    $approveQuery = "UPDATE members SET status = 'Approved' WHERE id = $memberId";

    if ($conn->query($approveQuery) === TRUE) {
        echo "Member approved successfully!";
    } else {
        http_response_code(500); // Send error code if something goes wrong
        echo "Error: " . $conn->error;
    }
}
?>
