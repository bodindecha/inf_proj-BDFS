<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "File manager";
	$header_desc = "Private directory browsing and file management";

	// Safety first
	if (!isset($_SESSION["auth"])) {
		header("Location: /$my_url");
		exit(0);
	}

	# error_reporting(0);
	set_time_limit(0);
	if (function_exists("get_magic_quotes_gpc"))
        if (get_magic_quotes_gpc())
		    foreach ($_POST as $key => $value)
			    $_POST[$key] = stripslashes($value);
	
	require($dirPWroot."resource/php/core/db_connect.php");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			.message > *:not(:last-child) { margin-bottom: 10px; }
			.form:not(.modern) input[type="file"] {
				height: 40px;
				transform: translateY(0);
				opacity: 100%; filter: opacity(100%);
			}
			.form:not(.modern) input[type="file"]:after {
				transform: translateY(-50%);
			}
			main pre code { white-space: pre-wrap !important; }
		</style>
		<script type="text/javascript">
			
		</script>
    </head>
    <body>
		<?php require($dirPWroot."resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2><?=$header_title?></h2>
                <div class="table center"><table>
                    <tr>
                        <td align="left">Path :
<?php
    if (isset($_GET["path"])) $path = $_GET["path"];
	else {
		# $path = getcwd();
		# $path = preg_replace("/(\/|\\\\)[^\/\\\\]+$/", "/", $_SERVER['PHP_SELF']);
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
	$lpath = $dirPWroot.ltrim($path, "/");
	echo '</td></tr><tr><td>';
	if (isset($_FILES["file"])) {
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "$lpath/".$_FILES["file"]["name"])) {
			echo '<center class="message green">Upload Success</center>';
			slog("devtool", "new", "file", "$path/".$_FILES["file"]["name"], "pass", "", "", false, true);
		} else {
			echo '<center class="message red">Upload Failed</center>';
			slog("devtool", "new", "file", "$path/".$_FILES["file"]["name"], "fail", "", "", false, true);
		}
	}
?>
    <form class="form inline" method="post" enctype="multipart/form-data">
        <div class="group">
            <span>File Upload</span>
            <input type="file" class="default" data-text="Drag & Drop or Browse..." name="file" required />
        </div>
        <button class="cyan">upload</button>
    </form>
