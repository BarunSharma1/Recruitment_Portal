<?php
include('includes/config.php');

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $sql = "DELETE FROM user_profiles WHERE Code='$code'";
    $conn->query($sql);

    $sql = "DELETE FROM qualifications WHERE User_Code='$code'";
    $conn->query($sql);

    header("Location: index.php");
    exit;
}
?>
