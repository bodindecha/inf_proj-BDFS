<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "เปิดบัญชีแทนผู้อื่น";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	require_once($dirPWroot."project/BDFS/resource/hps/permission.php");
	$has_perm = getProjectPermission(false);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=MOD" };
				var sv = { inited: false, blacklist: [] };
				var initialize = function() {
					if (!sv.inited) {
						
						sv.inited = true;
					}
				},
				choose = function() {
					fsa.start("เลือกบัญชีผู้ใช้งาน", 'main .form input[name="user"]', 'main .form input[name="user"] + input[readonly]', sv.blacklist.join(","));
				},
				validate = function() {
					(function() {
						sv.user = d.forms[0]["user"].value.trim();
						if (!sv.user.length) {
							$('main .form input[name="user"] + input[readonly]').focus();
							return app.ui.notify(1, [1, "Please select a user"]);
						} checkAval();
					}()); return false;
				}, checkAval = async function() {
					await ajax(cv.API_URL, {act: "putNewAccount", param: sv.user}).then(function(dat) {
						if (dat) {
							switch (dat.type) {
								case "EXISTED": app.ui.notify(1, [2, "ผู้ใช้งานนี้เปิดบัญชีแล้ว"]); break;
								case "CREATED": app.ui.notify(1, [0, "เปิดบัญชีแทนผู้ใช้งานสำเร็จ"]); break;
							} sv.blacklist.push(sv.user);
							$('main .form input[name="user"]').val("");
							$('main .form input[name="user"] + input[readonly]').val("");
						} sv.user = "";
					});
				};
				return {
					init: initialize,
					selectAccount: choose,
					confirm: validate
				};
			}(document));
		</script>
		<script type="text/javascript" src="/resource/js/extend/fs-account.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				<form class="form">
					<div class="group">
						<span data-title="บัญชีผู้ใช้งานอินเตอร์เน็ต">ชื่อบัญชี</span>
						<input type="hidden" name="user" />
						<input type="text" onFocus="BDTB.selectAccount()" readonly />
					</div>
					<button class="blue" onClick="return BDTB.confirm()">เปิดบัญชี(แทน)</button>
				</form>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>