<?php

/*
FastCoding Configuration File
*/

// auto include
static $OJ_WEBPATH = "/home/judge/src/web";
static $ALLOW_COPY = false;
static $NEED_TITLES = 1;// 这个不要改，多个题目的我不打算写
static $AGREE_ACC = 85; // 自定义
static $MAX_TIME = 10; // 分钟
static $WAIT_TIME = 4; // 分钟，原分钟 - 1 为最终分钟，因为其开始会默认加上 60 秒钟，因此需要减去 1
static $ENABLE_DEBUGGING = false;
$DEBUGGING = false;
if (isset($_GET ["debugging"])) $DEBUGGING = true;
if (!$ENABLE_DEBUGGING && $DEBUGGING) $DEBUGGING = false;

require_once($OJ_WEBPATH . '/include/cache_start.php');
require_once($OJ_WEBPATH . "/include/db_info.inc.php");
require_once($OJ_WEBPATH . '/include/setlang.php');
require_once($OJ_WEBPATH . '/include/my_func.inc.php');
if (!isset($_SESSION[$OJ_NAME.'_'.'user_id']) && $DEBUGGING == false) {
    header("Location: /loginpage.php");
    exit(0);
}
$un = $_SESSION[$OJ_NAME . "_user_id"];
static $OJ_NAME = "Fast CODING";

// Q: 为什么把 OJ_NAME 放在最后？
// A：这是因为 OJ_NAME 和 db_info.inc.php 中的常量相同，会把原本的给顶替掉，但是如果放在导入后面，那么就会把 db_info 里的顶替掉。