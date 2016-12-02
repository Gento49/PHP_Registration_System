<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 

header('X-FRAME-OPTIONS: SAMEORIGIN');
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Mail Registration Form</title>
<meta charset="utf-8">
</head>
<body>
<h1>Mail Registration Form</h1>
 
<form action="registration_mail_check.php" method="post">
 
<p>MailAddress：<input type="text" name="mail" size="50"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="Register">
 
</form>
 
</body>
</html>