<?php
include('includes/config.php');
include('includes/functions.php');

$code = $_GET['code'];
$sql = "SELECT user_profiles.*, qualifications.Education FROM user_profiles 
        LEFT JOIN qualifications ON user_profiles.Code = qualifications.User_Code 
        WHERE Code='$code'";
$result = $conn->query($sql);
$profile = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>View Profile</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1>View Profile</h1>
            </div>
            <div class="card-body">
                <p><strong>Code:</strong> <?php echo $profile['Code']; ?></p>
                <p><strong>Name:</strong> <?php echo $profile['Name']; ?></p>
                <p><strong>Email:</strong> <?php echo $profile['Email']; ?></p>
                <p><strong>Mobile:</strong> <?php echo $profile['Mobile']; ?></p>
                <p><strong>Gender:</strong> <?php echo $profile['Gender']; ?></p>
                <p><strong>Age:</strong> <?php echo $profile['Age']; ?></p>
                <p><strong>State:</strong> <?php echo $profile['State']; ?></p>
                <p><strong>District:</strong> <?php echo $profile['District']; ?></p>
                <p><strong>Education:</strong> <?php echo $profile['Education']; ?></p>
                <p><strong>Date of Contact:</strong> <?php echo $profile['Date_of_Contact']; ?></p>
                <p><strong>Remark:</strong> <?php echo $profile['Remark']; ?></p>
                <a href="index.php" class="btn btn-primary">Back to List</a>
            </div>
        </div>
    </div>
</body>
</html>
