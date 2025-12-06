<?php
    session_start();
    require '../includes/db_connect.php';

    if(!isset($_SESSION['user_id'])){
        header("Location: ../auth/login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Check if already a member
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = :uid");
    $stmt->execute([':uid'=>$user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    $already_member = $existing ? true : false;

    $msg = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);

    if(!$already_member && isset($_POST['become'])){
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        $_SESSION['old_input'] = ['phone'=>$phone, 'address'=>$address];

        if(!preg_match('/^\d{10}$/', $phone)){
            $_SESSION['msg'] = "Phone number must be exactly 10 digits.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            try{
                $join_date = date('Y-m-d');

                $stmt = $pdo->prepare("INSERT INTO members (user_id, phone, address, join_date) 
                                    VALUES (:uid, :phone, :address, :join_date)");
                $stmt->execute([
                    ':uid'=>$user_id,
                    ':phone'=> $phone,
                    ':address'=> $address,
                    ':join_date'=> $join_date
                ]);

                // Update role to 'member'
                $stmt2 = $pdo->prepare("UPDATE users SET role='member' WHERE user_id=:uid");
                $stmt2->execute([':uid'=>$user_id]);
                $_SESSION['role'] = 'member';

                $_SESSION['msg'] = "Congratulations! You are now a library member.";
                unset($_SESSION['old_input']);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;

            } catch(PDOException $e){
                $_SESSION['msg'] = "Error: ".$e->getMessage();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }

    $phone_value = $old_input['phone'] ?? '';
    $address_value = $old_input['address'] ?? '';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Become a Member</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script>
        function validateForm() {
            const phone = document.forms["memberForm"]["phone"].value.trim();
            const phoneRegex = /^\d{10}$/;
            if(!phoneRegex.test(phone)){
                alert("Phone number must be exactly 10 digits.");
                return false;
            }
            return true;
        }
        </script>
    </head>
    <body class="p-3">
        <div class="container">
        <h2>Become a Library Member</h2>
        <?php if($msg): ?>
            <div class="alert <?php echo $already_member ? 'alert-success' : 'alert-info'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        <?php if(!$already_member): ?>
        <form name="memberForm" method="post" onsubmit="return validateForm();">
        <div class="mb-3">
        <label>Phone:</label>
        <input type="text" name="phone" class="form-control" 
            pattern="\d{10}" title="Enter exactly 10 digits" 
            maxlength="10" required
            value="<?= htmlspecialchars($phone_value) ?>"
            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
        </div>
        <div class="mb-3">
        <label>Address:</label>
        <input type="text" name="address" class="form-control" required
            value="<?= htmlspecialchars($address_value) ?>">
        </div>
        <input type="submit" name="become" class="btn btn-primary" value="Become Member">
        </form>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
        <?php endif; ?>
        </div>
    </body>
</html>
