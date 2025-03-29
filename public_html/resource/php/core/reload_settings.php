<?php
	if (!isset($dirPWroot)) $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    /* require($dirPWroot."resource/php/core/db_connect.php");
    $getsysinf = $db -> query("SELECT name,value FROM config_sys");
    if ($getsysinf -> num_rows >= 1) {
        if (isset($_SESSION['stif'])) unset($_SESSION['stif']); $_SESSION['stif'] = array();
        while ($getinf = $getsysinf -> fetch_assoc()) $_SESSION['stif'][$getinf['name']] = $getinf['value'];
        // Get spec data
        $grade = ($_SESSION['auth']['info']['grade'] ?? ($_SESSION['auth']['info']['grade'] ?? "")); if ($grade<>"") $grade = ", $grade";
        $room = ($_SESSION['auth']['info']['room'] ?? ($_SESSION['auth']['info']['room'] ?? "")); if ($room<>"") $room = ", $room";
        $getsepinf = $db -> query("SELECT name,value FROM config_sep WHERE year=".$_SESSION['stif']['t_year']." AND sem IN(0, ".$_SESSION['stif']['t_sem'].") AND grade IN(0$grade) AND room IN(0$room)");
        if ($getsepinf -> num_rows >= 1) {
            while ($readinf = $getsepinf -> fetch_assoc()) $_SESSION['stif'][$readinf['name']] = $readinf['value'];
        }
    } $db -> close(); */
?>