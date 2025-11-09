<?php
/*
FastCoding 主要游戏逻辑
*/

include '../system/config.php';

// 1. 通过 race_id 取得 fastcoding 项目，如果为 new 则为创建一个新游戏。
if (!isset($_GET["race_id"])) {
    if (!$DEBUGGING)
        header("Location: ./join.php");
    exit();
}

$race_id = $_GET["race_id"];

// 处理退出比赛
if (isset($_GET["act"]) && $_GET["act"] == "exit") {
    $result = pdo_query("SELECT joiners FROM fastcoding WHERE `join_id` = ?", $race_id);
    if (!empty($result)) {
        $joiners = $result[0]["joiners"];
        // 从joiners中移除当前用户
        $joiners_array = explode(",", $joiners);
        $joiners_array = array_filter($joiners_array, function($user) use ($un) {
            return $user !== $un;
        });
        $new_joiners = implode(",", $joiners_array);
        
        $sql = "UPDATE `fastcoding` SET `joiners` = ? WHERE `join_id` = ?";
        pdo_query($sql, $new_joiners, $race_id);
    }
    
    if (!$DEBUGGING) {
        header("Location: ./join.php");
        exit();
    }
}

// 创建新游戏
if ($race_id == "new") {
    $sql = "INSERT INTO `fastcoding` (`problems`, `join_id`, `joiners`, `Started`, `started_times`, `create_times`, `finished_users`) VALUES (?,?,?,0,'',?,'')";
    // generate join_id, sample: YYYY-MM-DD-HH-MM-RANDOM
    $join_id = date("Y-m-d-H-i-").strval(rand(1000,9999));
    $joiners = $un;

    // 获取允许的题目列表
    $allow_titles_content = file_get_contents("../system/allow_titles.php");
    $allow_titles = explode(",", $allow_titles_content);
    
    if ($DEBUGGING) {
        echo "Allowed titles: " . $allow_titles_content . "<br>";
        foreach($allow_titles as $alti) {
            echo "allowed " . $alti . "<br>";
        }
    }

    // 随机选择题目
    shuffle($allow_titles);
    $problems = implode(",", array_slice($allow_titles, 0, $NEED_TITLES));
    
    if ($DEBUGGING) echo "Selected problems: " . $problems . "<br>";

    $create_times = date("Y-m-d H:i:s");
    pdo_query($sql, $problems, $join_id, $joiners, $create_times);
    
    if (!$DEBUGGING) {
        header("Location: ./game.php?race_id=" . $join_id);
        exit();
    } else {
        $race_id = $join_id;
    }
}

// 2. 如果不是 new，则读取
$sql = "SELECT * FROM `fastcoding` WHERE `join_id` = ?";
$q_race = pdo_query($sql, $race_id);

// 3. 如果没有这个 race_id，则跳转回 join.php
if (count($q_race) == 0) {
    if (!$DEBUGGING)
        header("Location: ./join.php?err=nraceid");
    exit();
}

$race = $q_race[0]; // 保险起见

// 4. 更新用户排名信息
$result = pdo_query("SELECT `joined_fastcodings_list`, `joined_fastcodings` FROM `fastcoding_ranking` WHERE `user_id` = ?", $un);

if (!empty($result)) {
    $userRanking = $result[0]['joined_fastcodings_list'] ?? '';
    $joinedList = $userRanking ? explode(',', $userRanking) : [];
    if (!in_array($race_id, $joinedList)) {
        $newList = $userRanking ? $userRanking . ',' . $race_id : $race_id;
        $sql = "UPDATE `fastcoding_ranking` SET `joined_fastcodings` = `joined_fastcodings` + 1, `joined_fastcodings_list` = ? WHERE `user_id` = ?";
        pdo_query($sql, $newList, $un);
        if ($DEBUGGING) echo "Updated ranking entry<br>";
    } else {
        if ($DEBUGGING) echo "No ranking update needed<br>";
    }
} else {
    pdo_query("INSERT INTO `fastcoding_ranking`(`user_id`, `joined_fastcodings`, `cleared_fastcodings`, `joined_fastcodings_list`) VALUES (?,?,?,?)", $un, 1, 0, $race_id);
    if ($DEBUGGING) echo "Created new ranking entry<br>";
}

