<?php

$_SESSION['cookies'] = $dns->login($_REQUEST[user],$_REQUEST[pass]);

if (!$_SESSION['cookies'])
{
	unset($_SESSION);
	$msg = "?msg=".base64_encode("Invalid Login Attempted");
}

header("Location: index.php".$msg);

?>
