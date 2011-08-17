<?php
/************************************/
/* Default ip for new host creation */
/************************************/
$config['demo'] 		= 1;			// Set to 0 when using in production

//...... Default up address for your hosting
$config['hostIP'] 		= '127.0.0.1';

//...... Default DNS record time to live
$config['defaultTTL']	= '86400';


/**********************/
/* Path configuration */
/**********************/
$config['cookie_dir']	= ini_get('session.save_path');
$config['template_dir']	= __DIR__ . '/templates/';
$config['page_dir']		= __DIR__ . '/pages/';
$config['action_dir']	= __DIR__ . '/actions/';
$config['include_dir']	= __DIR__ . '/include/';


//Version Information
$config['version'] = "1.7";

/*************/
/* Curl Info */
/*************/
//..... If you feel the need ...
$config['edns_agent'] = "Mozilla/4.0 (EveryDNS PHP Api {$config['version']})";
?>
