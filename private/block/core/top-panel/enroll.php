<?php
	// Extra variable assignments
	$hasSignOutBtn = !isset($_SESSION["auth"]["override"]);
	$isStaff = has_perm("admission");
?>
<header class="auto --shortcut --search">
	<div class="section">
		<div class="item">
			<a onClick="app.UI.toggleMenu()" href="javascript:" name="menu"><div class="hamburger">
				<span class="bar"></span>
				<span class="bar"></span>
				<span class="bar"></span>
			</div></a>
		</div>
		<div class="item image text">
			<a href="<?=$APP_CONST["baseURL"]?>e/enroll/" name="logo" draggable="true">
				<img src="<?=$APP_CONST["baseURL"]?>_resx/upload/img/brand/logo/blue.png" />
				<span>งานรับนักเรียน</span>
			</a>
		</div>
	</div>
	<div class="section slider hscroll sscroll">
		<?php if ($isStaff) { ?>
			<div class="item text icon">
				<a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/<?=""/*(!empty($home_menu ?? "")?"#menu=$home_menu":"")*/?>"><i class="material-icons">dashboard</i><span>เมนูหลัก</span></a>
			</div>
			<div class="item super">
				<div class="menu text icon">
					<a href="javascript:"><i class="material-icons">assessment</i><span>การตอบกลับ</span></a>
					<ul class="dropdown">
						<li><a class="title"><span>นักเรียนเดิม</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/response/M4-present-v2"><span>รายงานตัว</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/response/M4-change-v2"><span>เปลี่ยนกลุ่มการเรียน</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/response/M4-confirm-v2"><span>ยืนยันสิทธิ์</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/response/M4-switch"><span>เปลี่ยนแปลงสิทธิ์</span></a></li>
						<li><a class="title"><span>นักเรียนใหม่</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/response/new-student-v2"><span>รายงานตัว</span></a></li>
					</ul>
				</div>
				<div class="menu text icon">
					<a href="javascript:"><i class="material-icons">receipt</i><span>จัดการข้อมูล</span></a>
					<ul class="dropdown">
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/print-form"><i class="material-icons">print</i><span>พิมพ์เอกสาร</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/delete-response"><i class="material-icons">delete</i><span>ลบการตอบกลับ</span></a></li>
					</ul>
				</div>
				<div class="menu text icon">
					<a href="javascript:"><i class="material-icons">settings</i><span>กระทำการ</span></a>
					<ul class="dropdown">
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/time-control"><i class="material-icons">date_range</i><span>ตั้งค่าเวลา</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/edit-direction"><i class="material-icons">web</i><span>แก้ไขคำชี้แจง</span></a></li>
						<hr>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/import-data"><i class="material-icons">unarchive</i><span>นำเข้าข้อมูล</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/export-result"><i class="material-icons">archive</i><span>นำออกข้อมูล</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/download-doc"><i class="material-icons">download</i><span>รวมหลักฐาน</span></a></li>
						<?php if ($isAdministrator) { ?>
						<hr>
						<li><a href="<?=$APP_CONST["baseURL"]?>e/enroll/report/file-manager"><i class="material-icons">source</i><span>จัดการเอกสารแม่แบบ</span></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } else { ?>
			<div class="item text">
				<a href="<?=$APP_CONST["baseURL"]?>e/enroll/new"><span>นักเรียนใหม่</span></a>
				<a <?=($isSignedIn ? 'href="'.$APP_CONST["baseURL"].'e/enroll/M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:"')?>><span>นักเรียนเดิม<?=($isSignedIn ? "" : " (เข้าสู่ระบบ)")?></span></a>
			</div>
		<?php } ?>
	</div>
	<div class="section reverse">
		<div class="item super icon text">
			<div class="menu" name="account">
				<a href="javascript:"><span class="minimizable"><?=($isSignedIn ? $_SESSION["auth"]["name"]["th"]["a"] : "เจ้าหน้าที่")?></span><span class="material-symbols-rounded">account_circle</span></a>
				<ul class="dropdown icon text">
				<?php if (!$isSignedIn) { ?>
					<li><a data-href="<?=$signinURL?>" onClick="sys.auth.orize('e%2Fenroll%2Freport%2F')" href="javascript:"><span class="material-symbols-rounded">login</span><span>Sign in</span></a></li>
				<?php } else { ?>
					<li><a href="<?=$APP_CONST["baseURL"]?>account/my"><span>My profile</span></a></li>
					<?php if ($hasSignOutBtn) { ?>
						<hr>
						<li><a href="javascript:" onClick="sys.auth.out()"><span class="material-symbols-rounded">logout</span><span>Sign out</span></a></li>
					<?php } ?>
				<?php } ?>
				</ul>
			</div>
			<div class="menu corner" name="settings">
				<a href="javascript:"><i class="material-icons">settings</i></a>
				<div class="dropdown form form-bs">
					<div class="group">
						<label class="icon" data-title="Language"><span class="material-symbols-rounded">language</span></label>
						<select name="lang">
							<option value="EN">English</option>
							<option value="TH">ภาษาไทย</option>
						</select>
					</div>
					<div class="group center">
						<button class="white icon" data-title="Light" onClick="app.UI.theme('light')"><i class="material-icons">wb_sunny</i></button>
						<button class="gray icon" data-title="Auto" onClick="app.UI.theme('auto')"><i class="material-icons">phonelink</i></button>
						<button class="black icon" data-title="Dark" onClick="app.UI.theme('dark')"><i class="material-icons">brightness_3</i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<?php
	foreach (array("hasSignOutBtn", "isStaff") as $tmp) unset($$tmp);
?>