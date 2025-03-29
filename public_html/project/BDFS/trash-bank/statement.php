<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "รายการฝากขยะ";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	require_once($dirPWroot."project/BDFS/resource/hps/permission.php");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .browser {
				--box-border: 1.25px solid var(--clr-bs-gray-dark);
				display: flex; flex-direction: column;
			}
			main .browser div.head {
				padding: 2.5px 12.5px;
				border: var(--box-border); border-radius: 7.5px 7.5px 0px 0px;
				background-color: var(--clr-pp-grey-400);
				overflow: hidden; align-items: center;
			}
			main .browser div.head select { background-color: var(--clr-pp-grey-200); }
			main .browser div.body {
				max-height: 640px;
				border-top: none; border-bottom: var(--box-border); border-left: var(--box-border); border-right: var(--box-border);
				border-radius: 0px 0px 7.5px 7.5px;
				background-color: var(--clr-gg-grey-300);
				overflow: auto;
			}
			main .browser div.body tr > * {
				padding: 5px;
				border-left: none; border-right: none;
			}
			main .browser div.body tr td {
				text-align: center;
				transition: var(--time-tst-fast);
			}
			main .browser div.body tr[onClick^="BDTB.openTrans("] { cursor: pointer; }
			main .browser div.body tr[onClick^="BDTB.openTrans("]:hover td { background-color: var(--fade-black-7); }
			main .browser div.body tr td:nth-child(1) { font-family: "IBM Plex Sans Thai", monospace; }
			main .browser div.body tr td:nth-child(n+2):nth-last-child(n+2) > span[data-pad] { display: inline-block; }
			main .browser div.body tr td:nth-child(n+2):nth-last-child(n+2) > span[data-pad]:before {
				color: transparent;
				content: attr(data-pad);
			}
			main .browser div.body tr td:nth-child(5) { font-family: "IBM Plex Sans Thai", "Sarabun", sans-serif; }
			@media only screen and (max-width: 768px) {
				main .browser div.head { padding: 2.5px 7.5px; }
			}
		</style>
		<script type="text/javascript">
			<?php if ($has_perm) { ?>
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=MOD" };
				var sv = { inited: false };
				var initialize = function() {
					if (!sv.inited) {
						$('main .browser select[name="month"]').on("change", loadStateList);
						sv.inited = true;
					}
				},
				selectUser = function() {
					fsa.start("ค้นหาบัญชีผู้บริจาค", 'main .form [name="accountID"]', 'main .form [name="accountID"] + input[readonly]', cv.authuser, "trash-bank-user");
				},
				loadStateMonth = async function() {
					var account = $('main input[name="accountID"]').val();
					if (!account.length) return $("main .browser").hide();
					sv.account = account;
					d.querySelector("main .browser div.body tbody").innerHTML = "";
					$("main .browser div.body center").show();
					// Load month list
					await ajax(cv.API_URL, {act: "getStateMonth", param: {accountID: sv.account}}).then(function(dat) {
						if (dat) {
							var opts = $('main .browser select[name="month"]')
								.html('<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>');
							dat.forEach(em => opts.append('<option value="'+em.month+'">'+em.month.replace("-", "/")+' ['+em.trns+']</option>'));
							$("main .browser").show();
						}
					});
				},
				loadStateList = async function() {
					var q = d.querySelector('main .browser select[name="month"]').value.trim();
					if (q.length) {
						await ajax(cv.API_URL, {act: "getStateList", param: {accountID: sv.account}, month: q}).then(function(dat) {
							if (dat) {
								var target = d.querySelector("main .browser div.body tbody"); target.innerHTML = "";
								var empty = $("main .browser div.body center");
								if (dat.length) {
									empty.hide();
									dat.forEach(et => {
										target.innerHTML += '<tr><td>'+et.no+'</td><td>'+amtDisp(et.amtPlastic)+'</td><td>'+amtDisp(et.amtPaper)+'</td><td>'+amtDisp(et.amtMetal)+'</td><td>'+et.trns+'</td></tr>';
									});
								} else empty.show();
							}
						});
					} else app.ui.notify(1, [2, "Please select a valid month"]);
				};
				return {
					init: initialize,
					selectDonor: selectUser,
					getStatement: loadStateMonth,
					loadStatement: loadStateList,
				};
			}(document));
			const amtDisp = amount => '<span data-pad="'+"0".repeat(3-amount.length)+'">'+amount+'</span>',
				validate_field = BDTB.getStatement;
			<?php } ?>
		</script>
		<script type="text/javascript" src="/resource/js/extend/fs-account.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				<form class="form --inline">
					<div class="group">
						<span>บัญชีผู้ฝาก</span>
						<input type="hidden" name="accountID" />
						<input type="text" readonly onFocus="BDTB.selectDonor()" />
					</div>
				</form>
				<div class="browser" style="display: none;">
					<div class="head form inline">
						<?=$_COOKIE['set_lang']=="th"?"เดือนที่ทำรายการ":"Month of records"?> <select name="month">
							<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>
						</select>
					</div>
					<div class="body table">
						<table><thead><tr>
							<th><?=$_COOKIE['set_lang']=="th"?"เลขที่":"No."?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนพลาสติก":"Amt. Plastic"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนกระดาษ":"Amt. Paper"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนโลหะ":"Amt. Metal"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"เวลา":"Time"?></th>
						</tr></thead><tbody></tbody></table>
						<center class="message gray"><?=$_COOKIE['set_lang']=="th"?"ไม่มีการทำรายการ<br>กรุณาเลือกเดือนอื่น":"No transactions.<br>Please select other month."?></center>
					</div>
				</div>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>