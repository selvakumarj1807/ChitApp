<?php include 'db.php'; ?>

<?php include 'header.php'; ?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<h3 style="text-align: center;"><?php echo $username ?> - Collection</h3>

<div class="row" style="margin-top: 30px;">
    <div class="col-xl-6 col-xxl-12">
        <div class="row">
            <div class="col-sm-6">
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
        where c.id='$id' ORDER BY c.id DESC";

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
                        <div class="card">
                            <div class="card-header d-sm-flex d-block pb-0 border-0 align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="fs-20 text-black mb-1">
                                        <?= htmlspecialchars($name); ?>
                                        <span class="text-muted mx-1">-</span>
                                        <span class="text-secondary" style="font-size:15px;"><?= htmlspecialchars($mobileDigits ?: '-'); ?></span>
                                    </h5>
                                    <p class="mb-0 fs-12">₹ <?= number_format($loan); ?></p>
                                    <p class="mb-0 fs-12">
                                        <i class="fa fa-calendar-o mr-2" aria-hidden="true"></i>
                                        Disbursement on <?= htmlspecialchars($disb_display); ?>
                                    </p>
                                </div>

                                <!-- Date selector (Bootstrap dropdown) -->
                                <div class="dropdown custom-dropdown ml-0 ml-sm-3 mt-3 mt-sm-0">
                                    <button id="dateDropdownBtn" class="btn btn-light d-flex align-items-center svg-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">
                                        <!-- SVG icon (kept from your original) -->


                                        <!-- Visible selected date label (updated via JS) -->
                                        <span id="selectedDateLabel" class="text-black fs-16 mr-2">Select Date</span>
                                        <i class="fa fa-angle-down text-black scale3 ml-2" aria-hidden="true"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="dateDropdownBtn" style="min-width: 250px;">
                                        <!-- Hidden input (for forms) -->
                                        <input type="hidden" id="selectedDateHidden" name="selected_date" value="">

                                        <!-- Native date input -->
                                        <div class="form-group mb-2">
                                            <label for="datePickerInput" class="mb-1">Choose date</label>
                                            <input id="datePickerInput" class="form-control" type="date" />
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button id="setTodayBtn" type="button" class="btn btn-sm btn-outline-secondary">Today</button>
                                            <div>
                                                <button id="applyDateBtn" type="button" class="btn btn-sm btn-primary">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div class="basic-form">
                                    <form class="form-wrapper">
                                        <div class="form-group">
                                            <div class="input-group input-group-lg">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Amount</span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="0">
                                                <input type="hidden" id="datePickerInput" class="form-control" type="date" />
                                            </div>
                                        </div>



                                        <div class="row mt-4 align-items-center">
                                            <div class="col-lg-6">
                                                <div>
                                                    <p class="mb-0 fs-14"></p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="d-flex justify-content-end">
                                                    <a href="javascript:void(0);" class="btn  btn-success text-white text-nowrap">
                                                        Submit
                                                    </a>

                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="mb-0 fs-20 text-black">Customer Name</h4>

                    </div>
                    <div class="card-body p-2 pb-0">

                        <div class="table-responsive">
                            <table class="table text-center bg-info-hover tr-rounded order-tbl">
                                <thead>
                                    <tr>
                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">02/12/2025</td>
                                        <td class="text-center">10000</td>
                                        <td class="text-center">90000</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Inline JS: set default to today and wire up buttons -->
<script>
    (function() {
        // Elements
        const dateInput = document.getElementById('datePickerInput');
        const label = document.getElementById('selectedDateLabel');
        const hidden = document.getElementById('selectedDateHidden');
        const applyBtn = document.getElementById('applyDateBtn');
        const todayBtn = document.getElementById('setTodayBtn');

        // Helper: format "21 November, 2025"
        function formatReadable(dateObj) {
            const opts = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return new Date(dateObj.getTime() - (dateObj.getTimezoneOffset() * 60000)).toLocaleDateString('en-GB', opts);
        }

        // Today in 'YYYY-MM-DD' for input[type=date]
        function getTodayISO() {
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        // Initialize: set to today
        const todayISO = getTodayISO();
        dateInput.value = todayISO;
        hidden.value = todayISO;
        label.textContent = formatReadable(new Date(todayISO));

        // Apply button: copy date to hidden and label, then close dropdown (Bootstrap handles close)
        applyBtn.addEventListener('click', function() {
            const val = dateInput.value;
            if (!val) return;
            hidden.value = val;
            label.textContent = formatReadable(new Date(val));
            // close dropdown programmatically (Bootstrap 4/5 compatibility)
            const dropdown = dateInput.closest('.dropdown');
            if (dropdown) {
                const toggle = dropdown.querySelector('[data-toggle="dropdown"]');
                if (toggle && typeof jQuery !== 'undefined') {
                    jQuery(toggle).dropdown('toggle');
                } else if (toggle) {
                    // try to dispatch click on the toggle to close if jQuery not present
                    toggle.click();
                }
            }
        });

        // Today button
        todayBtn.addEventListener('click', function() {
            dateInput.value = todayISO;
        });

        // If user manually changes date, update hidden & label live (optional)
        dateInput.addEventListener('change', function() {
            const v = dateInput.value;
            if (!v) return;
            hidden.value = v;
            label.textContent = formatReadable(new Date(v));
        });

        // Expose chosen date to global if needed:
        window.getSelectedCollectionDate = () => hidden.value;
    })();
</script>
<?php include 'footer.php'; ?>