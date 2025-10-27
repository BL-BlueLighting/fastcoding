<?php

/*
FastCoding 前端 API.
*/
include './config.php';

if (!isset($_GET ["act"]) || !isset($_GET ["race_id"])) {
    echo "{'status': 0}";
    die();
}

$act = $_GET ["act"];
$race_id = $_GET ["race_id"];

if ($act == "user_list") {
    $sql = "select joiners from `fastcoding` where `join_id` = ?";
    $joiners = pdo_query($sql, $race_id) [0] ["joiners"];
    // json response
    $content = array(
        "status" => 1,
        "joiners" => $joiners
    );

    echo json_encode($content);
}

else if ($act == "startTime") {
    $sql = "select create_times from `fastcoding` where `join_id` = ?";
    $time = pdo_query($sql, $race_id) [0] ["create_times"];
    $content = array(
        "status" => 1,
        "startTime" => $time
    );

    echo json_encode($content);
}

else if ($act == "getAvatar") {
    $content = array();
    if (!isset($_GET ["user_id"])) {
        $content = array(
            "status" => 0,
        );
    }
    else {
        // 查 email 数据
        $sql = "select email from `users` where `user_id` = ?";
        $email = pdo_query($sql, $_GET ["user_id"]) [0] ["email"];

        if (isset($_GET ["debugging"])) echo $email;

        $is_qq = false;
        $qq=stripos($email,"@qq.com");
        if($qq>0){
            $qq=urlencode(substr($email,0,$qq));
            $grav_url="https://q1.qlogo.cn/g?b=qq&nk=$qq&s=5";
            $is_qq = true;
        };

        if ($is_qq) {
            $content = array(
                "status" => 1,
                "avatar" => $grav_url
            );
        }
        else {
            $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?size=100";
            $content = array(
                "status" => 1,
                "avatar" => $grav_url
            );
        }
    }

    echo json_encode($content);
}

else if ($act == "doesHeOver") {
    $content = array();
    if (!isset($_GET ["user_id"])) {
        $content = array(
            "status" => 0,
        );
    }
    else {
        $sql = "select finished_users from fastcoding where `join_id` = ?";
        $__result = pdo_query($sql, $race_id) [0] ["finished_users"];
        $result = explode(",", $__result);
        if (in_array($_GET ["user_id"], $result)) {
            $content = array(
                "status" => 1,
                "is_over" => true
            );
        }
        else {
            $content = array(
                "status" => 1,
                "is_over" => false
            );
        }
    }

    echo json_encode($content);
}

else if ($act == "callAccepted") {
    $content = array();
    if (!isset($_GET ["user_id"])) {
        $content = array(
            "status" => 0,
        );
    }
    else {

        // 读取 solution, race
        $race = pdo_query("select problems from fastcoding where `join_id` = ?", $race_id) [0];
        $problem = $race ["problems"];
        $solution = pdo_query("select * from solution where `user_id` = ? and `problem_id` = ? and `result` = 4", $_GET ["user_id"], $problem);

        if (!$solution) {
            $content = array(
                "status" => 0,
                "message" => "Failed to check your grade. Please try again."
            );
        }

        // 添加 finished_users
        $finished_users = $race ["finished_users"];
        if ($finished_users == "") $finished_users = $un;
        else $finished_users . "," . $un;

        pdo_query("update `fastcoding` set `finished_users` = ? where `join_id` = ?", $finished_users, $race_id);

        // 添加或修改 fastcoding_ranking
        $sql = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        $user = pdo_query($sql, $user_id);

        if ($user) {
            $sql = "UPDATE `fastcoding_ranking` SET `cleared_fastcodings` = `cleared_fastcodings` + 1 WHERE `user_id` = ?";
            pdo_query($sql, $user_id);
        }
        else {
            pdo_query("INSERT INTO `fastcoding_ranking`(`user_id`, `joined_fastcodings`, `cleared_fastcodings`) VALUES (?,?,?)", $user_id, 1, 1);
        }
        
        $content = array(
            "status" => 1,
            "message" => "You accepted titles."
        );
        
    }

    echo json_encode($content);
}