<?php
    echo '</td></tr>';
	if (isset($_GET["filesrc"])) {
		$lang = end(explode(".", $_GET["filesrc"]));
		if ($lang == "php") $lang = "html";
		echo "<tr><td>Current File : ".
		    $_GET["filesrc"].
		    "</tr></td></table></div>".
		    '<pre><code class="language-'.$lang.'" lang="'.$lang.'">'.htmlspecialchars(file_get_contents($dirPWroot.ltrim($_GET["filesrc"], "/"))).'</code></pre>';
	} else if (isset($_GET["option"]) && $_POST["opt"]<>"delete") {
		echo "</table></div><center class=\"message gray\"><div>".ltrim($_POST["path"], ".")."</div>";
		if ($_POST["opt"]=="chmod") {
			if (isset($_POST["perm"]) && false) {
				if (chmod($_POST["path"], intval($_POST["perm"]))) {
					echo '<center class="message green">Change Permission Success</center>';
					slog("devtool", "overwrite", "file", $_POST["path"]." -> ".$_POST["perm"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Change Permission Failed</center>';
					slog("devtool", "overwrite", "file", $_POST["path"]." -> ".$_POST["perm"], "fail", "", "", false, true);
				}
			}
?>
	<center class="message yellow">Function unavailable. You cannot modify this part now.</center>
    <form class="form inline" method="post">
        <div class="group">
            <span>Permission</span>
            <input name="perm" type="text" size="4" value="<?=substr(sprintf("%o", fileperms($_POST["path"])), -4)?>" pattern="0\d{3}" required />
        </div>
        <input type="hidden" name="path" value="<?=$_POST["path"]?>">
        <input type="hidden" name="opt" value="chmod">
        <button disabled class="yellow">Go</button>
    </form>
<?php
		} else if ($_POST["opt"] == "rename") {
			if (isset($_POST["newname"])) {
				if (rename($_POST["path"], "$lpath/".$_POST["newname"])) {
					echo '<center class="message green">Rename File Success</center>';
					slog("devtool", "trans", "file", "$lpath/".$_POST["name"]." -> ".$_POST["newname"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Rename File Fail</center>';
					slog("devtool", "trans", "file", "$lpath/".$_POST["name"]." -> ".$_POST["newname"], "fail", "", "", false, true);
				}
				$_POST["name"] = $_POST["newname"];
			}
?>
    <form class="form inline" method="post">
        <div class="group">
            <span>New Name</span>
            <input name="newname" type="text" size="20" value="<?=$_POST["name"]?>" required />
        </div>
        <input type="hidden" name="path" value="<?=$_POST["path"]?>">
        <input type="hidden" name="opt" value="rename">
        <button class="blue">Go</button>
    </form>
<?php
		} else if ($_POST["opt"] == "edit") {
			if (isset($_POST["src"])) {
				$fp = fopen($_POST["path"], "w");
				if (fwrite($fp, $_POST["src"])) {
                    echo '<center class="message green">Success Edit File</center>';
					slog("devtool", "edit", "file", $_POST["path"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Fail Edit File</center>';
					slog("devtool", "edit", "file", $_POST["path"], "fail", "", "", false, true);
				}
				fclose($fp);
			}
?>
    <form class="form" method="post">
        <textarea name="src" cols="80" rows="20"><?=htmlspecialchars(file_get_contents($_POST["path"]))?></textarea>
        <input type="hidden" name="path" value="<?=$_POST["path"]?>">
        <input type="hidden" name="opt" value="edit">
        <button class="green">Save</button>
    </form>
<?php
		} echo "</center>";
	} else {
		echo '</table></div>';
		if (isset($_GET["option"]) && $_POST["opt"]=="delete") {
			echo "<center class=\"message gray\">";
			if ($_POST["type"] == "dir") {
				if (rmdir($_POST["path"])) {
					echo '<center class="message green">Directory Deleted</center>';
					slog("devtool", "del", "file", "D: ".$_POST["path"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Directory Delete Fail</center>';
					slog("devtool", "del", "file", "D: ".$_POST["path"], "fail", "", "", false, true);
				}
			} else if ($_POST["type"] == "file") {
				if (unlink($_POST["path"])) {
                    echo '<center class="message green">File Deleted</center>';
					slog("devtool", "del", "file", "F: ".$_POST["path"], "pass", "", "", false, true);
                } else {
					echo '<center class="message red">File Delete Fail</center>';
					slog("devtool", "del", "file", "F: ".$_POST["path"], "fail", "", "", false, true);
				}
			}
			echo "</center>";
		}
?>
    <div class="table" id="content"><table><thead>
        <tr class="first">
            <th>Name</th>
            <th>Size</th>
            <th>Permission</th>
            <th>Modify</th>
        </tr>
	</thead><tbody>
		<tr><td colspan="4" center>...Folder...</td></tr>
<?php
		$scandir = scandir($lpath);
		foreach ($scandir as $dir) {
			if (!is_dir("$lpath/$dir") || $dir == "." || $dir == "..") continue;
?>
        <tr>
            <td><a href="?path=<?="$path/$dir"?>"><?=$dir?></a></td>
            <td center>--</td>
            <td center>
<?php
			if (is_writable("$lpath/$dir"))          echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$dir"))    echo '<font style="color: var(--clr-bs-red);">';
            echo perms("$lpath/$dir");
			if (is_writable("$lpath/$dir") || !is_readable("$lpath/$dir")) echo '</font>';
?>
            </td>
            <td>
                <form class="form inline center" method="post" action="?option&path=<?=$path?>">
                    <select name="opt" required>
                        <option value selected disabled>Select</option>
                        <option value="delete">Delete</option>
                        <option value="chmod">Chmod</option>
                        <option value="rename">Rename</option>
                    </select>
                    <input type="hidden" name="type" value="dir">
                    <input type="hidden" name="name" value="<?=$dir?>">
                    <input type="hidden" name="path" value="<?="$lpath/$dir"?>">
                    <button class="default">&gt;</button>
                </form>
            </td>
        </tr>
<?php
		}
		echo '<tr><td colspan="4" center>...File...</td></tr>';
		foreach ($scandir as $file) {
			if (!is_file("$lpath/$file")) continue;
			$size = filesize("$lpath/$file") / 1024;
			$size = round($size, 2);
			if ($size >= 1024) $size = round($size / 1024, 2)." MB";
			else $size = $size." KB";
			echo '<tr><td><a href="?filesrc='.$path.'/'.$file.'&path='.$path.'">'.$file.'</a></td><td align="right">'.$size.'</td><td center>';
			if (is_writable("$lpath/$file"))         echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$file"))   echo '<font style="color: var(--clr-bs-red);">';
			echo perms("$lpath/$file");
			if (is_writable("$lpath/$file") || !is_readable("$lpath/$file")) echo "</font>";
?>
            </td>
            <td>
                <form class="form inline center" method="post" action="?option&path=<?=$path?>">
                    <select name="opt" required>
                        <option value selected disabled>Select</option>
                        <option value="delete">Delete</option>
                        <option value="chmod">Chmod</option>
                        <option value="rename">Rename</option>
                        <option value="edit">Edit</option>
                    </select>
                    <input type="hidden" name="type" value="file">
                    <input type="hidden" name="name" value="<?=$file?>">
                    <input type="hidden" name="path" value="<?="$lpath/$file"?>">
                    <button class="default">&gt;</button>
                </form>
            </td>
        </tr>
<?php
		} echo '</tbody></table></div>';
	}
?>
    </body>
</html>
<?php
	function perms($file) {
		$perms = fileperms($file);
		if (($perms & 49152) == 49152)	  $info = "s";
		else if (($perms & 40960) == 40960) $info = "l";
		else if (($perms & 32768) == 32768) $info = "-";
		else if (($perms & 24576) == 24576) $info = "b";
		else if (($perms & 16384) == 16384) $info = "d";
		else if (($perms & 8192) == 8192)   $info = "c";
		else if (($perms & 4096) == 4096)   $info = "p";
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
	$db -> close();
?>
            </div>
        </main>
        <?php require($dirPWroot."resource/hpe/material.php"); ?>
        <footer>
            <?php require($dirPWroot."resource/hpe/footer.php"); ?>
        </footer>
    </body>
</html>