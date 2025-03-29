<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "สมุดบัญชีของฉัน";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			body {
				background-image: url('/resource/images/trash-bank.jpg');
				background-repeat: no-repeat; background-position: left, top, center;
				background-size: cover;
			}
			main {
				background-color: var(--fade-white-2) !important;
				backdrop-filter: blur(5px);
			}
			main p { margin: 0 0 10px; }
			main .form p { margin-bottom: 0; }
			main .center button, main [center] button, main .center [role="button"], main [center] [role="button"] { margin: 0 auto; }
			main .loading { padding: 25px 5px 10px; }
			main .loading img { margin: 0 auto; }
			main .status [name="stt"] { font-weight: 500; }
			main .status .refresh { position: absolute; right: calc(10px + 1rem); }
			main .status .refresh i { transform: rotate(135deg); }
			main .status .refresh[disabled] i { animation: rot_rfi 1.5s ease-in-out infinite; }
			@keyframes rot_rfi {
				from {transform: rotate(135deg); }
				to {transform: rotate(-225deg); }
			}
			main .status code { padding: 0.5px 2.5px; }
			main .statement .browser {
				--box-border: 1.25px solid var(--clr-bs-gray-dark);
				display: flex; flex-direction: column;
			}
			main .statement .browser div.head {
				padding: 2.5px 12.5px;
				border: var(--box-border); border-radius: 7.5px 7.5px 0px 0px;
				background-color: var(--clr-pp-grey-400);
				overflow: hidden; align-items: center;
			}
			main .statement .browser div.head select { background-color: var(--clr-pp-grey-200); }
			main .statement .browser div.body {
				max-height: 640px;
				border-top: none; border-bottom: var(--box-border); border-left: var(--box-border); border-right: var(--box-border);
				border-radius: 0px 0px 7.5px 7.5px;
				background-color: var(--clr-gg-grey-300);
				overflow: auto;
			}
			main .statement .browser div.body tr > * {
				padding: 5px;
				border-left: none; border-right: none;
			}
			main .statement .browser div.body tr td {
				text-align: center;
				transition: var(--time-tst-fast);
			}
			main .statement .browser div.body tr[onClick^="BDTB.openTrans("] { cursor: pointer; }
			main .statement .browser div.body tr[onClick^="BDTB.openTrans("]:hover td { background-color: var(--fade-black-7); }
			main .statement .browser div.body tr td:nth-child(1) { font-family: "IBM Plex Sans Thai", monospace; }
			main .statement .browser div.body tr td:nth-child(n+2):nth-last-child(n+2) > span[data-pad] { display: inline-block; }
			main .statement .browser div.body tr td:nth-child(n+2):nth-last-child(n+2) > span[data-pad]:before {
				color: transparent;
				content: attr(data-pad);
			}
			main .statement .browser div.body tr td:nth-child(5) { font-family: "IBM Plex Sans Thai", "Sarabun", sans-serif; }
			@media only screen and (max-width: 768px) {
				main .statement .browser div.head { padding: 2.5px 7.5px; }
			}
			.EULA ol {
				font-size: 18.75px;
				list-style-type: thai;
			}
			.EULA .form { margin: 5px; }
			@media only screen and (max-width: 768px) {
				.EULA ol { font-size: 12.5px; }
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=USR" };
				var sv = { inited: false, refreshTimeout: null }, actionButton;
				var initialize = function() {
					if (!sv.inited) {
						setTimeout(getAccount, 500);
						actionButton = {
							freeze: () => $('main button, main .statement .browser select[name="month"]').attr("disabled", ""),
							unfreeze: () => $('main button, main .statement .browser select[name="month"]').removeAttr("disabled")
						};
						$('main .statement .browser select[name="month"]').on("change", loadStateList);
						sv.inited = true;
					}
				},
				resetRefreshTimer = function() {
					if (sv.refreshAble) {
						$("main .status .refresh").removeAttr("disabled");
						$("main .loading").hide();
						sv.refreshTimeout = null;
					}
				},
				getAccount = async function(user=false) {
					$("main .loading").show();
					if (user) {
						$("main .status .refresh").attr("disabled", "");
						sv.refreshAble = false;
						sv.refrestTimeout = setTimeout(resetRefreshTimer, 5000);
					} await ajax(cv.API_URL, {act: "getAccountStatus"}).then(function(dat) {
						sv.refreshAble = true;
						if (sv.refreshTimeout != null) resetRefreshTimer();
						if (dat == false) setTimeout(getAccount, 30000);
						else {
							if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
							if (!user) $("main .loading").hide();
							switch (dat.stt) {
								case "NONE": {
									$("main .status, main .statement, main .statement .browser").hide();
									actionButton.unfreeze();
									$("main .statement .start, main .new-account").show();
									$("main .loading").hide();
								} break;
								case "ACTIVE": case "CLOSED": {
									$("main .new-account").hide();
									Object.keys(dat).forEach(ei => {
										d.querySelector('main .status [name="'+ei+'"]').value = dat[ei];
									}); $('main .status [name="stt"]').css("color", "var(--clr-bs-"+(dat["stt"]=="ACTIVE"?"green":"red")+")");
									$("main .status, main .statement").show();
								} break;
							}
						}
					});
				},
				createAccount = async function(agree=null) {
					if (agree == null) app.ui.lightbox.open("top", {title: "ข้อตกลง", allowclose: true, autoclose: 300000, html: $("main .EULA.wrapper").html()});
					else {
						app.ui.lightbox.close();
						if (!agree) return;
						actionButton.freeze();
						$("main .loading").show();
						await ajax(cv.API_URL, {act: "putNewAccount"}).then(function(dat) {
							if (dat) {
								switch (dat.type) {
									case "CREATED": app.ui.notify(1, [0, "Your trash bank account has been created."]); break;
									case "EXISTED": app.ui.notify(1, [1, "Your trash bank account already exists."]); break;
								} getAccount();
							} else {
								$("main .loading").hide();
								actionButton.unfreeze();
							}
						});
					}
				},
				loadStateMonth = async function() {
					actionButton.freeze();
					$("main .loading").show();
					await ajax(cv.API_URL, {act: "getStateMonth"}).then(function(dat) {
						$("main .loading").hide();
						actionButton.unfreeze();
						if (dat) {
							$("main .statement .start").hide();
							var opts = $('main .statement .browser select[name="month"]');
							dat.forEach(em => opts.append('<option value="'+em.month+'">'+em.month.replace("-", "/")+' ['+em.trns+']</option>'));
							$("main .statement .browser").show();
						}
					});
				},
				loadStateList = async function() {
					var q = d.querySelector('main .statement .browser select[name="month"]').value.trim();
					if (q.length) {
						actionButton.freeze();
						$("main .loading").show();
						await ajax(cv.API_URL, {act: "getStateList", month: q}).then(function(dat) {
							$("main .loading").hide();
							actionButton.unfreeze();
							if (dat) {
								var target = d.querySelector("main .statement .browser div.body tbody"); target.innerHTML = "";
								var empty = $("main .statement .browser div.body center");
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
				const amtDisp = amount => '<span data-pad="'+"0".repeat(3-amount.length)+'">'+amount+'</span>';
				return {
					init: initialize,
					createAccount: createAccount,
					getStatement: loadStateMonth,
					loadStatement: loadStateList,
					refresh: data => { data=="status" ? getAccount(true) : void(0); }
				};
			}(document));
		</script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2><?=$header_title?></h2>
				<div class="new-account message default center" style="display: none;">
					<p><?=$_SESSION["auth"]["name"]["th"]["a"]?><br>คุณยังไม่มีบัญชีธนาคารขยะ</p>
					<button class="blue" onClick="BDTB.createAccount()">เปิดบัญชี</button>
				</div>
				<div class="status form message cyan" style="display: none;">
					<p center><u>ชื่อบัญชี</u><br><b><?=$_SESSION["auth"]["name"]["th"]["a"]?></b></p>
					<p>สถานะบัญชี: <code><output name="stt"></output></code></p>
					<div class="group">
						<span>บริจาคทั้งหมด</span>
						<input name="act" type="number" readonly />
						<span>รายการ</span>
					</div><div class="group">
						<span>ยอดขยะคงเหลือ</span>
						<input name="amt" type="number" readonly />
						<span>ชิ้น</span>
					</div><div class="group">
						<span>ยอดขยะสะสม</span>
						<input name="tot" type="number" readonly />
						<span>ชิ้น</span>
					</div><div class="group" data-title="ได้รับเมื่อโรงเรียนนำขยะไปจำหน่าย" hidden>
						<span>เครดิต</span>
						<input name="bal" type="number" readonly />
						<span>ลายเซ็น</span>
					</div>
					<p>ข้อมูลเมื่อ <output name="upd"></output></p>
					<div class="refresh">
						<a onClick="BDTB.refresh('status')" href="javascript:"><i class="material-icons">sync</i></a>
					</div>
				</div>
				<div class="statement" style="display: none;">
					<div class="start center">
						<button class="gray hollow" onClick="BDTB.getStatement()" style="transform: scale(0.8);">View statement</button>
					</div>
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
				</div>
				<div class="loading center"><img src="/resource/images/widget-load_spinner.gif" draggable="false" height="50"></div>
				<div class="EULA wrapper" hidden><div class="EULA">
					<ol>
						<li>ขยะที่นักเรียนนำมาฝากได้คือ ขวดน้ำพลาสติกและแก้วน้ำพลาสติก</li>
						<li>นักเรียนต้องเทน้ำออกจากขวดหรือแก้วน้ำพลาสติกให้หมด และบีบอัดขวดหรือแก้วน้ำก่อนนำมามอบให้ธนาคารขยะรีไซเคิล</li>
						<li>ธนาคารขยะรีไซเคิล ตั้งอยู่บริเวณโรงอาหารชั้น ๑ ทางออกไปยังอาคาร ๑</li>
						<li>ช่วงเวลาที่นักเรียนสามารถนำขยะมาฝากธนาคารขยะรีไซเคิลคือ วันพฤหัสบดีคาบที่ ๑ (คาบกิจกรรมชุมนุม) และคาบ ๙ - ๑๐ (๑๕.๑๐ - ๑๖.๕๐ น.) โดยนักเรียนจะต้องนำมาฝากด้วยจนเองเท่านั้น</li>
						<li>นักเรียนที่จะเข้าร่วมกิจกรรมจะต้องสมัครเป็นสมาชิกธนาคารรีไซเคิลก่อน และรับสมุดคู่ฝากถอนธนาคารขยะรีไซเคิลเพื่อใช้บันทึกข้อมูลการรับฝากขยะ</li>
						<li>นักเรียนเริ่มฝากขยะได้ตั้งแต่วันพฤหัสบดีที่ ๒๔ พฤศจิกายน พ.ศ.๒๕๖๕ เป็นต้นไป</li>
						<li>รายได้ที่เกิดจากการจำหน่ายขวดน้ำพลาสติกและแก้วน้ำพลาสติกจะนำมาใช้ในการดำเนินโครงการโรงเรียนปลอดขยะ</li>
					</ol>
					<hr>
					<div class="form"><div class="group split">
						<button class="red hollow" onClick="BDTB.createAccount(false)">ยกเลิก/ย้อนกลับ</button>
						<button class="green" onClick="BDTB.createAccount(true)">ตกลง/ยืนยัน</button>
					</div></div>
				</div></div>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>