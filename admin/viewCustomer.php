<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
?>

<?php
include 'db.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $sql = "DELETE FROM customers WHERE id = $id";

    if ($conn->query($sql)) {
        echo "<script>alert('Customers Deleted Successfully!');</script>";
        echo "<script>window.location.href='viewCustomer.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting employee');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Zenix - Crypto Admin Dashboard">
    <meta property="og:title" content="Zenix - Crypto Admin Dashboard">
    <meta property="og:description" content="Zenix - Crypto Admin Dashboard">
    <meta property="og:image" content="https://zenix.dexignzone.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">
    <title>Zenix - Crypto Admin Dashboard </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Datatable -->
    <link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="index.php" class="brand-logo">


            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Chat box start
        ***********************************-->

        <!--**********************************
            Chat box End
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">

                        </div>
                        <ul class="navbar-nav header-right main-notification">


                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <img src="images/profile/pic1.jpg" width="20" alt="">

                                    <div class="header-info">
                                        <span>Admin</span>
                                        <small>Super Admin</small>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="app-profile.html" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary"
                                            width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span class="ml-2">Profile </span>
                                    </a>
                                    <a href="email-inbox.html" class="dropdown-item ai-icon">
                                        <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success"
                                            width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                            </path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="ml-2">Inbox </span>
                                    </a>
                                    <a href="logout.php" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger"
                                            width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        <span class="ml-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <?php include 'links.php'; ?>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <div class="container-fluid">


                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Customer Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example3" class="display" style="min-width: 800px">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Loan Amount</th>
                                            <th>Interest Rate</th>
                                            <th>Intrest Amount</th>
                                            <th>Disbursement Amount</th>
                                            <th>Disbursement date</th>
                                            <th>Assign Employee</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query with LEFT JOIN to get employee name
                                        $sql = "SELECT c.id,
               c.customer_name,
               c.mobile,
               c.loan_amount,
               c.interest_rate,
               c.interest_amount,
               c.disbursement_amount,
               c.disbursement_date,
               e.name AS employee_name
        FROM customers c
        LEFT JOIN employees e ON c.employee = e.id
        ORDER BY c.id DESC";

                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                // Clean / compute values
                                                $id = (int)$row['id'];
                                                $name = $row['customer_name'];
                                                $rawMobile = $row['mobile'];
                                                $mobileDigits = preg_replace('/\D+/', '', (string)$rawMobile); // digits only
                                                $loan = is_numeric($row['loan_amount']) ? (int)$row['loan_amount'] : 0.0;
                                                $rate = is_numeric($row['interest_rate']) ? (int)$row['interest_rate'] : 0.0;
                                                $interest = is_numeric($row['interest_amount']) ? (int)$row['interest_amount'] : ($loan * $rate / 100.0);
                                                $disb = is_numeric($row['disbursement_amount']) ? (int)$row['disbursement_amount'] : ($loan - $interest);

                                                // Format disbursement date as "24 November, 2025"
                                                $disb_display = '-';
                                                if (!empty($row['disbursement_date'])) {
                                                    $d = DateTime::createFromFormat('Y-m-d', $row['disbursement_date']);
                                                    if ($d) {
                                                        $disb_display = $d->format('j F, Y'); // e.g. 24 November, 2025
                                                    } else {
                                                        // try strtotime fallback
                                                        $ts = strtotime($row['disbursement_date']);
                                                        if ($ts !== false) $disb_display = date('j F, Y', $ts);
                                                    }
                                                }

                                                $employee_name = !empty($row['employee_name']) ? $row['employee_name'] : '-';
                                        ?>
                                                <tr id="row-<?php echo $id; ?>">
                                                    <td><?= htmlspecialchars($name); ?></td>
                                                    <td>
                                                        <a href="#" class="callLink" data-number="<?= htmlspecialchars($mobileDigits); ?>" style="text-decoration:none; color:#000;">
                                                            <?= htmlspecialchars($mobileDigits ?: '-'); ?>
                                                        </a>
                                                    </td>
                                                    <td><?= number_format($loan); ?></td>
                                                    <td><?= htmlspecialchars(rtrim(rtrim((string)$rate, '0'), '.')) . '%'; ?></td>
                                                    <td><?= number_format($interest); ?></td>
                                                    <td><?= number_format($disb); ?></td>
                                                    <td><?= htmlspecialchars($disb_display); ?></td>
                                                    <td><?= htmlspecialchars($employee_name); ?></td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <!-- EDIT BUTTON -->
                                                            <a href="addCustomer.php?edit=<?= $id; ?>" class="btn btn-primary shadow btn-xs sharp mr-1" title="Edit">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>

                                                            <!-- DELETE BUTTON -->
                                                            <a href="viewCustomer.php?delete=<?= $id; ?>"
                                                                onclick="return confirm('Are you sure you want to delete this Customer?');"
                                                                class="btn btn-danger shadow btn-xs sharp" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            } // end while
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No customers found.</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->

            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->
        </div>

    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->

    <script>
        function isMobileDevice() {
            return /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent);
        }

        const callLinks = document.querySelectorAll(".callLink");

        callLinks.forEach(link => {
            const number = link.textContent.trim().replace(/[^0-9]/g, '');
            if (isMobileDevice()) {
                link.href = "tel:" + number;
            } else {
                link.href = "https://wa.me/91" + number;
                link.target = "_blank";
            }
        });
    </script>

    <!-- Required vendors -->
    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="vendor/global/global.min.js"></script>
    <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <!-- Datatable -->
    <script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="js/plugins-init/datatables.init.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/deznav-init.js"></script>
    <script src="js/demo.js"></script>
    <script src="js/styleSwitcher.js"></script>
</body>

</html>