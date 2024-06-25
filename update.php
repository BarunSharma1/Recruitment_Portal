<?php
include('includes/config.php');
include('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $education = $_POST['education'];
    $date_of_contact = $_POST['date_of_contact'];
    $remark = $_POST['remark'];

    $sql = "UPDATE user_profiles SET 
            Name='$name', Email='$email', Mobile='$mobile', Gender='$gender', 
            Age=$age, State='$state', District='$district', Date_of_Contact='$date_of_contact', Remark='$remark'
            WHERE Code='$code'";
    $conn->query($sql);

    $sql = "UPDATE qualifications SET Education='$education' WHERE User_Code='$code'";
    $conn->query($sql);

    header("Location: index.php");
}

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
    <title>Update Profile</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h1>Update Profile</h1>
            </div>
            <div class="card-body">
                <form method="post" action="update.php">
                    <input type="hidden" name="code" value="<?php echo $profile['Code']; ?>">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo $profile['Name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo $profile['Email']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile:</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $profile['Mobile']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <input type="text" name="gender" id="gender" class="form-control" value="<?php echo $profile['Gender']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" name="age" id="age" class="form-control" value="<?php echo $profile['Age']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="state">State:</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?php echo $profile['State']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="district">District:</label>
                        <input type="text" name="district" id="district" class="form-control" value="<?php echo $profile['District']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="education">Education:</label>
                        <input type="text" name="education" id="education" class="form-control" value="<?php echo $profile['Education']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_of_contact">Date of Contact:</label>
                        <input type="date" name="date_of_contact" id="date_of_contact" class="form-control" value="<?php echo $profile['Date_of_Contact']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark:</label>
                        <textarea name="remark" id="remark" class="form-control"><?php echo $profile['Remark']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
                <a href="index.php" class="btn btn-primary mt-3">Back to List</a>
            </div>
        </div>
    </div>
</body>
</html>
