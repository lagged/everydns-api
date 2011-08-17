<?php

set_time_limit(0);
if (count(explode(".",$_REQUEST["originalIP"])) != 4) {
    trigger_error("Original IP ".$_REQUEST["originalIP"] . " is not a valid IP - if it is an IP Stem it must end with a period.",E_USER_ERROR);
    exit();
}
if (count(explode(".",$_REQUEST["newIP"])) != 4) {
    trigger_error("Replacement IP ". $_REQUEST["newIP"] .  " is not a valid IP - if it is an IP Stem it must end with a period.",E_USER_ERROR);
    exit();
}
$domains = $dns->getDomainRecords($_REQUEST['did'],$_REQUEST['domainName']);
$list = array();

foreach($domains as $domain) {
    if (substr($_REQUEST["originalIP"],-1,1) == "."
        && substr($domain["value"],0,strlen($_REQUEST["originalIP"])) == $_REQUEST["originalIP"]) {
        //IP Stem
        $domain["newValue"] = $_REQUEST["newIP"] . substr($domain["value"],strlen($_REQUEST["originalIP"]),strlen($domain["value"]));
        $list[] = $domain;

    }
    else if ($domain["value"] == $_REQUEST["originalIP"]) {
        //Single IP
        $domain["newValue"] = $_REQUEST["newIP"];
        $list[] = $domain;
    }
}

foreach($list as $domain) 
{
    switch($domain["rec"]) {
        case "A":
        default:
            $domain["record"] = 1;
            break;
        case "CNAME":
        default:
            $domain["record"] = 2;
            break;
        case "NS":
        default:
            $domain["record"] = 3;
            break;
        case "MX":
        default:
            $domain["record"] = 4;
            break;
    }
    if ($domain["mx"] == "N/A") {
        $domain["mx"] = "";
    }
    $dns->addRecord($_REQUEST['did'],
            $_REQUEST['domainName'],
            $domain['fqdn'],
            $domain['record'],
            $domain['newValue'],	
            $domain['mx'],
            $domain['ttl']);

    $dns->delRecord($domain['rid'],$_REQUEST['did'],$domain['md5domain']);
}

header("Location: index.php?page=editDns&did={$_REQUEST['did']}&domainName={$_REQUEST['domainName']}");

?> 
