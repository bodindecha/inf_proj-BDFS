<?php if ($isAdministrator) { ?>
	<div class="item super icon">
		<div class="menu">
			<a href="javascript:"><i class="material-icons">security</i><!--span>การควบคุม</span--></a>
			<ul class="dropdown text icon">
				<li><a href="<?=$APP_CONST["baseURL"]?>m/configurate"><i class="material-icons">settings</i><span>ตั้งค่าระบบ</span></a></li>
				<li><a href="<?=$APP_CONST["baseURL"]?>m/sync/data"><i class="material-icons">sync</i><span>sync ข้อมูล</span></a></li>
				<hr>
				<li><a href="<?=$APP_CONST["baseURL"]?>m/user/directory"><i class="material-icons">person</i><span>บัญชีผู้ใช้งาน</span></a></li>
				<li><a href="<?=$APP_CONST["baseURL"]?>m/user/suspend"><i class="material-icons">block</i><span>ระงับบัญชี</span></a></li>
			</ul>
		</div>
	</div>
<?php } if ($isDeveloper) { ?>
	<div class="item super image">
		<div class="menu">
			<a href="javascript:"><i class="material-icons">code</i></a>
			<ul class="dropdown text">
				<li><a class="title"><span>ผู้พัฒนาระบบ</span></a></li>
				<li><a href="<?=$APP_CONST["baseURL"]?>d/log/search"><span>ค้นบันทึก</span></a></li>
				<!-- <li><a href="<?=$APP_CONST["baseURL"]?>d/docs/home"><span>คู่มือนักพัฒนา</span></a></li> -->
				<li><a class="title"><span>ทรัพยากร</span></a></li>
				<li><a href="<?=$APP_CONST["baseURL"]?>_resx/service/dev/css-var"><span>ตัวแปร CSS</span></a></li>
				<li><a href="<?=$APP_CONST["baseURL"]?>_resx/service/dev/font-list"><span>รายการฟ้อนท์</span></a></li>
				<hr>
				<li><a href="<?=$APP_CONST["baseURL"]?>_resx/service/dev/file-manager"><span>File manager</span></a></li>
			</ul>
		</div>
	</div>
<?php } ?>
<div class="item super text">
	<div class="menu">
		<a href="javascript:"><span>IS & PBL</span></a>
		<ul class="dropdown text">
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v2/project-list" data-href="<?=$APP_CONST["baseURL"]?>t/PBL/v1/list"><span>รายชื่อโครงงาน</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v1/no-group"><span>นักเรียนไม่มีกลุ่ม</span></a></li>
			<hr>
			<li><a class="title"><span>เทอม 1</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v1/submission"><span>ตรวจแผนผังความคิด</span></a></li>
			<li><a class="title"><span>เทอม 2</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v2/grade-paper"><span>ตรวจเล่มรายงาน</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v2/mark-paper"><span>ประเมินผลเล่มรายงาน</span></a></li>
			<hr>
			<li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v2/analytics"><span>ประมวลข้อมูล</span></a></li>
			<?php if ($isAdministrator) { ?><li><a href="<?=$APP_CONST["baseURL"]?>t/PBL/v2/group/home"><span>เปิดโครงงาน</span></a></li><?php } ?>
		</ul>
	</div>
</div>
<div class="item text">
	<!-- <a href="<?=$APP_CONST["baseURL"]?>archive"><span>กิจกรรม</span></a> -->
	<a href="<?=$APP_CONST["baseURL"]?>t/student-list"><span>ใบรายชื่อ</span></a>
</div>
<div class="item super text">
	<div class="menu">
		<a href="javascript:"><span>อื่นๆ</span></a>
		<ul class="dropdown text">
			<li><a class="title"><span>โครงการ</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/"><span>โรงเรียนปลอดขยะ</span></a></li>
			<hr>
			<li><a href="<?=$APP_CONST["baseURL"]?>v2/service/app/url-short/dashboard"><span>ย่อลิงก์</span></a></li>
			<li><a href="<?=$APP_CONST["baseURL"]?>service/app/file-share/"><span>แชร์ไฟล์</span></a></li>
		</ul>
	</div>
</div>