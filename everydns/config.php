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
$config['template_dir']	= 'templates/';
$config['page_dir']		= 'pages/';
$config['action_dir']	= 'actions/';
$config['include_dir']	= 'include/';


//Version Information
$config['version'] = "1.7";

/*************/
/* Curl Info */
/*************/
//..... If you feel the need ...
$config['edns_agent'] = "Mozilla/4.0 (EveryDNS PHP Api {$config['version']})";
?>
