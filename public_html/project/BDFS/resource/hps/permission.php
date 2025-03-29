<?php
    if (!isset($dirPWroot)) $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    if (!isset($_SESSION)) session_start();
    if (!function_exists("getProjectPermission")) { function getProjectPermission($stdStaff = true) {
        global $dirPWroot, $self;
        if (!isset($_SESSION["auth"])) return false;
        else if (!isset($self) || empty($self)) $self = $_SESSION["auth"]["user"] ?? "";
        /* if (has_perm("BDFS")) return true;
        else if (!$stdStaff) return false; */
        if (in_array($self, array("TianTcl", "test02", "99999"))) return true;
        require($dirPWroot."resource/php/core/db_connect.php");
        $getperm = $db -> query("SELECT refID,active FROM TrashBank_staff WHERE idcode='$self'".($stdStaff?"":" AND idcode NOT RLIKE '[0-9]{5}'"));
        $db -> close();
        if (!$getperm || $getperm -> num_rows <> 1) return false;
        $readperm = $getperm -> fetch_array(MYSQLI_ASSOC);
        return ($readperm["active"]=="Y");
    } } $has_perm = getProjectPermission();
?>