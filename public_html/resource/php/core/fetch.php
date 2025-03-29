<?php
	session_start();
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    header("Access-Control-Allow-Origin: https://pathwayspeechcontest.cf");
	if (isset($_REQUEST['app']) && isset($_REQUEST['cmd'])) {
        $app = $_REQUEST['app']; $cmd = $_REQUEST['cmd']; $attr = $_REQUEST['attr'] ?? "";
        require($dirPWroot."resource/php/core/db_connect.php");
        if ($app == "fs" && isset($_REQUEST['q'])) {
            $q = $db -> real_escape_string(trim($_REQUEST['q']));
            if ($cmd == "address") {
                $database = "schoollist"; require($dirPWroot."m/sync/db_connect.php"); unset($database); // $rdb = connect_to_reg("schoollist");
                $sql = "SELECT a.refID AS subdistrictI, a.nameth AS subdistrictN, b.refID AS districtI, b.nameth AS districtN, c.refID AS provinceI, c.nameth AS provinceN FROM subdistrict a LEFT JOIN district b ON a.district=b.refID LEFT JOIN province c ON a.province=c.refID WHERE a.nameth LIKE '$q%' OR b.nameth LIKE '$q%' OR b.nameen LIKE '$q%' OR c.nameth LIKE '$q%' OR c.nameen LIKE '$q%' LIMIT 50";
                $result = $rdb -> query($sql);
                $matchset = array(); if ($result && $result -> num_rows > 0) {
                    while ($addr = $result -> fetch_assoc()) array_push($matchset, $addr);
                } echo '{"success": true, "info": '.json_encode($matchset).'}';
                $rdb -> close();
            }
        } $db -> close();
    }
?>