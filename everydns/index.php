<?php
/***********************************************/
/* All Code Copyright 2004 Matthew Frederico   */
/* Under the GPL License                       */
/***********************************************/
/* Author: matt@ultrize.com                    */
/* Url   : http://www.ultrize.com/everydns     */
/***********************************************/

ob_start('ob_gzhandler');
session_save_path(getcwd()."/sessions");
session_start();

include("config.php");
include($config['include_dir']."Smarty.class.php");
include($config['include_dir']."everyDns.class.php");

$dns = new EveryDNS($config);

$X = new Smarty();
$X->template_dir    =  $config['template_dir'];
$X->compile_dir     =  $config['template_dir']."templates_c";

if (isset($_REQUEST['msg'])) $X->assign('msg',base64_decode($_REQUEST['msg']));

$X->assign('config',$config);

if (isset($_REQUEST['action']))
{
	if ($_REQUEST['action'] == 'login' && !$_SESSION['cookies'])
	{
		$_SESSION['cookies'] = '';
		include("actions/login.php");
	}
}

if (isset($_SESSION['cookies']))
{
	if (!isset($_REQUEST['page']) && !isset($_REQUEST['action'])) $_REQUEST['page']='showDns';

	if (isset($_REQUEST['page']))
	{
		//...... Set up menu system
		$menuitem['showDns']		= 'DOMAINS';

		$thisPage = basename($_REQUEST['page']);
		//...... Basename keeps us out of path-hack harm
		if (file_exists($config['page_dir']."$thisPage.php")) include($config['page_dir']."$thisPage.php");
		if (!isset($_REQUEST['nohead'])) 
		{
			//...... Menu.php is in the include directory
			include($config['include_dir']."menu.php");
			$X->display("menu.html");
		}
		//...... Watch this one .. It can lead to "blank" pages?
		if (file_exists($config['template_dir'].$_REQUEST['page'].".html"))
		{	
			$X->display($_REQUEST['page'].".html");
		}
		else $X->display('blank.html');
		if (!isset($_REQUEST['nohead'])) 
		{
			$X->display("footer.html");
		}
	}
	else
	{
		include($config['action_dir'].basename($_REQUEST['action']).".php");
	}
}
else
{
	$X->display('login.html');
}

?>
