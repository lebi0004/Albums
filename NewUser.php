<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Functions.php";
include("./common/header.php");


$errors = [];
$txtId = $_POST['txtId'] ?? '';
$txtName = $_POST['txtName'] ?? '';
$txtPhoneNumber = $_POST['txtPhoneNumber'] ?? '';
$txtPassword = $_POST['txtPassword'] ?? '';
$txtPasswordConfirm = $_POST['txtPasswordConfirm'] ?? '';

if (isset($_POST['regSubmit'])) {
    // Validate fields
    if (empty($txtId)) {
        $errors['txtId'] = "User ID is required.";
    }

    if (empty($txtName)) {
        $errors['txtName'] = "Name is required.";
    }

    if (!preg_match("/^\d{3}-\d{3}-\d{4}$/", $txtPhoneNumber)) {
        $errors['txtPhoneNumber'] = "Phone Number must be in the format nnn-nnn-nnnn.";
    }

    if (strlen($txtPassword) < 6 ||
            !preg_match("/[A-Z]/", $txtPassword) ||
            !preg_match("/[a-z]/", $txtPassword) ||
            !preg_match("/\d/", $txtPassword)) {
        $errors['txtPassword'] = "Password must be at least 6 characters long, and contain at least one uppercase letter, one lowercase letter, and one digit.";
    }

    if ($txtPassword !== $txtPasswordConfirm) {
        $errors['txtPasswordConfirm'] = "Passwords do not match.";
    }

    // Check if User ID already exists
    if (empty($errors)) {
        $existingUser = getUserById($txtId);
        if ($existingUser) {
            $errors['txtId'] = "A user with this ID has already signed up.";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password before storing
        $hashedPassword = password_hash($txtPassword, PASSWORD_DEFAULT);
        addNewUser($txtId, $txtName, $txtPhoneNumber, $hashedPassword);

        // Redirect to login page
        header("Location: Login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <form action="NewUser.php" method="post" class="mx-auto" style="max-width: 400px;">
        <div class="text-center" style="padding-top: 70px;">
            <h1 class="display-4">Sign up</h1>
            <p class="display-7">All fields are required</p>
        </div>

        <div class="row mb-3">
            <label for="userId" class="col-sm-4 col-form-label text-end">User ID:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="userId" name="txtId" value="<?php echo htmlspecialchars($txtId); ?>">
                <div class="text-danger"><?php echo $errors['txtId'] ?? ''; ?></div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="userName" class="col-sm-4 col-form-label text-end">Name:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="userName" name="txtName" value="<?php echo htmlspecialchars($txtName); ?>">
                <div class="text-danger"><?php echo $errors['txtName'] ?? ''; ?></div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="phoneNumber" class="col-sm-4 col-form-label text-end">Phone Number:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="phoneNumber" name="txtPhoneNumber" value="<?php echo htmlspecialchars($txtPhoneNumber); ?>">
                <div class="text-danger"><?php echo $errors['txtPhoneNumber'] ?? ''; ?></div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="createPassword" class="col-sm-4 col-form-label text-end">Password:</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="createPassword" name="txtPassword">
                <div class="text-danger"><?php echo $errors['txtPassword'] ?? ''; ?></div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="confirmPassword" class="col-sm-4 col-form-label text-end">Password Again:</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="confirmPassword" name="txtPasswordConfirm">
                <div class="text-danger"><?php echo $errors['txtPasswordConfirm'] ?? ''; ?></div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" name="regSubmit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-secondary">Clear</button>
        </div>
    </form>
</body>
</html>

<?php include('./common/footer.php'); ?>