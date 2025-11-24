<?php
// addCustomer.php
include 'header.php';
require_once 'db.php';

// Initialize state
$update = false;
$id = "";
$customer_name = "";
$mobile = "";
$loan_amount = "";
$interest_rate = "";
$interest_amount = 0.00;
$disbursement_amount = 0.00;
$disbursement_date = ""; // YYYY-MM-DD or empty
$disbursement_display = ""; // human friendly e.g. "21 November, 2025"
$employee = "";
$errors = [];
$success = "";

// Load employees for dropdown
$employees = [];
$empSql = "SELECT id, name FROM employees ORDER BY name ASC";
if ($res = $conn->query($empSql)) {
    while ($r = $res->fetch_assoc()) {
        $employees[] = $r;
    }
    $res->free();
}

// If edit requested
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $update = true;
    $stmt = $conn->prepare("SELECT id, customer_name, mobile, loan_amount, interest_rate, interest_amount, disbursement_amount, disbursement_date, employee FROM customers WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $customer_name = $row['customer_name'];
            $mobile = $row['mobile'];
            $loan_amount = $row['loan_amount'];
            $interest_rate = $row['interest_rate'];
            $interest_amount = $row['interest_amount'];
            $disbursement_amount = $row['disbursement_amount'];
            $disbursement_date = $row['disbursement_date'];
            $employee = $row['employee'];
            if (!empty($disbursement_date)) {
                $d = DateTime::createFromFormat('Y-m-d', $disbursement_date);
                if ($d) $disbursement_display = $d->format('j F, Y');
            }
        }
        $stmt->close();
    }
}

