<?php

$dns->setDefaults($_REQUEST['domain'],$_REQUEST['did']);
header("Location: index.php?page=editDns&did=$_REQUEST[did]&domainName=$_REQUEST[domain]");

?> 
