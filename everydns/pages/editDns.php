<?php

$domains = $dns->getDomainRecords($_GET['did'],$_GET['domainName']);

$X->assign('did',$_GET['did']);
$X->assign('domainName',$_GET['domainName']);
$X->assign('domains',$domains);

?>
