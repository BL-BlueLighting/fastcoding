<?php
require_once "../system/config.php";

/*
fastcoding table:
ID: self-add value
problems: problem_id list, use ',' to split.
join_id: system auto generate, share it to another user to join a game.
joiners: players list, use ',' to split (no space)
Started: does this game started? 
started_times: if it started, this storage times game started.
*/

$err = false;
$error_content = "";

// 检测用户是否已经加入一场比赛
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
    $un = $_SESSION[$OJ_NAME.'_'.'user_id'];
    $all_races = pdo_query("SELECT * FROM fastcoding");
    foreach ($all_races as $race) {
        $all_users = explode(",", $race["joiners"]);
        if (in_array($un, $all_users)) {
            header("Location: ./game.php?race_id=" . $race["join_id"]);
            exit();
        }
    }
}

if (isset($_GET["race_id"])) {
    $raceid = $_GET["race_id"];
    $race = [];

    // 随机加入游戏
    if ($raceid == "random") {
        $races = pdo_query("SELECT * FROM fastcoding WHERE `Started` = 0"); // 屏蔽已经开始的比赛

        if (empty($races)) {
            $err = true;
            $error_content = "当前没有可加入的比赛。";
        } else {
            $luck_index = array_rand($races);
            $race = $races[$luck_index];
            $raceid = $race["join_id"];
        }
    }
    // 新建游戏
    else if ($raceid == "new") {
        header("Location: ./game.php?race_id=new");
        exit();
    }
    // 通过Race ID加入
    else {
        $result = pdo_query("SELECT * FROM fastcoding WHERE `join_id` = ?", $raceid);
        if (!empty($result)) {
            $race = $result[0];
        } else {
            $err = true;
            $error_content = "Race ID 不正确。";
        }
    }

    // 检查房间是否已满
    if (!$err && !empty($race)) {
        $joiners = explode(",", $race["joiners"]);
        if (count($joiners) >= 8) {
            $err = true;
            $error_content = "房间已满。";
        }
    }

    // 检查比赛是否已经开始
    if (!$err && !empty($race) && $race["Started"]) {
        $err = true;
        $error_content = "该比赛已经开始。";
    }

    // 加入游戏
    if (!$err && !empty($race)) {
        $joiners = explode(",", $race["joiners"]);
        if (empty($joiners[0])) {
            $joiners = array($un);
        } else {
            $joiners[] = $un;
        }
        $new_joiners = implode(",", $joiners);
        
        $sql = "UPDATE `fastcoding` SET `joiners` = ? WHERE `join_id` = ?";
        pdo_query($sql, $new_joiners, $raceid);

        header("Location: ./game.php?race_id=" . $raceid);
        exit();
    }
}

if (isset($_GET["err"])) {
    if ($_GET["err"] == "nraceid") {
        // 不是正确的 raceid
        $err = true;
        $error_content = "Race ID 不正确。";
    }
    else if ($_GET["err"] == "njoiner") {
        $err = true; // 没有加入
        $error_content = "您没有加入该场游戏的权限。";
    }
}

?>

<!doctype html>
<html>
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 加入游戏</title>
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
                <p class="fastcoding-subtitle">加入游戏 / Join game</p>
            </div>

            <!-- 错误提示 -->
            <?php if ($err): ?>
            <div class="fastcoding-card fade-in" style="border-left: 4px solid var(--danger-color);">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="color: var(--danger-color); font-size: 1.5rem; margin-right: 15px;"></i>
                    <div>
                        <h4 style="color: var(--danger-color); margin-bottom: 5px;">错误</h4>
                        <p style="margin: 0;"><?php echo $error_content; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 主要内容 -->
            <div class="row" style="margin-top: 30px;">
                <!-- 左侧说明区域 -->
                <div class="col-md-6">
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h2 class="card-title">如何加入游戏</h2>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 25px;">
                            <img src="../src/img.png" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        </div>
                        
                        <div class="instruction-list">
                            <div class="instruction-item">
                                <div class="instruction-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="instruction-text">
                                    <h5>通过 Race ID 加入</h5>
                                    <p>如果您有好友分享的 Race ID，请在右侧输入框中填写</p>
                                </div>
                            </div>
                            
                            <div class="instruction-item">
                                <div class="instruction-icon">
                                    <i class="fas fa-random"></i>
                                </div>
                                <div class="instruction-text">
                                    <h5>随机加入游戏</h5>
                                    <p>系统将为您随机匹配一个可加入的游戏房间</p>
                                </div>
                            </div>
                            
                            <div class="instruction-item">
                                <div class="instruction-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="instruction-text">
                                    <h5>创建新游戏</h5>
                                    <p>创建一个新的游戏房间，邀请好友加入</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 右侧操作区域 -->
                <div class="col-md-6">
                    <!-- 通过 Race ID 加入 -->
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card" style="margin-right: 10px;"></i>
                                通过 Race ID 加入游戏
                            </h3>
                        </div>
                        <form action="./join.php" method="GET">
                            <div class="input-group" style="margin-bottom: 20px;">
                                <span class="input-group-text" id="race_id_label">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="在此处填写 Race ID" aria-label="RaceID" aria-describedby="race_id_label" name="race_id" required>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-sign-in-alt"></i> 加入游戏
                            </button>
                        </form>
                    </div>
                    
                    <!-- 随机加入游戏 -->
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-random" style="margin-right: 10px;"></i>
                                随机加入游戏
                            </h3>
                        </div>
                        <p style="margin-bottom: 20px; color: #666;">系统将为您随机匹配一个可加入的游戏房间</p>
                        <form action="./join.php" method="GET">
                            <input type="text" value="random" name="race_id" hidden>
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                <i class="fas fa-dice"></i> 随机加入
                            </button>
                        </form>
                    </div>
                    
                    <!-- 新建游戏 -->
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus-circle" style="margin-right: 10px;"></i>
                                新建一场游戏
                            </h3>
                        </div>
                        <p style="margin-bottom: 20px; color: #666;">创建一个新的游戏房间，邀请好友加入</p>
                        <form action="./join.php" method="GET">
                            <input type="text" value="new" name="race_id" hidden>
                            <button type="submit" class="btn btn-warning" style="width: 100%;">
                                <i class="fas fa-plus"></i> 新建游戏
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- 底部按钮 -->
            <div class="text-center mt-4 mb-5">
                <a href="../index.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> 返回首页
                </a>
            </div>
        </div>

        <style>
            .instruction-list {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }
            
            .instruction-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .instruction-icon {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            
            .instruction-text h5 {
                color: var(--primary-color);
                margin-bottom: 5px;
            }
            
            .instruction-text p {
                color: #666;
                margin: 0;
                line-height: 1.4;
            }
            
            .input-group-text {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                border: none;
            }
            
            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>