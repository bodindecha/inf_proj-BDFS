<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$wording = ($_GET["type"]=="lost" ? "สูญหาย" : "ที่พบเจอ");
	$header_title = "รายการของ$wording";
	$header_desc = "Lost n' Found - BODIN";
	$home_menu = "lost-n-found";
	$navtabpath = $dirPWroot."project/BDFS/resource/hpe/aside-navigator.php";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main { overflow: visible; }
			main .list {
				padding-left: 0;
				list-style-type: none;
				display: flex; gap: 25px; justify-content: center;
				flex-wrap: wrap;
			}
			main .item {
				--img-h: 145px;
				width: 0px; height: var(--img-h);
				box-shadow: 0 0 0.3rem 1.25px #999; border-radius: 5px;
				overflow: hidden; transition: var(--time-tst-xfast);

				animation: new-item .4s ease-out 1; animation-fill-mode: forwards; animation-delay: calc(var(--i) * 0.04s); /* animation-direction: alternate; */
				/* animation-name: new-item, hide-item;
				animation-duration: .4s, .4s;
				animation-timing-function: ease-out, ease-in;
				animation-delay: calc(var(--i) * 0.04s), 0s;
				animation-iteration-count: 1, 0;
				animation-fill-mode: forwards, backwards; */
			}
			@keyframes new-item {
				from { width: 0px; }
				to { width: 320px; }
			}
			@keyframes hide-item {
				from { width: 320px; }
				to { width: 0px; }
			}
			main .item:hover { box-shadow: 0 0 0.3rem 1.5px #777; }
			main .item .wrapper { display: flex; }
			main .item div.photo {
				width: var(--img-h); height: var(--img-h);
				overflow: hidden;
			}
			main .item div.photo img {
				width: 100%; height: 100%;
				object-fit: contain;
				transition: .35s ease-in-out;
			}
			main .item:hover div.photo img { transform: scale(1.125); }
			main .item .detail {
				padding: 5px 5px 5px 10px;
				width: 160px; max-height: calc(var(--img-h) - 10px);
				display: flex; flex-direction: column; justify-content: space-between;
			}
			main .item .detail * { margin: 0; }
			/* main .item .detail a:link, main .item .detail a:visited { color: var(--clr-main-black-absolute); text-decoration: none; } */
			main .item .detail h3 {
				margin: 2.5px 0;
				cursor: pointer;
			}
			main .item .detail div { max-height: calc(var(--img-h) - 30px); }
			main .item .detail div span { font-size: 15px; }
			main .item .detail > span { font-size: 13px; }
			main .control { padding-top: 10px; }
			main .control span { color: var(--clr-bs-gray); }
			@media only screen and (max-width: 768px) {
				main .item .detail > span:last-child { font-size: 10px; }
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				BDFS.init();
			});
			const BDFS = (function(d) {
				const cv = { API_URL: "/project/BDFS/LnF/api" };
				var sv = {}, kbd = {};
				var initialize = function() {
					BDFS.load();
					$(document).on("keydown keyup", updateKbdState);
				},
				updateKbdState = function(e) {
					kbd = {
						keyCode: e.which || e.keyCode,
						keyChar: String.fromCharCode(e.which || e.keyCode) || e.key || e.code,
						isCrtling: e.ctrlKey,
						isShifting: e.shiftKey,
						isAlting: e.altKey
					};
				},
				getItem = async function(startingAt=0) {
					var btn = $('main .control button[name="loader"]');
					btn.attr("disabled", "");
					await ajax(cv.API_URL, {type: "post", act: "list", param: {offset: startingAt, type: "<?=$_GET["type"]=="lost"?"L":"F"?>"}}).then(async function(dat) {
						if (dat.next == sv.lastLoad) return btn.replaceWith('<span>——— End of results ———</span>');
						sv.lastLoad = dat.next;
						btn.attr("onClick", "BDFS.load("+sv.lastLoad+")").
							removeAttr("disabled");
						if (dat) {
							var container = $("main .list"),
								itemCount = 0, imgAction;
							await dat.list.forEach(ei => {
								if (ei["image"]==null) {
									ei["image"] = "default.png";
									imgAction = "";
								} else imgAction = 'onClick="BDFS.expand(this)"';
								container.append('<li class="item item-new" style="--i: '+(itemCount++).toString()+';"><div class="wrapper"><div class="photo"><img src="/resource/upload/BDFS/LnF-items/'+ei["image"]+'" '+imgAction+' draggable="false" alt="'+ei["name"]+'" data-dark="false" /></div><div class="detail"><div><h3 onClick="BDFS.view(\''+ei["item"]+'\', this, event)">'+ei["name"]+'</h3><span>'+ei["location"]+'</span></div><span>'+ei["time"]+'</span></div></div></li>');
							}); setTimeout(function() {
								try { Grade(d.querySelectorAll("main .item div.photo:not([style])")); } catch(e) {}
								$("main .list .item-new").css("--i", "0").removeClass("item-new");
							}, 250);
						}
					});
				}, viewItem = function(link, me) {
					var itemURL = "/project/BDFS/LnF/item/"+link;
					if (kbd.isAlting) app.ui.lightbox.open("top", {title: me.innerText, allowclose: true, html: '<iframe src="'+itemURL+'" style="width:90vw;height:80vh;border:none">Loading..</iframe>'});
					else window.open(itemURL);
				},
				openPhoto = function(me) {
					var imgURL = me.getAttribute("src");
					app.ui.lightbox.open("mid", {allowclose: true, html: '<img src="'+imgURL+'" style="width:80vw;height:80vh;object-fit:contain;" draggable="false" data-dark="false" alt="Zoomed image view" />'});
				},
				search = function() {
					var query = $('main .oform input[name="find"]').val().trim();
					w3.filterHTML("main .list", "main .list .item", query);
				};
				return {
					init: initialize,
					load: getItem,
					view: viewItem,
					expand: openPhoto,
					filterByText: search
				};
			}(document));
		</script>
		<script type="text/javascript" src="/resource/js/lib/grade.min.js"></script>
		<script type="text/javascript" src="/resource/js/lib/w3.min.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."project/BDFS/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2><?=$header_title?></h2>
				<form class="form oform" onSubmit="return false;">
					<div class="group">
						<span><i class="material-icons">search</i></span>
						<input type="search" name="find" placeholder="Find..." onInput="BDFS.filterByText()">
					</div>
				</form>
				<ul class="list"></ul>
				<div class="control center">
					<button class="blue hollow small" name="loader">Load more</button>
				</div>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>