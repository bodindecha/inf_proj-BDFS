<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "B.D.F.S - Project";
	$header_desc = "Dashboard";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	header("Location: /v2/");
	exit(0);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<link rel="stylesheet" href="/resource/css/extend/all-index.css">
		<script type="text/javascript" src="/resource/js/extend/all-index.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<p><?php echo ($_COOKIE['set_lang']=="en"?"Welcome ":"ยินดีต้อนรับ ").$_SESSION['auth']['name'][/*$_COOKIE['set_lang']*/'th']['a']; ?> (<?php echo $_SESSION['auth']['user']; ?>)</p>
				<p><?php echo ($_COOKIE['set_lang']=="en"?"to project B.D.F.S (Barter-Deposit-Found-Share)":"เข้าสู่โปรเจค B.D.F.S (ฝาก-แลก-แจก-คืน)"); ?></p><br>
				<br>
				<input name="trash-bank" type="checkbox" id="ref_menu-a"><label for="ref_menu-a">ธนาคารขยะรีไซเคิล</label><ul>
					<li><a href="/project/BDFS/trash-bank/my">สมุดบัญชีของฉัน</a></li>
					<li class="dl">&nbsp;</li>
					<li><a href="/project/BDFS/trash-bank/moderate/">สำหรับเจ้าหน้าที่</a></li>
				</ul>
				<input name="P-2-N" type="checkbox" id="ref_menu-b"><label for="ref_menu-b" disabled>ของพี่ให้น้อง</label><ul>
					<li><a href="/project/BDFS/P-2-N/">...</a></li>
				</ul>
				<input name="lost-n-found" type="checkbox" id="ref_menu-c"><label for="ref_menu-c">ของหายได้คืน</label><ul>
					<li><a href="/project/BDFS/LnF/post"><span>สร้างประกาศ</span></a></li>
					<li class="dl">&nbsp;</li>
					<li><a href="/project/BDFS/LnF/list/lost"><span>รายการของหาย</span></a></li>
					<li><a href="/project/BDFS/LnF/list/found"><span>รายการของที่พบ</span></a></li>
				</ul>
				<input name="punsuke-village" type="checkbox" id="ref_menu-d"><label for="ref_menu-d" disabled>หมู่บ้านปันสุข</label><ul>
					<li><a disabled href="/project/BDFS/punsuke-village/">...</a></li>
				</ul>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>