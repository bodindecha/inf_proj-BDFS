<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "Developers";
	$header["desc"] = "App CSS variable lists";
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] > main .container { width: 95%; }
	app[name=main] > main .container > div {
		margin: 7.5px 0; padding: 5px 10px;
		border-radius: 7.5px;
		transition: var(--time-tst-fast);
	}
	app[name=main] > main .container > div:hover { background-color: rgba(200, 200, 200, .3125); }
	app[name=main] > main div b {
		padding-bottom: 5px;
		font-size: 25px; line-height: 30px; font-family: "Sarabun", serif;
		border-bottom: 1.25px solid #888;
		display: block;
	}
	app[name=main] > main div ul {
		margin: 0; padding: 17.5px 7.5px 12.5px;
		list-style-type: none; --lh: 18.75px;
		display: flex; flex-wrap: wrap; gap: 15px;
	}
	app[name=main] > main div ul a {
		padding: 7.5px;
		font-size: 12.5px; line-height: var(--lh); font-family: 'Quicksand', sans-serif;
		color: var(--clr-main-black-absolute) !important; text-decoration: none;
		background-color: #FFF;
		border-radius: 4px; border: .25px solid #999; box-shadow: 0 0 5px 2.5px rgba(0, 0, 0, .125);
		display: inline-block; transition: all calc(var(--time-tst-xfast) / 2), top none, left none;
	}
	app[name=main] > main div ul a:hover { text-decoration: none !important; }
	app[name=main] > main div ul a:active { transform: scale(1.0625); }
	app[name=main] > main div ul a li * { cursor: pointer; }
	app[name=main] > main div.timings ul a li span {
		margin-top: 2.5px;
		width: auto; height: var(--lh);
		border-radius: var(--lh); background-color: rgba(0, 0, 0, .09375);
		display: block;
	}
	app[name=main] > main div.timings ul a li span:after {
		position: relative; top: 0; left: 0%; transform: scale(.75);
		width: var(--lh); height: var(--lh);
		border-radius: 50%; background-color: #999;
		display: block; content: ""; transition: var(--v) linear;
	}
	app[name=main] > main div.timings ul a:hover li span:after { left: calc(100% - var(--lh)); }
	app[name=main] > main div.colors ul a { overflow: hidden; }
	app[name=main] > main div.colors ul a li, app[name=main] > main div.shadows ul a li { height: var(--lh); }
	app[name=main] > main div.colors ul a li div, app[name=main] > main div.shadows ul a li div {
		position: relative; transform: translate(-7.5px, -7.5px);
		width: calc(var(--lh) + 15px); height: calc(var(--lh) + 15px);
		background-color: var(--v); border-radius: 2.5px 0 0 2.5px; box-shadow: 2.5px 0 2.5px -.5px rgba(0, 0, 0, .25);
		display: inline-block;
	}
	app[name=main] > main div.shadows ul a li div { --f: #00000000; background-color: var(--f); box-shadow: none; }
	app[name=main] > main div.colors ul a li label, app[name=main] > main div.shadows ul a li label { position: relative; top: -19px; }
	app[name=main] > main div.colors ul a li label:after {
		position: absolute; top: -1px; left: 0;
		width: 0%;
		font-size: 12.5px; line-height: var(--lh); font-family: 'Quicksand', sans-serif;
		color: var(--v); text-shadow: 0 0 2.5px #000;
		display: block; content: attr(data-text); white-space: nowrap;
		overflow: hidden; transition: calc(var(--time-tst-xfast) * 2 / 3);
	}
	app[name=main] > main div.colors ul a:hover li label:after { width: 100%; }
	app[name=main] > main div.shadows ul a li div:before {
		position: relative; top: 50%; left: 75%; transform: translate(-75%, -50%);
		width: 50%; height: 50%;
		box-shadow: var(--s); border-radius: 50%;
		display: block; content: "";
	}
</style>
<script type="text/javascript">
	// Initial function
	$(document).ready(css);
	function css() {
		// delete Hammer.defaults.cssProps.userSelect;
		var s_xhr = new XMLHttpRequest(); s_xhr.open("GET", "/_resx/static/style/core/stylevar.css", true); s_xhr.responseType = "text"; s_xhr.onload = function() { return passcss(this.responseText); }; s_xhr.send();
	}
	function passcss(tmp) {
		var nc = [], tc = tmp.replace(/((\/\* [A-Za-z0-9\s&]+ \*\/)|(\:root,\ app\[name=main\] {))/g, "").replace(/(?:\r\n|\r|\n|\t)/g, "").split("}");
		tc.pop();
		for (let i = 0; i < tc.length; i++) {
			var ts = tc[i].split(/(;\s*--)/g);
			for (let j = 0; j<ts.length; j++) {
				if (ts[j].includes(";")||ts[j].match(/^(\s*)$/)) ts.splice(j, 1);
				else ts[j] = ts[j].replace(/(;|--)/g, "");
			} if (ts.length>0) nc.push(ts);
		}
		for (let k = 0; k < nc.length; k++) {
			for (let l = 0; l<nc[k].length; l++) {
				nc[k][l] = nc[k][l].split(": ");
				if (nc[k][l][1].includes(";")) nc[k][l][1] = nc[k][l][1].replace(";", "");
			}
		}
		render(nc);	return true;
	}
	function render(vals) {
		var ctn = $("app[name=main] > main .container"), keys = ["Timings", "Shadows & Fadings", "Colors"], str = [
			(a,b) => '<label>'+a+' ('+b+')</label><span style="--v:'+b+'"></span>',
			function(a,b) {
				var c = (b.length=="9"?"f:":"s:0 0 ")+b+(b.length!="9"?" #000":"");
				return '<div style="--'+c+'"></div><label>'+a+' ('+b+')</label>';
			}, (a,b) => '<div style="--v:'+b+'"></div><label style="--v:'+b+'" data-text="'+a+' ('+b.toUpperCase()+')">'+a+' ('+b.toUpperCase()+')</label>'
		];
		for (let i = 0; i<keys.length; i++) {
			ctn.append($('<div class="'+keys[i].toLowerCase()+'"><b>'+keys[i]+'</b><ul></ul></div>'));
			var prt = $("app[name=main] > main div."+keys[i].toLowerCase().split(" ")[0]+" ul");
			for (let j = 0; j<vals[i].length; j++) prt.append($('<a href="javascript:" onClick="copy('+(j+1).toString()+', '+(i+1).toString()+')" draggable="false"><li>'+str[i](vals[i][j][0], vals[i][j][1])+'</li></a>'));
		} // Add effect
		// document.querySelectorAll("app[name=main] > main div ul a").forEach(effectDragDraw);
		// $("app[name=main] > main div ul a").draggable();
		$("app[name=main] > main div ul").sortable();
	}
	function effectDragDraw(item) {
		var leash = new Hammer(item);
		leash.get("pan").set({direction: Hammer.DIRECTION_ALL});
		leash.add(new Hammer.Press({time: 500}));

		var currentTarget = null, originalIndex = null;
		leash.on("press", function(event) {
			currentTarget = $(event.target);
			currentTarget.addClass("dragging");
			originalIndex = currentTarget.index();
		});
		leash.on("panmove", function(event) {
			if (!currentTarget) return;
			var x = event.deltaX;
			var y = event.deltaY;
			currentTarget.css({
				"transform": "translate3d(" + x + "px," + y + "px,0)",
				"transition": "none"
			});
		});
		leash.on("panend", function(event) {
			if (!currentTarget) return;
			currentTarget.removeClass("dragging");
			currentTarget.css({
				"transform": "",
				"transition": ""
			});
			var newIndex = currentTarget.index();
			if (newIndex != originalIndex) {
				currentTarget.detach();
				if (newIndex > originalIndex) newIndex--;
				currentTarget.insertBefore(list.childNodes[newIndex]);
			}
			currentTarget = null;
			originalIndex = null;
		});
	}
	// Page function
	function copy(oi, of) {
		const elem = document.createElement("textarea"); let cssvarname = $("app[name=main] > main div:nth-of-type("+of.toString()+") ul a:nth-child("+oi.toString()+") li label").text(); let ctxt = cssvarname.substr(0, (cssvarname.indexOf(" ")>-1?cssvarname.indexOf(" "):cssvarname.length));
		elem.value = "--"+ctxt;
		document.body.appendChild(elem); elem.select(); document.execCommand("copy"); document.body.removeChild(elem);
		app.UI.notify(0, "CSS variable name copied!<br>\""+ctxt+"\"");
	}
</script>
<!-- <script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/hammer.min.js"></script> -->
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/jQuery/UI.min.js"></script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?></h2>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>