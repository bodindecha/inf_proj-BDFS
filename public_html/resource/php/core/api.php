<?php
	session_start();
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	if (isset($_REQUEST['app']) && isset($_REQUEST['cmd'])) { $has_data = true; $app = $_REQUEST['app']; $cmd = $_REQUEST['cmd']; $attr = $_REQUEST['attr']; }
	else $has_data = false; if ($has_data) {
		require_once("../core/reload_settings.php"); require("../core/db_connect.php"); require_once("../core/config.php");
		if ($app == "fs-teacher") {
			if ($cmd == "find") {
				$q = $db -> real_escape_string($_GET['q']);
				if ($attr<>"") {
					$exclude = implode("','", explode(",", ($db -> real_escape_string($attr))));
					$exc = ($attr<>"" ? "AND NOT namecode IN('".$exclude."')" : "");
                } else $exc = ""; $rs = '<span onClick="fst.end(null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span>'; if ($q<>"") {
                    $search = $db -> query("SELECT namecode,namefth,namelth FROM user_t WHERE status='A' AND (namecode LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $exc AND NOT proj IN('Y', 'Z') AND NOT subj RLIKE '[ก-ฌ]' ORDER BY namefth LIMIT 50");
                    if ($search -> num_rows > 0)
                        while ($er = $search -> fetch_assoc()) $rs .= '<span onClick="fst.end(\''.$er['namecode'].'\', this)">ครู'.$er['namefth'].'  '.$er['namelth'].'</span>';
				} echo $rs;
			}
		} else if ($app == "fs-subject") {
			if ($cmd == "find") {
				$q = $db -> real_escape_string($_GET['q']);
				if ($attr<>"") {
					$exclude = implode("','", explode(",", ($db -> real_escape_string($attr))));
					$exc = ($attr<>"" ? "AND NOT codeth IN('".$exclude."')" : "");
                } else $exc = ""; $rs = '<span onClick="fss.end(null, null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span>'; if ($q<>"") {
                    $search = $db -> query("SELECT codeth,level,nameth FROM dat_subj WHERE (codeth LIKE '$q%' OR nameth LIKE '$q%' OR codeen LIKE '$q%' OR nameen LIKE '$q%') $exc AND NOT codeth LIKE '%4____' AND NOT (codeth LIKE '%3____' AND level='L') ORDER BY nameth");
                    if ($search -> num_rows > 0)
                        while ($er = $search -> fetch_assoc()) $rs .= '<span onClick="fss.end(\''.$er['codeth'].'\', \''.$er['level'].'\', this)">'.$er['nameth'].' ('.$er['codeth'].')</span>';
				} echo $rs;
			}
		} else if ($app == "fs-account") {
			if ($cmd == "find") {
				$q = $db -> real_escape_string($_GET['q']);
				if ($attr<>"") {
					$exclude = implode("','", explode(",", ($db -> real_escape_string($attr))));
                } else $exc = ""; $rs = '<span onClick="fsa.end(null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span>'; if ($q<>"") {
					$mode = $_GET['mode'] ?? "all";
					$excT = ($attr<>"" ? "AND NOT namecode IN('".$exclude."')" : ""); $excS = ($attr<>"" ? "AND NOT stdid IN('".$exclude."')" : "");
					if ($mode == "all") {
						$search = $db -> query("(SELECT namecode idcode,namep,namefth,namelth FROM user_t a WHERE (namecode LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excT ORDER BY namefth) UNION ALL (SELECT stdid idcode,namep,namefth,namelth FROM user_s a WHERE (stdid LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excS ORDER BY namefth) LIMIT 50");
					} else if ($mode == "active") {
						$search = $db -> query("(SELECT namecode idcode,namep,namefth,namelth FROM user_t a WHERE status='A' AND (namecode LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excT ORDER BY namefth) UNION ALL (SELECT stdid idcode,namep,namefth,namelth FROM user_s a WHERE status='A' AND (stdid LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excS ORDER BY namefth) LIMIT 50");
					} else if ($mode == "active-std") {
						$search = $db -> query("SELECT stdid idcode,namep,namefth,namelth FROM user_s a WHERE status='A' AND (stdid LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excS ORDER BY namefth LIMIT 50");
					} else if ($mode == "log") {
						$exc = ($attr<>"" ? "AND NOT b.exor IN('".$exclude."')" : "");
						$search = $db -> query("(SELECT a.namecode idcode,a.namep,a.namefth,a.namelth FROM log_action b INNER JOIN user_t a ON b.exor=a.namecode WHERE b.exor NOT RLIKE '^[0-9]{5}$' AND (a.namecode LIKE '$q%' OR a.namefth LIKE '$q%' OR a.namefen LIKE '$q%' OR a.namelth LIKE '$q%' OR a.namelen LIKE '$q%' OR a.namenth LIKE '$q%' OR a.namenen LIKE '$q%') $exc GROUP BY b.exor ORDER BY a.namefth) UNION ALL (SELECT a.stdid idcode,a.namep,a.namefth,a.namelth FROM log_action b INNER JOIN user_s a ON CAST(b.exor AS INT)=a.stdid WHERE b.exor RLIKE '^[0-9]{5}$' AND (a.stdid LIKE '$q%' OR a.namefth LIKE '$q%' OR a.namefen LIKE '$q%' OR a.namelth LIKE '$q%' OR a.namelen LIKE '$q%' OR a.namenth LIKE '$q%' OR a.namenen LIKE '$q%') $exc GROUP BY b.exor ORDER BY a.namefth) LIMIT 50");
					} else if ($mode == "PBL_no-group") {
						if (isset($attr["grade"])) {
							$gen = grade2gen($attr["grade"]);
							$excG = "AND grade=".$attr["grade"]." AND room=".$attr["room"];
							$excS = "AND gen=$gen AND room=".$attr["room"];
						} else { $excG = ""; $excS = ""; }
						$read_pg = $db -> query("SELECT GROUP_CONCAT(mbr1) s1,GROUP_CONCAT(mbr2) s2,GROUP_CONCAT(mbr3) s3,GROUP_CONCAT(mbr4) s4,GROUP_CONCAT(mbr5) s5,GROUP_CONCAT(mbr6) s6,GROUP_CONCAT(mbr7) s7 FROM PBL_group WHERE year=".$_SESSION['stif']['t_year']." AND NOT mbr1 IS NULL $excG");
						$get_pg = $read_pg -> fetch_array(MYSQLI_ASSOC);
						$all_reged = array_filter(array_merge(explode(",", $get_pg['s1']), explode(",", $get_pg['s2']), explode(",", $get_pg['s3']), explode(",", $get_pg['s4']), explode(",", $get_pg['s5']), explode(",", $get_pg['s6']), explode(",", $get_pg['s7'])));
						$search = $db -> query("SELECT stdid idcode,namep,namefth,namelth FROM user_s WHERE status='A' AND number<=90 ".(count($all_reged)?"AND NOT stdid IN(".implode(",", $all_reged).")":"")." AND (stdid LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $excS ORDER BY namefth LIMIT 50");
					} else if ($mode == "trash-bank-user") {
						$excS = ($attr<>"" ? "AND NOT a.stdid IN('".$exclude."')" : "");
						$search = $db -> query("SELECT a.stdid idcode,b.namep,b.namefth,b.namelth FROM TrashBank_savings a INNER JOIN user_s b ON a.stdid=b.stdid WHERE (a.stdid LIKE '$q%' OR b.namefth LIKE '$q%' OR b.namefen LIKE '$q%' OR b.namelth LIKE '$q%' OR b.namelen LIKE '$q%' OR b.namenth LIKE '$q%' OR b.namenen LIKE '$q%') $excS ORDER BY b.namefth LIMIT 50");
					}
					if ($search -> num_rows > 0)
                        while ($er = $search -> fetch_assoc()) $rs .= '<span onClick="fsa.end(\''.$er['idcode'].'\', this)">'.prefixcode2text($er['namep'])['th'].$er['namefth'].'  '.$er['namelth'].' ('.$er['idcode'].')</span>';
				} echo $rs;
			}
		} else if ($app == "fs-school") {
			header("Access-Control-Allow-Origin: https://pathwayspeechcontest.cf");
            require($dirPWroot."m/sync/db_connect.php");
            $rdb = connect_to_reg("schoollist");
			if ($cmd == "find") {
				$q = $rdb -> real_escape_string($_GET['q']);
				if ($attr<>"") {
					$exclude = implode("','", explode(",", ($db -> real_escape_string($attr))));
					$exc = ($attr<>"" ? "AND NOT (a.refID IN('".$exclude."') OR a.nameth IN('".$exclude."') OR a.nameen IN('".$exclude."'))" : "");
                } else $exc = ""; $rs = '<span onClick="school.set(null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span>'; if ($q<>"") {
                    $search = $rdb -> query("SELECT a.nameth,b.nameth AS province FROM school a LEFT JOIN province b ON a.province=b.refID WHERE (a.refID LIKE '$q%' OR a.nameth LIKE '$q%' OR a.nameen LIKE '$q%') $exc ORDER BY a.nameth,province,a.refID LIMIT 50");
                    if ($search -> num_rows > 0)
                        while ($er = $search -> fetch_assoc()) $rs .= '<span onClick="school.set(\''.$er['nameth'].'\', this)">'.$er['nameth'].' <sub>'.$er['province'].'</sub></span>';
				} echo $rs;
			}
            $rdb -> close();
		} /* else if ($app == "l-bodin") { // Deprecated
			header("Access-Control-Allow-Origin: https://bod.in.th");
			if ($cmd == "get") {
				if ($attr == "FullName") {
					$user = $db -> real_escape_string($_GET['u']);
					$type = preg_match('/^\d{5}$/', $user) ? "s" : "t";
					$uf = $type=="s" ? "stdid" : "namecode";
					$search = $db -> query("SELECT namep,namefth,namelth,namefen,namelen FROM user_$type WHERE $uf='$user'");
					if ($search -> num_rows == 1) {
						$read = $search -> fetch_array(MYSQLI_ASSOC);
						$name = array(
							"th" => prefixcode2text($read['namep'])['th'].$read['namefth']."  ".$read['namelth'],
							"en" => prefixcode2text($read['namep'])['en'].$read['namefen']." ".$read['namelen']
						); echo '{"success": true, "name": '.json_encode($name).'}';
					} else echo '{"success": false, "reason": [1, "User not found!"]}';
				}
			}
		} */ else if ($app == "user-dir") {
			if ($cmd == "search") {

			}
		}
		$db -> close();
	}
?>