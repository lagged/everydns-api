#!/usr/bin/env php
<?php
/**
 * A script to export your records from EveryDNS to a CSV.
 *
 * @category API
 * @package  EveryDNS_Export
 * @author   Till Klampaeckel <till@lagged.biz>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @version  0.1.0
 * @link     http://www.lagged.biz/
 */

if (PHP_SAPI != 'cli') {
    echo "This is meant to run from the shell." . PHP_EOL;
    exit(1);
}

if ($_SERVER['argc'] != 3) {
    echo "Usage: ./" . basename(__FILE__) . " email@example.org password" . PHP_EOL;
    exit(2);
}

$username = $_SERVER['argv'][1];
$password = $_SERVER['argv'][2];

$root = dirname(__DIR__);

require_once $root . '/config.php';
require_once $config['include_dir'] . '/everyDns.class.php';

$dns = new EveryDNS($config);
$dns->login($username, $password);

/**
 * @desc This is the data from EveryDNS - I've omitted their record ID and md5hash
 */
$cols = array(
    //'rid', 'md5domain',
    'fqdn', 'rec', 'value', 'mx', 'ttl'
);

/**
 * @desc CSV header
 */
echo implode(";", $cols) . PHP_EOL;

$domains = $dns->getDomains();
foreach ($domains as $domainName => $did) {
    $records = $dns->getDomainRecords($did, $domainName);
    foreach ($records as $record) {
        /*foreach ($cols as $col) {
            echo '"' . $record[$col] . '";';
        }*/
        echo implode(';', array_slice($record, 2));
        echo PHP_EOL;
    }
}
