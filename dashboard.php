<?php include 'db.php'; ?>

<?php include 'header.php'; ?>
<style>
    /* keep original values and improve layout: 3 cards per row on large screens */
    .project-list-group .card {
        height: 100%;
    }

    .project-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .project-card .card-body {
        flex: 1 1 auto;
    }

    .status-btn {
        min-width: 88px;
        text-align: center;
    }

    .project-meta {
        font-size: 0.95rem;
    }

    .mb-card {
        margin-bottom: 1rem;
    }
</style>
<h3 style="text-align: center;"><?php echo $username ?> - Collection</h3>
<div class="project-nav">
    <div class="card-action card-tabs  mr-auto">
        <ul class="nav nav-tabs style-2">
            <li class="nav-item">
                <a href="#navpills-1" class="nav-link active" data-toggle="tab" aria-expanded="false">
                    All Customers <span class="badge badge-pill shadow-primary badge-primary">154</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#navpills-3" class="nav-link" data-toggle="tab" aria-expanded="true">
                    Paid <span class="badge badge-pill badge-warning shadow-warning">4</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#navpills-4" class="nav-link" data-toggle="tab" aria-expanded="true">
                    Pending <span class="badge badge-pill badge-danger shadow-danger">1</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content project-list-group" id="myTabContent">

    <!-- TAB 1: All Customers -->
    <div class="tab-pane fade active show" id="navpills-1">

        <div class="row">


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
        where e.name = '$username' ORDER BY c.id DESC";

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
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-card">
                        <div class="card project-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="col-xl-12 p-0">
                                            <p class="text-primary mb-1"><?= htmlspecialchars($name); ?> - <span style="color: #A6A1CF;"><?= htmlspecialchars($mobileDigits ?: '-'); ?></span></p>
                                            <h5 class="title font-w600 mb-2">
                                                <a href="payAmount.php?id=<?php echo $id; ?>" class="text-black">₹ <?= number_format($loan); ?></a>
                                            </h5>
                                            <a href="payAmount.php?id=<?php echo $id; ?>">
                                                <div class="text-dark project-meta">
                                                    <i class="fa fa-calendar-o mr-3" aria-hidden="true"></i>Disbursement on <?= htmlspecialchars($disb_display); ?>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <a href="payAmount.php?id=<?php echo $id; ?>">
                                        <div class="text-right">
                                            <div class="d-flex align-items-center">
                                                <span class="btn bgl-warning text-warning status-btn mr-3">Pending</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="mt-auto">
                                    <!-- footer or meta if needed -->
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                } // end while
            } else {
                ?>
                <p>No customers found.</p>
            <?php
            }
            ?>



        </div> <!-- /.row -->

    </div> <!-- /.tab-pane -->

</div> <!-- /.tab-content -->

<?php include 'footer.php'; ?>