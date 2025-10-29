<?php




/*
FastCoding 主要游戏逻辑
*/

include '../system/config.php';

// 1. 通过 race_id 取得 fastcoding 项目，如果为 new 则为创建一个新游戏。
if (!isset($_GET ["race_id"])) {
    if (!$DEBUGGING)
        header("Location: ./join.php");
}
$race_id = $_GET ["race_id"];

if (isset($_GET ["act"])) {
    $joiners = pdo_query("select joiners from fastcoding where `join_id` = ?", $race_id) [0] ["joiners"];
    // 替换 $joiners 的 $un 为空，和单个逗号为空。
    $result = preg_replace('/\b'. $un .'\b|(?<!,),(?!,)/', '', $joiners);
    $sql = "UPDATE `fastcoding` SET `joiners` = ? WHERE `join_id` = ?";
    pdo_query($sql, $result, $race_id);
    if (!$DEBUGGING) header("Location: ./join.php");
}

if ($race_id == "new") {
    $sql = "insert INTO `fastcoding` (`problems`, `join_id`, `joiners`, `Started`, `started_times`, `create_times`, `finished_users`) VALUES (?,?,?,0,'',?,'')";
    // generate join_id, sample: YYYY-MM-DD-HH-MM-RANDOM
    $join_id = date("Y-m-d-H-i-").strval(rand(1000,9999));
    $joiners = $un;

    // get problems
    $p_sql = "select problem_id FROM problem";
    $_ids = pdo_query($p_sql);

    $problems = "";

    // random id from need_titles (需要的题目数量来随机)
    $titles = array();
    foreach ($_ids as $id) {
        $titles[] = $id[0];
    }
    shuffle($titles);

    for ($i = 0; $i < $NEED_TITLES; $i++) {
        $problems .= $titles[$i].",";
    }
    $problems = substr($problems, 0, -1);

    $create_times = date("Y-m-d H:i:s");
    pdo_query($sql, $problems, $join_id, $joiners, $create_times);

    header("Location: ./game.php?race_id=". $join_id);
}

// 2. 如果不是 new，则读取
$sql = "select * FROM `fastcoding` WHERE `join_id` = ?";
$q_race = pdo_query($sql, $race_id);

// 3. 如果没有这个 race_id，则跳转回 join.php
if (count($q_race) == 0) {
    if (!$DEBUGGING)
        header("Location: ./join.php?err=nraceid");
}

$race = $q_race [0]; // 保险起见

// 4. 修改 ranking

$result = pdo_query("SELECT `joined_fastcodings_list`, `joined_fastcodings` FROM `fastcoding_ranking` WHERE `user_id` = ?", $un);

if ($result) {
    $userRanking = $result[0]['joined_fastcodings_list'] ?? '';
    $joinedList = $userRanking ? explode(',', $userRanking) : [];
    if (!in_array($race_id, $joinedList)) {
        $sql = "UPDATE `fastcoding_ranking` SET `joined_fastcodings` = `joined_fastcodings` + 1, `joined_fastcodings_list` = CONCAT(`joined_fastcodings_list`, ?, ?) WHERE `user_id` = ?";
        $newList = $userRanking ? ",$race_id" : $race_id;
        pdo_query($sql, $newList, '', $un);
        if ($DEBUGGING) echo "entry";
    } else {
        if ($DEBUGGING) echo "no_entry";
    }
} else {
    pdo_query("INSERT INTO `fastcoding_ranking`(`user_id`, `joined_fastcodings`, `cleared_fastcodings`, `joined_fastcodings_list`) VALUES (?,?,?,?)", $un, 1, 0, $race_id);
    if ($DEBUGGING) echo "entry";
}


?>

