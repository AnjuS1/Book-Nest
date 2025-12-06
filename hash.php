<?php
$password = 'admin'; // the plain password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
