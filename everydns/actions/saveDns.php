<?php

$dns->addRecord($_REQUEST[did],
		$_REQUEST[domainName],
		$_REQUEST[fqdn],
		$_REQUEST[record],
		$_REQUEST[value],	
		$_REQUEST[mx],
		$_REQUEST[ttl]);

header("Location: index.php?page=editDns&did=$_REQUEST[did]&domainName=$_REQUEST[domainName]");

?> 
