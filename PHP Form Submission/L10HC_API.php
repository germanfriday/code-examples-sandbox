<?php
// L10HC_API - v1.0 rel 1 (12/5/03)
// requirements for session data - 
//	1. LevelTen Hit Counter PHP v3.15 rel 3
//

if($_GET['acct'] != '') {
	$acct = $_GET['acct'];	
} else {
	$acct = '.';
}
$acct = $dataDir.$acct;
$acctCk = ($acct == '') ? '' : $acct;

$acct = "."; //temp

if(!file_exists("$acct/L10HC_AcctParams.php")) {
	print "$pageHdr<p class=err>LevelTen Hit Counter not found!</p> A working LevelTen Hit Counter was not found by L10HC_API.php. You must either install the LevelTen Hit Counter, (<a href='http://www.leveltendesign.com/L10Apps/HC/download.php?hct=L10Fm-ErrMsg' target=_blank>download it here</a>) or remove the L10HC_API.php file from the directory.</a><p>For help on this issue, see <a href='http://www.leveltendesign.com/L10Apps/Fm/help_troubleshooting.php#L10HCNotFound?hct=L10Fm-ErrMsg' target=_blank>LevelTen Formmail troubleshooting</a> $pageFtr";
	exit;
}

include_once("$acct/L10HC_AcctParams.php");
include_once("L10HC_Params.php");
include_once("L10_LIB.php");
include_once("L10_LIB_DB.php");

function getVID($acct='') {
	global $vID,$trackByIP,$acctCk;
	
	$acctCk = $acct;
	
	include_once("L10HC_LIB.php");
	
	$vID = 0;
	
	$vIP = getenv("REMOTE_ADDR");
	$vKey = $vIP;
	$trackByIP = 1;
	parseCookies();
	
	if($trackByIP > 0) {
		$sql = "SELECT * FROM L10HC_recentVisits where vKey=\"$vKey\"";		
		$result = db_query($sql,__FILE__,__LINE__);
	
		$rVData = db_fetch_array($result,__FILE__,__LINE__);
		if($rVData) {
			$vID = $rVData['vID'];
		}
	}
	
	return $vID;
}

function getSessions($vID,$iCnt,$secured,$format) {
	global $password,$reporterScriptURL;
	
	
	$params = array( 
		vID => $vID,
		iCnt => $iCnt,
		secured => $secured,
		format => $format,
		formatFrame => 'ret',
		full => 1,
	);
	
	$API = 1;
	include_once("L10HC_Admin.php");
	return sessionReport($params);
	
}

?>