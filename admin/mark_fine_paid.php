<?php
session_start();
require '../includes/db_connect.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if(isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("UPDATE fines SET paid_status='Paid', paid_method='offline' WHERE fine_id=:id");
    try{
        $stmt->execute([':id'=>$id]);
        echo json_encode(['success' => true, 'paid_status' => 'Paid offline']);
    }catch(PDOException $e){
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Fine ID missing']);
}
?>
