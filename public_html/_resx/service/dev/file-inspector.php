<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "File inspector";
	$header["desc"] = "Private directory browsing and file management";

	$rootFolder = "";

	// Set up
	define("MASTER_TOKEN", base64_decode("dGVzdGlmaWNhdGU1NTU")); # testificate555
	define("MASTER_TOKEN_NAME", "ftpa-token");

	// Safety first
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	if (isset($_REQUEST["optout"]) && TianTcl::sessVar(MASTER_TOKEN_NAME) <> null) unset($_SESSION["var"][MASTER_TOKEN_NAME]);

	$user_token = trim($_REQUEST["token"] ?? TianTcl::sessVar(MASTER_TOKEN_NAME, coalesce: ""));
	# $user_token = trim($_REQUEST["token"] ?? TianTcl::sessVar(MASTER_TOKEN_NAME, "__getValue", "")); // For PHP < 8.0
	if (isset($_REQUEST["token"])) TianTcl::sessVar(MASTER_TOKEN_NAME, $user_token);
	if ($user_token <> MASTER_TOKEN) die("<center><br><h2>You don't have permission to use this page</h2><br><form method=post><lable>Token: <input type=password name=token maxsize=50 required size=25 autofocus /></label> <button>Authenticate</button></form></center>");

	# error_reporting(0);
	set_time_limit(0);
	if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
		foreach ($_POST as $key => $value) $_POST[$key] = stripslashes($value);
	
	require($APP_RootDir."private/script/function/database.php");

	function perms($file) {
		$perms = fileperms($file);
		if (($perms & 49152) == 49152)		$info = "s";
		else if (($perms & 40960) == 40960)	$info = "l";
		else if (($perms & 32768) == 32768)	$info = "-";
		else if (($perms & 24576) == 24576)	$info = "b";
		else if (($perms & 16384) == 16384)	$info = "d";
		else if (($perms & 8192) == 8192)	$info = "c";
		else if (($perms & 4096) == 4096)	$info = "p";
		else								$info = "u";
		$info .= $perms & 256	? "r" : "-";
		$info .= $perms & 128	? "w" : "-";
		$info .= $perms & 64	? ($perms & 2048 ? "s" : "x") : ($perms & 2048	? "S" : "-");
		$info .= $perms & 32	? "r" : "-";
		$info .= $perms & 16	? "w" : "-";
		$info .= $perms & 8		? ($perms & 1024 ? "s" : "x") : ($perms & 1024	? "S" : "-");
		$info .= $perms & 4		? "r" : "-";
		$info .= $perms & 2		? "w" : "-";
		$info .= $perms & 1		? ($perms & 512  ? "t" : "x") : ($perms & 512	? "T" : "-");
		return $info;
	}

	$media_files = array("ico", "jpg", "jpeg", "png", "apng", "webp", "webm", "mp4", "mp3", "pdf");
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] .message > *:not(:last-child) { margin-bottom: 10px; }
	app[name=main] .form input[type="file"] {
		height: 38px !important;
		line-height: 3;
		display: flex;
	}
	app[name=main] .form input[type="file"]::file-selector-button { display: none; }
	app[name=main] pre code {
		font-size: .8em !important;
		white-space: pre-wrap !important;
		display: block !important;
	}
	app[name=main] .file-preview {
		padding: 0 !important;
		width: 100% !important; /* aspect-ratio: 4 / 3; */
		border: 1px solid var(--clr-main-black-absolute); border-radius: 1rem;
		background-color: var(--clr-gg-grey-300);
	}
</style>
<script type="text/javascript">
	
</script>
<?php $APP_PAGE -> print -> nav(); ?>
<main>
	<section class="container">
		<h2 class="css-flex css-flex-split">
			<?=$header["title"]?>
			<form method="post"><button class="red hollow pill icon ripple-click" name="optout"><i class="material-icons">logout</i></button></form>
		</h2>
		<div class="table list center"><table>
			<tr>
				<td align="left">Path :
