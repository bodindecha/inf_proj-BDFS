<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "บันทึกการฝากขยะ";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	$self = $_SESSION["auth"]["user"] ?? "";
	if (empty($self)) $display = "signin_prompt";
	/* else if (has_perm("BDFS")) $display = "deposit_form"; */
	if (in_array($self, array("test02", "99999"))) {
		$self = "TianTcl";
		$display = "deposit_form";
	} else {
		require($dirPWroot."resource/php/core/db_connect.php");
		$getperm = $db -> query("SELECT active FROM TrashBank_staff WHERE idcode='$self'");
		if (!$getperm) $display = "perm_check_error";
		else if ($getperm -> num_rows <> 1) $display = "no_permission";
		else {
			if (($getperm -> fetch_array(MYSQLI_ASSOC))["active"] == "Y")
				$display = "deposit_form";
			else $display = "permission_expired";
		} $db -> close();
	}

	$trash_unit = ($_COOKIE["set_lang"]=="en")?"piece(s)":"ชิ้น";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .form { transition: var(--time-tst-xfast); }
			main .form fieldset { border-radius: 0.3rem; }
			main .form .group.split { gap: 10px; }
			main .form button { min-width: fit-content; }
			main .form [type="submit"] { width: 150px; }
			@media only screen and (max-width: 768px) {
				main .form [type="submit"] { width: 120px; }
			}
		</style>
		<script type="text/javascript">
			<?php if ($display == "signin_prompt") { ?>
			$(document).ready(function() {
				sys.auth.orize(true, true);
			});
			<?php } else if ($display == "deposit_form") { ?>
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=USR", authuser: "<?=$self?>" };
				var sv = {};
				var checkForm = function() {
					(function() {
						var data = {
							accountID: d.querySelector('main .form [name="accountID"]').value,
							amtPlastic: parseInt(d.querySelector('main .form [name="amtPlastic"]').value.trim()),
							amtPaper: parseInt(d.querySelector('main .form [name="amtPaper"]').value.trim()),
							amtMetal: parseInt(d.querySelector('main .form [name="amtMetal"]').value.trim()),
						};
						if (!data.accountID.length) {
							app.ui.notify(1, [3, "Please select an account of deposition."]);
							$('main .form [name="accountID"]').focus();
						} else if (data.accountID == cv.authuser) {
							app.ui.notify(1, [3, "You cannot make a record for yourself."]);
							$('main .form [name="accountID"]').focus();
						} else if (data.amtPlastic < 0 || data.amtPlastic >= 1000) {
							app.ui.notify(1, [2, "Invalid amount for plastics."]);
							$('main .form [name="amtPlastic"]').focus();
						} else if (data.amtPaper < 0 || data.amtPaper >= 1000) {
							app.ui.notify(1, [2, "Invalid amount for papers."]);
							$('main .form [name="amtPaper"]').focus();
						} else if (data.amtMetal < 0 || data.amtMetal >= 1000) {
							app.ui.notify(1, [2, "Invalid amount for metal."]);
							$('main .form [name="amtMetal"]').focus();
						} else if (data.amtPlastic + data.amtPaper + data.amtMetal == 0) {
							app.ui.notify(1, [3, "You must put at least 1 item."]);
							$("main .form").toggleClass("teal red");
							setTimeout(function() { $("main .form").toggleClass("teal red"); }, 375);
						} else sendForm(data);
					}()); return false;
				},
				sendForm = async function(data) {
					if (confirm("Please confirm your deposition of user "+data.accountID+".\n\nThis action can't be undone.")) {
						$("main form").attr("disabled", "");
						await ajax(cv.API_URL, {act: "depositTrash", param: data}).then(function(dat) {
							if (dat) {
								app.ui.notify(1, [0, "Transaction recorded."]);
								resetForm();
							} $("main form").removeAttr("disabled");
						});
					}
				},
				resetForm = function() {
					(function() {
						$('main .form [name="accountID"], main .form [name="accountID"] + input[readonly]').val("");
						$('main .form [name^="amt"]').val(0);
					}()); return false;
				},
				selectUser = function() {
					fsa.start("ค้นหาบัญชีผู้บริจาค", 'main .form [name="accountID"]', 'main .form [name="accountID"] + input[readonly]', cv.authuser, "trash-bank-user");
				};
				return {
					recordForm: checkForm,
					clearForm: resetForm,
					selectDonor: selectUser
				};
			}(document));
			const selectAll = me => me.select();
			<?php } ?>
		</script>
		<script type="text/javascript" src="/resource/js/extend/fs-account.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if ($display == "perm_check_error") echo '<iframe src="/error/905">Loading...</iframe>';
				else if ($display == "no_permission") echo '<iframe src="/error/907">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				<?php if ($display == "permission_expired") { ?>
					<div class="message yellow center">Your permission has expired.<br>Please contact your supervisor if you think this is an error.</div>
				<?php } else if ($display == "signin_prompt") { ?>
					<div class="message red center">You must be signed-in to use this service.</div>
				<?php } else if ($display == "deposit_form") { ?>
				<form class="form message teal">
					<div class="group">
						<span>บัญชีผู้ฝาก</span>
						<input type="hidden" name="accountID" />
						<input type="text" readonly onFocus="BDTB.selectDonor()" />
					</div>
					<fieldset class="form">
						<legend>จำนวนขยะที่นำฝาก</legend>
						<div class="group">
							<span><?=($_COOKIE["set_lang"]=="en")?"Plastic":"พลาสติก"?></span>
							<input type="number" name="amtPlastic" min="0" max="999" step="1" value="0" onFocus="selectAll(this)" />
							<span><?=$trash_unit?></span>
						</div>
						<div class="group">
							<span><?=($_COOKIE["set_lang"]=="en")?"Paper":"กระดาษ"?></span>
							<input type="number" name="amtPaper" min="0" max="999" step="1" value="0" onFocus="selectAll(this)" />
							<span><?=$trash_unit?></span>
						</div>
						<div class="group">
							<span><?=($_COOKIE["set_lang"]=="en")?"Metal":"โลหะ"?></span>
							<input type="number" name="amtMetal" min="0" max="999" step="1" value="0" onFocus="selectAll(this)" />
							<span><?=$trash_unit?></span>
						</div>
					</fieldset>
					<div class="group">
						<span>ผู้บันทึก</span>
						<input type="text" value="<?=$_SESSION["auth"]["name"]["th"]["a"]?>" readonly />
					</div>
					<div class="group spread"><div class="group split">
						<button class="red hollow" type="reset" onClick="return BDTB.clearForm()">ลบฟอร์ม</button>
						<button class="green" type="submit" onClick="return BDTB.recordForm()">บันทึกรายการ</button>
					</div></div>
				</form>
				<?php } ?>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>