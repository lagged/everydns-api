<?php

$result = $dns->getDomains();

$_SESSION['domains'] = $result;
$X->assign('domains',$result);

?>
