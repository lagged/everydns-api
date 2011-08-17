<?php

$dns->delRecord($_REQUEST['rid'],$_REQUEST['did'],$_REQUEST['domainName']);
header("Location: index.php?page=editDns&did={$_REQUEST['did']}&domainName={$_REQUEST['domainName']}");

?> 
