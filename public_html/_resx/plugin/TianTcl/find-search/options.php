<?php
	# $normal_params = false;
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	// Execute
	if (!API::$attr["bypassValidation"] && (!isset(API::$attr["q"]) || !strlen(trim(API::$attr["q"])))) API::errorMessage(1, "Empty search query");
	else {
		function exclude($colName) {
			global $APP_CONST, $vToken;
			$exceptlist = API::$attr["except"] ?? "";
			if (!strlen(API::$attr["except"])) return "";
			try {
				$readExcept = explode(",", API::$attr["except"]);
				if (!preg_match($APP_CONST["REGEX"]["vToken"], $readExcept[0])) goto normalExcept;
				for ($ei = 0; $ei < count($readExcept); $ei++) $readExcept[$ei] = $vToken -> read($readExcept[$ei]);
				$readExcept = implode(",", $readExcept);
				if (preg_match("/(,,|^,?$)/", $readExcept)) goto normalExcept;
				$exceptlist = $readExcept;
			} catch (exception $exception) {}
			normalExcept:
			if (!preg_match("/^\d+(,\d+)*$/", $exceptlist)) {
				$exceptlist = explode(",", $exceptlist);
				$exceptlist = "'".implode("','", array_map("escapeSQL", $exceptlist))."'";
			} return "AND NOT $colName IN($exceptlist)";
		} $q = escapeSQL(API::$attr["q"]); $rs = array();
		if (strlen($q) > 256) API::errorMessage(1, "Search query too long");
		else if (!API::$attr["bypassValidation"] && preg_match("/^[_%+.]+$/", $q)) API::errorMessage(1, "Invalid search query");
		else switch (API::$action) {
			case "constants": {
				$APP_DB[1] = connect_to_database(1);
				if (gettype($APP_DB[1]) == "array") API::errorMessage(3, "Unable to connect to data source.");
				else {
					switch (API::$command) {
						case "name-prefix": {
							$search = $APP_DB[1] -> query("SELECT refID AS ID,COALESCE(abbrth,nameth) AS namepth,COALESCE(abbren,nameen) AS namepen FROM name_prefix WHERE nameth LIKE '$q%' OR abbrth LIKE '$q%' OR nameen LIKE '$q%' OR abbren LIKE '$q%' LIMIT $resultLimit");
							if ($search && $search -> num_rows) while ($namep = $search -> fetch_assoc()) array_push($rs, $namep);
							API::successState($rs);
						} break;
						case "address": {
							$search = $APP_DB[1] -> query("SELECT a.refID AS subdistrictI, a.nameth AS subdistrictN, b.refID AS districtI, b.nameth AS districtN, c.refID AS provinceI, c.nameth AS provinceN FROM subdistrict a LEFT JOIN district b ON a.district=b.refID LEFT JOIN province c ON a.province=c.refID WHERE a.nameth LIKE '$q%' OR b.nameth LIKE '$q%' OR b.nameen LIKE '$q%' OR c.nameth LIKE '$q%' OR c.nameen LIKE '$q%' LIMIT 50");
							if ($search && $search -> num_rows) while ($addr = $search -> fetch_assoc()) array_push($rs, $addr);
							API::successState($rs);
						} break;
						case "school": {
							if (API::$attr["except"] <> "") {
								$exclude = implode("','", explode(",", escapeSQL($attr["except"])));
								$exc = (API::$attr["except"] <> "" ? "AND NOT (a.refID IN('".$exclude."') OR a.nameth IN('".$exclude."') OR a.nameen IN('".$exclude."'))" : "");
							} else $exc = "";
							$search = $APP_DB[1] -> query("SELECT a.refID,a.nameth,b.nameth AS province FROM school a LEFT JOIN province b ON a.province=b.refID WHERE (a.refID LIKE '$q%' OR a.nameth LIKE '$q%' OR a.nameen LIKE '$q%') $exc ORDER BY a.nameth,province,a.refID LIMIT 50");
							if ($search && $search -> num_rows) while ($er = $search -> fetch_assoc()) array_push($rs, array(
								"ID" => $er["refID"],
								"name" => $er["nameth"],
								"province" => $er["province"]
							)); API::successState($rs);
						} break;
						default: API::errorMessage(1, "Invalid category"); break;
					} $APP_DB[1] -> close();
				}
			} break;
			case "app": {
				require($APP_RootDir."private/script/lib/TianTcl/virtual-token.php");
				switch (API::$command) {
					case "account": {
						/* $exc = exclude("refID");
						$search = $APP_DB[0] -> query("SELECT a.refID,a.username,b.namefth,b.namelth FROM user_data a INNER JOIN user_info b ON b.refID=a.refID WHERE a.status IN('A', 'U') AND (a.refID LIKE '$q%' OR a.username LIKE '$q%' OR b.namefth LIKE '$q%' OR b.namefen LIKE '$q%' OR b.namelth LIKE '$q%' OR b.namelen LIKE '$q%' OR b.namenth LIKE '$q%' OR b.namenen LIKE '$q%') $exc ORDER BY b.namefth LIMIT 50");
						if ($search && $search -> num_rows) while ($er = $search -> fetch_assoc()) array_push($rs, array(
							"ID" => $er["refID"],
							"username" => $er["username"],
							"display" => $er["namefth"]."  ".$er["namelth"]
						)); */ API::successState($rs);
					} break;
					case "teacher": {
						$exc = exclude("namecode");
						$search = $APP_DB[0] -> query("SELECT namecode,namefth,namelth FROM user_t WHERE status='A' AND (namecode LIKE '$q%' OR namefth LIKE '$q%' OR namefen LIKE '$q%' OR namelth LIKE '$q%' OR namelen LIKE '$q%' OR namenth LIKE '$q%' OR namenen LIKE '$q%') $exc AND NOT proj IN('Y', 'Z') AND NOT subj RLIKE '[Y-Zก-ฌ]' ORDER BY namefth LIMIT 20");
						if ($search && $search -> num_rows) while ($er = $search -> fetch_assoc()) array_push($rs, array(
							"idcode" => $er["namecode"],
							"display" => "ครู".$er["namefth"]."  ".$er["namelth"]
						)); API::successState($rs);
					} break;
					case "subject": {
						$exc = exclude("codeth");
						$search = $APP_DB[0] -> query("SELECT codeth,level,nameth FROM dat_subj WHERE (codeth LIKE '$q%' OR nameth LIKE '$q%' OR codeen LIKE '$q%' OR nameen LIKE '$q%') $exc AND NOT codeth LIKE '%4____' AND NOT (codeth LIKE '%3____' AND level='L') ORDER BY nameth LIMIT 50");
						if ($search && $search -> num_rows) while ($er = $search -> fetch_assoc()) array_push($rs, array(
							"code" => $er["codeth"],
							"name" => $er["nameth"],
							"level" => $er["level"]
						)); API::successState($rs);
					} break;
					default: API::errorMessage(1, "Invalid category"); break;
				}
			} break;
			default: API::errorMessage(1, "Invalid type"); break;
		}
	} API::sendOutput();
?>