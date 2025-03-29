<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "สร้างประกาศ";
	$header_desc = "Lost n' Found - BODIN";
	$home_menu = "lost-n-found";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .form textarea {
				min-height: 48px; max-height: 192px;
				resize: vertical;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				seek_param();
				$('main .form [name="usf"]').on("change", function() { validate_file(false); });
			});
			function seek_param() { if (location.hash.length > 1) {
				// Extract hashes
				var hash = {}; location.hash.substring(1, location.hash.length).split("&").forEach((ehs) => {
					let ths = ehs.split("=");
					hash[ths[0]] = ths[1];
				});
				// Let's see
				if (typeof hash.status !== "undefined") gainNoti(hash.status);
				history.replaceState(null, null, location.pathname+location.search);
			} }
			function gainNoti(status) {
				switch (status) {
					case "success": app.ui.notify(1, [0, "Your post is being posted."]); break;
					case "failed": app.ui.notify(1, [3, "Your post has not been published."]); break;
				}
			}
			var sv = {};
			const mb2b = MB => MB*1024000,
				kb2mb = KB => KB/1024,
				b2kb = B => B/1024,
				b2mb = B => B/1024000;
			var byte2text = function(bytes) {
				let nv;
				if (bytes < 1024000) nv = Math.round(b2kb(bytes)*100)/100;
				else nv = Math.round(b2mb(bytes)*100)/100;
				if (!nv*100%100) nv = parseInt(nv);
				return nv+(bytes < 1024000 ? " KB" : " MB");
			};
			var validate_file = function(recheck) {
				var f = document.querySelector('.form [name="usf"]').files[0],
					preview = $("main .form div.file-box"), fprop = {
						name: document.querySelector('main .form input[data-name="name"]'),
						size: document.querySelector('main .form input[data-name="size"]')
					};
				// if (!recheck && typeof sv.img_link === "string") URL.revokeObjectURL(sv.img_link);
				if (typeof f !== "undefined") {
					let filename = f.name.toLowerCase().split(".");
					if (["png", "jpg", "jpeg", "heic", "heif", "gif"].includes(filename[filename.length-1]) && (f.size > 0 && f.size < mb2b(5))) {
						if (!recheck) {
							fprop["name"].value = f.name;
							fprop["size"].value = byte2text(f.size);
							try { if (!isSafari) {
								sv.img_link = URL.createObjectURL(f);
								preview.css("background-image", 'url("'+sv.img_link+'")');
							} } catch(ex) {}
						} return true;
					} else app.ui.notify(1, [2, "กรุณาตรวจสอบว่าไฟล์ของคุณเป็นประเภท PNG/JPG/JPEG/HEIC/HEIF/GIF และมีขนาดไม่เกิน 5MB"]);
				} else {
					fprop["name"].value = ""; fprop["size"].value = "";
					preview.removeAttr("style");
					if (recheck) app.ui.notify(1, [1, "กรุณาเลือกไฟล์"]);
				} return false;
			};
			var sendForm = function(type, e) {
				// if (e.preventDefault) e.preventDefault();
				$('main form input[name="param[type]"]').val(type);
				// document.querySelector("main form").submit();
			}
		</script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2><?=$header_title?></h2>
				<form class="form" method="post" enctype="multipart/form-data" action="api?type=post&act=new">
					<div class="group">
						<span>สิ่งของ</span>
						<input type="text" name="param[name]" required maxlength="30" placeholder="เช่น กระติกน้ำ, พวงกุญแจ" />
					</div>
					<span>คำอธิบายสิ่งของ</span>
					<textarea name="param[desc]" maxlength="250" placeholder="เช่น สีแดง, ลายจุด"></textarea>
					<div class="group">
						<span>สถานที่พบเจอ/สูญหาย</span>
						<input type="text" name="param[loc]" required maxlength="50" />
					</div>
					<div class="group">
						<span>เวลาที่พบเจอ/สูญหาย (โดยประมาณ)</span>
						<input type="time" name="param[time]" />
					</div>
					<span>รูปภาพประกรอบ</span>
					<div class="file-box">
						<input type="file" name="usf" />
					</div>
					<div class="group split">
						<div class="group">
							<span>ชื่อไฟล์</span>
							<input type="text" readonly data-name="name" />
						</div>
						<div class="group">
							<span>ขนาดไฟล์</span>
							<input type="text" readonly data-name="size" />
						</div>
					</div>
					<div class="group">
						<span>ประเภทสิ่งของ</span>
						<select name="param[ctgr]" required>
							<option value selected disabled>---กรุณาเลือก---</option>
							<option value="edu">อุปกรณ์การเรียน</option>
							<option value="sport">อุปกรณ์กีฬา</option>
							<option value="personal">เครื่องใช้ส่วนตัว</option>
							<option value="other">อื่นๆ</option>
						</select>
					</div>
					<input name="param[type]" type="hidden" />
					<div class="group spread" style="gap: 10px;">
						<button onClick="sendForm('L', event)" class="orange full-x">ประกาศ<b>ของหาย</b></button>
						<button onClick="sendForm('F', event)" class="cyan full-x">ประกาศ<b>พบของ</b></button>
					</div>
				</form>

			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>