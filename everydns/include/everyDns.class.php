<?php
########################################
# EveryDNS class v 1.5
# Don't forget to donate to everyDNS!
# http://www.everydns.com
########################################
#______________________________________
#
# (C) 2005 Ultrize Internet Technology
# Author : Matthew Frederico
# Contact: matt@ultrize.com
# URL    : http://www.ultrize.com
#______________________________________


class EveryDNS
{
	var $everyDns	= "http://www.everydns.com";
	var $recTypes	= array('A'=>1,'CNAME'=>2,'NS'=>3,'MX'=>4,'TXT'=>5);
	var $debug		= 0;
	var $config		= array();
	var $cookies	= '';
	var $page		= array();
	var $domains	= array();

	function __construct($config)
	{
		$this->config = array_merge($config,$this->config);	
	}

	function baseDomain($fqdn)
	{
		if (count(explode('.',$fqdn)) > 2) list(,$baseDomain) = explode('.',$fqdn,2); else $baseDomain = $fqdn;
		return($baseDomain);
	}

	function delDomain($did)
	{
		//...... Simulate delete clicked
		$params = "action=delDomain&did=$did";
		$info = $this->curl_string ($this->everyDns."/dns.php",$this->config['edns_agent'],$params);

		//...... Confirm deletion
		$params = "action=confDomain&deldid=$did";
		$info = $this->curl_string ($this->everyDns."/dns.php",$this->config['edns_agent'],$params);
	}

	function delRecord($rid,$did,$md5domain)
	{
		$params = "action=deleteRec&rid={$rid}&{$md5domain}&did={$did}";
		$info = $this->curl_string ($this->everyDns."/dns.php",$this->config['edns_agent'],$params);
	}

	function addRecord($did,$domainName,$fqdn,$recordType,$value,$mx,$ttl)
	{
		$params = "action=addRecord&did=".urlencode($did)."&domain=".urlencode($domainName)."&field1=".urlencode($fqdn)."&type=".$this->recTypes[$recordType]."&field2=".urlencode($value)."&mxVal={$mx}&ttl={$ttl}&Submit=Add+Record";
		$info = $this->curl_string ($this->everyDns."/dns.php",$this->config['edns_agent'],$params);
	}

	function modifyRecord($did,$recordData)
	{

		$this->delRecord($recordData['rid'],$did,$recordData['md5domain']);
		$this->addRecord($did,$this->baseDomain($recordData['fqdn']),$recordData['fqdn'],$recordData['rec'],$recordData['value'],$recordData['mx'],$recordData['ttl']);
	}
	
	function getRecordInfo($records,$fqdn,$recType = 'A')
	{
		foreach($records as $idx=>$recordVals)
			if ($recordVals['fqdn'] == $fqdn && $recordVals['rec'] == strtoupper($recType)) return($recordVals);
	}

