<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");

	if (!isset($_REQUEST["furl"]) || !strlen($_REQUEST["furl"])) TianTcl::http_response_code(902);
	$path = ltrim(trim($_REQUEST["furl"]), "/");
	$directory = preg_match("/^(private|$APP_CONST[publicDir])\//", $path) ? "" : $APP_CONST["publicDir"];
	$real = $APP_RootDir.$directory.$APP_CONST["baseURL"].$path;
	$extension = pathinfo($path, PATHINFO_EXTENSION);
	if (isset($_REQUEST["name"])) {
		$name = trim($_REQUEST["name"]);
		if (!str_ends_with($name, ".$extension")) $name .= ".$extension";
	} else $name = basename($path);
	if (!file_exists($real)) TianTcl::http_response_code(900);

	$extn_app = ["png", "jpg", "jpeg", "heic", "heif", "gif", "webp", "svg", "ico"];
	$extn_viewable = array(
		"image" => ["tiff"],
		"video" => ["webm", "mp4", "3gpp", "mov", "avi"],
		"text" => ["txt", "csv", "tsv", "json", "md", "log", "ini", "conf", "cfg"],
		"code" => ["css", "html", "htm", "php", "sql", "js", "c", "cpp", "py", "ps1"],
		"msWord" => ["doc", "docx"],
		"msExcel" => ["xls", "xlsx"],
		"msPowerpnt" => ["ppt", "pptx", "ppsm"],
		"documents" => ["pdf", "pages"],
		"adobe" => ["ai", "psd"],
		"font" => ["ttf"],
		"archive" => ["zip", "rar", "7z", "tar", "gz", "bz2", "xz"],
		"etc" => ["dxf", "eps", "ps", "xps", ],
	); $extn_support = array();
	foreach($extn_viewable as $group => $extn_avail) $extn_support = array_merge($extn_support, $extn_avail);
	if (!in_array($extension, array_merge($extn_app, $extn_support))) TianTcl::http_response_code(905);
	$useAppImgViewer = in_array($extension, $extn_app);
	if ($useAppImgViewer) {
		try {
			$size = getimagesize($real);
			$header["title"] = "$name ($size[0]×$size[1]) - Image viewer";
		} catch (Exception $e) {
			$size = [0, 0];
			$header["title"] = "$name - Image viewer";
		} $header["cover"] = $path;
	} else {
		$header["title"] = "$name - File viewer";
		$iframeSrc = filesize($real) > 25*1024*1024 ? "/$path?token=".uniqid() : "https://docs.google.com/gview?embedded=true&url=https%3A%2F%2F".urlencode($_SERVER["SERVER_NAME"]."/".$path);
	}
	$APP_PAGE -> print -> head();
?>
<?php if (!$useAppImgViewer) { ?>
	<style type="text/css">
		main > iframe { position: absolute; top: 0px; z-index: 1; }
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			$("app[name='main'] main .message.lime").text(app.settings["lang"] == "TH" ? "หากไม่มีไฟล์ปรากฏขึ้นใน 10 วินาที กรุณากดปิดหน้านี้และเปิดใหม่" : "If nothing shows up within 10 seconds. Please re-open this viewer.");
		});
	</script>
