<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");

	if (isset($_GET['u']) xor isset($_GET['user']) xor isset($_GET['username'])) $user = trim($_GET['u'].$_GET['user'].$_GET['username']);
	if (isset($_GET['p']) xor isset($_GET['pass']) xor isset($_GET['password']) xor isset($_GET['pwd']) xor isset($_GET['pswd'])) $pass = trim($_GET['p'].$_GET['pass'].$_GET['password'].$_GET['pwd'].$_GET['pswd']);

	$return_url = urlencode(urlencode($_GET['return_url'] ?? ""));
	if (!empty($return_url)) $return_url = "%253Freturn_url%253D$return_url";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			html body main div.container div.broadcast > a { transform: translateY(-100%); float: right; }
			form.auth-wrapper {
				margin: 10px 0px; padding: 5px;
				display: block !important;
			}
			form.auth-wrapper > *:not(.reg-fontsize) {
				margin: 2.5px 0px;
				font-size: 20px; font-family: "THSarabunNew", serif;
			}
			form.auth-wrapper input:invalid, form.auth-wrapper input[invalid] { border: 1px solid var(--clr-bs-red) !important; }
			form.auth-wrapper input:invalid:focus, form.auth-wrapper input[invalid]:focus { box-shadow: 0 0 0 0.25rem rgb(220 53 69 / 37.5%) !important; }
			html body main div.container div.sl-frame {
				margin-top: 30px;
				height: 0px;
				border: 1px solid var(--clr-bs-gray); border-radius: 5px;
				display: none; overflow: hidden;
			}
			html body main div.container div.sl-frame p {
				margin: 2.5px 0px 0px; padding: 1.25px 35px 1.25px 5px;
				height: 30px; line-height: 30px;
				border-bottom: 1px solid var(--clr-bs-gray);
			}
			html body main div.container div.sl-frame iframe {
				width: 100%; height: calc(100% - 31px);
				border: none;
			}
			html body main div.container div.sl-frame div {
				position: absolute; right: 11px; transform: translateY(calc(-100% - 1px));
				width: 35px; height: 35px;
				text-align: center;
				cursor: pointer; transition: background-color var(--time-tst-xfast), color var(--time-tst-fast);
			}
			html body main div.container div.sl-frame div:hover { background-color: var(--fade-black-8); }
			html body main div.container div.sl-frame div:active { color: var(--clr-bs-red); }
			html body main div.container div.sl-frame div i.material-icons { transform: translateY(5px); pointer-events: none; }
			html body main div.container ul { list-style-type: square; }
			html body main div.container ul h3 { margin: 25px 0px 10px; font-weight: 500; }
			@media only screen and (max-width: 768px) {
				form.auth-wrapper > * { font-size: 12.5px; }
				form.auth-wrapper font { font-size: 12.5px; }
				html body main div.container div.sl-frame p {
					margin: 0px; padding: 1.25px 22.5px 1.25px 5px;
					height: 20px; line-height: 22.5px;
				}
				html body main div.container div.sl-frame div { width: 22.5px; height: 22.5px; }
				html body main div.container div.sl-frame div i.material-icons { transform: none; }
				html body main div.container div.sl-frame iframe { height: calc(100% - 22.5px); }
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				<?php if(isset($user)&&isset($pass))echo'document.querySelector("form.auth-wrapper button").click()'; ?>
			});
			function CloseAnnouncement(me) {
				me = $(me.parentNode); me.animate({opacity: 0}, 500, function() {
					me.animate({height: 0, padding: 0, margin: 0, "border-width": 0}, 500, function() {
						me.remove();
						ppa.set_long_term_cookie("var-BC_HomeLogin", 1);
					});
				});
			}
			function disallowLogin() {
				(function() {
					app.ui.notify(1, [2, "ขณะนี้ระบบอยู่ระหว่างการปรับปรุง กรุณาเข้ามาใหม่ภายหลัง"]);
					$("form.auth-wrapper button").addClass("disabled").attr("disabled", "");
				}()); return false;
			}
		</script>
		<script type="text/javascript" src="/resource/js/lib/notify.min.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<!--center><button onClick="sys.auth.orize()" class="green">เข้าสู่ระบบ</button></center-->
				<form class="auth-wrapper form" method="post" onInput="sys.auth.validate()" --onSubmit="return disallowLogin()">
					<h2><?php echo $_COOKIE['set_lang']=="en"?"Please Sign-on":"กรุณาเข้าสู่ระบบ"; ?></h2>
					<?php if (!isset($_COOKIE['var-BC_HomeLogin']) || intval($_COOKIE['var-BC_HomeLogin'])<1) { ?><div class="broadcast message gray">
						<center>เข้าสู่ระบบโดยใส่บัญชีผู้ใช้งานและรหัสเข้าใช้งานอินเทอร์เน็ตโรงเรียนแล้วกดปุ่ม "เข้าสู่ระบบ"</center>
						<a onClick="CloseAnnouncement(this)" href="javascript:void(0)">⨯</a><!--[ปิด]-->
					</div><?php } ?>
					<label><?php echo $_COOKIE['set_lang']=="en"?"Username / Student ID":"เลขประจำตัวนักเรียน / ชื่อผู้ใช้งาน"; ?></label><input name="user" type="text" <?php echo(isset($user)?"value=\"$user\"":"autofocus");?> placeholder="ไม่ต้องใส่ @bodin.ac.th"><br>
					<label><?php echo $_COOKIE['set_lang']=="en"?"Password":"รหัสผ่าน"; ?></label><input name="pass" type="password" <?php echo(isset($pass)?"value=\"$pass\"":(isset($user)?"autofocus":""));?> placeholder="พิมพ์อะไรมั่วๆก็ได้"><br>
					<!--label>ประเภทผู้ใช้งาน</label><select name="zone"><option value="0">นักเรียน</option><option value="1">ข้าราชการครู</option><option value="2">ครูอัตราจ้าง / บุคลากร</option></select-->
					<center><button class="blue full-x dont-ripple" disabled onClick="return sys.auth.tempt('<?php echo $_GET['return_url']??""; ?>')"><?php echo $_COOKIE['set_lang']=="en"?"Sign in":"เข้าสู่ระบบ"; ?></button></center>
				</form>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>