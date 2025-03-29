<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "Trash bank - B.D.F.S";
	$header_desc = "Dashboard";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	require_once($dirPWroot."project/BDFS/resource/hps/permission.php");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main ul { height: auto !important; }
			main ul li.dl { width: 100% !important; }
		</style>
		<link rel="stylesheet" href="/resource/css/extend/all-index.css">
		<script type="text/javascript">

		</script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
		<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<p><?php echo ($_COOKIE['set_lang']=="en"?"Welcome ":"ยินดีต้อนรับ ").$_SESSION['auth']['name'][/*$_COOKIE['set_lang']*/'th']['a']; ?> (<?php echo $_SESSION['auth']['user']; ?>)</p>
				<p><?php echo ($_COOKIE['set_lang']=="en"?"to project B.D.F.S - Recycle trash bank session":"เข้าสู่โปรเจค B.D.F.S - แผนกธนาคารขยะรีไซเคิล"); ?></p>
				<br>
				<p><?php echo ($_COOKIE['set_lang']=="en"?"You can take action by selecting menu from list below":"คุณสามารถเลือกทำการได้จากรายการเมนูด้านล่าง"); ?></p>
				<ul>
					<li><a href="/project/BDFS/trash-bank/deposit">บันทึกการฝากขยะ</a></li>
					<li><a href="/project/BDFS/trash-bank/statement">ตรวจสอบ statement บัญชี</a></li>
				</ul><br>
				<?php if (getProjectPermission(false)) { ?>
				<details open>
					<summary>สำหรับครูณัฐพงศ์</summary><ul>
						<li><a href="/project/BDFS/trash-bank/moderate/storage">สรุปยอดปริมาณขยะ</a></li>
						<li><a href="/project/BDFS/trash-bank/moderate/transactions">รายการฝาก/ถอนขยะทั้งหมด</a></li>
						<li class="dl">&nbsp;</li>
						<li class="dt">ถอนขยะ</li>
						<li><a href="/project/BDFS/trash-bank/moderate/withdraw">เป็นลายเซ็น</a></li>
						<li disabled><a href="/project/BDFS/trash-bank/moderate/clear-bank">นำจำหน่าย</a></li>
						<li class="dl">&nbsp;</li>
						<li disabled><a href="/project/BDFS/trash-bank/moderate/new-staff">Manage นายธนาคาร</a></li>
						<li><a href="/project/BDFS/trash-bank/moderate/open-account">เปิดบัญชีให้ผู้อื่น</a></li>
					</ul>
				</details>
				<?php } ?>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>