// 5. 添加 joiners
$result = pdo_query("SELECT `joiners` FROM fastcoding WHERE `join_id` = ?", $race_id);
if (!empty($result)) {
    $_joiners = $result[0]["joiners"];
    $joiners = explode(",", $_joiners);
    
    if ($DEBUGGING) echo "Current joiners: " . $_joiners . "<br>";
    
    if (!in_array($un, $joiners)) {
        if ($DEBUGGING) echo "User not in joiners, adding<br>";
        $joiners[] = $un;
        $new_joiners = implode(",", $joiners);
        pdo_query("UPDATE fastcoding SET `joiners` = ? WHERE `join_id` = ?", $new_joiners, $race_id);
    }
}
?>

<!doctype html>
<html>
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 游戏</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../fastcoding.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <?php include "../nav.php"; ?>
        
        <div class="fastcoding-container">
            <!-- 头部 -->
            <div class="fastcoding-header fade-in">
                <h1 class="fastcoding-title">FastCoding</h1>
                <p class="fastcoding-subtitle">游戏等待室 / Game Lobby</p>
            </div>

            <!-- 游戏信息卡片 -->
            <div class="fastcoding-card fade-in">
                <div class="card-header">
                    <h2 class="card-title">比赛信息</h2>
                    <div class="race-id-display">
                        <span>Race ID: <strong><?php echo $race["join_id"]?></strong></span>
<<<<<<< HEAD
                        <button type="button" class="btn btn-primary btn-sm" id="btncopy">
=======
                        <button type="button" class="btn btn-primary btn-sm" onclick="copyToClipboard()">
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
                            <i class="fas fa-copy"></i> 复制
                        </button>
                    </div>
                </div>
                
                <div class="game-status">
                    <div class="countdown-section">
                        <h4>距离游戏开始：</h4>
                        <div id="_begin_time" class="countdown-timer">加载中...</div>
                    </div>
                    
                    <div class="alert alert-warning" role="alert" id="playerMoreNeed" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        需要至少 2 个及以上的人来开始 FastCoding。
                    </div>
                    
                    <div class="game-tips">
                        <p><i class="fas fa-info-circle"></i> 提示：若该比赛的 Race ID 后六个数字距现在时间过长，或超过时间过长，请点击下方退出比赛再进入其他比赛。</p>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn btn-danger" onclick="javascript:window.location.href='./game.php?race_id=<?php echo $race["join_id"]; ?>&act=exit';">
                            <i class="fas fa-sign-out-alt"></i> 退出比赛
                        </button>
                    </div>
                </div>
            </div>

            <!-- 玩家列表卡片 -->
            <div class="fastcoding-card fade-in">
                <div class="card-header">
                    <h2 class="card-title">玩家列表</h2>
                    <span id="player-count">0 位玩家</span>
                </div>
                
                <div id="user_list" class="user-list-container">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i> 加载玩家列表中...
                    </div>
                </div>
            </div>
        </div>

        <script>