<?php } else { ?>
	<style type="text/css">
		main {
			min-height: 100% !important; height: var(--window-height) !important;
			overflow: hidden;
		}
		main .container {
			position: relative; top: 50%; transform: translateY(-50%);
			width: <?=$size[0]??"0";?>px !important; max-width: 100%; height: <?=$size[1]??"0";?>px !important; max-height: 100%;
			overflow: visible;
		}
		main .container .wrapper {
			width: 100%; height: 100%;
			background-image: linear-gradient(45deg,#EFEFEF 25%,rgba(239,239,239,0) 25%,rgba(239,239,239,0) 75%,#EFEFEF 75%,#EFEFEF),linear-gradient(45deg,#EFEFEF 25%,rgba(239,239,239,0) 25%,rgba(239,239,239,0) 75%,#EFEFEF 75%,#EFEFEF); background-position: 0 0,10px 10px; background-size: 21px 21px;
			transition: var(--time-tst-fast);
		}
		main .container .wrapper > * {
			position: absolute; top: 0px;
			width: 100%; height: 100%;
		}
		main .container .wrapper div { opacity: 0.5; filter: opacity(0.5); }
		main .container .wrapper div img { opacity: 0; filter: opacity(0); }
		main .container .wrapper span {
			background-image: url("/<?=$path?>"); background-size: contain;
			/* backdrop-filter: blur(7.5px); */ background-repeat: no-repeat; background-position: center;
		}
		main div.controller, main div.controller div.sgt {
			position: absolute; top: 0px;
			width: 100%; height: 100%;
		}
		main div.controller div.bar {
			--h: 50px; --pd-s: 50px;
			padding: 175px var(--pd-s) 25px;
			position: absolute; bottom: 0px; z-index: 1;
			width: calc(100% - var(--pd-s) * 2); height: var(--h);
			transition: var(--time-tst-fast);
		}
		main div.controller div.bar.force {
			transform: translateY(0px);
			opacity: 1; filter: opacity(1);
			pointer-events: auto;
		}
		main div.controller div.bar ul {
			margin: 0px auto; padding: 0px 5px;
			width: 550px; height: calc(100% - 5px);
			background-color: var(--clr-bs-light);
			border-radius: calc(var(--h) / 2); border: 2.5px solid var(--clr-bs-dark);
			display: flex; justify-content: space-around;
			overflow: visible; transition: var(--time-tst-medium);
		}
		main div.controller div.bar ul li {
			height: calc(var(--h) - 5px);
			white-space: nowrap;
			display: flex; list-style-type: none;
		}
		main div.controller div.bar ul > * { transform: translateY(-0.5px); }
		main div.controller div.bar ul li > * {
			min-width: 30px; height: 100%;
			font-size: 24px; line-height: calc(var(--h) - 5px);
			text-align: center;
		}
		main div.controller div.bar ul li a {
			--mg-f: 7.5px;
			margin: var(--mg-f) 0px;
			height: calc(var(--h) - 5px - var(--mg-f) * 2);
			font-size: 24px; line-height: calc(var(--h) - 5px - var(--mg-f) * 2);
			border-radius: calc((var(--h) - 5px - var(--mg-f) * 2) / 2);
			transition: var(--time-tst-xfast);
		}
		main div.controller div.bar ul li a:link, main div.controller div.bar ul li a:hover { color: var(--clr-bs-gray); }
		main div.controller div.bar ul li a:active, main div.controller div.bar ul li a:focus { color: var(--clr-bs-gray-dark); }
		main div.controller div.bar ul li a:hover { background-color: rgba(0, 0, 0, 0.125); }
		/* main div.controller div.bar ul li a i { position: relative; top: 50%; left: 50%; transform: translate(-62.5%, -50%); } */
		main div.controller div.bar ul li a span {
			position: absolute; top: var(--mg-f); transform: translateX(calc(-100% + 2.5px));
			min-width: inherit; height: calc(100% - var(--mg-f) * 2);
			border-radius: calc((var(--h) - 5px - var(--mg-f) * 2) / 2);
			display: inline-block;
		}
		main div.controller div.bar ul li label { padding: 0px 6.25px; }
		main div.controller div.bar ul li label select {
			border-radius: 3.75px; border: 1px solid var(--clr-bs-gray);
			background-color: var(--clr-gg-grey-100);
			font-size: 20px;
		}
		main div.controller div.bar ul li input[type="checkbox"] { transform: scale(0.75); }
		main div.controller div.bar ul > span {
			position: relative; top: 50%; transform: translateY(-50%);
			width: 1.25px; height: 75%;
			background-color: var(--clr-gg-grey-500);
			display: block;
		}
		main *[data-title]:before {
			padding: 7.5px;
			position: absolute; top: -33.5px; left: 50%; transform: translateX(-50%);
			height: 10px;
			background-color: var(--clr-bs-dark); border-radius: 5px; border: 1px solid var(--clr-bs-light);
			box-shadow: 0px 0px 2.5px 2.5px rgba(127, 127, 127, 0.375);
			color: var(--clr-bs-light); white-space: nowrap;
			font-size: 12.5px; line-height: 10px; font-family: "Balsamiq Sans";
			display: none; content: attr(data-title); pointer-events: none;
		}
		main *[data-title]:after {
			position: absolute; top: -12.5px; left: 50%; transform: translateX(-50%) rotate(45deg);
			width: 10px; height: 10px;
			background-color: var(--clr-bs-dark);
			border-right: 1px solid var(--clr-bs-light); border-bottom: 1px solid var(--clr-bs-light);
			box-shadow: 2.25px 2.25px 0.25px 0.75px rgba(127, 127, 127, 0.09375);
			display: none; content: ""; pointer-events: none;
		}
		main *[data-title]:hover:before, main *[data-title]:active:before, main *[data-title]:hover:after, main *[data-title]:active:after { display: block; }
		@media only screen and (min-width: 768.003px) {
			main div.controller div.bar {
				transform: translateY(25px);
				opacity: 0; filter: opacity(0);
			}
			main div.controller div.bar:hover {
				transform: translateY(0px);
				opacity: 1; filter: opacity(1);
			}
		}
		@media only screen and (max-width: 768px) {
			main div.controller div.bar {
				--pd-s: 25px; --h: 30px;
				padding: 12.5px var(--pd-s);
			}
			main div.controller div.bar {
				bottom: 75px;
				opacity: 0.25; filter: opacity(0.25);
				pointer-events: none;
			}
			main div.controller div.bar.on { opacity: 1; filter: opacity(1); pointer-events: initial; }
			main div.controller div.bar ul { padding: 0px 1.25px; width: 350px; }
			main div.controller div.bar ul li > * { min-width: 20px; font-size: 12.5px; }
			main div.controller div.bar ul li a { --mg-f: 2.5px; font-size: 12px; width: 20px; }
			/* main div.controller div.bar ul li a i { transform: translate(-50%, -50%) scale(0.75); } */
			main div.controller div.bar ul li label { padding: 0px 2.5px; }
			main div.controller div.bar ul li label select { font-size: 10px; }
			/* main *[data-title]:before { transform: translateX(calc(-50% - 7.5px)); }
			main *[data-title]:after { transform: translateX(calc(-50% - 7.5px)) rotate(45deg); } */
		}
		@media only print {
			main .container .wrapper { background: transparent; }
			main div.controller { display: none; }
		}
	</style>
	<script type="text/javascript">
		const zoom = {
			level: [12.5,25,35,50,65,75,80,90,100,110,120,125,150,175,200,300,400,500], now: 8,
			inc: function() { if (zoom.now+1<zoom.level.length) { zoom.now++; zoom.init(); } },
			dec: function() { if (zoom.now-1>=0) { zoom.now--; zoom.init(); } },
			init: function(sel=true) {
				let percent = zoom.level[zoom.now];
				$("main .container .wrapper").css("transform", "scale(" + (percent / 100).toString() + ") rotate(var(--rot))");
				if (sel) $('[name="zoom"] option[value="' + percent.toString() + '"]').prop("selected", true);
			}
		}, rot = {
			now: 0,
			cw: function() { /* rot.now = (rot.now+90==360) ? 0 : rot.now+90; */ rot.now+=90; rot.init(); },
			cc: function() { /* rot.now = (rot.now-90==-360) ? 0 : rot.now-90; */ rot.now-=90; rot.init(); },
			init: function() { $("main .container .wrapper").css("--rot", rot.now.toString()+"deg"); }
		}; var prevkey = [], cooldownload = { t: null, s: 5 };
		$(document).ready(function() {
			zoom.level.forEach(percent => {
				let this_opt = $('<option value="' + percent.toString() + '">' + percent.toString() + ' %</option>');
				$('[name="zoom"]').append(this_opt);
			}); zoom.init(); rot.init();
			$('[name="zoom"]').on("change", function() {
				zoom.now = zoom.level.indexOf(parseFloat(this.value));
				zoom.init(false);
			});
			$('[name="asc"]').on("change", function() { $("main div.controller div.bar").toggleClass("force"); });
			$("main div.controller div.sgt").on("click", function() { $("main div.controller div.bar").toggleClass("on"); });
			setTimeout(function() {
				// Grade(document.querySelectorAll("main .container .wrapper div"));
				// setTimeout(function() { $("main .container .wrapper div img").remove(); }, 250);
			}, 250);
			$(document).on("keypress keydown", function(e) {
				let prik = e.which || e.keyCode, ckeyp = String.fromCharCode(prik) || e.key || e.code, isCrtling = e.ctrlKey, isShifting = e.shiftKey, isAlting = e.altKey;
				prevkey.push(prik); if (prevkey.length > 3) prevkey.shift();
				if (prik == 38) { e.preventDefault(); zoom.inc(); }
				else if (prik == 40) { e.preventDefault(); zoom.dec(); }
				else if (prik == 39) { e.preventDefault(); rot.cw(); }
				else if (prik == 37) { e.preventDefault(); rot.cc(); }
				else if (ckeyp == "c") { document.querySelector('[name="asc"]').checked = !document.querySelector('[name="asc"]').checked; $('[name="asc"]').trigger("change"); }
				else if (ckeyp == "D" && isCrtling) download_file(e);
				// else app.UI.notify(1, prik.toString());
			}); $("html body").css("--h", $(window).height().toString()+"px");
			<?php if (!$useAppImgViewer) { ?>
				if ($("app[name=main] > main iframe[src^=/]").length && "<?=$extension?>" == "pdf" && !app._var.hasPDFviewer()) {
					$("app[name=main] > main iframe").remove();
					$("app[name=main] > main > .container").append(`<div class="message orange css-text-center">Cannot display/preview this file</div>`);
				}
			<?php } ?>
		});
		function download_file(e) {
			if (typeof e !== "undefined") e.preventDefault();
			if (cooldownload.t == null) {
				cooldownload.t = setInterval(function() {
					if (!--cooldownload.s) { clearInterval(cooldownload.t); cooldownload.t = null; cooldownload.s = 5; }
				}, 1000);
				app.UI.notify(0, "Download is starting ...");
				// var adlder = document.querySelector("a.adlder");
				let getfileurl = AppConfig.baseURL+"_resx/service/download?furl=<?=substr($path, 6, strlen($path) - 6).($name<>basename($path) ? "&name=$name" : "")?>";
				// $(adlder).attr("href", getfileurl); adlder.click(); $(adlder).removeAttr("href");
				document.querySelector('main iframe[name="dlframe"]').src = getfileurl;
			} else app.UI.notify(1, "Please wait ... You can download again in ("+cooldownload.s+")");
		}
		function print_file(me) {
			// window.print();
			$(me).attr("disabled", "");
			var noti = app.UI.notify(1, "Preparing file for printing ...");
			printJS({
				printable: atob("<?=rtrim(base64_encode($APP_CONST["baseURL"].$path), "=")?>"),
				type: "image",
				onLoadingEnd: function() {
					app.UI.notify.close(noti);
					$(me).removeAttr("disabled");
				}
			});
		}
	</script>
	<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/print.min.js"></script>
<?php } $APP_PAGE -> print -> nav(); ?>
<main>
	<?php if (!$useAppImgViewer) { ?>
		<section class="container css-pos-u">
			<div class="message lime css-text-center"></div>
		</section>
		<iframe class="css-pos-a" src="<?=$iframeSrc?>">Loading...</iframe>
	<?php } else { ?>
		<section class="container">
			<div class="wrapper">
				<div></div>
				<span></span>
			</div>
		</section>
		<div class="controller">
			<div class="sgt"></div>
			<div class="bar"><ul>
				<li>
					<a onClick="rot.cc()" data-title="Rotate counter-clockwise (←)" role="button" class="action small pill" href="javascript:"><i class="material-icons">rotate_left</i></a>
					<label>Rotate</label>
					<a onClick="rot.cw()" data-title="Rotate clockwise (→)" role="button" class="action small pill" href="javascript:"><i class="material-icons">rotate_right</i></a>
				</li>
				<span></span>
				<li>
					<a onClick="zoom.dec()" data-title="Zoom Out (↓)" role="button" class="action small pill" href="javascript:"><i class="material-icons">zoom_out</i></a>
					<label>Zoom <select name="zoom"></select></label>
					<a onClick="zoom.inc()"data-title="Zoom In (↑)" role="button" class="action small pill" href="javascript:"><i class="material-icons">zoom_in</i></a>
				</li>
				<span></span>
				<li data-title="always show controller (c)">
					<input name="asc" type="checkbox" id="prv-asc"><label for="prv-asc">ASC</label>
				</li>
				<span></span>
				<li>
					<a onClick="print_file(this)" data-title="Print (ctrl+P)" role="button" class="action small pill" href="javascript:"><i class="material-icons">print</i></a>
					<a <?=(preg_match('/_resx\/(static|upload)\/img\/.+\.(png|jpg|jpeg|heic|heif|gif)$/', $path) ? "" : "disabled")?> onClick="download_file(event)" data-title="Download (ctrl+D)" role="button" class="action small pill" href="javascript:"><i class="material-icons">download</i></a>
					<a class="adlder" target="dlframe" download="<?=$name?>" hidden></a>
					<iframe name="dlframe" hidden></iframe>
				</li>
			</ul></div>
			<iframe name="dlframe" hidden></iframe>
		</div>
	<?php } ?>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>