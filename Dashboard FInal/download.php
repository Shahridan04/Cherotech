<?php
include 'db_connect.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get file information from database
    $sql = "SELECT file_path, name FROM files WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        $file_path = $row['file_path'];
        
        if(file_exists($file_path)) {
            // Set headers for file download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            
            // Output file contents
            readfile($file_path);
            exit;
        }
    }
}

// If file not found or error occurs
echo "File not found!";
?>