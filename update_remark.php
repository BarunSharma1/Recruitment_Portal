<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $remark = $_POST['remark'];

    $sql = "UPDATE user_profiles SET Remark='$remark' WHERE Code='$code'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
