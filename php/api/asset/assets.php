<?php
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && $file != 'assets.php') {
        echo "<a href='$file' style='font-size: larger'>$file</a><br>";
    }
}

?>