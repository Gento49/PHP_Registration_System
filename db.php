<?php
 
function db_connect(){
	$dsn = 'mysql:host=hoge;dbname=hoge;charset=utf8';
	$user = 'hoge';
	$password = 'hoge';
	
	try{
		$dbh = new PDO($dsn, $user, $password);
		return $dbh;
	}catch (PDOException $e){
	    	print('Error:'.$e->getMessage());
	    	die();
	}
}
 
?>