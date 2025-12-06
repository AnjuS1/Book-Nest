<?php
session_start();
require 'db_connect.php';

$msg = "";
$user_data = [];

if(isset($_SESSION['reset_user_id'])){
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE user_id=:uid");
    $stmt->execute([':uid'=>$_SESSION['reset_user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['submit_verify'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name=:name AND email=:email");
    $stmt->execute([':name'=>$username, ':email'=>$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        $_SESSION['reset_user_id'] = $user['user_id'];
        $user_data = ['name'=>$user['name'], 'email'=>$user['email']];
    } else {
        $msg = "No user found with that username and email combination.";
    }
}

if(isset($_POST['reset_password'])){
    if(!isset($_SESSION['reset_user_id'])){
        $msg = "Session expired. Please try again.";
    } else {
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        if($password !== $confirm){
            $msg = "Passwords do not match.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=:pwd WHERE user_id=:uid");
            $stmt->execute([
                ':pwd' => $hashed,
                ':uid' => $_SESSION['reset_user_id']
            ]);
            unset($_SESSION['reset_user_id']);
            $msg = "Password updated successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container" style="max-width: 500px;">
    <h2 class="mb-3">Forgot Password</h2>

    <?php if($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if(isset($_SESSION['reset_user_id'])): ?>
        <form method="post">
            <div class="mb-3">
                <label>New Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password:</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <input type="submit" name="reset_password" class="btn btn-primary" value="Update Password">
        </form>
    <?php else: ?>
        <form method="post">
            <div class="mb-3">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <input type="submit" name="submit_verify" class="btn btn-primary" value="Verify">
        </form>
    <?php endif; ?>

    <p class="mt-2"><a href="login.php">Back to Login</a></p>
</div>
</body>
</html>
