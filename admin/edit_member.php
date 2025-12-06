<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
        die("Access denied."); 
    }
    if (!isset($_GET['user_id'])) {
        $_SESSION['msg'] = "User ID missing.";
        header("Location: members_list.php");
        exit();
    }

    $user_id = (int)$_GET['user_id'];
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id, u.name, u.email,
            m.member_id, m.phone, m.address
        FROM users u
        LEFT JOIN members m ON u.user_id = m.user_id
        WHERE u.user_id = :uid
    ");
    $stmt->execute([':uid' => $user_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $_SESSION['msg'] = "User not found.";
        header("Location: members_list.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (!preg_match('/^\d{10}$/', $phone)) {
            $errors[] = "Phone number must be exactly 10 digits.";
        }
        if (empty($address)) {
            $errors[] = "Address cannot be empty.";
        }
        if ($errors) {
            $_SESSION['msg'] = implode(' ', $errors);
            $_SESSION['old_input'] = ['name'=>$name, 'email'=>$email, 'phone'=>$phone, 'address'=>$address];
            header("Location: edit_member.php?user_id=$user_id");
            exit();
        } else {
            try {
                $pdo->beginTransaction();

                $updateUser = $pdo->prepare("
                    UPDATE users SET name = :name, email = :email
                    WHERE user_id = :uid
                ");
                $updateUser->execute([':name'=>$name, ':email'=>$email, ':uid'=>$user_id]);

                if (!empty($data['member_id'])) {
                    $updateMember = $pdo->prepare("
                        UPDATE members SET phone = :phone, address = :address
                        WHERE user_id = :uid
                    ");
                    $updateMember->execute([':phone'=>$phone, ':address'=>$address, ':uid'=>$user_id]);
                }
                $pdo->commit();
                $_SESSION['msg'] = "User updated successfully!";
                header("Location: edit_member.php?user_id=$user_id");
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $_SESSION['msg'] = "Error: ".$e->getMessage();
                $_SESSION['old_input'] = ['name'=>$name, 'email'=>$email, 'phone'=>$phone, 'address'=>$address];
                header("Location: edit_member.php?user_id=$user_id");
                exit();
            }
        }
    }

    $msg = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);
    $old_input = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);

    $name_value    = $old_input['name'] ?? $data['name'];
    $email_value   = $old_input['email'] ?? $data['email'];
    $phone_value   = $old_input['phone'] ?? $data['phone'];
    $address_value = $old_input['address'] ?? $data['address'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit User</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light p-4">

    <div class="container">
        <div class="card p-4" style="max-width: 600px; margin: auto;">
            <h2 class="mb-3 text-center">Edit User</h2>

            <?php if ($msg): ?>
                <div class="alert <?= strpos($msg,'Error')===0 ? 'alert-danger' : 'alert-success' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($name_value) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email_value) ?>" class="form-control" required>
                </div>

                <?php if (!empty($data['member_id'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Phone:</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($phone_value) ?>" class="form-control" maxlength="10" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address:</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($address_value) ?>" class="form-control" required>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="members_list.php" class="btn btn-secondary">Back</a>
                    <input type="submit" name="update" value="Update User" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    <script>
        const phoneInput = document.querySelector('input[name="phone"]');
        if(phoneInput){
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0,10);
            });
        }
    </script>
    </body>
</html>