<!doctype html>
<html data-bs-theme="dark">
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 游戏</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include "../nav.php"; ?>
        <main style="margin-top: 27px; margin-left: 27px; text-align: center;">
            <center>
                <img src="../src/img.png" style="width: 640px; height: 360px;">
            </center>
            <br/>
            <div class="row">
                <div class="col">
                    <h3>FastCoding - 游戏</h3>
                    <h5>Race ID: <?php echo $race ["join_id"]?> <button type="button" class="btn btn-primary" onclick="copyToClipboard()">Copy</button></h5>
                    <h4 style="display: inline-flex;">距离游戏开始：<h2 id="_begin_time" style="display: inline-flex;"></h2></h4>
                    <center>
                        <div class="alert alert-warning" role="alert" hidden id="playerMoreNeed" style="width: 500px;">
                            需要至少 2 个及以上的人来开始 FastCoding。
                        </div>
                    </center>
                    <button class="btn btn-outline-danger" onclick="javascript:window.location.href='./game.php?race_id=<?php echo $race ["join_id"]; ?>&act=exit';">退出比赛</button>
                    <p>用户列表：<p id="user_list" style="display: inline-flex"></p></p>
                </div>
            </div>
        </main>
        <script>
            function copyToClipboard() {
                var join_id = "<?php echo $race ["join_id"] ?>";
                navigator.clipboard.writeText(join_id).then(function() {
                    alert("Copied to clipboard!");
                });
            }

            let userCount = 0;
            
            // 轮询 api.php?act=user_list&race_id=<?php echo $race ["join_id"]?>

            async function getUserAvatar(userId) {
                const response = await fetch(`api.php?act=getAvatar&race_id=gunmu&user_id=${userId}`);
                const data = await response.json();
                return data.avatar.replaceAll("\\", "");
            }

            async function renderUserList() {
                try {
                    // 获取用户列表
                    const response = await fetch(`api.php?act=user_list&race_id=<?php echo $race["join_id"]?>`);
                    const data = await response.json();
                    const userList = data.joiners.split(",");
                    
                    if (userList.length == 1) {
                        document.getElementById("playerMoreNeed").hidden = false;
                    }

                    userCount = userList.length;

                    // 创建头像获取的Promise数组
                    const avatarPromises = userList.map(async (userId) => {
                        const avatar = await getUserAvatar(userId);
                        return {
                            userId: userId,
                            avatar: avatar
                        };
                    });

                    // 等待所有头像获取完成
                    const usersWithAvatars = await Promise.all(avatarPromises);
                    
                    // 生成HTML
                    const userCards = usersWithAvatars.map(user => `
                        <div class="col-md-3 mb-4">
                            <div class="card">
                                <img src="${user.avatar}" class="card-img-top" alt="用户头像">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="/userinfo.php?user=${user.userId}">${user.userId}</a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // 插入到页面
                    document.getElementById("user_list").innerHTML = `
                        <div class="container">
                            <div class="row">
                                ${userCards}
                            </div>
                        </div>
                    `;
                } catch (error) {
                    console.error('Error rendering user list:', error);
                    document.getElementById("user_list").innerHTML = '<div class="alert alert-danger">加载用户列表失败</div>';
                }
            }

            // 定时更新用户列表
            setInterval(renderUserList, 5000); // 每5秒更新一次

            // 初始加载
            renderUserList();

            // 轮询 api.php?act=startTime&race_id=<?php echo $race ["join_id"];?>

            let countdownElement = document.getElementById("_begin_time");
            const WAIT_TIME = <?php echo $WAIT_TIME; ?>;
            const RACE_ID = "<?php echo $race ["join_id"]; ?>";

            setInterval(function() {
                fetch("api.php?act=startTime&race_id=" + RACE_ID)
                    .then(response => response.json())
                    .then(data => {
                        // 转换时间
                        var startTime = new Date(data.startTime);
                        var now = new Date();
                        var diff = now - startTime;
                        
                        // 计算分钟数和秒数
                        var totalSeconds = Math.floor(diff / 1000);
                        var minutes = Math.floor(totalSeconds / 60);
                        var seconds = totalSeconds % 60;
                        
                        // 检查是否超过等待时间
                        if (minutes >= WAIT_TIME) {
                            // 跳转到 coding.php

                            //如果有 debugging 标签则不 jump
                            if (<?php if ($DEBUGGING) echo "false"; else echo "true"; ?> && userCount >= 2)
                            {
                                window.location.href = "./coding.php?race_id=" + RACE_ID;
                            }
                            else {
                                var remainingMinutes = WAIT_TIME - minutes;
                                var remainingSeconds = 60 - seconds;
                                
                                if (userCount < 2) {
                                    countdownElement.innerHTML = "等待新成员加入"
                                }
                                else {
                                    // 格式化显示
                                    countdownElement.innerHTML = `${remainingMinutes}分 ${remainingSeconds}秒`;
                                }
                            }
                        } else {
                            // 显示剩余时间
                            var remainingMinutes = WAIT_TIME - minutes;
                            var remainingSeconds = 60 - seconds;
                            
                            // 格式化显示
                            countdownElement.innerHTML = `${remainingMinutes}分 ${remainingSeconds}秒`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        countdownElement.innerHTML = '获取比赛时间失败，请刷新页面重试';
                    });
            }, 1000);
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>