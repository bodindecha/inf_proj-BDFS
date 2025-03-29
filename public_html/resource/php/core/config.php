<?php
	function statuscode2text($statuscode) {
		switch ($statuscode) {
			case "A": $statusen = "Active"; $statusth = "เปิดใช้งาน"; break;
			case "I": $statusen = "Inactive"; $statusth = "ถูกปิดใช้งาน"; break;
			case "O": $statusen = "Deactivated"; $statusth = "ถูกปิดใช้งานชั่วคราว"; break;
			case "U": $statusen = "Unactivated"; $statusth = "ยังไม่ถูกเปิดใช้งาน"; break;
			case "S": $statusen = "Suspended"; $statusth = "ถูกระงับ"; break;
			case "D": $statusen = "Deleted"; $statusth = "ถูกลบ"; break;
			default: $statusen = ""; $statusth = ""; break;
		} return array("en" => $statusen, "th" => $statusth);
	}
	function prefixcode2text($prefixcode) {
		switch (intval($prefixcode)) {
			case 1: $namepen = "Master"; $namepth = "ด.ช."; break;
			case 2: $namepen = "Mr."; $namepth = "นาย"; break;
			case 3: $namepen = "Miss"; $namepth = "ด.ญ."; break;
			case 4: $namepen = "Ms."; $namepth = "น.ส."; break;
			case 5: $namepen = "Mrs."; $namepth = "นาง"; break;
			case 6: $namepen = "A/Sub Lt. "; $namepth = "ว่าที่ ร.ต."; break;
			case 7: $namepen = "A/Sub Lt."; $namepth = "ว่าที่ ร.ต.หญิง "; break;
			case 8: $namepen = "Sqn Ldr."; $namepth = "น. ต.หญิง"; break;
			case 9: $namepen = "Dr."; $namepth = "ดร."; break;
			default: $namepen = ""; $namepth = ""; break;
		} return array("en" => $namepen, "th" => $namepth);
	}
	function projcode2name($projectcode) {
		switch ($projectcode) {
			case "D": $projecten = "DPST"; $projectth = "พสวท"; break;
			case "S": $projecten = "SMTE"; $projectth = "SMTE"; break;
			case "E": $projecten = "Lecturer"; $projectth = "ครูเสริม"; break;
			case "H": $projecten = "Teacher"; $projectth = "ครู"; break;
			case "Y": $projecten = "Manager"; $projectth = "ผู้บริหาร"; break;
			case "Z": $projecten = "Officer"; $projectth = "เจ้าหน้าที่"; break;
			default: $projecten = ""; $projectth = ""; break;
		} return array("en" => $projecten, "th" => $projectth);
	}
	function subjcode2name($subjectcode) {
		switch ($subjectcode) {
			case "A": $subjecten = "Mathematics"; $subjectth = "คณิตศาสตร์"; break;
			case "B": $subjecten = "Physics"; $subjectth = "ฟิสิกส์"; break;
			case "C": $subjecten = "Chemistry"; $subjectth = "เคมี"; break;
			case "D": $subjecten = "Biology"; $subjectth = "ชีววิทยา"; break;
			case "E": $subjecten = "Earth science"; $subjectth = "โลก ดาราศาสตร์ และอวกาศ"; break;
			case "F": $subjecten = "Computer & Technology"; $subjectth = "คอมพิวเตอร์และเทคโนโลยี"; break;
			case "G": $subjecten = "Thai language"; $subjectth = "ภาษาไทย"; break;
			case "H": $subjecten = "Social studies"; $subjectth = "สังคมศึกษา ศาสนา และวัฒนธรรม"; break;
			case "I": $subjecten = "Foreign language"; $subjectth = "ภาษาต่างประเทศ"; break;
			case "J": $subjecten = "Art & Music"; $subjectth = "ศิลปะ และดนตรี"; break;
			case "K": $subjecten = "Guidance"; $subjectth = "แนะแนว"; break;
			case "L": $subjecten = "Health & P.E."; $subjectth = "สุขะศึกษาและพลศึกษา"; break;
			case "M": $subjecten = "Occupation & Housework"; $subjectth = "การงานอาชีพ"; break;
			case "N": $subjecten = "Science"; $subjectth = "วิทยาศาสตร์"; break;
			case "Y": $subjecten = "Staff"; $subjectth = "ผู้ประสานงาน"; break;
			case "Z": $subjecten = "Admin & Moderator"; $subjectth = "ผู้ดูแลระบบ"; break;
			case "ก": $subjecten = "Director office"; $subjectth = "สำนักผู้อำนวยการ"; break;
			case "ข": $subjecten = "Academic management"; $subjectth = "กลุ่มบริหารวิชาการ"; break;
			case "ค": $subjecten = "General management"; $subjectth = "กลุ่มบริหารทั่วไป"; break;
			case "ง": $subjecten = "Budget management"; $subjectth = "กลุ่มบริหารงบประมาณ"; break;
			case "จ": $subjecten = "Personnel management"; $subjectth = "กลุ่มบริหารงานบุคคล"; break;
			case "ฉ": $subjecten = "Registrar"; $subjectth = "งานทะเบียนวัดผล"; break;
			case "ช": $subjecten = "Plans & Policies"; $subjectth = "นโยบายและแผน"; break;
			case "ซ": $subjecten = "Premises"; $subjectth = "งานอาคารและสถานที่"; break;
			case "ฌ": $subjecten = "Librarians"; $subjectth = "งานห้องสมุด"; break;
			default: $subjecten = ""; $subjectth = ""; break;
		} return array("en" => $subjecten, "th" => $subjectth);
	}
	function gen2grade($gen) {
		if (!isset($_SESSION['stif']['t_year'])) require("reload_settings.php");
		return (intval($_SESSION['stif']['t_year']) - 2508 - intval($gen));
	}
	function grade2gen($grade) {
		if (!isset($_SESSION['stif']['t_year'])) require("reload_settings.php");
		return (intval($_SESSION['stif']['t_year']) - 2508 - intval($grade));
	}
	function gen2graduate($gen) {
		return (2514 + intval($gen));
	}
	function month2text($monthno) {
		switch (intval($monthno)) {
			case 1: $monthens = "Jan"; $monthenl = "uary"; $monthths = "ม.ค."; $monththl = "มกราคม"; break;
			case 2: $monthens = "Feb"; $monthenl = "uary"; $monthths = "ก.พ."; $monththl = "กุมภาพันธ์"; break;
			case 3: $monthens = "Mar"; $monthenl = "ch"; $monthths = "มี.ค."; $monththl = "มีนาคม"; break;
			case 4: $monthens = "Apr"; $monthenl = "il"; $monthths = "เม.ย."; $monththl = "เมษายน"; break;
			case 5: $monthens = "May"; $monthenl = ""; $monthths = "พ.ค."; $monththl = "พฤษภาคม"; break;
			case 6: $monthens = "Jun"; $monthenl = "e"; $monthths = "มิ.ย."; $monththl = "มิถุนายน"; break;
			case 7: $monthens = "Jul"; $monthenl = "y"; $monthths = "ก.ค."; $monththl = "กรกฎาคม"; break;
			case 8: $monthens = "Aug"; $monthenl = "ust"; $monthths = "ส.ค."; $monththl = "สิงหาคม"; break;
			case 9: $monthens = "Sep"; $monthenl = "tember"; $monthths = "ก.ย."; $monththl = "กันยายน"; break;
			case 10: $monthens = "Oct"; $monthenl = "ober"; $monthths = "ต.ค."; $monththl = "ตุลาคม"; break;
			case 11: $monthens = "Nov"; $monthenl = "ember"; $monthths = "พ.ย."; $monththl = "พฤศจิกายน"; break;
			case 12: $monthens = "Dec"; $monthenl = "ember"; $monthths = "ธ.ค."; $monththl = "ธันวาคม"; break;
			default: $monthens = ""; $monthenl = ""; $monthths = ""; $monththl = ""; break;
		} return array(
			"en" => array($monthens, $monthens.$monthenl),
			"th" => array($monthths, $monththl)
		);
	}
	function roomrolecode2text($rolecode) {
		switch ($rolecode) {
			case "L": $rolenameen = "Leader"; $rolenameth = "หัวหน้าห้อง"; break;
			case "D": $rolenameen = "Deputy-leader"; $rolenameth = "รองหัวหน้าห้อง"; break;
			case "S": $rolenameen = "Secretary"; $rolenameth = "เลขานุการ"; break;
			case "A": $rolenameen = "Academic"; $rolenameth = "วิชาการ"; break;
			case "T": $rolenameen = "Treasurer"; $rolenameth = "เหรัญญิก"; break;
			default: $rolenameen = ""; $rolenameth = ""; break;
		} return array("en" => $rolenameen, "th" => $rolenameth);
	}
	function roomrolestat2text($rolestat) {
		switch ($rolestat) {
			case "A": $statnameen = "Asking"; $statnameth = "รอการตัดสินใจ"; break;
			case "C": $statnameen = "Confirmed"; $statnameth = "ตกลงรับหน้าที่"; break;
			case "R": $statnameen = "Rejected"; $statnameth = "ปฏิเสธหน้าที่"; break;
			case "S": $statnameen = "Suspended"; $statnameth = "ถูกถอนจากหน้าที่"; break;
			default: $statnameen = ""; $statnameth = ""; break;
		} return array("en" => $statnameen, "th" => $statnameth);
	}
	function size2text($size) {
		$size = round(intval($size)/1024);
		if ($size >= 1000) return strval(round($size/1000, 1))." MB";
		else return strval($size)." KB";
	}
	$periods = array(
		array(815, 905),
		array(905, 955),
		array(1010, 1100),
		array(1100, 1150),
		array(1150, 1240),
		array(1240, 1330),
		array(1330, 1420),
		array(1420, 1510),
		array(1510, 1600),
		array(1600, 1650)
	);
	function is_period($period, $periods) {
		$prd = intval($period)-1; $now = intval(date("Hi"));
		return ($periods[$prd][0] <= $now && $now <= $periods[$prd][1]);
	}
	function this_period($periods) {
		for ($p = 1; $p <= count($periods); $p++) {
			if (is_period($p, $periods)) return $p;
		} return 0;
	}
	function subjtype2text($subjtype) {
		switch ($subjtype) {
			case "B": $subjtypeen = "Basic"; $subjtypeth = "พื้นฐาน"; break;
			case "E": $subjtypeen = "Additional"; $subjtypeth = "เพิ่มเติม"; break;
			case "A": $subjtypeen = "Activity"; $subjtypeth = "กิจกรรม"; break;
			case "N": $subjtypeen = "No unit"; $subjtypeth = "ไม่มีหน่วยกิจ"; break;
		} return array("en" => $subjtypeen, "th" => $subjtypeth);
	}
	function pblcode2text($pblcode) {
		switch ($pblcode) {
			case "A": $grpnameeen = "Mathematics"; $grpnameeth = "คณิตศาสตร์"; break;
			case "B": $grpnameeen = "STEM"; $grpnameeth = "สะเต็มศึกษา"; break;
			case "C": $grpnameeen = "Botanic Garden"; $grpnameeth = "สวนพฤกษศาสตร์โรงเรียน"; break;
			case "D": $grpnameeen = "Thai language"; $grpnameeth = "ภาษาไทย"; break;
			case "E": $grpnameeen = "Social studies"; $grpnameeth = "สังคมศึกษา ศาสนาและวัฒนธรรม"; break;
			case "F": $grpnameeen = "Economic sufficiency"; $grpnameeth = "หลักปรัชญาของเศรษฐกิจพอเพียง"; break;
			case "G": $grpnameeen = "English language"; $grpnameeth = "ภาษาอังกฤษ"; break;
			case "H": $grpnameeen = "Foreign languages"; $grpnameeth = "ภาษาต่างประเทศ"; break;
			case "I": $grpnameeen = "Art & Music"; $grpnameeth = "ศิลปะ"; break;
			case "J": $grpnameeen = "Health & P.E."; $grpnameeth = "สุขศึกษาและพลศึกษา"; break;
			case "K": $grpnameeen = "Occupation & Housework"; $grpnameeth = "การงานอาชีพ"; break;
			case "L": $grpnameeen = "Computer & Robots"; $grpnameeth = "คอมพิวเตอร์และหุ่นยนต์"; break;
			case "M": $grpnameeen = "Science"; $grpnameeth = "วิทยาศาสตร์"; break;
			default: $grpnameeen = ""; $grpnameeth = ""; break;
		} return array("en" => $grpnameeen, "th" => $grpnameeth);
	}
?>