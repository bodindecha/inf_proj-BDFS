<?php
    session_start();
	header("Access-Control-Allow-Origin: https://pathwayspeechcontest.cf");
    if (!function_exists("pvlog")) { function pvlog($iUrl, $iRemark="", $forced=false) {
		// Connect
        $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
		require($dirPWroot."resource/php/core/db_connect.php");
		require($dirPWroot."resource/php/core/getip.php");
		// Clean data
		$dExor = $_SESSION['auth']['user'] ?? "guest";
		$dUrl = trim(strval($iUrl)); try { $dUrl = $db -> real_escape_string($dUrl); } catch(Exception$e){}
		$dRemark = trim(strval($iRemark)); try { $dRemark = $db -> real_escape_string($dRemark); } catch(Exception$e){}
		$dSessid = trim(strval($_POST['psid']??(session_id()??($_COOKIE['PHPSESSID']??"")))); try { $dSessid = $db -> real_escape_string($dSessid); } catch(Exception$e){}
		// Filter user
		$avoid_user = array(/*"34216", "42629",*/ "99999", "99998", "99997", /*"TianTcl",*/ /*"test01",*/ "test02");
		if (!$forced && in_array($dExor, $avoid_user)) return false;
		// Record em
		$success = $db -> query("INSERT INTO log_pageview (exor,url,remark,sessid,ip) VALUES ('$dExor','$dUrl','$dRemark','$dSessid','$ip')");
		// Close connection
		$db -> close();
		// Returns status (bool)
		return $success;
	} }
    // Prepare
    $url = $_POST['url'] ?? ""; // $_SERVER['REQUEST_URI'] if in file;
    if (empty($url)) $url = ltrim($_SERVER['HTTP_REFERER'], "https://".$_SERVER['SERVER_NAME']);
    # if (empty($url)) $url = rtrim($_SERVER['SCRIPT_URI'], ".php").$_SERVER['QUERY_STRING'];
    if (!empty($url)) pvlog($url);
	# echo '{"url": '.json_encode($url).'}';
?>