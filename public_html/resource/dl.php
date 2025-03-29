<?php
	if (isset($_GET['furl'])) {
        $file = $_GET['furl'];
        if (preg_match("/^\/.+$/", $file)) $file = ltrim($file, "/");
        $real = "../$file";
        
        if (file_exists($real)) {
            // Temporary fix
            header("Location: /$file");
            $success = true;

            // The unfixed
            # $success = file_put_contents($name, file_get_contents($real));
            /* header("Content-disposition=attachment; filename=$name");
            $success = fpassthru(fopen($real, "rb")) || readfile($real); */
        } else $success = false;
    } else $success = false;
    if (!$success) echo '<script type="text/javascript">
            if (self == top) location = "/'.$furl.'";
            else $(document.body).load("/error/905");
        </script>
		<script type="text/javascript" src="/resource/js/lib/jquery.min.js"></script>';
?>