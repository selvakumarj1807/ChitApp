<?php include 'header.php'; ?>
<?php require('db.php'); ?>

<?php
// ---------- CHECK IF EDIT ----------
$update = false;
$id = "";
$name = "";
$mobile = "";

// If update request comes: addEmployee.php?edit=ID
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;

    // Fetch employee details
    $sql = "SELECT * FROM employees WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $mobile = $row['mobile'];
    }
}

// ---------------- SAVE / UPDATE SUBMIT ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $mobile = $_POST['mobile'];

    if (isset($_POST['id']) && $_POST['id'] != "") {
        // UPDATE QUERY
        $id = $_POST['id'];
        $sql = "UPDATE employees SET name='$name', mobile='$mobile' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Employee Updated Successfully!');</script>";
            echo "<script>window.location.href='viewEmployee.php';</script>";
        }
    } else {
        // INSERT QUERY
        $employee_type = 'employee';
        $sql = "INSERT INTO employees(name, mobile, employee_type)
                VALUES('$name', '$mobile', '$employee_type')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Employee Added Successfully!');</script>";
            echo "<script>window.location.href='viewEmployee.php';</script>";
        }
    }
}
?>

<div class="col-xl-6 col-lg-6" style="margin-top: -45px;">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <?php echo $update ? "Update Employee" : "Add Employee"; ?>
            </h4>
        </div>

        <div class="card-body">
            <div class="basic-form">
                <form method="POST" action="addEmployee.php">
                    <fieldset>

                        <!-- Hidden field for update -->
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <div class="form-group">
                            <label>Employee Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?php echo $name; ?>"
                                   placeholder="Employee Name" required>
                        </div>

                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control"
                                   value="<?php echo $mobile; ?>"
                                   placeholder="Mobile" required>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <?php echo $update ? "Update" : "Submit"; ?>
                        </button>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
