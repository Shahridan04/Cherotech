<?php
include 'db_connect.php';

// Fetch pending members for approval
$pendingQuery = "SELECT * FROM members WHERE status = 'Pending'";
$pendingResult = $conn->query($pendingQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Members</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Approve Members</h1>

    <div id="pendingMembers">
        <?php if ($pendingResult->num_rows > 0): ?>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Membership Type</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $pendingResult->fetch_assoc()): ?>
                    <tr id="member-<?php echo $row['id']; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone_number']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['age']; ?></td>
                        <td><?php echo $row['membership_type']; ?></td>
                        <td>
                            <button onclick="approveMember(<?php echo $row['id']; ?>)">Approve</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No pending members for approval.</p>
        <?php endif; ?>
    </div>

    <script>
        function approveMember(memberId) {
            $.ajax({
                url: 'approve_member_ajax.php', // PHP file to handle the approval
                type: 'POST',
                data: { id: memberId },
                success: function(response) {
                    // On success, remove the row of the approved member
                    $('#member-' + memberId).remove();

                    // Check if there are any members left
                    if ($('#pendingMembers table tr').length == 1) {
                        $('#pendingMembers').html('<p>No pending members for approval.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        }
    </script>
</body>
</html>
