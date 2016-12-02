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
}else{
	
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
	
	
	if ($mail == ''){
		$errors['mail'] = "Mail is not entered.";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "The format of the e-mail address is incorrect.";
		}
		
		
	}
}
 
if (count($errors) === 0){
	
	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "http://◯◯◯.co.jp/registration_form.php"."?urltoken=".$urltoken;
	
	
	try{
		
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$statement = $dbh->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");
		
		
		$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
		$statement->execute();
			
		
		$dbh = null;	
		
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	
	//a destination mail
	$mailTo = $mail;
 
	
	$returnMail = 'test@test.com';
 
	$name = "Test";
	$mail = 'test@test.com';
	$subject = "Test";
 
$body = <<< EOM
Please register from the URL below within 24 hours.
{$url}
EOM;
 
	mb_language('ja');
	mb_internal_encoding('UTF-8');
 
	//Create a From header
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
 
	if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
	
	 	//Release all session variables
		$_SESSION = array();
	
		//Deleting cookies
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}
	
 		//Session Destroy
 		session_destroy();
 	
 		$message = "I sent you an email. Please register from the URL described in the email within 24 hours.";
 	
	 } else {
		$errors['mail_error'] = "Failed to send mail.";
	}	
}
 
?>
 
<!DOCTYPE html>
<html>
<head>
<title>Confirm Mail</title>
<meta charset="utf-8">
</head>
<body>
<h1>Confilm Mail</h1>
 
<?php if (count($errors) === 0): ?>
 
<p><?=$message?></p>
 
<p>You will receive an email with this URL.</p>
<a href="<?=$url?>"><?=$url?></a>
 
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