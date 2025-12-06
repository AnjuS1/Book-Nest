<?php
    session_start();
    require 'includes/db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link href="style.css" rel="stylesheet">
    <style>
        /* index */
        .index-body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('./Images/Library.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }

        .welcome-text h1 {
            font-size: 4em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
        }

        .welcome-text h2 {
            font-size: 2em;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }

        .welcome-text p {
            font-size: 1.2em;
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }

        .btn-index {
            padding: 15px 30px;
            margin: 10px;
            font-size: 1.2em;
            color: black;
            background-color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-index:hover {
            background-color: grey;
        }
    </style>
</head>
<body class="index-body">
    <div class="welcome-text">
        <h1>BOOK NEST</h1>
        <h2>Welcome to Our Library</h2>
        <p>Explore books, manage your account, and more!</p>
        <a href="includes/login.php" class="btn-index">Login</a>
        <a href="includes/register.php" class="btn-index">Register</a>
    </div>
</body>
</html>
