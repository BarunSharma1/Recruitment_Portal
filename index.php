<?php
include('includes/config.php');
include('includes/functions.php');

$state = isset($_GET['state']) ? $_GET['state'] : '';
$district = isset($_GET['district']) ? $_GET['district'] : '';
$education = isset($_GET['education']) ? $_GET['education'] : '';
$limit = isset($_GET['limit']) ? $_GET['limit'] : 100;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$states = getStates($conn);
$districts = getDistricts($conn);
$educations = getEducations($conn);
$profile_count = getProfileCount($conn, $state, $district, $education);
$total_pages = ceil($profile_count / $limit);

$profiles = getFilteredProfiles($conn, $state, $district, $education, $limit, $offset);

if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="filtered_data.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Code', 'Name','eKYC_Completed', 'Email', 'Mobile', 'Gender', 'Age','Beneficiary_ID_Status' ,'State', 'District','Profile_Completion' ,'Education', 'Date of Contact', 'Remark'));

    while ($row = $profiles->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>User Profiles</title>
    <style>
        body {
            font-size: 14px;
        }
        .table td, .table th {
            padding: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-12">
                <h1>User Profiles</h1>
                <form method="get" action="index.php" class="form-inline mb-3">
                    <div class="form-group mr-3">
                        <label for="state" class="mr-2">State:</label>
                        <select name="state" id="state" class="form-control">
                            <option value="">All</option>
                            <?php while($row = $states->fetch_assoc()): ?>
                            <option value="<?php echo $row['State']; ?>" <?php echo $row['State'] == $state ? 'selected' : ''; ?>>
                                <?php echo $row['State']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <label for="district" class="mr-2">District:</label>
                        <select name="district" id="district" class="form-control">
                            <option value="">All</option>
                            <?php while($row = $districts->fetch_assoc()): ?>
                            <option value="<?php echo $row['District']; ?>" <?php echo $row['District'] == $district ? 'selected' : ''; ?>>
                                <?php echo $row['District']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        <label for="education" class="mr-2">Education:</label>
                        <?php while($row = $educations->fetch_assoc()): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="education" value="<?php echo $row['Education']; ?>" 
                                   <?php echo $row['Education'] == $education ? 'checked' : ''; ?>>
                            <label class="form-check-label"><?php echo $row['Education']; ?></label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="form-group mr-3">
                        <label for="limit" class="mr-2">Entries per page:</label>
                        <select name="limit" id="limit" class="form-control">
                            <option value="1" <?php echo $limit == 1 ? 'selected' : ''; ?>>1</option>
                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                            <option value="500" <?php echo $limit == 500 ? 'selected' : ''; ?>>500</option>
                            <option value="<?php echo $profile_count; ?>" <?php echo $limit == $profile_count ? 'selected' : ''; ?>>All</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="index.php?<?php echo http_build_query($_GET); ?>&download=1" class="btn btn-success ml-3">Download</a>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>eKYC Status</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Education</th>
                                <th>Date of Contact</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $profiles->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['Code']; ?></td>
                                <td><?php echo $row['Name']; ?></td>
                                <td><?php echo $row['eKYC_Completed']; ?></td>
                                <td><?php echo $row['Email']; ?></td>
                                <td><?php echo $row['Mobile']; ?></td>
                                <td><?php echo $row['Gender']; ?></td>
                                <td><?php echo $row['Age']; ?></td>
                                <td><?php echo $row['State']; ?></td>
                                <td><?php echo $row['District']; ?></td>
                                <td><?php echo $row['Education']; ?></td>
                                <td>
                                    <input type="date" class="form-control form-control-sm" value="<?php echo $row['Date_of_Contact']; ?>" onchange="updateDateOfContact('<?php echo $row['Code']; ?>', this.value)">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $row['Remark']; ?>" onchange="updateRemark('<?php echo $row['Code']; ?>', this.value)">
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="index.php?state=<?php echo $state; ?>&district=<?php echo $district; ?>&education=<?php echo $education; ?>&limit=<?php echo $limit; ?>&page=<?php echo $page-1; ?>">Previous</a>
                        </li>
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="index.php?state=<?php echo $state; ?>&district=<?php echo $district; ?>&education=<?php echo $education; ?>&limit=<?php echo $limit; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                            <a class="page-link" href="index.php?state=<?php echo $state; ?>&district=<?php echo $district; ?>&education=<?php echo $education; ?>&limit=<?php echo $limit; ?>&page=<?php echo $page+1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <script src="jquery-3.7.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
        function updateDateOfContact(code, dateOfContact) {
            $.ajax({
                url: 'update_date_of_contact.php',
                type: 'POST',
                data: {
                    code: code,
                    date_of_contact: dateOfContact
                },
                success: function(response) {
                    console.log('Date of Contact updated');
                }
            });
        }

        function updateRemark(code, remark) {
            $.ajax({
                url: 'update_remark.php',
                type: 'POST',
                data: {
                    code: code,
                    remark: remark
                },
                success: function(response) {
                    console.log('Remark updated');
                }
            });
        }

        $(document).ready(function() {
            function adjustTable() {
                var table = $('.table-responsive');
                var width = $(window).width();
                if (width < 768) {
                    table.css('overflow-x', 'scroll');
                } else {
                    table.css('overflow-x', 'auto');
                }
            }
            adjustTable();
            $(window).resize(function() {
                adjustTable();
            });
        });
    </script>
</body>
</html>
