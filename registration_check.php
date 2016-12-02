<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 

if ($_POST['token'] != $_SESSION['token']){
	echo "There's a possibility of unauthorized access";
	exit();
}
 

header('X-FRAME-OPTIONS: SAMEORIGIN');
 

function spaceTrim ($str) {
	// Beginning of String
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// End of String
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}
 

$errors = array();
 
if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}else{
	
	$account = isset($_POST['account']) ? $_POST['account'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;
	
	
	$account = spaceTrim($account);
	$password = spaceTrim($password);
 
	
	if ($account == ''):
		$errors['account'] = "The Account has not been entered.";
	elseif(mb_strlen($account)>10):
		$errors['account_length'] = "The Account can not exceed 10 characters.";
	endif;
	
	
	if ($password == ''):
		$errors['password'] = "Password has not been entered.";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password"])):
		$errors['password_length'] = "Please enter a password of at least 5 letters and less than 30 letters of alphanumeric characters.";
	else:
		$password_hide = str_repeat('*', strlen($password));
	endif;
	
}
 
//If there is no error, then register in session
if(count($errors) === 0){
	$_SESSION['account'] = $account;
	$_SESSION['password'] = $password;
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Member registration confirmation</title>
<meta charset="utf-8">
</head>
<body>
<h1>Member registration confirmation</h1>
 
<?php if (count($errors) === 0): ?>
 
 
<form action="registration_complete.php" method="post">
 
<p>MailAddress：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
<p>Account：<?=htmlspecialchars($account, ENT_QUOTES)?></p>
<p>Password：<?=$password_hide?></p>
 
<input type="button" value="Back" onClick="history.back()">
<input type="hidden" name="token" value="<?=$_POST['token']?>">
<input type="submit" value="Register">
 
</form>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<input type="button" value="Back" onClick="history.back()">
 
<?php endif; ?>
 
</body>
</html>