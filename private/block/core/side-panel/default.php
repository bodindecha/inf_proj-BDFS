<?php
	/* if (!isset($APP_RootDir)) $APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	include($APP_RootDir."public_html/resource/hpe/aside-navigator.php"); */
?>
<div class="center" style="margin: 10px 0;">
	<a role="button" class="gray hollow ripple-click"
		data-href="<?=$APP_CONST["baseURL"]?>go?url=mailto%3Anoc%40bodin.ac.th%3Fsubject%3Dเว็บ%20INF-BD"
		href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Fmail.google.com%2Fa%2Fbodin.ac.th%2F%3Ffs%3D1%26tf%3Dcm%26to%3Dnoc%40bodin.ac.th%26su%3Dเว็บ%20INF-BD"
	target="_blank">ติดต่อสอบถาม/แจ้งปัญหาการใช้งาน</a>
</div>
<section class="nav">
	<style type="text/css">
		/* ALL version */
		app[name=main] > aside.navigator-tab { font-size: 1.5em; }
		app[name=main] > aside.navigator-tab section.nav > ul { margin: 0px 0px 10px; padding-left: 5px !important; }
		app[name=main] > aside.navigator-tab section.nav ul {
			padding-left: 25px;
			list-style-type: disc; white-space: nowrap;
		}
		app[name=main] > aside.navigator-tab section.nav ul > li.this-page, app[name=main] > aside.navigator-tab section.nav ul > li.this-page a {
			color: var(--clr-bs-indigo); font-weight: bold;
			pointer-events: none;
		}
		/* V1 */
		app[name=main] > aside.navigator-tab section.nav ul > li {
			padding-right: 10px;
			width: fit-content; height: 20px; line-height: 20px;
		}
		app[name=main] > aside.navigator-tab section.nav ul > li.sub-detail { list-style-type: none; }
		app[name=main] > aside.navigator-tab section.nav ul > li.seperator {
			width: 80%; height: 10px;
			background-image: linear-gradient(to bottom, transparent 0%, transparent 42.5%, var(--fade-black-7) 42.5%, var(--fade-black-7) 57.5%, transparent 57.5%, transparent 100%);
			list-style-type: none;
		}
		/* V2 */
		app[name=main] > aside.navigator-tab section.nav ul > li::marker { transform: translateX(5px); }
		app[name=main] > aside.navigator-tab section.nav ul > li.epdb {
			min-height: 20px; height: auto;
			list-style-type: none;
		}
		app[name=main] > aside.navigator-tab section.nav ul > li.epdb > details summary { transform: translateX(-10px); }
	</style>
	<ul>
		<?php if (isset($_SESSION["auth"]["type"])) { ?>
			<div class="group">
				<label><strong>Sitemap</strong></label>
				<ul>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/">หน้าหลัก (เมนู)</a></li>
				</ul>
			</div>
			<details class="group" open>
				<summary>ธนาคารขยะรีไซเคิล</summary>
				<ul>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/trash-bank/my">สมุดบัญชีของฉัน</a></li>
					<li class="seperator">&nbsp;</li>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/trash-bank/moderate/">สำหรับเจ้าหน้าที่</a></li>
				</ul>
			</details>
			<details class="group" disabled>
				<summary>ของพี่ให้น้อง</summary>
				<ul>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/P-2-N/">...</a></li>
				</ul>
			</details>
			<details class="group" open>
				<summary>ของหายได้คืน</summary>
				<ul>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/post"><span>สร้างประกาศ</span></a></li>
					<li class="seperator">&nbsp;</li>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/list/lost"><span>รายการของหาย</span></a></li>
					<li><a href="<?=$APP_CONST["baseURL"]?>project/BDFS/LnF/list/found"><span>รายการของที่พบ</span></a></li>
				</ul>
			</details>
			<details class="group" disabled>
				<summary>หมู่บ้านปันสุข</summary>
				<ul>
					<li><a disabled href="<?=$APP_CONST["baseURL"]?>project/BDFS/punsuke-village/">...</a></li>
				</ul>
			</details>
		<?php } // All group ?>
		<details class="group" open>
			<summary>กลับสู่</summary>
			<ul>
				<li><a href="<?=$APP_CONST["baseURL"]?>">เว็บหลัก (สาระสนเทศ)</a></li>
			</ul>
		</details>
	</ul>
</section>