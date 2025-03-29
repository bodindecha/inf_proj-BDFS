<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require_once($dirPWroot."resource/php/extend/_RGI.php");
    // Execute
	$self = $_SESSION["auth"]["user"];
	if (empty($self)) errorMessage(3, "You are not signed-in. Please reload and try again."); else
    switch ($type) {
        case "post": {
			switch ($command) {
				case "new": {
					$name = escapeSQL($attr["name"]);
					$desc = escapeSQL(nl2br(htmlspecialchars($attr["desc"])));
					$time = date("Y-m-d")." ".$attr["time"];
					$loca = escapeSQL($attr["loc"]);
					$ctgr = escapeSQL($attr["ctgr"]);
					$type = escapeSQL($attr["type"]);
					if (isset($_FILES["usf"])) {
						$origin = $_FILES["usf"]["name"];
						$extn = pathinfo($origin, PATHINFO_EXTENSION);
						$filename = "$self-".time().".$extn";
						$destination = $dirPWroot."resource/upload/BDFS/LnF-items/$filename";
						// Size limit 5 MB
						$uploadSuccess = move_uploaded_file($_FILES["usf"]["tmp_name"], $destination);
					} // Insert data
					$photo = ($uploadSuccess??false) ? "'$filename'" : "NULL";
					$success = $db -> query("INSERT INTO LostNFound_items (name,description,occurs,location,category,photo,author,post_type,ip) VALUES('$name','$desc','$time','$loca','$ctgr',$photo,'$self','$type','$ip')");
					if ($success) {
						slog("LostNFound", "new", "post", $db -> insert_id, "pass");
						header("Location: post#status=success");
					} else {
						slog("LostNFound", "new", "post", "$name, $desc, $loca", "fail");
						if ($uploadSuccess??false) unlink($destination);
						header("Location: post#status=failed");
					}
				} break;
				case "list": {
					$amount = 20;
					$start = escapeSQL($attr["offset"]);
					$type = escapeSQL($attr["type"]);
					$get = $db -> query("SELECT refID AS item,name,occurs AS time,photo AS image,location FROM LostNFound_items WHERE post_type='$type' AND partner IS NULL ORDER BY occurs DESC LIMIT $start, $amount");
					require($dirPWroot."resource/php/lib/TianTcl/virtual-token.php");
					$list = array(); if ($get && $get -> num_rows) while ($read = $get -> fetch_assoc()) {
						$read["item"] = str_repeat("0", 5-strlen($read["item"])).$read["item"]."/#/".($vToken -> create($read["item"]));
						$read["time"] = date("วันที่ d/m/Y เวลา H:i", strtotime($read["time"]));
						array_push($list, $read);
					} successState(array(
						"list" => $list,
						"next" => intval($start)+($get -> num_rows)
					));
				} break;
				case "preview": {
					
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		default: errorMessage(1, "Invalid type"); break;
	} $db -> close();
	sendOutput($return);
?>