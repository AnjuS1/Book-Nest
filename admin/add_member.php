<?php
session_start();
require '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION["old"] = $_POST;
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $errors = [];
    
    if ($name == '' || $email == '' || $phone == '' || $address == '' || $password == '') {
    $errors[] = "All fields are required.";
    }

    # Email validation 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    # Phone validation 
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        header("Location: add_member.php");
        exit;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $join_date = date('Y-m-d');
    try {
        $stmtUser = $pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (:name, :email, :password, 'member')
        ");
        $stmtUser->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => $hashedPassword
        ]);
        $user_id = $pdo->lastInsertId();

        $stmtMember = $pdo->prepare("
            INSERT INTO members (user_id, phone, address, join_date)
            VALUES (:user_id, :phone, :address, :join_date)
        ");
        $stmtMember->execute([
            ':user_id'   => $user_id,
            ':phone'     => $phone,
            ':address'   => $address,
            ':join_date' => $join_date
        ]);

        $_SESSION["success"] = "Member added successfully!";
    } catch (PDOException $e) {
        $_SESSION["errors"] = ["Error: " . $e->getMessage()];
    }
    header("Location: add_member.php");
    exit;
}

$old     = $_SESSION["old"]     ?? [];
$errors  = $_SESSION["errors"]  ?? [];
$success = $_SESSION["success"] ?? '';

unset($_SESSION["old"], $_SESSION["errors"], $_SESSION["success"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add Member</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../adminstyle.css" rel="stylesheet">
    </head>
    <body class="bg-light p-4 add-member">
    <div class="add-member-container">
        <div class="card shadow p-4">
            <h2 class="mb-4 text-center text-primary">Add New Member</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?>
                        <?= htmlspecialchars($e) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" class="form-control"
                        value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone:</label>
                    <input type="text" name="phone" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);" 
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" name="address" class="form-control"
                        value="<?= htmlspecialchars($old['address'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="members_list.php" class="btn btn-secondary">Back to Members</a>
                    <input type="submit" name="add" value="Add Member" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    </body>
</html>
