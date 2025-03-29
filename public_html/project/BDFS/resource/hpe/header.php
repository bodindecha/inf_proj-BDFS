<header>
    <section class="slider hscroll sscroll"><div class="ocs">
		<div class="head-item menu">
			<a onClick="app.ui.toggle.navtab()" href="javascript:" opened="<?php echo ($_COOKIE['sui_open-nt']??"false"); ?>"><div>
				<span class="bar"></span>
				<span class="bar"></span>
				<span class="bar"></span>
			</div></a>
		</div>
        <?php if (isset($_SESSION['auth']['type'])) { ?>
			<div class="head-item logo image text">
				<a href="/project/BDFS/<?=(!empty($home_menu??"")?"#menu=$home_menu":"")?>" draggable="true"><img src="/resource/images/logo-5.png" data-dark="false"><span>โรงเรียนปลอดขยะ</span></a>
			</div>
			<div class="head-item super">
				<div class="menu">
					<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><span>ธนาคารขยะ</span></a>
					<ul class="dropdown">
						<a href="/project/BDFS/trash-bank/my"><span>สมุดบัญชีของฉัน</span></a>
						<hr>
						<a href="/project/BDFS/trash-bank/moderate/"><span>สำหรับเจ้าหน้าที่</span></a>
					</ul>
				</div>
			</div>
			<div class="head-item super" disabled>
				<div class="menu">
					<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><span>ของพี่ให้น้อง</span></a>
					<ul class="dropdown">
						<a href="/project/BDFS/P-2-N/"><span>...</span></a>
					</ul>
				</div>
			</div>
			<div class="head-item super">
				<div class="menu">
					<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><span>ของหายได้คืน</span></a>
					<ul class="dropdown">
						<a href="/project/BDFS/LnF/post"><span>สร้างประกาศ</span></a>
						<hr>
						<a href="/project/BDFS/LnF/list/lost"><span>รายการของหาย</span></a>
						<a href="/project/BDFS/LnF/list/found"><span>รายการของที่พบ</span></a>
					</ul>
				</div>
			</div>
			<div class="head-item super" disabled>
				<div class="menu">
					<a data-onClick="app.ui.toggle.hmenu(this)" href="javascript:"><span>หมู่บ้านปันสุข</span></a>
					<ul class="dropdown">
						<a disabled href="/project/BDFS/punsuke-village/"><span>...</span></a>
					</ul>
				</div>
			</div>
			<div class="head-item text">
				<a onclick="sys.auth.out()" href="javascript:" draggable="false"><span>ออกจากระบบ</span></a>
			</div>
        <?php } else { ?>
			<div class="head-item logo image text">
				<a href="/" draggable="true"><img src="/resource/images/logo-5.png" data-dark="false"><span>โรงเรียนปลอดขยะ</span></a>
			</div>
        <?php } ?>
	</div></section>
    <section class="slider hscroll sscroll"><div class="ocs">
		<div class="head-item lang"><select name="hl">
			<option>th</option>
			<option>en</option>
		</select></div>
		<div class="head-item clrt icon">
			<a onClick="app.ui.change.theme('dark')" href="javascript:"><i class="material-icons">brightness_6</i></a>
		</div>
	</div></section>
</header>