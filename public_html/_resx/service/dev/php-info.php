<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "Developer";
	$header["desc"] = "PHP Information";
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	.container pre {
		margin: 0;
		font-family: monospace;
	}
	.container a:link {
		color: #009; text-decoration: none;
		background-color: #FFF;
	}
	.container a:hover { text-decoration: underline; }
	.container table {
		width: 934px;
		border-collapse: collapse; border: 0;
		box-shadow: 1px 2px 3px #CCC;
	}
	.container .center table {
		margin: 1em auto;
		text-align: left;
	}
	.container td, .container th {
		padding: 4px 5px;
		border: 1px solid #666;
		font-size: 75%; vertical-align: baseline;
	}
	.container th {
		position: sticky; top: 0;
		background: inherit;
	}
	.container h1 { font-size: 150%; }
	.container h2 { font-size: 125%; }
	.container .p { text-align: left; }
	.container .e {
		width: 300px;
		background-color: #CCF;
		font-weight: bold;
	}
	.container .h {
		background-color: #99C;
		font-weight: bold;
	}
	.container .v {
		max-width: 300px;
		background-color: #DDD;
		word-wrap: break-word;
		overflow-x: auto;
	}
	.container .v i { color: #999; }
	.container img {
		float: right;
		border: 0;
	}
	.container hr {
		width: 934px; height: 1px;
		background-color: #CCC; border: 0;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$("app[name=main] > main > .container").load(AppConfig.baseURL+"_resx/static/block/core/php-info.html div.center");
	});
</script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container slider"></section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>