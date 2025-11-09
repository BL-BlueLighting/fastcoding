<?php
require_once "./system/config.php";
?>

<!doctype html>
<html data-bs-theme="dark">
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 首页</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="./fastcoding.css">
    </head>
    <body>
        <?php include "./nav.php"; ?>
        
        <div class="fastcoding-container">
            <!-- 头部 -->
            <div class="fastcoding-header fade-in">
                <h1 class="fastcoding-title">FastCoding</h1>
                <p class="fastcoding-subtitle">- The race of Coding! -</p>
            </div>

            <!-- 主要内容区域 -->
            <div class="row" style="margin-top: 30px;">
                <!-- 左侧介绍区域 -->
                <div class="col-md-7">
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h2 class="card-title">欢迎来到 FastCoding</h2>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 25px;">
                            <img src="./src/img.png" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        </div>

                        <h5 style="color: var(--primary-color); margin-bottom: 15px;">这是什么？</h5>
                        <p style="line-height: 1.6; margin-bottom: 15px;">也许你是第一次来到这个陌生的界面，不过，没关系。</p>
                        <p style="line-height: 1.6; margin-bottom: 15px;">这，还是你熟悉的 Online Judge。</p>
                        <p style="line-height: 1.6; margin-bottom: 25px;">只不过，现在，我们要开始比拼速度了。</p>
                        
                        <h5 style="color: var(--primary-color); margin-bottom: 15px;">玩法是什么？</h5>
                        <p style="line-height: 1.6; margin-bottom: 20px;">参见 <a href="./rules.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">玩法说明</a></p>
                    </div>
                </div>
                
                <!-- 右侧游戏区域 -->
                <div class="col-md-5">
                    <div class="fastcoding-card fade-in" style="height: 100%;">
                        <div class="card-header">
                            <h2 class="card-title">开始游戏</h2>
                        </div>
                        
                        <div style="text-align: center; padding: 30px 0;">
                            <h4 style="margin-bottom: 25px; color: #ccc;">加入一场极速编程对决</h4>
                            <button type="button" class="btn btn-primary" onclick="javascript:window.location.href='./gaming/join.php';" style="padding: 15px 30px; font-size: 1.2rem;">
                                开始游戏
                            </button>
                            
                            <div style="margin-top: 30px; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 10px;">
                                <h6 style="color: var(--primary-color); margin-bottom: 15px;">游戏特色</h6>
                                <ul style="text-align: left; color: #ccc; line-height: 1.8;">
                                    <li>CHECK YOUR SKILL - 验证你的实力</li>
                                    <li>TITLE GOOD FOR YOU - 好题目，好比赛！</li>
                                    <li>QUICKLY RANKING - 排名系统，有面！</li>
                                    <li>GLOBAL P.K. - 与来自中国的各个玩家竞技</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 功能卡片区域 -->
            <div class="row" style="margin-top: 25px;">
                <div class="col-md-4">
                    <div class="fastcoding-card fade-in" style="text-align: center;">
                        <h5 style="color: var(--primary-color); margin-bottom: 15px;">🏆 排行榜</h5>
                        <p style="color: #ccc; margin-bottom: 20px;">查看全球玩家排名</p>
                        <a href="./ranklist.php" class="btn btn-success" style="width: 100%;">查看排名</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fastcoding-card fade-in" style="text-align: center;">
                        <h5 style="color: var(--primary-color); margin-bottom: 15px;">🎯 创建房间</h5>
                        <p style="color: #ccc; margin-bottom: 20px;">随机比赛题目</p>
                        <a href="./gaming/game.php?race_id=new" class="btn btn-warning" style="width: 100%;">创建比赛</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="fastcoding-card fade-in" style="text-align: center;">
                        <h5 style="color: var(--primary-color); margin-bottom: 15px;">🔙 返回主页</h5>
                        <p style="color: #ccc; margin-bottom: 20px;">返回 OJ 主页</p>
                        <a href="/index.php" class="btn btn-info" style="width: 100%;">点我返回</a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>