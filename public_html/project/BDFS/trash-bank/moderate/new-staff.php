<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "เพิ่มนายธนาคาร";
	$header_desc = "ธนาคารขยะรีไซเคิล";
	$header_cover = "images/trash-bank.jpg";
	$home_menu = "trash-bank";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";

	require_once($dirPWroot."project/BDFS/resource/hps/permission.php");
	$has_perm = getProjectPermission(false);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = { API_URL: "/project/BDFS/trash-bank/_/api?type=MOD" };
				var sv = { inited: false };
				var initialize = function() {
					if (!sv.inited) {
						
						sv.inited = true;
					}
				};
				return {
					init: initialize
				};
			}(document));
		</script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>