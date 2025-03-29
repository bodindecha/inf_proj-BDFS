<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "สถิติการฝากขยะ";
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
			main .table { text-align: center; }
			main .table td:nth-child(1), main .table td:nth-child(5) { text-align: right; }
			main .table output { margin-right: 0.5px; }
			main .refresh i { transform: rotate(135deg); }
			main .refresh[disabled] i { animation: rot_rfi 1.5s ease-in-out infinite; }
			@keyframes rot_rfi {
				from {transform: rotate(135deg); }
				to {transform: rotate(-225deg); }
			}
		</style>
		<script type="text/javascript">
			<?php if ($has_perm) { ?>
			$(document).ready(function() {
				BDTB.init();
			});
			const BDTB = (function(d) {
				const cv = {
					API_URL: "/project/BDFS/trash-bank/_/api?type=MOD",
					isGranted: eval("<?=($has_perm?"true":"false")?>"),
					typeAmt: 4, types: ["Plastic", "Paper", "Metal"],
					values: ["store", "recycled", "total"]
				};
				var sv = { inited: false, refreshTimeout: null };
				var initialize = function() {
					if (!sv.inited) {
						if (!cv.isGranted) $("main .table tr > :nth-child(5)").remove();
						getStatics();
						sv.inited = true;
					}
				}, getStatics = async function() {
					$("main .refresh").attr("disabled", "");
					sv.refreshAble = false;
					sv.refrestTimeout = setTimeout(resetRefreshTimer, 5000);
					await ajax(cv.API_URL, {act: "getStatics"}).then(function(dat) {
						sv.refreshAble = true;
						if (sv.refreshTimeout != null) resetRefreshTimer();
						if (dat == false) return setTimeout(getStatics, 30000);
						if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
						$('main output[name="accounts"]').val(dat["accounts"]);
						delete dat["accounts"];
						Object.keys(dat).forEach(ei => {
							d.querySelector('main .table [name="'+ei+'"]').value = parseInt(dat[ei]);
						}); cv.types.forEach(et => {
							d.querySelector('main .table [name="total:'+et+'"]').value = parseInt(d.querySelector('main .table [name="store:'+et+'"]').value) + parseInt(d.querySelector('main .table [name="recycled:'+et+'"]').value);
						}); let sum;
						(cv.isGranted ? [...cv.values, "sold"] : cv.values).forEach(ev => {
							sum = 0;
							cv.types.forEach(et => {
								sum += parseInt(d.querySelector('main .table [name="'+ev+':'+et+'"]').value);
							}); d.querySelector('main .table [name="'+ev+':Total"]').value = sum;
						});
					});
				},
				resetRefreshTimer = function() {
					if (sv.refreshAble) {
						$("main .refresh").removeAttr("disabled");
						sv.refreshTimeout = null;
					}
				};
				return {
					init: initialize,
					refresh: getStatics
				};
			}(document));
			<?php } ?>
		</script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
		<?php if (!$has_perm) echo '<iframe src="/error/901">Loading...</iframe>'; else { ?>
			<div class="container">
				<h2><?=$header_title?></h2>
				<p>ขณะนี้มีบัญชีเปิดใช้งานทั้งหมด <output name="accounts"></output> บัญชี</p>
				<div class="table"><table><thead><tr>
					<th>ประเภท</th>
					<th>เก็บอยู่</th>
					<th>ถอนออก</th>
					<th>รวมทั้งหมด</th>
					<th hidden>เครดิต</th>
				</tr></thead><tbody>
					<tr>
						<td><?=($_COOKIE["set_lang"]=="en")?"Plastic":"พลาสติก"?></td>
						<td><output name="store:Plastic"></output></td>
						<td><output name="recycled:Plastic"></output></td>
						<td><output name="total:Plastic"></output></td>
						<td hidden><output name="sold:Plastic"></output></td>
					</tr>
					<tr>
						<td><?=($_COOKIE["set_lang"]=="en")?"Paper":"กระดาษ"?></td>
						<td><output name="store:Paper"></output></td>
						<td><output name="recycled:Paper"></output></td>
						<td><output name="total:Paper"></output></td>
						<td hidden><output name="sold:Paper"></output></td>
					</tr>
					<tr>
						<td><?=($_COOKIE["set_lang"]=="en")?"Metal":"โลหะ"?></td>
						<td><output name="store:Metal"></output></td>
						<td><output name="recycled:Metal"></output></td>
						<td><output name="total:Metal"></output></td>
						<td hidden><output name="sold:Metal"></output></td>
					</tr>
					<tr>
						<td><?=($_COOKIE["set_lang"]=="en")?"Total":"รวมทั้งสิ้น"?></td>
						<td><output name="store:Total"></output></td>
						<td><output name="recycled:Total"></output></td>
						<td><output name="total:Total"></output></td>
						<td hidden><output name="sold:Total"></output></td>
					</tr><!-- ฿ -->
				</tbody></table></div>
				<div class="refresh center form"><div class="group">
					<span><i class="material-icons">sync</i></span>
					<button class="default" onClick="BDTB.refresh()">Refresh</button>
				</div></div>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>