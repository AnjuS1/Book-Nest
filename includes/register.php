<?php
    session_start();
    require 'db_connect.php';

    $error = $msg = "";
    if(isset($_POST['register'])){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        if($password !== $cpassword){
            $_SESSION["error"] = "Passwords do not match";
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
            $stmt->execute([':email'=>$email]);
            if($stmt->fetch()){
                $_SESSION["error"] = "Email already exists";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (:name,:email,:password,:role)");
                try{
                    $stmt->execute([
                        ':name'=>$name,
                        ':email'=>$email,
                        ':password'=>password_hash($password,PASSWORD_DEFAULT),
                        ':role'=>'user'  // new users start as 'user'
                    ]);
                    $msg = "Registration successful. You can now login.";
                }catch(PDOException $e){
                    $_SESSION["error"] = "Error: ".$e->getMessage();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-3">
        <div class="container">
        <h2>Register</h2>
        <?php
            if ( isset($_SESSION["error"]) ) {
                echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
                unset($_SESSION["error"]);
            }
        ?>
        <?php if($msg) echo "<p class='text-success'>$msg</p>"; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Confirm Password:</label>
                    <input type="password" name="cpassword" class="form-control" required>
                </div>
                <input type="submit" name="register" class="btn btn-primary" value="Register">
            </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </body>
</html>
