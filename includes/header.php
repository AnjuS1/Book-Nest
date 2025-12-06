<?php
  if(!isset($_SESSION['user_id'])){
      header("Location: ../index.php");
      exit;
  }
  $user_name = $_SESSION['name'];
  $role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html>
  <head>
      <title>Book Nest</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="../css/styles.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
      <div class="container-fluid">
        <h1>Book Nest</h1>
        <div class="collapse navbar-collapse justify-content-end">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="btn btn-danger" href="/library/includes/logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container">
  </body>
</html>
