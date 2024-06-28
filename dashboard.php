<?php
include('config.php');
include('functions.php');

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Pagination variables
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10; // Default rows per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number

// Fetch distinct states for filter
$states = [];
$statesQuery = "SELECT DISTINCT State FROM user_data WHERE State IS NOT NULL";
$statesResult = $conn->query($statesQuery);
while ($row = $statesResult->fetch_assoc()) {
    $states[] = $row['State'];
}

// Fetch distinct districts for filter
$districts = [];
$districtsQuery = "SELECT DISTINCT District FROM user_data WHERE District IS NOT NULL";
$districtsResult = $conn->query($districtsQuery);
while ($row = $districtsResult->fetch_assoc()) {
    $districts[] = $row['District'];
}

// Fetch distinct education values for filter
$educations = [];
$educationsQuery = "SELECT DISTINCT Education FROM user_data WHERE Education IS NOT NULL";
$educationsResult = $conn->query($educationsQuery);
while ($row = $educationsResult->fetch_assoc()) {
    $educations[] = $row['Education'];
}

// Build filter query based on selected filters
$filterQuery = "SELECT * FROM user_data WHERE 1=1";

if (isset($_GET['state']) && $_GET['state'] != '') {
    $selectedState = $conn->real_escape_string($_GET['state']);
    $filterQuery .= " AND State = '$selectedState'";
}

if (isset($_GET['district']) && $_GET['district'] != '') {
    $selectedDistrict = $conn->real_escape_string($_GET['district']);
    $filterQuery .= " AND District = '$selectedDistrict'";
}

if (isset($_GET['education']) && $_GET['education'] != '') {
    $selectedEducation = $conn->real_escape_string($_GET['education']);
    $filterQuery .= " AND Education = '$selectedEducation'";
}

// Fetch total number of rows based on filters
$totalRowsQuery = "SELECT COUNT(*) AS total FROM ($filterQuery) AS filtered";
$totalRowsResult = $conn->query($totalRowsQuery);
$totalRows = $totalRowsResult->fetch_assoc()['total'];

// Calculate total number of pages
$totalPages = ceil($totalRows / $rowsPerPage);

// Calculate starting point for fetching rows
$start = ($current_page - 1) * $rowsPerPage;

// Fetch data for the current page
$sql = "$filterQuery LIMIT $start, $rowsPerPage";
$result = $conn->query($sql);

// Check if there are any rows returned
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as associative array
} else {
    $rows = []; // Initialize an empty array if no rows are returned
}

// Fetch column names from the user_data table
$columns = [];
$columnsQuery = "SHOW COLUMNS FROM user_data";
$columnsResult = $conn->query($columnsQuery);
while ($row = $columnsResult->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Handle form submission to update database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $ids = $_POST['id'];
    foreach ($ids as $index => $id) {
        // Initialize variables for each field
        $recruiterName = $_POST['Recruiter_Name'][$index] ?? 'NA';
        $address = $_POST['Address'][$index] ?? 'NA';
        $pincode = $_POST['Pincode'][$index] ?? 'NA';
        $dateOfBirth = $_POST['Date_of_Birth'][$index] ?? 'NA';
        $position = $_POST['Position'][$index] ?? 'NA';
        $communicationDate = $_POST['Communication_Date'][$index] ?? 'NA';
        $jobInterest = $_POST['Job_Interest'][$index] ?? 'NA';
        $clientName = $_POST['Client_Name'][$index] ?? 'NA';
        $jobRoleConsidered = $_POST['Job_Role_Considered'][$index] ?? 'NA';
        $interviewDate = $_POST['Interview_Date'][$index] ?? 'NA';
        $status = $_POST['Status'][$index] ?? 'NA';
        $dateOfContact = $_POST['Date_of_Contact'][$index] ?? 'NA';
        $remark = $_POST['Remark'][$index] ?? 'NA';
        $tellecallerName = $_POST['Tellecaller_Name'][$index] ?? 'NA';
        $location = $_POST['Location'][$index] ?? 'NA';
        $tat = $_POST['TAT'][$index] ?? 'NA';
        $clientNameModified = $_POST['Client_Name_Modified'][$index] ?? 'NA';
        $clientLocation = $_POST['Client_Location'][$index] ?? 'NA';
        $clientCode = $_POST['Client_Code'][$index] ?? 'NA';

        // Build SQL update query
        $updateQuery = "UPDATE user_data SET 
            Recruiter_Name = '" . $conn->real_escape_string($recruiterName) . "',
            Address = '" . $conn->real_escape_string($address) . "',
            Pincode = '" . $conn->real_escape_string($pincode) . "',
            Date_of_Birth = '" . $conn->real_escape_string($dateOfBirth) . "',
            Position = '" . $conn->real_escape_string($position) . "',
            Communication_Date = '" . $conn->real_escape_string($communicationDate) . "',
            Job_Interest = '" . $conn->real_escape_string($jobInterest) . "',
            Client_Name = '" . $conn->real_escape_string($clientName) . "',
            Job_Role_Considered = '" . $conn->real_escape_string($jobRoleConsidered) . "',
            Interview_Date = '" . $conn->real_escape_string($interviewDate) . "',
            Status = '" . $conn->real_escape_string($status) . "',
            Date_of_Contact = '" . $conn->real_escape_string($dateOfContact) . "',
            Remark = '" . $conn->real_escape_string($remark) . "',
            Tellecaller_Name = '" . $conn->real_escape_string($tellecallerName) . "',
            Location = '" . $conn->real_escape_string($location) . "',
            TAT = '" . $conn->real_escape_string($tat) . "',
            Client_Name_Modified = '" . $conn->real_escape_string($clientNameModified) . "',
            Client_Location = '" . $conn->real_escape_string($clientLocation) . "',
            Client_Code = '" . $conn->real_escape_string($clientCode) . "'
            WHERE id = $id";

        // Execute update query
        if ($conn->query($updateQuery)) {
            echo "<script>alert('Data saved successfully for ID: $id');</script>";
        } else {
            echo "<script>alert('Error saving data for ID: $id');</script>";
        }
    }
}

