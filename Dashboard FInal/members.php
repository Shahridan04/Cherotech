<?php
// Include database connection
include 'db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $membership = isset($_POST['membership']) ? $_POST['membership'] : null;
    
    if ($id) {
        // Update existing member
        $sql_update = "UPDATE files 
                       SET name=?, email=?, phone=?, gender=?, age=?, membership=?
                       WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        // Bind parameters for the update query
        $stmt->bind_param("ssssisi", $name, $email, $phone_number, $gender, $age, $membership, $id);
    } else {
        // Add new member
        // File upload handling
        $file_path = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $upload_dir = 'uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique file name and set file path
            $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
            $file_path = $upload_dir . $file_name;
            
            // Move uploaded file to destination
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                // File uploaded successfully
                $stmt = $conn->prepare("INSERT INTO files (name, email, phone, gender, age, membership, file_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
                // Bind parameters for the insert query
                $stmt->bind_param("ssssisss", $name, $email, $phone_number, $gender, $age, $membership, $file_path);
            } else {
                // Error handling for file upload
                echo "<p style='color:red;'>Error uploading file!</p>";
            }
        } else {
            // No file uploaded or upload error
            echo "<p style='color:red;'>No file uploaded or there was an upload error.</p>";
        }
    }
    
    // Execute the statement and display success or error message
    if ($stmt && $stmt->execute()) {
        echo "<p style='color:green;'>Member saved successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
    // Close the statement if it was created
    if ($stmt) {
        $stmt->close();
    }
}

// Fetch only approved members from the database
$sql = "SELECT * FROM files WHERE status = 'Approved'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management</title>
    <link rel="stylesheet" href="style admin.css"> <!-- Ensure this path is correct -->
    <style>
        /* Styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            overflow: auto; /* Allow body to scroll normally */
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        /* Modal styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
            overflow-y: auto; /* Allow scrolling if needed */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* Centered */
            padding: 20px;
            border: 1px solid #888;
            width: 90%; /* Use 90% of the screen width */
            max-width: 600px; /* Limit max width */
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

        /* Form input styling */
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

        input[type="file"] {
            margin-bottom: 20px; /* Added margin for spacing */
        }

        /* Button styling */
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px; /* Added margin for spacing */
        }

        button:hover {
            background-color: #218838;
        }

        #addMemberBtn {
            padding: 5px 10px;
            font-size: 14px;
        }

        .reset-button {
            background-color: #ffc107; /* Yellow color for reset button */
        }

        .reset-button:hover {
            background-color: #e0a800; /* Darker yellow on hover */
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff; /* Ensure table has a white background */
            border-radius: 8px;
            overflow: hidden; /* For rounded corners */
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

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Link button styling */
        .file-link,
        .edit-link,
        .delete-link {
            display: inline-block;
            padding: 5px 10px;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
        }

        .file-link {
            background-color: #28a745;
        }

        .edit-link {
            background-color: #007bff;
        }

        .delete-link {
            background-color: #dc3545;
        }

        #searchBar {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .modal-content {
                width: 90%;
            }

            input[type="text"],
            input[type="email"],
            input[type="number"],
            select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h1>Members Management</h1>

    <!-- Search bar to filter members -->
    <input type="text" id="searchBar" onkeyup="searchMember()" placeholder="Search for members...">
    <button id="addMemberBtn">Add New Member</button>

    <!-- The Modal for adding/editing members -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Add New Member</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" id="memberId">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" required>

                <label for="gender">Gender:</label>
                <select name="gender" id="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="age">Age:</label>
                <input type="number" name="age" id="age" required>

                <label for="membership_type">Membership Type:</label>
                <select name="membership" id="membership">
                    <option value="Monthly">Monthly</option>
                    <option value="3-Month">3-Month</option>
                    <option value="Yearly">Yearly</option>
                </select>

                <div id="fileUploadSection">
                    <label for="file">Upload Document:</label>
                    <input type="file" name="file" id="file">
                </div>

                <button type="submit">Save Member</button>
                <button type="reset" class="reset-button">Reset</button>
            </form>
        </div>
    </div>

    <h2>Approved Members List</h2>
    <?php if ($result->num_rows > 0): ?>
        <table id="membersTable">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Membership Type</th>
                <th>Document</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["name"]; ?></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $row["phone"]; ?></td>
                    <td><?php echo $row["gender"]; ?></td>
                    <td><?php echo $row["age"]; ?></td>
                    <td><?php echo $row["membership"]; ?></td>
                    <td>
                        <?php if ($row["file_path"]): ?>
                            <a href="download.php?id=<?php echo $row["id"]; ?>" class="file-link">Download Receipt</a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Edit and delete actions for each member -->
                        <a href='#' class='edit-link' onclick="editMember(<?php echo htmlspecialchars(json_encode($row)); ?>); return false;">Edit</a>
                        <a href='delete_member.php?id=<?php echo $row["id"]; ?>' class='delete-link' onclick="return deleteMember(<?php echo $row["id"]; ?>);">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No approved members found.</p>
    <?php endif; ?>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("addMemberBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            openAddMemberModal();
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Function to open modal for adding a new member
        function openAddMemberModal() {
            document.getElementById("modalTitle").innerText = "Add New Member";
            document.getElementById("memberId").value = "";
            document.getElementById("name").value = "";
            document.getElementById("email").value = "";
            document.getElementById("phone_number").value = "";
            document.getElementById("gender").value = "Male";
            document.getElementById("age").value = "";
            document.getElementById("membership").value = "Monthly";
            document.getElementById("fileUploadSection").style.display = "block";
            modal.style.display = "block";
        }

        // Function to open modal for editing an existing member
        function editMember(member) {
            document.getElementById("modalTitle").innerText = "Edit Member";
            document.getElementById("memberId").value = member.id;
            document.getElementById("name").value = member.name;
            document.getElementById("email").value = member.email;
            document.getElementById("phone_number").value = member.phone;
            document.getElementById("gender").value = member.gender;
            document.getElementById("age").value = member.age;
            document.getElementById("membership").value = member.membership;
            document.getElementById("fileUploadSection").style.display = "none";
            modal.style.display = "block";
        }

        // Search member function to filter table rows
        function searchMember() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchBar");
            filter = input.value.toUpperCase();
            table = document.getElementById("membersTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those that don't match the search query
            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }

        // Delete member function without reloading the page
        function deleteMember(id) {
            if (confirm('Are you sure?')) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "delete_member.php?id=" + id, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        location.reload();
                    }
                };
                xhr.send();
            }
            return false;
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