// Handle save/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collect and trim
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $loan_amount = isset($_POST['loan_amount']) ? trim($_POST['loan_amount']) : '';
    $interest_rate = isset($_POST['interest_rate']) ? trim($_POST['interest_rate']) : '';
    $disbursement_date_raw = isset($_POST['disbursement_date']) ? trim($_POST['disbursement_date']) : '';
    $employee = isset($_POST['employee']) ? trim($_POST['employee']) : '';
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
    $update = !is_null($id);

    // Validation
    $errors = [];
    if ($customer_name === '') $errors[] = "Customer name is required.";
    if ($mobile === '') $errors[] = "Mobile is required.";
    if ($loan_amount === '' || !is_numeric($loan_amount) || $loan_amount < 0) $errors[] = "Loan amount must be a non-negative number.";
    if ($interest_rate === '' || !is_numeric($interest_rate) || $interest_rate < 0) $errors[] = "Interest rate must be a non-negative number.";
    if ($employee !== '' && !ctype_digit((string)$employee)) $errors[] = "Invalid employee selected.";

    // Normalize incoming disbursement date:
    $disbursement_date = "";
    if ($disbursement_date_raw !== '') {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $disbursement_date_raw)) {
            $disbursement_date = $disbursement_date_raw;
        } else {
            $try = DateTime::createFromFormat('j F, Y', $disbursement_date_raw);
            if ($try) {
                $disbursement_date = $try->format('Y-m-d');
            } else {
                $try2 = DateTime::createFromFormat('j F Y', $disbursement_date_raw);
                if ($try2) {
                    $disbursement_date = $try2->format('Y-m-d');
                } else {
                    $ts = strtotime($disbursement_date_raw);
                    if ($ts !== false) $disbursement_date = date('Y-m-d', $ts);
                }
            }
        }
    }

    // compute interest/disbursement server-side
    $loan = (float) $loan_amount;
    $rate = (float) $interest_rate;
    $interest_amount = ($loan * $rate) / 100.0;
    $disbursement_amount = $loan - $interest_amount;

    // If validation passed, perform DB op
    if (empty($errors)) {
        if ($update) {
            // Update with conditional handling for NULL date
            if ($disbursement_date !== '') {
                $sql = "UPDATE customers SET customer_name = ?, mobile = ?, loan_amount = ?, interest_rate = ?, interest_amount = ?, disbursement_amount = ?, disbursement_date = ?, employee = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $types = "ssddddsii";
                    $empInt = (int)$employee;
                    $stmt->bind_param($types, $customer_name, $mobile, $loan, $rate, $interest_amount, $disbursement_amount, $disbursement_date, $empInt, $id);
                    if ($stmt->execute()) {
                        $success = "Customer updated successfully.";
                        echo "<script>alert('Customer Updated Successfully!');</script>";
                        echo "<script>window.location.href='viewCustomer.php';</script>";
                        exit;
                    } else {
                        $errors[] = "Update failed: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Prepare failed: " . $conn->error;
                }
            } else {
                // set disbursement_date = NULL
                $sql = "UPDATE customers SET customer_name = ?, mobile = ?, loan_amount = ?, interest_rate = ?, interest_amount = ?, disbursement_amount = ?, disbursement_date = NULL, employee = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $types = "ssddddii";
                    $empInt = (int)$employee;
                    $stmt->bind_param($types, $customer_name, $mobile, $loan, $rate, $interest_amount, $disbursement_amount, $empInt, $id);
                    if ($stmt->execute()) {
                        $success = "Customer updated successfully.";
                        echo "<script>alert('Customer Updated Successfully!');</script>";
                        echo "<script>window.location.href='viewCustomer.php';</script>";
                        exit;
                    } else {
                        $errors[] = "Update failed: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Prepare failed: " . $conn->error;
                }
            }
        } else {
            // Insert with conditional handling for date
            if ($disbursement_date !== '') {
                $sql = "INSERT INTO customers (customer_name, mobile, loan_amount, interest_rate, interest_amount, disbursement_amount, disbursement_date, employee) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $types = "ssddddsi";
                    $empInt = (int)$employee;
                    $stmt->bind_param($types, $customer_name, $mobile, $loan, $rate, $interest_amount, $disbursement_amount, $disbursement_date, $empInt);
                    if ($stmt->execute()) {
                        $success = "Customer added successfully.";
                        echo "<script>alert('Customer Added Successfully!');</script>";
                        echo "<script>window.location.href='viewCustomer.php';</script>";
                        exit;
                    } else {
                        $errors[] = "Insert failed: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Prepare failed: " . $conn->error;
                }
            } else {
                // Insert without date (NULL)
                $sql = "INSERT INTO customers (customer_name, mobile, loan_amount, interest_rate, interest_amount, disbursement_amount, employee) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $types = "ssddddi";
                    $empInt = (int)$employee;
                    $stmt->bind_param($types, $customer_name, $mobile, $loan, $rate, $interest_amount, $disbursement_amount, $empInt);
                    if ($stmt->execute()) {
                        $success = "Customer added successfully.";
                        echo "<script>alert('Customer Added Successfully!');</script>";
                        echo "<script>window.location.href='viewCustomer.php';</script>";
                        exit;
                    } else {
                        $errors[] = "Insert failed: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "Prepare failed: " . $conn->error;
                }
            }
        }
    }

    // Format display date for the form if present
    if (!empty($disbursement_date)) {
        $dd = DateTime::createFromFormat('Y-m-d', $disbursement_date);
        if ($dd) $disbursement_display = $dd->format('j F, Y');
    } else {
        $disbursement_display = '';
    }
}
?>

<style>
    /* small responsive tweaks */
    .big-select {
        padding-top: 12px !important;
        padding-bottom: 12px !important;
        font-size: 16px;
        height: auto !important;
    }

    .result-box {
        background: #f5f5f5;
        padding: 12px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 16px;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .card {
            margin-bottom: 12px;
        }

        .row .col-xl-6 {
            margin-top: 0 !important;
        }
    }
</style>