// Function to download filtered data as Excel
if (isset($_POST['download'])) {
    // Fetch filtered data
    $downloadResult = $conn->query($filterQuery);
    $data = [];
    while ($row = $downloadResult->fetch_assoc()) {
        $data[] = $row;
    }

    // Generate Excel file
    header("Content-Disposition: attachment; filename=filtered_data.xls");
    header("Content-Type: application/vnd.ms-excel");

    echo implode("\t", array_keys($data[0])) . "\n";
    foreach ($data as $row) {
        echo implode("\t", array_values($row)) . "\n";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
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
<div class="container-fluid mt-5">
    <h1>Welcome, <?= $_SESSION['username'] ?>!</h1>
    <p>This is the dashboard. From here, you can navigate to different pages using the menu above.</p>

    <!-- Filters form -->
    <form method="get" class="mb-4">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <label for="state">State</label>
                <select id="state" name="state" class="form-control">
                    <option value="">All</option>
                    <?php foreach ($states as $state): ?>
                        <option value="<?= htmlspecialchars($state) ?>" <?= (isset($_GET['state']) && $_GET['state'] == $state) ? 'selected' : '' ?>><?= htmlspecialchars($state) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="district">District</label>
                <select id="district" name="district" class="form-control">
                    <option value="">All</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= htmlspecialchars($district) ?>" <?= (isset($_GET['district']) && $_GET['district'] == $district) ? 'selected' : '' ?>><?= htmlspecialchars($district) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Education</label>
                <div>
                    <?php foreach ($educations as $education): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="education" id="education_<?= htmlspecialchars($education) ?>" value="<?= htmlspecialchars($education) ?>" <?= (isset($_GET['education']) && $_GET['education'] == $education) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="education_<?= htmlspecialchars($education) ?>"><?= htmlspecialchars($education) ?></label>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="education" id="education_all" value="" <?= !isset($_GET['education']) || $_GET['education'] == '' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="education_all">All</label>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Pagination controls -->
    <div class="mb-3">
        <label for="rowsPerPage">Rows per page:</label>
        <select id="rowsPerPage" class="form-control" onchange="changeRowsPerPage()">
            <option value="10" <?= $rowsPerPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="100" <?= $rowsPerPage == 100 ? 'selected' : '' ?>>100</option>
            <option value="1000" <?= $rowsPerPage == 1000 ? 'selected' : '' ?>>1000</option>
            <option value="<?= $totalRows ?>" <?= $rowsPerPage == $totalRows ? 'selected' : '' ?>>All</option>
        </select>
    </div>

    <!-- Display fetched data in a table -->
    <div class="table-responsive">
        <form method="post"> <!-- Form for saving edits -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?= htmlspecialchars($column) ?></th>
                        <?php endforeach; ?>
                        <th>Action</th> <!-- New column for edit and save buttons -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php if (strpos($column, 'Date') !== false): ?>
                                        <input type="date" class="form-control" name="<?= $column ?>[]" value="<?= htmlspecialchars($row[$column]) ?>">
                                    <?php elseif (in_array($column, ['Address', 'Recruiter_Name', 'Pincode', 'Position', 'Communication_Date', 'Job_Interest', 'Client_Name', 'Job_Role_Considered', 'Status', 'Remark', 'Tellecaller_Name', 'Location', 'TAT', 'Client_Name_Modified', 'Client_Location', 'Client_Code'])): ?>
                                        <input type="text" class="form-control" name="<?= $column ?>[]" value="<?= htmlspecialchars($row[$column]) ?>">
                                    <?php else: ?>
                                        <?= htmlspecialchars($row[$column]) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <input type="hidden" name="id[]" value="<?= $row['id'] ?>">
                                <button type="submit" name="save" class="btn btn-primary btn-sm">Save</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>

    <!-- Pagination links -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $current_page - 1 ?>&rowsPerPage=<?= $rowsPerPage ?>" tabindex="-1">Previous</a>
            </li>
            <?php for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $totalPages); $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&rowsPerPage=<?= $rowsPerPage ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $current_page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $current_page + 1 ?>&rowsPerPage=<?= $rowsPerPage ?>">Next</a>
            </li>
        </ul>
    </nav>

    <!-- Download button -->
    <form method="post">
        <button type="submit" name="download" class="btn btn-success">Download Filtered Data</button>
    </form>

    <!-- JavaScript to handle rows per page change -->
    <script>
        function changeRowsPerPage() {
            var select = document.getElementById("rowsPerPage");
            var value = select.options[select.selectedIndex].value;
            window.location.href = "dashboard.php?rowsPerPage=" + value;
        }
    </script>

</div>

</body>
</html>
