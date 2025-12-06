<?php
    session_start();
    require '../includes/db_connect.php';
    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ die("Access denied."); }

    if(isset($_POST['id'])){
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM fines WHERE fine_id=:id");
        try{
            $stmt->execute([':id'=>$id]);
        }catch(PDOException $e){
            die("Error: ".$e->getMessage());
        }
    }
?>
