<?php
// Database connection
include 'db_connect.php';

// Fetch pending members
$query = "SELECT * FROM files WHERE status = 'Pending'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Members</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .approve-btn, .reject-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }

        .approve-btn {
            background-color: #28a745;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn {
            background-color: #dc3545; /* Red color */
        }

        .reject-btn:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        .download-btn {
            background-color: #ff9800;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .download-btn:hover {
            background-color: #e68900;
        }

        td:last-child {
            width: 150px;
        }
    </style>
</head>
<body>
    <div>
        <h1>Pending Members</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Membership Type</th>
                <th>Action</th>
                <th>Receipt</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['membership']); ?></td>
                <td>
                    <button class="approve-btn" data-id="<?php echo $row['id']; ?>">Approve</button>
                    <button class="reject-btn" data-id="<?php echo $row['id']; ?>">Reject</button>
                </td>
                <td>
                    <?php if (!empty($row['file_path'])): ?>
                        <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="download-btn" download>Download Receipt</a>
                    <?php else: ?>
                        No Receipt
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('.approve-btn').click(function() {
                var memberId = $(this).data('id');
                $.ajax({
                    url: 'approve_member_ajax.php',
                    type: 'POST',
                    data: { id: memberId },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            });

            $('.reject-btn').click(function() {
                var memberId = $(this).data('id');
                $.ajax({
                    url: 'reject_member_ajax.php',
                    type: 'POST',
                    data: { id: memberId },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>
