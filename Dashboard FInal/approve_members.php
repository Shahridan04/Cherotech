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
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 0 auto; /* Center the table */
            background-color: #fff; /* White background for the table */
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
            background-color: #f9f9f9; /* Highlight row on hover */
        }

        .approve-btn {
            background-color: #28a745; /* Green color */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .approve-btn:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .download-btn {
            background-color: #ff9800; /* Orange color */
            color: white;
            padding: 5px 15px; /* Increased padding for a better look */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; /* Remove underline from link */
            display: inline-block; /* Ensure it behaves like a button */
        }

        .download-btn:hover {
            background-color: #e68900; /* Darker orange on hover */
        }

        /* Adjust column width */
        td:last-child {
            width: 150px; /* Set a fixed width for the receipt column */
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
                        location.reload(); // Refresh the page to see updated status
                    }
                });
            });
        });
    </script>
</body>
</html>
