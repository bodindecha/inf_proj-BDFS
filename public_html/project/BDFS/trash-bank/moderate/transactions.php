<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "รายการฝาก-ถอนขยะ";
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
			main .browser div.body tr td:nth-child(n+3):nth-last-child(n+2) > span[data-pad] { display: inline-block; }
			main .browser div.body tr td:nth-child(n+3):nth-last-child(n+2) > span[data-pad]:before {
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
						$('main .browser.d select[name="month"]').on("change", loadStateListD);
						$('main .browser.w select[name="month"]').on("change", loadStateListW);
						BDTB.getStatement();
						sv.inited = true;
					}
				}
				loadStateMonth = async function() {
					d.querySelector("main .browser.d div.body tbody").innerHTML = "";
					d.querySelector("main .browser.w div.body tbody").innerHTML = "";
					// Load month list
					await ajax(cv.API_URL, {act: "getStateMonth", param: {accountID: "m-ALL"}}).then(function(dat) {
						if (dat) {
							var opts = $('main .browser.d select[name="month"]')
								.html('<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>');
							dat.forEach(em => opts.append('<option value="'+em.month+'">'+em.month.replace("-", "/")+' ['+em.trns+']</option>'));
							$("main .browser.d").toggle("clip");
						}
					});
					await ajax(cv.API_URL, {act: "getWithdrewMonth"}).then(function(dat) {
						if (dat) {
							var opts = $('main .browser.w select[name="month"]')
								.html('<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>');
							dat.forEach(em => opts.append('<option value="'+em.month+'">'+em.month.replace("-", "/")+' ['+em.trns+']</option>'));
							$("main .browser.w").toggle("clip");
						}
					});
				},
				loadStateListD = async function() {
					var q = d.querySelector('main .browser.d select[name="month"]').value.trim();
					if (q.length) {
						await ajax(cv.API_URL, {act: "getStateList", param: {accountID: "m-ALL"}, month: q}).then(function(dat) {
							if (dat) {
								var target = d.querySelector("main .browser.d div.body tbody"); target.innerHTML = "";
								var empty = $("main .browser.d div.body center");
								if (dat.length) {
									empty.hide();
									dat.forEach(et => {
										target.innerHTML += '<tr><td>'+et.no+'</td><td>'+et.account+'</td><td>'+amtDisp(et.amtPlastic)+'</td><td>'+amtDisp(et.amtPaper)+'</td><td>'+amtDisp(et.amtMetal)+'</td><td>'+et.trns+'</td></tr>';
									});
								} else empty.show();
							}
						});
					} else app.ui.notify(1, [2, "Please select a valid month"]);
				},
				loadStateListW = async function() {
					var q = d.querySelector('main .browser.w select[name="month"]').value.trim();
					if (q.length) {
						await ajax(cv.API_URL, {act: "getWithdrawal", month: q}).then(function(dat) {
							if (dat) {
								var target = d.querySelector("main .browser.w div.body tbody"); target.innerHTML = "";
								var empty = $("main .browser.w div.body center");
								if (dat.length) {
									empty.hide();
									dat.forEach(et => {
										target.innerHTML += '<tr><td>'+et.no+'</td><td>'+et.account+'</td><td>'+amtDisp(et.amtPlastic)+'</td><td>'+amtDisp(et.amtPaper)+'</td><td>'+amtDisp(et.amtMetal)+'</td><td>'+et.trns+'</td></tr>';
									});
								} else empty.show();
							}
						});
					} else app.ui.notify(1, [2, "Please select a valid month"]);
				};
				return {
					init: initialize,
					getStatement: loadStateMonth,
					loadStatement: loadStateListD,
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
				<h2>รายการฝากขยะ</h2>
				<div class="browser d" style="display: none;">
					<div class="head form inline">
						<?=$_COOKIE['set_lang']=="th"?"เดือนที่ทำรายการ":"Month of records"?> <select name="month">
							<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>
						</select>
					</div>
					<div class="body table">
						<table><thead><tr>
							<th><?=$_COOKIE['set_lang']=="th"?"เลขที่":"No."?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"บ/ช":"Acc. No."?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนพลาสติก":"Amt. Plastic"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนกระดาษ":"Amt. Paper"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"จำนวนโลหะ":"Amt. Metal"?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"เวลา":"Time"?></th>
						</tr></thead><tbody></tbody></table>
						<center class="message gray"><?=$_COOKIE['set_lang']=="th"?"ไม่มีการทำรายการ<br>กรุณาเลือกเดือนอื่น":"No transactions.<br>Please select other month."?></center>
					</div>
				</div>
				<br>
				<h2>รายการถอนขยะ</h2>
				<div class="browser w" style="display: none;">
					<div class="head form inline">
						<?=$_COOKIE['set_lang']=="th"?"เดือนที่ทำรายการ":"Month of records"?> <select name="month">
							<option value selected disabled>---<?=$_COOKIE['set_lang']=="th"?"กรุณาเลือก":"Please select"?>---</option>
						</select>
					</div>
					<div class="body table">
						<table><thead><tr>
							<th><?=$_COOKIE['set_lang']=="th"?"เลขที่":"No."?></th>
							<th><?=$_COOKIE['set_lang']=="th"?"บ/ช":"Acc. No."?></th>
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