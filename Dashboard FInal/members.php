<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_member'])) {
        // Update member details
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $membership_type = $_POST['membership_type'];

        // Update query
        $sql_update = "UPDATE files 
                       SET name=?, email=?, phone=?, gender=?, age=?, membership=? 
                       WHERE id=?";
        
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssisi", $name, $email, $phone_number, $gender, $age, $membership_type, $id);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Member updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error updating member: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        // Add new member
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $membership_type = $_POST['membership_type'];
        
        // File upload handling
        $file_path = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $upload_dir = 'uploads/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                $stmt = $conn->prepare("INSERT INTO files (name, email, phone, gender, age, membership, file_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt->bind_param("ssssisss", $name, $email, $phone_number, $gender, $age, $membership_type, $file_path);
                
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>New member added successfully!</p>";
                } else {
                    echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color:red;'>Error uploading file!</p>";
            }
        } else {
            echo "<p style='color:red;'>No file uploaded or there was an upload error.</p>";
        }
    }
}

// Fetch only approved members
$sql = "SELECT * FROM files WHERE status = 'Approved'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure this path is correct -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            overflow: auto;
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
            overflow-y: auto;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #218838;
        }
        button[onclick*="confirm"] {
            background-color: #dc3545;
            color: white;
        }
        button[onclick*="confirm"]:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
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
    </style>
</head>
<body>
    <h1>Members Management</h1>

    <button id="addMemberBtn">Add New Member</button>

    <!-- The Modal for Adding Member -->
    <div id="addMemberModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddMemberModal()">&times;</span>
            <h2>Add New Member</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" name="name" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" required>

                <label for="gender">Gender:</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="age">Age:</label>
                <input type="number" name="age" required>

                <label for="membership_type">Membership Type:</label>
                <select name="membership_type">
                    <option value="Monthly">Monthly</option>
                    <option value="3-Month">3-Month</option>
                    <option value="Yearly">Yearly</option>
                </select>

                <label for="file">Upload Document:</label>
                <input type="file" name="file" required>

                <button type="submit">Add Member</button>
                <button type="reset" class="reset-button">Reset</button>
            </form>
        </div>
    </div>

    <!-- The Modal for Editing Member -->
    <div id="editMemberModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditMemberModal()">&times;</span>
            <h2>Edit Member</h2>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="id" id="editId">
                <label for="editName">Name:</label>
                <input type="text" name="name" id="editName" required>

                <label for="editEmail">Email:</label>
                <input type="email" name="email" id="editEmail" required>

                <label for="editPhone">Phone Number:</label>
                <input type="text" name="phone_number" id="editPhone" required>

                <label for="editGender">Gender:</label>
                <select name="gender" id="editGender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="editAge">Age:</label>
                <input type="number" name="age" id="editAge" required>

                <label for="editMembership">Membership Type:</label>
                <select name="membership_type" id="editMembership">
                    <option value="Monthly">Monthly</option>
                    <option value="3-Month">3-Month</option>
                    <option value="Yearly">Yearly</option>
                </select>

                <button type="submit" name="edit_member">Update Member</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Membership</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['membership']); ?></td>
                <td>
                    <button onclick="openEditMemberModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <button onclick="return confirm('Are you sure?');" style="background-color: #dc3545; color: white;">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        // JavaScript to handle modal opening and closing
        document.getElementById("addMemberBtn").onclick = function() {
            document.getElementById("addMemberModal").style.display = "block";
        }

        function closeAddMemberModal() {
            document.getElementById("addMemberModal").style.display = "none";
        }

        function openEditMemberModal(rowData) {
            document.getElementById("editId").value = rowData.id;
            document.getElementById("editName").value = rowData.name;
            document.getElementById("editEmail").value = rowData.email;
            document.getElementById("editPhone").value = rowData.phone;
            document.getElementById("editGender").value = rowData.gender;
            document.getElementById("editAge").value = rowData.age;
            document.getElementById("editMembership").value = rowData.membership;
            document.getElementById("editMemberModal").style.display = "block";
        }

        function closeEditMemberModal() {
            document.getElementById("editMemberModal").style.display = "none";
        }

        // Close modal if clicked outside of content
        window.onclick = function(event) {
            if (event.target == document.getElementById("addMemberModal")) {
                closeAddMemberModal();
            }
            if (event.target == document.getElementById("editMemberModal")) {
                closeEditMemberModal();
            }
        }
    </script>
</body>
</html>
