<?php
require_once "../system/config.php";

if (!isset($_GET["race_id"])) {
    header("Location: ./index.php");
    exit;
}

$race_id = $_GET["race_id"];

$sql = "select joiners from `fastcoding` where `join_id` = ?";
$_joiners = pdo_query($sql, $race_id) [0] ["joiners"];
$joiners = explode(",", $_joiners);

$notfinish = false;
if (isset($_GET["stat"]) && $_GET["stat"] == "notfinish") {
    $notfinish = true;
}

// 查 email 数据
function getAvatar ($__un) {
    $sql = "select email from `users` where `user_id` = ?";
    $email = pdo_query($sql, $__un) [0] ["email"];

    //if (isset($_GET ["debugging"])) echo $email;

    $is_qq = false;
    $qq=stripos($email,"@qq.com");
    $grav_url = "";

    if($qq>0){
        $qq=urlencode(substr($email,0,$qq));
        $grav_url="https://q1.qlogo.cn/g?b=qq&nk=$qq&s=5";
        $is_qq = true;
    }
    else {
        $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?size=100";
    }

    return $grav_url;
}

// 查询是否已经结束
function getEnd($__un)
{
    $sql = "select finished_users from fastcoding where `join_id` = ?";
    $__result = pdo_query($sql, $_GET ["race_id"]) [0] ["finished_users"];
    $result = explode(",", $__result);

    if (in_array($__un, $result)) {
        return "<span class='status-badge status-accepted'><i class='fas fa-check-circle'></i> ACCEPTED</span>";
    }
    else {
        return "<span class='status-badge status-pending'><i class='fas fa-clock'></i> WRITING</span>";
    }
} 
?>

<!doctype html>
<html>
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 游戏结束</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../fastcoding.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .results-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            
            .player-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 25px;
                margin-top: 30px;
            }
            
            .player-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                overflow: hidden;
                transition: all 0.3s ease;
                text-align: center;
            }
            
            .player-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            }
            
            .player-avatar {
                width: 100%;
                height: 200px;
                object-fit: cover;
                border-bottom: 3px solid var(--primary-color);
            }
            
            .player-info {
                padding: 20px;
            }
            
            .player-name {
                font-weight: 600;
                margin-bottom: 10px;
                color: #333;
            }
            
            .player-link {
                color: inherit;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .player-link:hover {
                color: var(--primary-color);
            }
            
            .status-badge {
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                display: inline-block;
            }
            
            .status-accepted {
                background: var(--success-color);
                color: white;
            }
            
            .status-pending {
                background: var(--warning-color);
                color: #333;
            }
            
            .race-info {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 30px;
                text-align: center;
            }
            
            .action-buttons {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 30px;
                margin-bottom: 30px;
            }
            
            .winner-crown {
                position: absolute;
                top: -10px;
                right: -10px;
                background: gold;
                color: #333;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            }
            
            .player-card-wrapper {
                position: relative;
            }
            
            @media (max-width: 768px) {
                .player-grid {
                    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                }
                
                .action-buttons {
                    flex-direction: column;
                    align-items: center;
                }
            }
        </style>
    </head>
    <body>
        <?php include "../nav.php"; ?>
        
        <div class="results-container">
            <!-- 头部 -->
            <div class="fastcoding-header fade-in">
                <h1 class="fastcoding-title">FastCoding</h1>
                <p class="fastcoding-subtitle">- The race of Coding! -</p>
            </div>

            <!-- 游戏结束信息 -->
            <div class="race-info fade-in">
                <h2><i class="fas fa-flag-checkered"></i> 游戏结束</h2>
                <p class="mb-0">比赛 RaceID: <strong><?php echo $race_id; ?></strong></p>
            </div>

<<<<<<< HEAD
            <?php if ($notfinish): ?>
                <div class="alert alert-warning fade-in" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> 注意！</h4>
                    <p>比赛时间已到，但并非所有参赛者都完成了比赛，且比赛已经被删除，因此将不会显示玩家列表。</p>
                </div>
            <?php else: ?>
=======
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
            <!-- 玩家结果 -->
            <div class="fastcoding-card fade-in">
                <div class="card-header">
                    <h2 class="card-title">比赛结果</h2>
                    <span><?php echo count($joiners); ?> 位参赛者</span>
                </div>
                
                <div class="player-grid">
                    <?php 
                    $first_player = true; // 标记第一个完成的玩家
                    foreach ($joiners as $joiner): 
                    ?>
                        <div class="player-card-wrapper">
                            <div class="player-card">
                                <img src="<?php echo getAvatar($joiner); ?>" class="player-avatar" alt="<?php echo $joiner; ?>的头像" 
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo $joiner; ?>&background=random&size=200'">
                                <div class="player-info">
                                    <h5 class="player-name">
                                        <a href="/userinfo.php?user=<?php echo $joiner; ?>" class="player-link">
                                            <?php echo $joiner; ?>
                                        </a>
                                    </h5>
                                    <?php echo getEnd($joiner); ?>
                                </div>
                            </div>
                            <?php 
                            // 假设第一个完成的玩家是胜利者（这里可以根据实际逻辑调整）
                            if ($first_player): 
                                $first_player = false;
                            ?>
                                <div class="winner-crown">
                                    <i class="fas fa-crown"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
<<<<<<< HEAD
            <?php endif; ?>
=======
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056

            <!-- 操作按钮 -->
            <div class="action-buttons">
                <a href="./join.php" class="btn btn-primary">
                    <i class="fas fa-gamepad"></i> 开始新游戏
                </a>
                <a href="../index.php" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> 返回首页
                </a>
<<<<<<< HEAD
                <a href="../ranklist.php" class="btn btn-success">
=======
                <a href="./ranklist.php" class="btn btn-success">
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
                    <i class="fas fa-trophy"></i> 查看排行榜
                </a>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // 删除FastCoding记录的轮询
            async function sendPostRequest(api, data) {
                const formData = new URLSearchParams();
                for (const key in data) {
                    formData.append(key, data[key]);
                }

                try {
                    const response = await fetch(api, {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP Error! Status: ${response.status}`);
                    }

                    return await response.json();

                } catch (error) {
                    console.error('API Request Failed:', error);
                    return { status: 'error', message: error.message };
                }
            }

            let intervalEntity = setInterval(function() {
                let race_id = "<?php echo $race_id ?>";
                sendPostRequest(`./api.php?act=callDeleteFastCoding&race_id=${race_id}`)
                    .then(result => {
                        if (result.status == 1) {
                            clearInterval(intervalEntity);
                            console.log("FastCoding记录已删除");
                        }
                    })
                    .catch(error => {
                        console.error("删除记录时出错:", error);
                    });
            }, 1000);
        </script>
    </body>
</html>
