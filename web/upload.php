<?php
// Check if file was uploaded without errors
if(isset($_FILES["mp3File"]) && $_FILES["mp3File"]["error"] == 0){
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["mp3File"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check file size (optional)
    if ($_FILES["mp3File"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow only MP3 files
    if($imageFileType != "mp3") {
        echo "Sorry, only MP3 files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["mp3File"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["mp3File"]["name"]). " has been uploaded.";
            // Store file path in MySQL database
            $filePath = $target_file;
            
            // Database connection parameters
            $servername = "127.0.0.1";
            $username = "root";
            $password = "hyn";
            $dbname = "uploaded_files";
            
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Prepare and execute SQL query to insert file path into database
            $stmt = $conn->prepare("INSERT INTO uploaded_files (file_name, file_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $fileName, $filePath);
            
            // Get file name
            $fileName = basename($_FILES["mp3File"]["name"]);
            
            // Execute query
            if ($stmt->execute() === TRUE) {
                echo "File path stored in database.";
            } else {
                echo "Error: " . $stmt->error;
            }
            
            // Close statement and connection
            $stmt->close();
            $conn->close();
            
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "No file uploaded.";
}
?>
