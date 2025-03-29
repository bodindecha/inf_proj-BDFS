<?php
	session_start();
    // Define functions
    function unauth($end = true, $ssoonly = false, $attr = "", $auto = false, $user = "") {
        if (!$ssoonly && isset($_SESSION['auth'])) unset($_SESSION['auth']); // authorized information
        if (!$ssoonly && isset($_SESSION['stif'])) unset($_SESSION['stif']); // static system infos
        if ($end) echo '{"success": true}';
    }
    function auther($reauth = false, $sso = false) {
        require("../core/db_connect.php");
        if (isset($_POST['failed'])) {
            switch (intval(trim($_POST['zone']))) {
                case 0: $zone = "s"; break;
                case 1: $zone = "t"; break;
                case 2: $zone = "t"; break;
                case 3: $zone = "t"; break;
                default: $zone = "undefined"; break;
            } $user = strval($db -> real_escape_string(trim($_POST['username'])));
            // log failed attemps
            slog("account", "login", $zone, $user, "fail", "", "Incorrect");
        } else {
            if (!$reauth) unauth(false, false, "", true);
            if (isset($_POST['username']) || $reauth) {
                if (isset($_POST['zone']) || $reauth) {
                    if ($reauth) $_POST['zone'] = ($_SESSION['auth']['type']=="s" ? 0 : 1);
                    switch (intval(trim($_POST['zone']))) {
                        case 0: $zone = "s"; $userfield = "stdid"; break;
                        case 1: $zone = "t"; $userfield = "namecode"; break;
                        case 2: $zone = "t"; $userfield = "namecode"; break;
                        case 3: $zone = "t"; $userfield = "namecode"; break;
                        default: $zone = "undefined"; $userfield = "undefined"; break;
                    } if ($zone<>"undefined" && $userfield<>"undefined") {
                        $user = ($reauth ? $_SESSION['auth']['user'] : strval($db -> real_escape_string(trim($_POST['username']))));
                        if ($user<>"") {
                            $userdat = $db -> query("SELECT * FROM user_$zone WHERE $userfield='$user'");
                            if ($userdat) {
                                if ($userdat -> num_rows == 1) {
                                    // Get system information
                                    require_once("../core/reload_settings.php");
                                    require_once("../core/config.php");
                                    // Save login information
                                    require("../core/db_connect.php");
                                    // Get user data
                                    $getdat = $userdat -> fetch_array(MYSQLI_ASSOC);
                                    // Check account status
                                    if ($getdat['status']=="A") {
                                        // Update prefix by age
                                        $namep = prefixcode2text($getdat['namep']);
                                        $_SESSION['auth'] = array(
                                            "type" => $zone,
                                            "user" => $user,
                                            "name" => array(
                                                "en" => array(
                                                    "p" => $namep['en'],
                                                    "f" => $getdat['namefen'],
                                                    "l" => $getdat['namelen'],
                                                    "a" => $namep['en']." ".$getdat['namefen']." ".$getdat['namelen']
                                                ), "th" => array(
                                                    "p" => $namep['th'],
                                                    "f" => $getdat['namefth'],
                                                    "l" => $getdat['namelth'],
                                                    "a" => $namep['th'].$getdat['namefth']."  ".$getdat['namelth']
                                                )
                                            )
                                        );
                                        if ($zone=="s") {
                                            $_SESSION['auth']['name']['en']['n'] = $getdat['namenen'];
                                            $_SESSION['auth']['name']['th']['n'] = $getdat['namenth'];
                                            $_SESSION['auth']['info'] = array(
                                                "birth" => array("y" => intval($getdat['birthy']), "m" => intval($getdat['birthm']), "d" => intval($getdat['birthd'])),
                                                "grade" => gen2grade($getdat['gen']), "room" => $getdat['room'], "number" => $getdat['number'], "gen" => $getdat['gen']
                                            );
                                            if (!$sso && isset($_POST['password'])) {
                                                $getqebjffnc = $db -> query("SELECT qebjffnc FROM user_$zone WHERE $userfield='$user'");
                                                if ($getqebjffnc -> num_rows == 1) {
                                                    $qebjffncinf = $getqebjffnc -> fetch_array(MYSQLI_ASSOC); $qebjffncold = end(explode(">", $qebjffncinf['qebjffnc']));
                                                    $qebjffncnew = strval($db -> real_escape_string(bin2hex(trim($_POST['password']))));
                                                    if ($qebjffncnew<>$qebjffncold) $db -> query("UPDATE user_$zone SET qebjffnc='".($qebjffncold<>""?$qebjffncinf['qebjffnc'].">":"")."$qebjffncnew' WHERE $userfield='$user'");
                                            } }
                                        } else if ($zone=="t") {
                                            $_SESSION['auth']['level'] = intval($getdat['authlvl']);
                                            $_SESSION['auth']['info'] = array(
                                                "projcode" => $getdat['proj'],
                                                "project" => projcode2name($getdat['proj']),
                                                "subjcode" => $getdat['subj'],
                                                "subject" => subjcode2name($getdat['subj']),
                                                "avatar" => $getdat['avatar']
                                            ); $_SESSION['auth']['perm'] = explode(";", $getdat['perm']);
                                            $_SESSION['auth']['attr'] = array(); if (count(explode("&", $getdat['attr']))>0) foreach (explode("&", $getdat['attr']) as $attrs) {
                                                $attr = explode("=", $attrs);
                                                if (count($attr)==2) $_SESSION['auth']['attr'][$attr[0]] = explode(",", $attr[1]);
                                            } $_SESSION['auth']['tag'] = explode(",", $getdat['tag']);
                                            $homeroom = $db -> query("SELECT grade,room FROM dat_homeroom WHERE year=".$_SESSION['stif']['t_year']." AND '$user' IN(tchr1, tchr2) ORDER BY sem");
                                            $hrrc = $homeroom -> num_rows; if ($hrrc >= 1) {
                                                if ($hrrc > 1) mysqli_data_seek($homeroom, $hrrc-1);
                                                $roomadv = $homeroom -> fetch_array(MYSQLI_ASSOC);
                                                $_SESSION['auth']['info']["grade"] = $roomadv['grade'];
                                                $_SESSION['auth']['info']["room"] = $roomadv['room'];
                                            } $_SESSION['auth']['qebjffnc'] = bin2hex(trim($_POST['password']));
                                        }
                                        if ($reauth) header("Location: /".$_GET['return_url']);
                                        else slog("account", "login", $zone, $user, "pass", $sso?"sso":"");
                                    } echo '{"success": true, "reason": ""}';
                                } else { echo '{"success": false, "reason": [1, "Your account is '.statuscode2text($getdat['status'])['en'].'<br>บัญชีของคุณถูก'.statuscode2text($getdat['status'])['th'].'"]}'; if(!$reauth)slog("account", "login", $zone, $user, "fail", $sso?"sso":"", statuscode2text($getdat['status'])['en']); }
                            } else { echo '{"success": false, "reason": [1, "You are not a user of this site."]}'; if(!$reauth)slog("account", "login", $zone, $user, "fail", $sso?"sso":"", "NotExisted"); } // No record of this user found
                        } else { echo '{"success": false, "reason": [3, "Unable to get user\'s data."]}'; if(!$reauth)slog("account", "login", $zone, $user, "fail", $sso?"sso":"", "InvalidQuery"); }
                    } else { echo '{"success": false, "reason": [3, "Username empty."]}'; if(!$reauth)slog("account", "login", $zone, $user, "fail", $sso?"sso":"", "NotEligible"); } /**/
                } else { echo '{"success": false, "reason": [3, "User type not defined."]}'; if(!$reauth)slog("account", "login", $_POST['zone'], $_POST['username'], "fail", $sso?"sso":"", "NotEligible"); }
            } else { echo '{"success": false, "reason": [1, "User type not set."]}'; slog("account", "login", $_POST['zone'], $_POST['username'], "fail", $sso?"sso":"", "NotEligible"); }
        }
    }

    if ($_GET["way"]=="in") {
        unauth(false);
        switch (intval(trim($_POST['zone']))) {
            case 0: $zone = "s"; break;
            case 1: $zone = "t"; break;
            case 2: $zone = "t"; break;
            case 3: $zone = "t"; break;
            default: $zone = "undefined"; break;
        } $user = trim($_POST['username']);
        $_SESSION['auth'] = array(
            "type" => $zone,
            "user" => $user,
            "name" => array(
                "en" => array(
                    "p" => "",
                    "f" => "User",
                    "l" => $user,
                    "a" => "User $user"
                ), "th" => array(
                    "p" => "",
                    "f" => "User",
                    "l" => $user,
                    "a" => "User $user"
                )
            )
        ); echo '{"success": true}';
        require("../core/db_connect.php");
        slog("account", "login", $zone, $user, "pass", "", "", true);
    } else if ($_GET["way"]=="out") unauth();
?>