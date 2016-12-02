<?php
session_start();
 
header("Content-type: text/html; charset=utf-8");
 

$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
 

header('X-FRAME-OPTIONS: SAMEORIGIN');
 

require_once("db.php");
$dbh = db_connect();
 

$errors = array();
 
if(empty($_GET)) {
	header("Location: registration_mail_form.php");
	exit();
}else{
	
	$urltoken = isset($_GET[urltoken]) ? $_GET[urltoken] : NULL;
	
	if ($urltoken == ''){
		$errors['urltoken'] = "Try Again";
	}else{
		try{
			
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			//Unregistered person whose flag is 0 · Within 24 hours from the temporary registration date
			$statement = $dbh->prepare("SELECT mail FROM pre_member WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
			$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$statement->execute();
			
			//Retrieve number of records
			$row_count = $statement->rowCount();
			
			//In the case of a token which is provisionally registered within 24 hours and has not been registered
			if( $row_count ==1){
				$mail_array = $statement->fetch();
				$mail = $mail_array[mail];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover'] = "This URL is not available. There is a problem such as the expiration date has passed. Please register again once again.";
			}
			
			//Database connection disconnected
			$dbh = null;
			
		}catch (PDOException $e){
			print('Error:'.$e->getMessage());
			die();
		}
	}
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Member registration</title>
<meta charset="utf-8">
</head>
<body>
<h1>Member registration</h1>
 
<?php if (count($errors) === 0): ?>
 
<form action="registration_check.php" method="post">
 
<p>MailAddress：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
<p>Account：<input type="text" name="account"></p>
<p>Password：<input type="text" name="password"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="Confirm">
 
</form>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<?php endif; ?>
 
</body>
</html>