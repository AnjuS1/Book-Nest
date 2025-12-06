<?php
    session_start();
    require '../includes/db_connect.php';

    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
        die("Access denied.");
    }
    if(!isset($_GET['id'])) {
        $_SESSION['msg'] = "Category ID missing.";
        header("Location: categories.php");
        exit();
    }
    $cat_id = (int)$_GET['id'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category = :id");
    $check->execute([':id' => $cat_id]);
    $in_use = $check->fetchColumn();

    if ($in_use > 0) {
        $_SESSION['msg'] = "Cannot delete this category because it is associated with $in_use book(s).";
        header("Location: categories.php");
        exit();
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = :id");
        $stmt->execute([':id' => $cat_id]);

        $_SESSION['msg'] = "Category deleted successfully!";
        header("Location: categories.php");
        exit();

    } catch(PDOException $e) {
        $_SESSION['msg'] = "Error deleting category: " . $e->getMessage();
        header("Location: categories.php");
        exit();
    }
?>
