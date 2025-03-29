<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require_once($dirPWroot."resource/php/extend/_RGI.php");
	// All area functions
	function getStatementOf($user) {
		global $db;
		if (empty($user)) return null;
		if ($user == "m-ALL") return $db -> query("SELECT idcode AS account,SUBSTRING(time, 1, 7) AS month,COUNT(1) AS trns FROM TrashBank_deposit GROUP BY month ORDER BY month DESC");
		return $db -> query("SELECT SUBSTRING(time, 1, 7) AS month,COUNT(1) AS trns FROM TrashBank_deposit WHERE idcode='$user' GROUP BY month ORDER BY month DESC");
	}
	function getStateListOf($user, $month) {
		global $db;
		if (empty($user)) return -1;
		if (empty($month)) return -2;
		if ($user == "m-ALL") return $db -> query("SELECT idcode AS account,refID AS no,amtPlastic,amtPaper,amtMetal,SUBSTRING(time, 1, 19) AS trns FROM TrashBank_deposit WHERE time LIKE '$month-%' ORDER BY trns DESC");
		return $db -> query("SELECT refID AS no,amtPlastic,amtPaper,amtMetal,SUBSTRING(time, 1, 19) AS trns FROM TrashBank_deposit WHERE idcode='$user' AND time LIKE '$month-%' ORDER BY trns DESC");
	}
    // Execute
	$self = $_SESSION["auth"]["user"] ?? "";
	if (empty($self)) errorMessage(3, "You are not signed-in. Please reload and try again."); else
    switch ($type) {
        case "USR": {
			switch ($command) {
				case "getAccountStatus": {
					$get = $db -> query("SELECT balPlastic+balPaper+balMetal AS balance,transaction,credits,balPlastic+balPaper+balMetal+rctPlastic+rctPaper+rctMetal AS waste,updated,closed FROM TrashBank_savings WHERE idcode='$self'");
					if (!$get) {
						errorMessage(3, "Unable to get account status.");
						errorMessage(1, "Retrying in 30 seconds.");
					} else if ($get -> num_rows == 0) successState(array(
						"stt" => "NONE"
					)); else if ($get -> num_rows <> 1) successState(array(
						"stt" => "ERROR",
						"message" => array(array(3, "Error getting your account status. Please contact our staff."))
					)); else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						successState(array(
							"amt" => intval($read["balance"]),
							"act" => intval($read["transaction"]),
							"bal" => intval($read["credits"]),
							"tot" => intval($read["waste"]),
							"upd" => date("d/m/Y H:i", strtotime($read["updated"])),
							"stt" => !empty($read["closed"]) ? "CLOSED" : "ACTIVE"
						));
					}
				} break;
				case "putNewAccount": {
					// Check existed
					$check = $db -> query("SELECT NULL FROM TrashBank_savings WHERE idcode='$self'");
					if ($check -> num_rows <> 0) {
						successState(array("type" => "EXISTED"));
						slog("TrashBank", "account", "new", $self, "fail", "", "Existed");
					} else {
						// Create account
						$success = $db -> query("INSERT INTO TrashBank_savings(idcode) VALUES('$self')");
						if ($success) {
							successState(array("type" => "CREATED"));
							slog("TrashBank", "account", "new", $self, "pass");
						} else {
							errorMessage(3, "Unable to create account. Please try again.");
							slog("TrashBank", "account", "new", $self, "fail", "", "InvalidQuery");
						}
					}
				} break;
				case "depositTrash": {
					$idcode = escapeSQL($attr["accountID"]);
					$amtPlastic = intval($attr["amtPlastic"]);
					$amtPaper = intval($attr["amtPaper"]);
					$amtMetal = intval($attr["amtMetal"]);
					$logDetail = "$idcode:$amtPlastic,$amtPaper,$amtMetal";
					# $total = $amtPlastic + $amtPaper + $amtMetal;
					// Check permission
					if ($idcode == $self) {
						errorMessage(3, "You cannot perform this action for yourself.");
						slog("TrashBank", "trans", "deposit", $logDetail, "fail", "", "Existed");
					} else {
						if (in_array($self, array("test02", "99999"))) $self = "TianTcl";
						$getperm = $db -> query("SELECT refID,active FROM TrashBank_staff WHERE idcode='$self'");
						if (!$getperm) {
							errorMessage(3, "Unable to check permission. Please try again.");
							slog("TrashBank", "trans", "deposit", $logDetail, "fail", "", "Incorrect");
						} else if ($getperm -> num_rows <> 1) {
							errorMessage(3, "You don't have permission to perform this action.");
							slog("TrashBank", "trans", "deposit", $logDetail, "fail", "", "Unauthorized");
						} else {
							$readperm = $getperm -> fetch_array(MYSQLI_ASSOC);
							if ($readperm["active"] <> "Y") {
								errorMessage(3, "Your permission have expired. Please contact your supervisor if this is a mistake.");
								slog("TrashBank", "trans", "deposit", $logDetail, "fail", "", "InvalidToken");
							} else {
								$staff = $readperm["refID"];
								// Create record
								$trsID = strval(intval(($db -> query("SELECT count(logid) AS amount FROM TrashBank_deposit WHERE time LIKE '".date("Y-m-d")." %'") -> fetch_array(MYSQLI_ASSOC))["amount"]) + 1);
								$trsID = strtoupper(base_convert(date("Ymd".str_repeat("0", 3-strlen($trsID)).$trsID), 10, 36));
								$success = $db -> multi_query(
									"INSERT INTO TrashBank_deposit(refID,idcode,amtPlastic,amtPaper,amtMetal,staff) VALUES('$trsID','$idcode',$amtPlastic,$amtPaper,$amtMetal,$staff);".
									"UPDATE TrashBank_savings SET balPlastic=balPlastic+$amtPlastic,balPaper=balPaper+$amtPaper,balMetal=balMetal+$amtMetal,transaction=transaction+1 WHERE idcode='$idcode';".
									"UPDATE TrashBank_staff SET action=action+1 WHERE refID=$staff;"
								); if ($success) {
									successState();
									slog("TrashBank", "trans", "deposit", $trsID, "pass");
								} else {
									errorMessage(3, "Unable to record transaction. Please try again.");
									slog("TrashBank", "trans", "deposit", $logDetail, "fail", "", "InvalidQuery");
								}
							}
						}
					}
				} break;
				case "getStateMonth": {
					$get = getStatementOf($self);
					if ($get == null) errorMessage(3, "Search account name is empty.");
					else if (!$get) errorMessage(3, "Unable to get list.");
					else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
					else {
						$months = array();
						while ($read = $get -> fetch_assoc()) array_push($months, $read);
						successState($months);
					}
				} break;
				case "getStateList": {
					if (!isset($_REQUEST["month"]) || empty(trim($_REQUEST["month"]))) errorMessage(2, "No month selected.");
					else {
						$search = escapeSQL($_REQUEST["month"]);
						$get = getStateListOf($self, $search);
						if ($get == -1) errorMessage(3, "Search account name is empty.");
						if ($get == -2) errorMessage(3, "Search month is empty.");
						else if (!$get) errorMessage(3, "Unable to fetch list.");
						else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
						else {
							$trns = array();
							while ($read = $get -> fetch_assoc()) array_push($trns, $read);
							successState($trns);
						}
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
        case "MOD": {
			switch ($command) {
				case "getStatics": {
					$getStock = $db -> query("SELECT COALESCE(SUM(balPlastic), 0) AS `store:Plastic`,COALESCE(SUM(balPaper), 0) AS `store:Paper`,COALESCE(SUM(balMetal), 0) AS `store:Metal`,COALESCE(SUM(rctPlastic), 0) AS `recycled:Plastic`,COALESCE(SUM(rctPaper), 0) AS `recycled:Paper`,COALESCE(SUM(rctMetal), 0) AS `recycled:Metal` FROM TrashBank_savings");
					$getIncome = $db -> query("SELECT COALESCE(SUM(amtPlastic), 0) AS `sold:Plastic`,COALESCE(SUM(amtPaper), 0) AS `sold:Paper`,COALESCE(SUM(amtMetal), 0) AS `sold:Metal` FROM TrashBank_withdraw");
					$getSummary = $db -> query("SELECT COUNT(idcode) AS accounts FROM TrashBank_savings WHERE closed IS NULL");
					if (!$getStock || !$getIncome || !$getSummary) {
						errorMessage(3, "Unable to get bank statics.");
						errorMessage(1, "Retrying in 30 seconds.");
					} else if ($getStock -> num_rows == 0 || $getIncome -> num_rows == 0) errorMessage(3, "None returned from getting bank statics.");
					else if ($getStock -> num_rows <> 1 || $getIncome -> num_rows <> 1 || $getSummary -> num_rows <> 1) errorMessage(3, "Error getting bank statics.");
					else {
						$readStock = $getStock -> fetch_array(MYSQLI_ASSOC);
						$readIncome = $getIncome -> fetch_array(MYSQLI_ASSOC);
						$readSummary = $getSummary -> fetch_array(MYSQLI_ASSOC);
						successState(array_merge($readStock, $readIncome, $readSummary));
					}
				} break;
				case "getStateMonth": {
					$get = getStatementOf($attr["accountID"]);
					if ($get == null) errorMessage(3, "Search account name is empty.");
					else if (!$get) errorMessage(3, "Unable to get list.");
					else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
					else {
						$months = array();
						while ($read = $get -> fetch_assoc()) array_push($months, $read);
						successState($months);
					}
				} break;
				case "getStateList": {
					if (!isset($_REQUEST["month"]) || empty(trim($_REQUEST["month"]))) errorMessage(2, "No month selected.");
					else {
						$search = escapeSQL($_REQUEST["month"]);
						$get = getStateListOf($attr["accountID"], $search);
						if ($get == -1) errorMessage(3, "Search account name is empty.");
						else if ($get == -2) errorMessage(3, "Search month is empty.");
						else if (!$get) errorMessage(3, "Unable to fetch list.");
						else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
						else {
							$trns = array();
							while ($read = $get -> fetch_assoc()) array_push($trns, $read);
							successState($trns);
						}
					}
				} break;
				case "getSavings": {
					$account = escapeSQL($attr["accountID"]);
					$get = $db -> query("SELECT balPlastic AS Plastic,balPaper AS Paper,balMetal AS Metal FROM TrashBank_savings WHERE idcode='$account'");
					if (!$get) errorMessage(3, "Unable to get account savings.");
					else if ($get -> num_rows == 0) errorMessage(3, "Account not found.");
					else if ($get -> num_rows <> 1) errorMessage(3, "Error getting your account status. Please contact our staff.");
					else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						successState($read);
					}
				} break;
				case "withdrawTrash": {
					$idcode = escapeSQL($attr["accountID"]);
					$amtPlastic = intval($attr["amtPlastic"]);
					$amtPaper = intval($attr["amtPaper"]);
					$amtMetal = intval($attr["amtMetal"]);
					$logDetail = "$idcode:$amtPlastic,$amtPaper,$amtMetal";
					# $total = $amtPlastic + $amtPaper + $amtMetal;
					// Check permission
					if ($idcode == $self) {
						errorMessage(3, "You cannot perform this action for yourself.");
						slog("TrashBank", "trans", "withdraw", $logDetail, "fail", "", "Existed");
					} else {
						if (in_array($self, array("test02", "99999"))) $self = "TianTcl";
						$getperm = $db -> query("SELECT refID,active FROM TrashBank_staff WHERE idcode='$self'");
						if (!$getperm) {
							errorMessage(3, "Unable to check permission. Please try again.");
							slog("TrashBank", "trans", "withdraw", $logDetail, "fail", "", "Incorrect");
						} else if ($getperm -> num_rows <> 1) {
							errorMessage(3, "You don't have permission to perform this action.");
							slog("TrashBank", "trans", "withdraw", $logDetail, "fail", "", "Unauthorized");
						} else {
							$readperm = $getperm -> fetch_array(MYSQLI_ASSOC);
							if ($readperm["active"] <> "Y") {
								errorMessage(3, "Your permission have expired. Please contact your supervisor if this is a mistake.");
								slog("TrashBank", "trans", "withdraw", $logDetail, "fail", "", "InvalidToken");
							} else {
								$staff = $readperm["refID"];
								// Create record
								$trsID = strval(intval(($db -> query("SELECT count(logid) AS amount FROM TrashBank_withdraw WHERE time LIKE '".date("Y-m-d")." %'") -> fetch_array(MYSQLI_ASSOC))["amount"]) + 1);
								$trsID = strtoupper(base_convert(date("Ymd".str_repeat("0", 3-strlen($trsID)).$trsID), 10, 36));
								$success = $db -> multi_query(
									"INSERT INTO TrashBank_withdraw(refID,idcode,amtPlastic,amtPaper,amtMetal,staff) VALUES('$trsID','$idcode',$amtPlastic,$amtPaper,$amtMetal,$staff);".
									"UPDATE TrashBank_savings SET balPlastic=balPlastic-$amtPlastic,balPaper=balPaper-$amtPaper,balMetal=balMetal-$amtMetal,transaction=transaction+1,rctPlastic=rctPlastic+$amtPlastic,rctPaper=rctPaper+$amtPaper,rctMetal=rctMetal+$amtMetal WHERE idcode='$idcode';".
									"UPDATE TrashBank_staff SET action=action+1 WHERE refID=$staff;"
								); if ($success) {
									successState();
									slog("TrashBank", "trans", "withdraw", $trsID, "pass");
								} else {
									errorMessage(3, "Unable to record transaction. Please try again.");
									slog("TrashBank", "trans", "withdraw", $logDetail, "fail", "", "InvalidQuery");
								}
							}
						}
					}
				} break;
				case "getWithdrewMonth": {
					$get = $db -> query("SELECT idcode AS account,SUBSTRING(time, 1, 7) AS month,COUNT(1) AS trns FROM TrashBank_withdraw GROUP BY month ORDER BY month DESC");
					if (!$get) errorMessage(3, "Unable to get list.");
					else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
					else {
						$months = array();
						while ($read = $get -> fetch_assoc()) array_push($months, $read);
						successState($months);
					}
				} break;
				case "getWithdrawal": {
					if (!isset($_REQUEST["month"]) || empty(trim($_REQUEST["month"]))) errorMessage(2, "No month selected.");
					else {
						$search = escapeSQL($_REQUEST["month"]);
						$get = $db -> query("SELECT idcode AS account,refID AS no,amtPlastic,amtPaper,amtMetal,SUBSTRING(time, 1, 19) AS trns FROM TrashBank_withdraw WHERE time LIKE '$search-%' ORDER BY trns DESC");
						if (!$get) errorMessage(3, "Unable to fetch list.");
						else if ($get -> num_rows < 1) errorMessage(2, "No transactions.");
						else {
							$trns = array();
							while ($read = $get -> fetch_assoc()) array_push($trns, $read);
							successState($trns);
						}
					}
				} break;
				case "putNewAccount": {
					$user = escapeSQL($attr);
					// Check existed
					$check = $db -> query("SELECT NULL FROM TrashBank_savings WHERE idcode='$user'");
					if ($check -> num_rows <> 0) {
						successState(array("type" => "EXISTED"));
						// Comment out because no checking in selection
						# slog("TrashBank", "account", "new", $user, "fail", "", "Existed");
					} else {
						// Create account
						$success = $db -> query("INSERT INTO TrashBank_savings(idcode) VALUES('$user')");
						if ($success) {
							successState(array("type" => "CREATED"));
							slog("TrashBank", "account", "new", $user, "pass");
						} else {
							errorMessage(3, "Unable to create account. Please try again.");
							slog("TrashBank", "account", "new", $user, "fail", "", "InvalidQuery");
						}
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		default: errorMessage(1, "Invalid type"); break;
	} $db -> close();
	sendOutput($return);
?>