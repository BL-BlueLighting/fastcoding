<?php

/*
FastCoding 给 php 自己开放的 api
*/

require '../system/config.php';

function source_browser() {
    $login = true;
    $C_info=pdo_query("SELECT `password` , `accesstime` FROM`users` WHERE`user_id`=? and defunct='N'",$login)[0];
    $C_len=strlen($C_info[1]);
    $C_res="";
    for($i=0;$i<strlen($C_info[0]);$i++){
        $tp=ord($C_info[0][$i]);
        $C_res.=chr(39+($tp*$tp+ord($C_info[1][$i % $C_len])*$tp)%88);
    }
    $C_res=sha1($C_res);
    $C_time=time()+86400;
    setcookie($COJ_NAME."_user",$login,$C_time);
    setcookie($COJ_NAME."_check",$C_res.(strlen($C_res)*strlen($C_res))%7,$C_time);
    setcookie($COJ_NAME."_source_browser","true",time()+60*60*30);
    return true;
}