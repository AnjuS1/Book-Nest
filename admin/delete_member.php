<?php
    session_start();
    require '../includes/db_connect.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        die("Access denied.");
    }
    if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
        die("Invalid request.");
    }

    $member_id = (int)$_GET['user_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :id AND role = 'member'");
        $stmt->execute([':id' => $member_id]);
        if ($stmt->rowCount() > 0) {
            header("Location: members_list.php?msg=Member+deleted+successfully");
            exit;
        } else {
            echo "No member found with that ID.";
        }
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Cannot delete this member because related records exist (issued books or fines).";
        } else {
            echo "Error deleting member: " . $e->getMessage();
        }
    }
?>
