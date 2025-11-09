<?php

$OJ_CACHE_SHARE = false;//!(isset($_GET['cid'])||isset($_GET['my']));
require_once('../../include/cache_start.php');
require_once('../../include/db_info.inc.php');
require_once('../../include/memcache.php');
require_once('../../include/my_func.inc.php');
require_once('../../include/const.inc.php');
require_once('../../include/setlang.php');

$allows = explode(",", $_GET["allows"]);

if (!isset($_SESSION [$OJ_NAME . "_" . "administrator"])) {
	header("Location: /whatrudoing.php");
	exit;	
}

file_put_contents("./allow_titles.php", implode(",", $allows));

echo "Saved allow titles.";
echo '<a href="javascript:window.history.back()">Back</a>';
