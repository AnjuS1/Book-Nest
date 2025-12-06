<?php
    session_start();
    require 'db_connect.php';

    // Handle visit count via cookie
    if(isset($_COOKIE['visit_count'])){
        $visit_count = $_COOKIE['visit_count'] + 1;
    } else {
        $visit_count = 1;
    }
    setcookie('visit_count', $visit_count, time() + 365*24*60*60); // cookie valid for 1 year

    $old_email = $_SESSION['old_email'] ?? '';
    unset($_SESSION['old_email']);

    // Error message
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);

    if(isset($_POST['login'])){
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Fetch user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute([':email'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            // Update visit count and last visit in DB
            $update = $pdo->prepare("
                UPDATE users 
                SET visit_count = visit_count + 1,
                    last_visit = NOW()
                WHERE user_id = :uid
            ");
            $update->execute([':uid' => $user['user_id']]);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if($user['role'] == 'admin'){
                header("Location: /library/admin/dashboard.php");
            } else {
                header("Location: /library/member/dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Incorrect email or password.";
            $_SESSION['old_email'] = $email; 
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-3">
        <div class="container">
        <h1>Book Nest<h1>
        <h2>Login</h2>
        <?php if($error): ?>
            <p style="color:red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old_email) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <input type="submit" name="login" class="btn btn-primary" value="Login">
        </form>
        <p class="mt-2">
            <p>Don't have an account? <a href="register.php"> Register here</a></p>
            <p>Forgot your password? <a href="forgot_password.php"> Forgot password</a></p>
        </p>
        </div>
    </body>
</html>