	function getDomainRecords($did,$domainName)
	{
		if (!$did) 
		{
			if ($this->debug) print "* Cannot get domain records for $domainName -> Unspecified 'id'\n";
			return(0);
		}

		$domain = array();
		
		$params = "action=editDomain&did={$did}&domain=".base64_encode($domainName);
		$info = $this->curl_string ($this->everyDns."/manage.php",$this->config['edns_agent'],$params);

		//...... Get the records
		preg_match_all("~<td><div align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">(.*?)</font></div></td>~",$info['content'],$matches);

		//...... Get the deletion data
		preg_match_all("~\[<a href=\"\./dns\.php\?action=deleteRec&rid=(.*?)&domain=(.*?)&did=(.*?)\">delete</a>\]~",$info['content'],$delete);

		unset($delete[0]);

		$dom =0;
		for ($x = 0;$x < count($matches[1]);$x++)
		{
			$domain[$dom]['rid']		= $delete[1][$dom];
			$domain[$dom]['md5domain']  = $delete[2][$dom];

			$domain[$dom]['fqdn']		= $matches[1][$x++];
			$domain[$dom]['rec']		= $matches[1][$x++];
			$domain[$dom]['value']		= $matches[1][$x++];
			$domain[$dom]['mx']			= $matches[1][$x++];
			$domain[$dom]['ttl']		= $matches[1][$x];
			$dom++;
		}
		return($domain);
	}

	function login($user,$pass)
	{
		$params = "action=login&username={$user}&password={$pass}";
		$this->page = $this->curl_string($this->everyDns."/account.php",$this->config['edns_agent'],$params);
		
		//...... Check for invalid login
		if (preg_match_all("/Login failed, try again!/",$this->page['content'],$domains))
		{
			$this->cookies = '';
			return('');
		}
		else
		{
			$this->cookies = $this->page['cookies'];
			return($this->page['content']);
		}
	}

	function getDomains($domainPage= '')
	{
		//...... Get domain list
		$params = "";
		if (!strlen($domainPage))
			$this->page = $this->curl_string($this->everyDns."/manage.php",$this->config['edns_agent'],$params);
		else 
		{
			$this->page['content'] = $domainPage;
		}

		if ($this->debug) print "****************\n{$this->page['content']}\n****************\n\n";

		preg_match_all("/<a href=\"\.\/dns\.php\?action=editDomain\&did=(.*?)\">(.*?)<\/a>/",$this->page['content'],$domains);

		$num_domains = count($domains[2]);
		$domain = array();

		for ($d = 0;$d < $num_domains;$d++)
		{
			$domain[$d]['id']		= $domains[1][$d];
			$domain[$d]['domain']	= $domains[2][$d];
			$this->domains["{$domains[2][$d]}"] = $domains[1][$d];
		}
		$this->cookies = $this->page['cookies'];
		return($this->domains);
	}

	function addDomain($newDomain)
	{
		//...... Add the domain
		$params = "action=addDomain&newdomain=$newDomain";
		$info = $this->curl_string($this->everyDns."/dns.php",$this->config['edns_agent'],$params,1);

		//...... Get all the domains
		$result = $this->getDomains();

		//...... Search through the domains to get correct id and domanName
		foreach($result['domains'] as $domain)
		{
			$id         = $domain['id'];
			$domainName = $domain['domain'];
			if ($domain['domain'] == $newDomain) break;
		}

		//...... Get the records for this domain
		$domains = $this->getDomainRecords($id,$newDomain);

		//......  Flag to Make sure we have a new domain
		$newdomain = 0;

		//...... Check to see if we have default everyDns settings
		foreach($domains as $data)
		{
			if (preg_match("/64.158.219.4|parked.everyDns.net/",$data['value']) || $data['value'] == '') $newdomain = 1;
		}

		//...... Set up standard default domain information before we delete parking stuff
		if ($newdomain)
		{
			//...... Set up defaults for generic mail, and www domains
			$this->setDefaults($newDomain,$id);
		}

		//...... Go through domains to find delete parking stuff
		foreach($domains as $data)
		{
			if($newdomain)
			{
				//...... Delete the everyDns parking stuff
				$this->delRecord($data['rid'],$id);
			}
		}

		return($id);
	}

	//...... Sets the default domain information
	function setDefaults($domainName,$id)
	{
			$this->addRecord($id,$domainName,"*.$domainName",    '1', $this->config['hostIP'], '',$this->config['defaultTTL']);
			$this->addRecord($id,$domainName,"mail.$domainName", '1', $this->config['hostIP'], '',$this->config['defaultTTL']);
			$this->addRecord($id,$domainName,"$domainName",      '1', $this->config['hostIP'], '',$this->config['defaultTTL']);
			$this->addRecord($id,$domainName,"$domainName",     '4', "mail.$domainName", '10',$this->config['defaultTTL']);
	}

	//...... Craft a correct url for everyDNS
	function curl_string ($url,$user_agent,$params,$post=0)
	{
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_REFERER,$this->everyDns);
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$params);
		//if ($post) curl_setopt ($ch, CURLOPT_POST,($post));
		curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->config['cookie_dir']."cookie.txt");
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->config['cookie_dir']."cookie.txt");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
		
		$result = curl_exec ($ch);
		if ($this->debug)
		{
			print "\n$url?$params\n=========================\n<br />";
			print_r($result);
			print "\n<br />$url?$params\n=========================\n<br />";
		}

		if (preg_match('~<b>Logged out</b>~',$result)) 
		{
			print "* Client logged out - please re-login\n";
			return(0);
		}
		$cookies = '';

		curl_close($ch);
		return array('cookies'=>$cookies,'content'=>$result);
	}

	function setDebug($debugMode)
	{
		$this->debug = $debugMode;
	}
}
?>
