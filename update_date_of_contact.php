<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $date_of_contact = $_POST['date_of_contact'];

    $sql = "UPDATE user_profiles SET Date_of_Contact='$date_of_contact' WHERE Code='$code'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