<?php
	if (isset($_GET["path"])) $path = $_GET["path"];
	else {
		# $path = getcwd();
		# $path = preg_replace("/(\/|\\\\)[^\/\\\\]+$/", "/", $_SERVER["PHP_SELF"]);
		$path = "/";
	} $path = str_replace("\\", "/", $path);
	$path = preg_replace("/(((^|\/)?\.\.?\/)+|\.$)/", "/", $path);
	$paths = explode("/", $path);
	foreach ($paths as $id => $pat) {
		if (!strlen($pat) && $id == 0) {
			$a = true;
			echo '<a href="javascript:" onClick="location.assign(location.pathname)">home</a> / ';
			continue;
		} if (!strlen($pat)) continue;
		echo '<a href="?path=';
		for ($i = 0; $i <= $id; $i++) {
			echo "{$paths[$i]}";
			if ($i <> $id) echo "/";
		}
		echo '">'.$pat."</a> / ";
	} $path = rtrim($path, "/");
	$lpath = $APP_RootDir.$rootFolder.ltrim($path, "/");
	echo '</td></tr>';
	if (isset($_GET["filesrc"])) {
		$lang = explode(".", $_GET["filesrc"]); $lang = strtolower(end($lang));
		if ($lang == "php") $lang = "html";
		echo "<tr><td>Current File : ".
			$_GET["filesrc"].
			"</tr></td></table></div>";
		if (in_array($lang, $media_files)) {
			$html_src = $rootFolder.ltrim($_GET["filesrc"], "/");
			$html_src = substr($html_src, strlen(explode("/", $html_src)[0]));
			if (explode("/", ltrim($_GET["filesrc"], "/"))[0] == "private") echo '<div class="message orange">Sorry, file content preview is currently unavailable.</div>';
			else if (in_array($lang, array_slice($media_files, 0, 6))) echo '<div class="message blue"><img class="file-preview" src="'.$html_src.'" /></div>';
			else echo '<iframe class="file-preview ratio ratio-4-3 land" allow="*" src="'.$html_src.'">Loading...</iframe>';
		} else echo '<pre><code contenteditable style="font-size: 15px !important;" class="language-'.$lang.'" lang="'.$lang.'">'.htmlspecialchars(file_get_contents($APP_RootDir.$rootFolder.ltrim($_GET["filesrc"], "/"))).'</code></pre>';
	} else {
		echo '</table></div>';
?>
	<div class="table static responsive" id="content"><table><thead>
		<tr class="first">
			<th>Name</th>
			<th>Size</th>
			<th>Permission</th>
		</tr>
	</thead><tbody>
		<tr><td colspan="3" center>— Folder —</td></tr>
<?php
		$scandir = scandir($lpath);
		foreach ($scandir as $dir) {
			if (!is_dir("$lpath/$dir") || $dir == "." || $dir == "..") continue;
?>
		<tr>
			<td><a href="?path=<?="$path/$dir"?>"><?=$dir?></a></td>
			<td center>—</td>
			<td center>
<?php
			if (is_writable("$lpath/$dir"))			echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$dir"))	echo '<font style="color: var(--clr-bs-red);">';
			echo perms("$lpath/$dir");
			if (is_writable("$lpath/$dir") || !is_readable("$lpath/$dir")) echo '</font>';
?>
			</td>
		</tr>
<?php
		}
		echo '</tbody><tbody><tr><td colspan="3" center>— File —</td></tr>';
		foreach ($scandir as $file) {
			if (!is_file("$lpath/$file")) continue;
			$size = filesize("$lpath/$file") / 1024;
			$size = round($size, 2);
			if ($size >= 1024) $size = round($size / 1024, 2)." MB";
			else $size = $size." KB";
			echo '<tr><td><a href="?filesrc='.$path.'/'.$file.'&path='.$path.'">'.$file.'</a></td><td align="right">'.$size.'</td><td center>';
			if (is_writable("$lpath/$file"))		echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$file"))	echo '<font style="color: var(--clr-bs-red);">';
			echo perms("$lpath/$file");
			if (is_writable("$lpath/$file") || !is_readable("$lpath/$file")) echo "</font>";
?>
			</td>
		</tr>
<?php
		} echo '</tbody></table></div>';
	} $APP_DB[0] -> close();
?>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>