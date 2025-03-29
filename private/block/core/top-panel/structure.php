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
			<a href="<?=$APP_CONST["baseURL"].($isSignedIn ? "v2/" : "")?>" name="logo" draggable="true">
				<img src="<?=$APP_CONST["baseURL"]?>_resx/upload/img/brand/logo/blue.png" />
				<span class="ref-hfm_<?=($isSignedIn ? "menu" : "home")?> minimizable"><?=($isSignedIn ? "Mainmenu" : "Homepage")?></span>
			</a>
		</div>
	</div>
	<div class="section slider hscroll sscroll">
		<?php if (!$isSignedIn) { ?>
			<div class="item super icon">
				<div class="menu">
					<a href="javascript:"><i class="material-icons">public</i></a>
					<ul class="dropdown text">
						<li><a href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F" target="_blank"><span>เว็บโรงเรียน</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Freg.bodin.ac.th" target="_blank"><span>งานทะเบียน</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Fpbl.bodin.ac.th" target="_blank"><span>เว็บ PBL Bodin</span></a></li>
						<li><a href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Frspg.bodin.ac.th" target="_blank"><span>สวนพฤกษศาสตร์</span></a></li>
					</ul>
				</div>
			</div>
		<?php
			} else {
				require("menu-".$_SESSION["auth"]["type"].".php");
				if (in_array($APP_USER, $APP_CONST["SYSTEM_OWNER"])) {
					?>
						<div class="item super image <?php if (!$hasSignOutBtn) echo 'sign-out'; ?>">
							<div class="menu">
								<a href="javascript:"><img src="<?=$APP_CONST["cdnURL"]?>a/TianTcl.net/img/logo/v1.png" data-dark="false"></a>
								<ul class="dropdown text">
									<li><a href="<?=$APP_CONST["baseURL"]?>service/4/TianTcl/"><span>✪ My Service</span></a></li>
									<li><a href="<?=$APP_CONST["baseURL"]?>service/4/TianTcl/login-as"><span>Override auth</span></a></li>
									<li><a href="<?=$APP_CONST["baseURL"]?>service/4/TianTcl/custom-page-link"><span>Open hidden</span></a></li>
									<hr>
									<li><a href="<?=$APP_CONST["baseURL"]?>service/4/proudsuaymak2/"><span>42880 Service</span></a></li>
									<li><a href="<?=$APP_CONST["baseURL"]?>service/4/SC/"><span>Student Council</span></a></li>
								</ul>
							</div>
						</div>
					<?php
				}
			}
		?>
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