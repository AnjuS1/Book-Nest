<?php
session_start();
require '../includes/db_connect.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['status'=>'error','msg'=>'Access denied.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$fine_id = intval($_POST['fine_id'] ?? 0);
$card_number = trim($_POST['card_number'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$cvv = trim($_POST['cvv'] ?? '');
$errors = [];

// Card number: 16 digits
if(!preg_match('/^\d{16}$/', $card_number)){
    $errors[] = "Card number must be 16 digits.";
}

// CVV: 3 digits
if(!preg_match('/^\d{3}$/', $cvv)){
    $errors[] = "CVV must be 3 digits.";
}

// Expiry: MM/YY format
if(!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)){
    $errors[] = "Invalid expiry format (MM/YY).";
} else {
    // Check expiry is in the future
    $parts = explode('/', $expiry);
    $expMonth = intval($parts[0]);
    $expYear = intval('20' . $parts[1]); 

    $currentYear = intval(date('Y'));
    $currentMonth = intval(date('m'));

    if($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)){
        $errors[] = "Invalid Details";
    }
}

if($errors){
    echo json_encode(['status'=>'error','msg'=>implode(' ', $errors)]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.fine_id
    FROM fines f
    JOIN members m ON f.member_id = m.member_id
    WHERE f.fine_id = :fid AND m.user_id = :uid
");
$stmt->execute([':fid' => $fine_id, ':uid' => $user_id]);
$fine = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$fine){
    echo json_encode(['status'=>'error','msg'=>'Fine not found or unauthorized.']);
    exit;
}


$stmt = $pdo->prepare("UPDATE fines SET paid_status='Paid', paid_method='online' WHERE fine_id=:fid");
$stmt->execute([':fid' => $fine['fine_id']]);

echo json_encode(['status'=>'success','msg'=>'Fine paid successfully.']);
exit;
?>