<<<<<<< HEAD
            document.getElementById("btncopy").addEventListener("click", async function () {
                const join_id = "<?php echo $race["join_id"] ?>";

                try {
                    const ta = document.createElement('textarea');
                    ta.value = join_id;
                    // 放到屏幕外并禁止滚动影响
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    ta.setSelectionRange(0, ta.value.length);
                    const successful = document.execCommand('copy');
                    document.body.removeChild(ta);

                    if (successful) {
                        showAlert('Race ID 已复制到剪贴板！', 'success');
                        return;
                    }
                    throw new Error('copy-failed');

                } catch (err) {
                    console.error('Copy to clipboard failed:', err);

                    try {
                        window.prompt('复制失败，请手动复制以下 Race ID：', join_id);
                    } catch (e) {
                        // 某些环境下 prompt 可能被阻止
                    }

                    showAlert('复制失败，请手动复制', 'error');
                }
            });

            function showAlert(message, type) {
                // 创建临时提示
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);
                
                // 3秒后自动移除
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 3000);
=======
            function copyToClipboard() {
                var join_id = "<?php echo $race["join_id"] ?>";
                navigator.clipboard.writeText(join_id).then(function() {
                    // 显示成功提示
                    showAlert('Race ID 已复制到剪贴板！', 'success');
                }).catch(function() {
                    showAlert('复制失败，请手动复制', 'error');
                });
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
            }

            function showAlert(message, type) {
                // 创建临时提示
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);
                
                // 3秒后自动移除
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 3000);
            }

            let userCount = 0;
            
            // 获取用户头像
            async function getUserAvatar(userId) {
                try {
                    const response = await fetch(`api.php?act=getAvatar&race_id=<?php echo $race["join_id"]?>&user_id=${userId}`);
                    const data = await response.json();
                    return data.avatar ? data.avatar.replaceAll("\\", "") : '../src/default_avatar.png';
                } catch (error) {
                    console.error('Error fetching avatar:', error);
                    return '../src/default_avatar.png';
                }
            }

            // 渲染用户列表
            async function renderUserList() {
                try {
                    // 获取用户列表
                    const response = await fetch(`api.php?act=user_list&race_id=<?php echo $race["join_id"]?>`);
                    const data = await response.json();
                    
                    if (!data.joiners) {
                        document.getElementById("user_list").innerHTML = '<div class="alert alert-warning">暂无玩家加入</div>';
                        return;
                    }
                    
                    const userList = data.joiners.split(",").filter(u => u.trim() !== "");
                    
                    // 更新玩家数量
                    userCount = userList.length;
                    document.getElementById("player-count").textContent = `${userCount} 位玩家`;
                    
                    // 检查是否需要显示警告
                    if (userCount < 2) {
                        document.getElementById("playerMoreNeed").style.display = 'block';
                    } else {
                        document.getElementById("playerMoreNeed").style.display = 'none';
                    }

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
                        <div class="user-card">
                            <div class="user-avatar">
                                <img src="${user.avatar}" alt="${user.userId}" onerror="this.src='../src/default_avatar.png'">
                            </div>
                            <div class="user-info">
                                <div class="user-name">${user.userId}</div>
                                <a href="/userinfo.php?user=${user.userId}" class="user-profile-link">查看资料</a>
                            </div>
                        </div>
                    `).join('');

                    // 插入到页面
                    document.getElementById("user_list").innerHTML = userCards;
                } catch (error) {
                    console.error('Error rendering user list:', error);
                    document.getElementById("user_list").innerHTML = '<div class="alert alert-danger">加载用户列表失败</div>';
                }
            }

            // 定时更新用户列表
            setInterval(renderUserList, 5000); // 每5秒更新一次

            // 初始加载
            renderUserList();

            // 倒计时逻辑
            let countdownElement = document.getElementById("_begin_time");
            const WAIT_TIME = <?php echo $WAIT_TIME; ?>;
            const RACE_ID = "<?php echo $race["join_id"]; ?>";

            function updateCountdown() {
                fetch("api.php?act=startTime&race_id=" + RACE_ID)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.startTime) {
                            countdownElement.innerHTML = '等待游戏开始...';
                            return;
                        }
                        
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
                            // 检查玩家数量
                            if (userCount >= 2) {
                                // 跳转到 coding.php
                                if (<?php echo $DEBUGGING ? 'false' : 'true'; ?>) {
                                    window.location.href = "./coding.php?race_id=" + RACE_ID;
                                } else {
                                    countdownElement.innerHTML = "游戏即将开始...";
                                }
                            } else {
                                var remainingMinutes = WAIT_TIME - minutes;
                                var remainingSeconds = 60 - seconds;
                                
                                if (userCount < 2) {
                                    countdownElement.innerHTML = "等待新成员加入...";
                                } else {
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
            }

            // 定时更新倒计时
            setInterval(updateCountdown, 1000);
            
            // 初始加载
            updateCountdown();
        </script>

        <style>
            .race-id-display {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .game-status {
                text-align: center;
                padding: 20px 0;
            }
            
            .countdown-section {
                margin-bottom: 20px;
            }
            
            .countdown-timer {
                font-size: 2.5rem;
                font-weight: bold;
                color: var(--primary-color);
                margin: 10px 0;
            }
            
            .game-tips {
                background: rgba(102, 126, 234, 0.05);
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
            }
            
            .game-tips p {
                margin: 0;
                color: #666;
            }
            
            .action-buttons {
                margin-top: 20px;
            }
            
            .user-list-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            
            .user-card {
                background: #f8f9fa;
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                transition: all 0.3s ease;
            }
            
            .user-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            
            .user-avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                overflow: hidden;
                margin: 0 auto 15px;
                border: 3px solid var(--primary-color);
            }
            
            .user-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .user-info {
                margin-top: 10px;
            }
            
            .user-name {
                font-weight: 600;
                margin-bottom: 5px;
            }
            
            .user-profile-link {
                color: var(--primary-color);
                text-decoration: none;
                font-size: 0.9rem;
            }
            
            .user-profile-link:hover {
                text-decoration: underline;
            }
            
            .loading-spinner {
                text-align: center;
                padding: 40px;
                color: #666;
            }
            
            @media (max-width: 768px) {
                .user-list-container {
                    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                }
                
                .countdown-timer {
                    font-size: 2rem;
                }
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>