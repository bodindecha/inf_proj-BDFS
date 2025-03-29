<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "Developers";
	$header["desc"] = "App Font lists";

	$appFonts = array("Jokerman", "Minecraft Ten", "Orange Juice", "Pixelmix", "TH Kodchasal", "TH Krub", "TH Sarabun New", "TH Sarabun PSK", "TianTcl EN-01", "TianTcl TH-01");
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] > main > .container {
		margin: calc(var(--top-height) + 20px) auto 50px;
		position: relative; /* top: 25px; left: 50%; */ transform: translateY(20px);
		/* width: 960px; max-width: 95vw; */ width: 95%;
	}
	app[name=main] > main ul {
		margin: 0px; padding: 0px;
		list-style-type: none;
		display: grid; grid-template-columns: repeat(2, 1fr);
	}
	@media only screen and (max-width: 768px) { app[name=main] > main ul { grid-template-columns: repeat(1, 1fr); } }
	@media only screen and (min-width: 1440px) { app[name=main] > main ul { grid-template-columns: repeat(3, 1fr); } }
	app[name=main] > main ul a:link, app[name=main] > main ul a:visited {
		text-decoration: none; color: var(--clr-main-black-absolute) !important;
		transition: var(--time-tst-fast);
	}
	app[name=main] > main ul a:hover { text-decoration: none !important; }
	app[name=main] > main ul a:active { transform: scale(1.125); z-index: 1; }
	app[name=main] > main ul a li {
		margin: 5px; padding: 5px;
		border-radius: 10px; border: 2.5px solid #999;
		background-color: rgba(250, 250, 250, 0.0625); backdrop-filter: blur(5px);
	}
	app[name=main] > main ul a:hover li { background-color: rgba(250, 250, 250, 0.625); }
	app[name=main] > main ul a li * { display: block; }
	app[name=main] > main ul a li b { font-size: 25px; text-decoration: underline; }
	app[name=main] > main ul a li span { font-size: 18.75px; }
</style>
<script type="text/javascript">
	$(document).ready(function() {
		var ggF = <?=json_encode($googleFonts)?>.concat(<?=json_encode($appFonts)?>).sort(), ctn = $("section.container > ul"), i = 1;
		ggF.forEach((ef) => {
			if (ef.indexOf(":") > -1) ef = ef.split(":")[0];
			ef = ef.replaceAll("+", " ");
			ctn.append($('<a href="javascript:" onClick="cfn('+(i++).toString()+')" draggable="false"><li style="font-family: \''+ef+'\'"><b>'+ef+"</b><span>A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9 ๐ ๑ ๒ ๓ ๔ ๕ ๖ ๗ ๘ ๙ ก ข ฃ ค ฅ ฆ ง จ ฉ ช ซ ฌ ญ ฎ ฏ ฐ ฑ ฒ ณ ด ต ถ ท ธ น บ ป ผ ฝ พ ฟ ภ ม ย ร ฤ ฤๅ ล ฦ ฦๅ ว ศ ษ ส ห ฬ อ ฮ ~ ? ! @ # $ % ^ & ( ) _ + - * / \ = - ` [ ] { } ; : ' \" , . < ></span></li></a>"));
		});
	});
	function cfn(fi) {
		const elem = document.createElement('textarea'); let fontname = $("app[name=main] > main ul a:nth-child("+fi.toString()+") li b").text();
		elem.value = fontname;
		document.body.appendChild(elem); elem.select(); document.execCommand('copy'); document.body.removeChild(elem);
		app.UI.notify(0, "Font name copied!<br>\""+fontname+"\"");
	}
</script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container">
		<ul></ul>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>