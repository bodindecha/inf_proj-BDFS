<style type="text/css">
	form.auth-wrapper {
		margin: 10px 0px; padding: 5px;
	}
	form.auth-wrapper > * {
		margin: 2.5px 0px;
		font-size: 20px; font-family: "THSarabunNew", serif;
	}
	form.auth-wrapper label { font-size: 17.5px; }
	form.auth-wrapper label span {
		color: var(--clr-pp-blue-grey-700);
		cursor: pointer;
	}
	form.auth-wrapper label span:hover { background-color: rgba(0, 0, 0, 0.125); }
	form.auth-wrapper input, form.auth-wrapper select {
		padding: 0px 10px; width: calc(100% - 22.5px);
		border-radius: 3px; border: 1px solid var(--clr-bs-gray-dark);
		transition: var(--time-tst-fast);
	}
	form.auth-wrapper select { width: 100%; }
	form.auth-wrapper input:invalid, form.auth-wrapper input[invalid] { border: 1px solid var(--clr-bs-red) !important; }
	form.auth-wrapper input:invalid:focus, form.auth-wrapper input[invalid]:focus { box-shadow: 0 0 0 0.25rem rgb(220 53 69 / 37.5%) !important; }
	form.auth-wrapper button { margin-top: 20px; }
	form.auth-wrapper font { font-size: 15px; }
	form.auth-wrapper font a:link, form.auth-wrapper font a:visited { text-decoration: none; color: var(--clr-bd-light-blue); }
	form.auth-wrapper font a:hover, form.auth-wrapper font a:active { text-decoration: underline; color: var(--clr-bd-low-light-blue); }
	@media only screen and (max-width: 768px) {
		form.auth-wrapper > * { font-size: 12.5px; }
		form.auth-wrapper label { font-size: 12.5px; }
		form.auth-wrapper font { font-size: 12.5px; }
	}
</style>
<?php $chosenEnglish = $_COOKIE["set_lang"] ?? "en" == "en"; ?>
<form class="auth-wrapper form form-bs" method="post" onInput="sys.auth.validate()">
	<div>
		<label><?=$chosenEnglish?"Username / Student ID":"เลขประจำตัวนักเรียน / ชื่อผู้ใช้งาน"?></label>
		<input name="user" type="text" autofocus>
	</div>
	<div>
		<label><?=$chosenEnglish?"Password":"รหัสผ่าน"?></label>
		<input name="pass" type="password">
	</div>
	<button class="blue full-x ripple-click" disabled onClick="return sys.auth.tempt('<?=urldecode($_GET["return_url"]??"")?>')"><?=$chosenEnglish?"Sign in":"เข้าสู่ระบบ"?></button>
</form>
<?php unset($chosenEnglish); ?>