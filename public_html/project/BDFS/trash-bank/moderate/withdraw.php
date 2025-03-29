<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "ถอนขยะฝากเป็นลายเซ็นจิตอาสา";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	require_once($dirPWroot."project/BDFS/resource/hps/permission.php");
	$has_perm = getProjectPermission(false);

	$trash_unit1 = ($_COOKIE["set_lang"]=="en")?"piece(s)":"ชิ้น";
	$trash_unit2 = ($_COOKIE["set_lang"]=="en")?"signature(s)":"ลายเซ็น";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .form { transition: var(--time-tst-xfast); }
			main .form fieldset { border-radius: 0.3rem; }
			main .form .group.split { gap: 10px; }
			main .form .group.split > .group:nth-child(1) { width: 100%; }
			main .form [size="3"] { min-width: 50px; }
			main .form [readonly]::-webkit-inner-spin-button, main .form [readonly]::-webkit-outer-spin-button { -webkit-appearance: none; } main .form [readonly] { --moz-appearance: none; }
			main .form button { min-width: fit-content; }
			main .form [type="submit"] { width: 150px; }
			@media only screen and (max-width: 768px) {
				main .form [type="submit"] { width: 120px; }
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=MOD", TT: ["Plastic", "Paper", "Metal"] };
				var sv = { inited: false };
				var initialize = function() {
					if (!sv.inited) {
						$('main .form [name^="sgn"]').on("input", updateAmount);
						sv.inited = true;
					}
				},
				updateAmount = function() {
					cv.TT.forEach(et => {
						$('main .form [name="amt'+et+'"]').val($('main .form [name="sgn'+et+'"]').val()*50)
					})
				},
				checkForm = function() {
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
						} else if (data.amtPlastic < 0 || data.amtPlastic > $('main .form [name="amtPlastic"]').attr("max")) {
							app.ui.notify(1, [2, "Invalid amount for plastics."]);
							$('main .form [name="sgnPlastic"]').focus();
						} else if (data.amtPaper < 0 || data.amtPaper > $('main .form [name="amtPaper"]').attr("max")) {
							app.ui.notify(1, [2, "Invalid amount for papers."]);
							$('main .form [name="sgnPaper"]').focus();
						} else if (data.amtMetal < 0 || data.amtMetal > $('main .form [name="amtMetal"]').attr("max")) {
							app.ui.notify(1, [2, "Invalid amount for metal."]);
							$('main .form [name="sgnMetal"]').focus();
						} else if (data.amtPlastic + data.amtPaper + data.amtMetal == 0) {
							app.ui.notify(1, [3, "You must put at least 1 item."]);
							$("main .form").toggleClass("orange red");
							setTimeout(function() { $("main .form").toggleClass("orange red"); }, 375);
						} else sendForm(data);
					}()); return false;
				},
				sendForm = async function(data) {
					if (confirm("Please confirm your withdrawal of user "+data.accountID+".\n\nThis action can't be undone.")) {
						$("main form").attr("disabled", "");
						await ajax(cv.API_URL, {act: "withdrawTrash", param: data}).then(function(dat) {
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
						$('main .form [name^="sgn"]').val(0).removeAttr("max");
						$('main .form [name^="amt"]').val(0).removeAttr("max");
						$('main .form [name^="bal"]').val("");
						$("main .form fieldset").attr("disabled", "")
					}()); return false;
				},
				selectUser = function() {
					fsa.start("ค้นหาบัญชีผู้บริจาค", 'main .form [name="accountID"]', 'main .form [name="accountID"] + input[readonly]', cv.authuser, "trash-bank-user");
				},
				getLimit = async function() {
					var account = $('main input[name="accountID"]').val();
					if (!account.length) return $("main .form fieldset").attr("disabled", "");
					sv.account = account;
					await ajax(cv.API_URL, {act: "getSavings", param: {accountID: sv.account}}).then(function(dat) {
						if (dat) {
							cv.TT.forEach(et => {
								$('main .form [name="bal'+et+'"]').val(dat[et]);
								$('main .form [name="amt'+et+'"]').attr("max", dat[et]);
								$('main .form [name="sgn'+et+'"]').attr("max", Math.floor(parseInt(dat[et])/50));
							}); $("main .form fieldset").removeAttr("disabled");	
						}
					});
				};
				return {
					init: initialize,
					recordForm: checkForm,
					clearForm: resetForm,
					selectDonor: selectUser,
					loadLimit: getLimit
				};
			}(document));
			const selectAll = me => me.select(),
				validate_field = BDTB.loadLimit;
		</script>
		<script type="text/javascript" src="/resource/js/extend/fs-account.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				<form class="form message orange">
					<div class="group">
						<span>บัญชีผู้ฝาก</span>
						<input type="hidden" name="accountID" />
						<input type="text" readonly onFocus="BDTB.selectDonor()" />
					</div>
					<fieldset class="form" disabled>
						<legend>จำนวนขยะที่จะถอน</legend>
						<div class="group split">
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Plastic":"พลาสติก"?></span>
								<input type="number" name="sgnPlastic" min="0" max="10" step="1" value="0" onFocus="selectAll(this)" />
								<span><?=$trash_unit2?></span>
							</div>
							<div class="group">
								<span>คิดเป็น</span>
								<input type="number" name="amtPlastic" readonly size="3" value="0" />
								<span><?=$trash_unit1?></span>
							</div>
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Have":"มี"?></span>
								<input type="number" name="balPlastic" readonly size="3" />
								<span><?=$trash_unit1?></span>
							</div>
						</div>
						<div class="group split">
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Paper":"กระดาษ"?></span>
								<input type="number" name="sgnPaper" min="0" max="10" step="1" value="0" onFocus="selectAll(this)" />
								<span><?=$trash_unit2?></span>
							</div>
							<div class="group">
								<span>คิดเป็น</span>
								<input type="number" name="amtPaper" readonly size="3" value="0" />
								<span><?=$trash_unit1?></span>
							</div>
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Have":"มี"?></span>
								<input type="number" name="balPaper" readonly size="3" />
								<span><?=$trash_unit1?></span>
							</div>
						</div>
						<div class="group split">
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Metal":"โลหะ"?></span>
								<input type="number" name="sgnMetal" min="0" max="10" step="1" value="0" onFocus="selectAll(this)" />
								<span><?=$trash_unit2?></span>
							</div>
							<div class="group">
								<span>คิดเป็น</span>
								<input type="number" name="amtMetal" readonly size="3" value="0" />
								<span><?=$trash_unit1?></span>
							</div>
							<div class="group">
								<span><?=($_COOKIE["set_lang"]=="en")?"Have":"มี"?></span>
								<input type="number" name="balMetal" readonly size="3" />
								<span><?=$trash_unit1?></span>
							</div>
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
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>