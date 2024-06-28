<?php
include('config.php');
include('functions.php');

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    // Database connection
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    require 'vendor/autoload.php'; // Ensure you have PhpSpreadsheet autoloaded

    $fileName = $_FILES['file']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileName);
    $data = $spreadsheet->getActiveSheet()->toArray();

    // Loop through data and insert into the database
    foreach ($data as $row) {
        // Prepare SQL query to insert a row into user_profiles table
        $sql = "INSERT INTO user_data (
                    Code, Name, eKYC_Completed, Email, Mobile, Gender, Age, 
                    Beneficiary_ID_Status, State, District, Profile_Completion, Education
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }

        // Assuming columns align with Excel data order, adjust if needed
        mysqli_stmt_bind_param($stmt, 'ssssssisssss',
            $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], 
            $row[7], $row[8], $row[9], $row[10], $row[11]
        );

        mysqli_stmt_execute($stmt);

        // Insert into qualifications table
        $education = explode(',', $row[11]); // Assuming multiple qualifications are comma-separated in the 'Education' column
        foreach ($education as $qual) {
            $qual_sql = "INSERT INTO qualifications (User_Code, Education) VALUES (?, ?)";
            $qual_stmt = mysqli_prepare($conn, $qual_sql);
            if (!$qual_stmt) {
                die("Prepare failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($qual_stmt, 'ss', $row[0], trim($qual));
            mysqli_stmt_execute($qual_stmt);
        }
    }

    echo "Data imported successfully";

    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="upload.php">Upload Data</a>
            </li>
            <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register User</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header">Upload Data</div>
                <div class="card-body">
                    <form method="POST" action="upload.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="file">Upload Excel File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
