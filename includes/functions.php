<?php
function getAllProfiles($conn, $limit, $offset) {
    $sql = "SELECT user_profiles.*, qualifications.Education 
            FROM user_profiles 
            LEFT JOIN qualifications ON user_profiles.Code = qualifications.User_Code 
            LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    return $result;
}

function getFilteredProfiles($conn, $state, $district, $education, $limit, $offset) {
    $sql = "SELECT user_profiles.*, qualifications.Education FROM user_profiles 
            LEFT JOIN qualifications ON user_profiles.Code = qualifications.User_Code 
            WHERE 1=1";

    if (!empty($state)) {
        $sql .= " AND State='$state'";
    }
    if (!empty($district)) {
        $sql .= " AND District='$district'";
    }
    if (!empty($education)) {
        $sql .= " AND qualifications.Education='$education'";
    }

    $sql .= " LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    return $result;
}

function getStates($conn) {
    $sql = "SELECT DISTINCT State FROM user_profiles";
    return $conn->query($sql);
}

function getDistricts($conn) {
    $sql = "SELECT DISTINCT District FROM user_profiles";
    return $conn->query($sql);
}

function getEducations($conn) {
    $sql = "SELECT DISTINCT Education FROM qualifications";
    return $conn->query($sql);
}

function getProfileCount($conn, $state, $district, $education) {
    $sql = "SELECT COUNT(*) as count FROM user_profiles 
            LEFT JOIN qualifications ON user_profiles.Code = qualifications.User_Code 
            WHERE 1=1";

    if (!empty($state)) {
        $sql .= " AND State='$state'";
    }
    if (!empty($district)) {
        $sql .= " AND District='$district'";
    }
    if (!empty($education)) {
        $sql .= " AND qualifications.Education='$education'";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}
?>