<div class="container" style="margin-top: -48px;">
    <!-- SINGLE COMBINED FORM -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form id="customerForm" method="POST" autocomplete="off">
        <?php if ($update): ?>
            <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <?php endif; ?>

        <div class="row">
            <!-- LEFT SECTION -->
            <div class="col-xl-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo $update ? 'Edit Customer' : 'Add Customer'; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <fieldset>
                                <div class="form-group">
                                    <input name="customer_name" type="text" class="form-control" placeholder="Customer Name" required
                                        value="<?php echo htmlspecialchars($customer_name); ?>">
                                </div>

                                <div class="form-group">
                                    <input name="mobile" type="text" class="form-control" placeholder="Mobile" required
                                        value="<?php echo htmlspecialchars($mobile); ?>">
                                </div>

                                <div class="form-group">
                                    <input name="loan_amount" id="loanAmount" type="number" class="form-control" placeholder="Loan Amount" min="0" step="0.01" required
                                        value="<?php echo htmlspecialchars($loan_amount); ?>">
                                </div>

                                <div class="form-group">
                                    <input name="interest_rate" id="interestRate" type="number" class="form-control" placeholder="Interest Rate (%)" min="0" step="0.01" required
                                        value="<?php echo htmlspecialchars($interest_rate); ?>">
                                </div>

                                <div class="form-group">
                                    <select name="employee" class="form-control default-select big-select" required>
                                        <option value="">Select Employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?php echo (int)$emp['id']; ?>" <?php echo ((string)$employee === (string)$emp['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($emp['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION -->
            <div class="col-xl-6 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <!-- Results / computed fields -->
                            <div class="result-box">
                                Interest Amount: <span id="interestAmountText"><?php echo number_format((float)$interest_amount, 2); ?></span>
                            </div>

                            <div class="result-box">
                                Loan Amount - Interest: <span id="amountMinusText"><?php echo number_format((float)$disbursement_amount, 2); ?></span>
                            </div>

                            <input type="hidden" name="interest_amount" id="interestAmountInput" value="<?php echo htmlspecialchars($interest_amount); ?>">

                            <div class="form-group" style="margin-top: 20px;">
                                <label>Disbursement Amount</label>
                                <input name="disbursement_amount" type="number" id="amountMinusInput" class="form-control" placeholder="Disbursement Amount" value="<?php echo htmlspecialchars($disbursement_amount); ?>">
                            </div>

                            <div class="form-group">
                                <label>Disbursement Date</label>
                                <input name="disbursement_date" class="datepicker-default form-control" id="datepicker" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($disbursement_date ?: $disbursement_display); ?>">
                            </div>

                            <div style="margin-top:20px;">
                                <button type="submit" class="btn btn-primary"><?php echo $update ? 'Update' : 'Submit'; ?></button>
                                <button type="button" id="resetBtn" class="btn btn-secondary ms-2">Reset</button>
                                <?php if ($update): ?>
                                    <a href="addCustomer.php" class="btn btn-outline-secondary ms-2">Create New</a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const loanAmount = document.getElementById("loanAmount");
    const interestRate = document.getElementById("interestRate");

    const interestAmountText = document.getElementById("interestAmountText");
    const amountMinusText = document.getElementById("amountMinusText");
    const amountMinusInput = document.getElementById("amountMinusInput");
    const interestAmountInput = document.getElementById("interestAmountInput");

    function calculate() {
        const loan = parseFloat(loanAmount.value) || 0;
        const rate = parseFloat(interestRate.value) || 0;

        const interest = (loan * rate) / 100;
        const minus = loan - interest;

        interestAmountText.innerText = interest.toFixed(2);
        amountMinusText.innerText = minus.toFixed(2);

        amountMinusInput.value = minus.toFixed(2);
        interestAmountInput.value = interest.toFixed(2);
    }

    if (loanAmount) loanAmount.addEventListener("input", calculate);
    if (interestRate) interestRate.addEventListener("input", calculate);

    document.addEventListener("DOMContentLoaded", calculate);

    const form = document.getElementById("customerForm");
    document.getElementById("resetBtn").addEventListener("click", function() {
        form.reset();
        calculate();
    });
</script>

<?php include 'footer.php'; ?>
