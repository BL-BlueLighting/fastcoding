<?php

/*
FastCoding 前端 API.
*/
include '../system/config.php';

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
            $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?size=500";
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
        $user_id = $_GET["user_id"]; // 定义 user_id 变量

        // 读取 solution, race
        $race = pdo_query("select * from fastcoding where `join_id` = ?", $race_id) [0];
        $problem = $race ["problems"];
        $solution = pdo_query("select * from solution where `user_id` = ? and `problem_id` = ? and `result` = 4", $user_id, $problem);

        if (empty($solution)) {
            $content = array(
                "status" => 0,
                "message" => "Failed to check your grade. Please try again."
            );
            echo json_encode($content);
            die();
        }

        // 添加 finished_users
        $finished_users = $race ["finished_users"];
        $finished_users_array = explode(",", $finished_users);

        // 如果用户不在完成列表中，则添加
        if (!in_array($user_id, $finished_users_array)) {
            if (empty($finished_users)) {
                $finished_users = $user_id;
            } else {
                $finished_users .= "," . $user_id; // 使用赋值操作符
            }
            pdo_query("update `fastcoding` set `finished_users` = ? where `join_id` = ?", $finished_users, $race_id);
        }

        // 添加或修改 fastcoding_ranking
        $ranking_record = pdo_query("SELECT * FROM fastcoding_ranking WHERE user_id = ?", $user_id);

        if (!empty($ranking_record)) {
            // 更新已存在的记录
            $sql = "UPDATE `fastcoding_ranking` SET `cleared_fastcodings` = `cleared_fastcodings` + 1 WHERE `user_id` = ?";
            pdo_query($sql, $user_id);
        } else {
            // 创建新记录
            pdo_query("INSERT INTO `fastcoding_ranking`(`user_id`, `joined_fastcodings`, `cleared_fastcodings`, `joined_fastcodings_list`) VALUES (?,?,?,?)",
                     $user_id, 1, 1, $race_id);
        }

        $content = array(
            "status" => 1,
            "message" => "You accepted titles."
        );
    }

    echo json_encode($content);
}

else if ($act == "callDeleteFastCoding") {
    $content = array();

    // 条件 1：所有人都通关，则可以删除。
    $sjoiners = pdo_query("select joiners from `fastcoding` where `join_id` = ?", $race_id);
    $sfinished_joiners = pdo_query("select finished_users from `fastcoding` where `join_id` = ?", $race_id);
    $joiners = count(explode(",", $sjoiners));
    $finished_joiners = count(explode(",", $sfinished_joiners));

    // 条件 2：如果时间相差大于 $MAX_TIME，则可以删除。
    $sStartime = pdo_query("select create_times from `fastcoding` where `join_id` = ?", $race_id)[0]['create_times'];
    $currentTime = time();
    $startTime = strtotime($sStartime);
    $divided = $currentTime - $sStartime;

    if ($joiners == $finished_joiners || $divided >= $MAX_TIME * 60) {

        $sql = "delete from `fastcoding` where `join_id` = ?";
        pdo_query($sql, $race_id);

        $content = array(
            "status" => 1,
            "message" => "Successfully to delete fastcoding."
        );

    }
    else {
        $content = array(
            "status" => 0,
            "message" => "Failed to delete fastcoding because someone not finished and time is not up."
        );
    }
}
<<<<<<< HEAD

// 延长时间
else if ($act == "extendTime") {
    $content = array();

    // 如果目前时间和开始时间相差还没到 $MAX_TIME，则不能延长时间，拒绝请求
    $sStartime = pdo_query("select create_times from `fastcoding` where `join_id` = ?", $race_id)[0]['create_times'];
    $currentTime = time();
    $startTime = strtotime($sStartime);
    $divided = $currentTime - $sStartime;

    if ($divided < $MAX_TIME * 60) {
        $content = array(
            "status" => 0,
            "message" => "Failed to extend time because time is not up."
        );

        echo json_encode($content);
        die();
    }

    $sql = "update `fastcoding` set `create_times` = DATE_ADD(`create_times`, INTERVAL ? MINUTE) where `join_id` = ?";
    pdo_query($sql, $EXTEND_TIME, $race_id);

    $content = array(
        "status" => 1,
        "message" => "Successfully to extend time."
    );

    echo json_encode($content);
}
=======
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
