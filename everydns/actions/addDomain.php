<?php

$did = $dns->addDomain($_REQUEST[newDomain]);
header("Location: index.php?page=editDns&did=$did&domainName=$_REQUEST[newDomain]");

?> 
