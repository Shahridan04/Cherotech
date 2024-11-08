<?php
// Database connection
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Update the member's status to approved
    $query = "UPDATE files SET status = 'Approved' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Member approved successfully!";
    } else {
        echo "Error approving member.";
    }
    mysqli_stmt_close($stmt);
}
?>
