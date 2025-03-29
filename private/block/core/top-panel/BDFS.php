<?php
	// Extra variable assignments
	$hasSignOutBtn = !isset($_SESSION["auth"]["override"]);
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
			<a href="<?=$APP_CONST["baseURL"]?>project/BDFS/<?=(!empty($home_menu??"")?"#menu=$home_menu":"")?>" name="logo" draggable="true">
				<img src="<?=$APP_CONST["baseURL"]?>_resx/upload/img/brand/logo/blue.png" />
				<span>โรงเรียนปลอดขยะ</span>
			</a>
		</div>
	</div>
	<div class="section slider hscroll sscroll">
		<div class="item super text">
			<div class="menu">
				<a href="javascript:"><span>ธนาคารขยะ</span></a>
				<ul class="dropdown text">
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/trash-bank/my"><span>สมุดบัญชีของฉัน</span></a></li>
					<hr>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/trash-bank/moderate/"><span>สำหรับเจ้าหน้าที่</span></a></li>
				</ul>
			</div>
			<div class="menu" disabled>
				<a href="javascript:"><span>ของพี่ให้น้อง</span></a>
				<ul class="dropdown text">
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/P-2-N/"><span>...</span></a></li>
				</ul>
			</div>
			<div class="menu">
				<a href="javascript:"><span>ของหายได้คืน</span></a>
				<ul class="dropdown text">
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/post"><span>สร้างประกาศ</span></a></li>
					<hr>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/list/lost"><span>รายการของหาย</span></a></li>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/list/found"><span>รายการของที่พบ</span></a></li>
				</ul>
			</div>
			<div class="menu" disabled>
				<a href="javascript:"><span>หมู่บ้านปันสุข</span></a>
				<ul class="dropdown text">
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/punsuke-village/"><span>...</span></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="section reverse">
		<div class="item super icon <?php if ($isSignedIn) echo "text"; ?>">
			<div class="menu" name="account">
				<a href="javascript:"><?php if ($isSignedIn) echo '<span class="minimizable">'.$_SESSION["auth"]["name"]["th"]["a"].'</span>'; ?><span class="material-symbols-rounded">account_circle</span></a>
				<ul class="dropdown icon text">
				<?php if (!$isSignedIn) { ?>
					<li><a href="<?=$signinURL?>"><span class="material-symbols-rounded">login</span><span>Sign in</span></a></li>
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
	foreach (array("hasSignOutBtn") as $tmp) unset($$tmp);
?>