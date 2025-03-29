<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "Dashboard"; // เมนูหลัก

	$permission = array(
		"requireType" => strtoupper($_SESSION["auth"]["type"] ?? "G"),
		"isModerator" => $isAdministrator,
		"isDeveloper" => $isDeveloper,
		"modUAC" => has_perm("user"),
		"modPBL" => has_perm("PBL"),
		"modBDFS" => has_perm("BDFS"),
		"modURL" => has_perm("URLs"),
		"modAPprog" => has_perm("APprog")
	);
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] > main h3:first-of-type { font-weight: normal; }
</style>
<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/ext/menu.css" crossorigin="anonymous" />
<script type="text/javascript">
	const TRANSLATION = ["@component-menu", location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+")];
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			PERMISSIONS: <?=json_encode($permission, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)?>
		};
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;
			menu.dashboard(["main", "?d=2025-03-07"], cv.PERMISSIONS, "<?=$_SESSION["auth"]["user"] ?? ""?>", function() {
				if (cv.PERMISSIONS.requireType == "S") $("app[name=main] main .menu-dash [data-reference=refM01-003] .menu-head a").attr("href", AppConfig.baseURL + "v2/s/PBL/");
			}); app.IO.URL.removeHash(null, "next");
			sv.inited = true;
		};
		return {
			init: initialize
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/ext/menu.js"></script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container">
		<p><span class="ref-00001">ยินดีต้อนรับ</span><a class="blend" href="<?=$APP_CONST["baseURL"]?>user/<?=$_SESSION["auth"]["user"]?>"><?=$_SESSION["auth"]["name"][$_COOKIE["set_lang"] ?? "th"]["a"]; ?></a></p>
		<p class="ref-00002">เข้าสู่ระบบสารสนเทศโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</p>
		<h3><span class="ref-00003">ขณะนี้ ภาคเรียนปัจจุบันในระบบคือ</span> <u><span class="ref-00004">ภาคเรียนที่</span> <?php echo $_SESSION["stif"]["t_sem"]; ?><span class="ref-00005"> ปีการศึกษา</span> <?php echo $_SESSION["stif"]["t_year"]; ?></u></h3>
		<?php include("_announcements.php"); ?>
		<p><span class="ref-00006">กดปุ่ม</span> <a role="button" class="black hollow pill small action icon disabled"><i class="material-icons">expand_more</i></a> <span class="ref-00007">เพื่อขยายดูรายการเมนูในหมวดหมู่</span></p>
		<div class="menu-dash"></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>