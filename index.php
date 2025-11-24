<?php
session_start();
require 'db.php';

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {

    $user = $_POST['username'];
    $pass = $_POST['password'];

    // -------------------- CHECK ADMIN USER TABLE --------------------
    $query_admin = "SELECT * FROM user WHERE username = '$user' AND password = '$pass'";
    $result_admin = mysqli_query($conn, $query_admin);

    if (mysqli_num_rows($result_admin) == 1) {
        $_SESSION['username'] = $user;
        $_SESSION['role'] = "Admin";

        echo "<script>alert('Admin Login Successful');</script>";
        echo "<script>window.location='dashboard.php';</script>";
        exit();
    }

    // -------------------- CHECK EMPLOYEE TABLE --------------------
    $query_emp = "SELECT * FROM employees WHERE name = '$user' AND mobile = '$pass'";
    $result_emp = mysqli_query($conn, $query_emp);

    if (mysqli_num_rows($result_emp) == 1) {
        $_SESSION['username'] = $user;
        $_SESSION['role'] = "Employee";

        echo "<script>alert('Employee Login Successful');</script>";
        echo "<script>window.location='dashboard.php';</script>";
        exit();
    }

    // -------------------- INVALID LOGIN --------------------
    echo "<script>alert('Invalid Username or Password');</script>";
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <title>Chit App - Login</title>
    <link href="admin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="admin/css/style.css" rel="stylesheet">
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">

                                    <h4 class="text-center mb-4">Sign in to your account</h4>

                                    <form method="POST">
                                        <div class="form-group">
                                            <label><strong>User Name</strong></label>
                                            <input type="text" name="username" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Password</strong></label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" name="login" class="btn btn-primary btn-block">
                                                Sign Me In
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="admin/vendor/global/global.min.js"></script>
    <script src="admin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="admin/js/custom.min.js"></script>

</body>

</html>