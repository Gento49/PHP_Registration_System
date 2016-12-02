<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 
if ($_POST['token'] != $_SESSION['token']){
	echo "There's a possibility of unauthorized access";
	exit();
}
 

header('X-FRAME-OPTIONS: SAMEORIGIN');
 

require_once("db.php");
$dbh = db_connect();
 

$errors = array();
 
if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}
 
$mail = $_SESSION['mail'];
$account = $_SESSION['account'];
 
//Hash
$password_hash =  password_hash($_SESSION['password'], PASSWORD_DEFAULT);
 

try{
	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$dbh->beginTransaction();
	
	
	$statement = $dbh->prepare("INSERT INTO member (account,mail,password) VALUES (:account,:mail,:password_hash)");
	
	$statement->bindValue(':account', $account, PDO::PARAM_STR);
	$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
	$statement->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	$statement->execute();
		
	
	$statement = $dbh->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
	
	$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
	$statement->execute();
	
	
	$dbh->commit();
		
	
	$dbh = null;
	
	
	$_SESSION = array();
	
	
	if (isset($_COOKIE["PHPSESSID"])) {
    		setcookie("PHPSESSID", '', time() - 1800, '/');
	}
	
 	
 	session_destroy();
 	
 	
	
}catch (PDOException $e){
	
	$dbh->rollBack();
	$errors['error'] = "Try Again";
	print('Error:'.$e->getMessage());
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Member registration complete</title>
<meta charset="utf-8">
</head>
<body>
 
<?php if (count($errors) === 0): ?>
<h1>Member registration complete</h1>
 
<p>Registration is completed. Please move to login page.</p>
<p><a href="">Login</a></p>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<?php endif; ?>
 
</body>
</html>