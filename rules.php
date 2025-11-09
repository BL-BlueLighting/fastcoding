<?php
require_once "./system/config.php";
?>

<!doctype html>
<html>
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 玩法说明</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="./fastcoding.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <?php include "./nav.php"; ?>
        
        <div class="fastcoding-container">
            <!-- 头部 -->
            <div class="fastcoding-header fade-in">
                <h1 class="fastcoding-title">FastCoding 玩法说明</h1>
                <p class="fastcoding-subtitle">游戏规则 / Game rules</p>
            </div>

            <!-- 主要内容 -->
            <div class="row">
                <!-- 左侧内容区域 -->
                <div class="col-md-8">
                    <div class="fastcoding-card fade-in">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <img src="./src/img.png" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        </div>
                        
                        <div class="rules-content">
                            <p style="line-height: 1.6; margin-bottom: 20px;">欢迎！这里是 BL.BlueLighting。</p>
                            <p style="line-height: 1.6; margin-bottom: 30px;">如果你没有玩过 <a href="https://www.codingame.com/multiplayer/clashofcode" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Clash of Code</a>，那么这是对于你的玩法说明。如果你玩过，最好也看看，有些差别。</p>
                            
                            <div class="rule-section">
                                <h4 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center;">
                                    <i class="fas fa-gamepad" style="margin-right: 10px;"></i> 如何游玩
                                </h4>
                                <p style="line-height: 1.6; margin-bottom: 15px;">点击主页的 <button type="button" class="btn btn-primary" disabled style="margin: 0 5px;">开始游戏</button> 按钮，或者点击菜单栏的加入，跳转到另一个页面。</p>
                                <p style="line-height: 1.6; margin-bottom: 15px;">如果您有您的好友分享的 RaceID，请使用 Race ID 加入游戏。</p>
                                <p style="line-height: 1.6; margin-bottom: 20px;">如果您没有 Race ID，请点击随机加入一场游戏。</p>
                            </div>
                            
                            <div class="rule-section">
                                <h4 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center;">
                                    <i class="fas fa-gavel" style="margin-right: 10px;"></i> 游戏规则
                                </h4>
                                <div class="rule-list">
                                    <div class="rule-item">
                                        <div class="rule-number">1</div>
                                        <div class="rule-text">不允许使用生成式 AI，页面会监测你是否切出页面，除了由游戏页面发起的请求外，不允许切出比赛页面。</div>
                                    </div>
                                    <div class="rule-item">
                                        <div class="rule-number">2</div>
                                        <div class="rule-text">本游戏是谁最先完成代码的编写，并获得 <?php echo $AGREE_ACC;?> 及以上的 ACC 视为通过，若您做完了所有题目，且符合条件，最先获胜。</div>
                                    </div>
                                    <div class="rule-item">
                                        <div class="rule-number">3</div>
                                        <div class="rule-text">本游戏会随机从题库中抽取 <?php echo $NEED_TITLES?> 道题目，并打乱，<b>并且不会同步您之前写过的任何代码</b>。</div>
                                    </div>
                                    <div class="rule-item">
                                        <div class="rule-number">4</div>
                                        <div class="rule-text">不允许使用 <span class="badge-custom">Ctrl + C</span> <span class="badge-custom">Ctrl + V</span> 等复制快捷键，任何试图复制粘贴的内容均会被记录。</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="rule-section">
                                <h4 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center;">
                                    <i class="fas fa-question-circle" style="margin-right: 10px;"></i> 显示问题
                                </h4>
                                <p style="line-height: 1.6; margin-bottom: 20px;">若您看见一个没有头像的人进入房间，并且页面布局乱了，请不要慌张。这是因为 Gravatar 服务没有在您的计算机上生成缓存，多刷新几次页面即可。</p>
                            </div>
                            
                            <div class="final-message">
                                <h3 style="color: var(--primary-color); text-align: center; margin-top: 30px;">
                                    <i class="fas fa-heart" style="color: #e74c3c; margin-right: 10px;"></i> 最后，祝您玩的愉快！
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 右侧快速导航区域 -->
                <div class="col-md-4">
                    <div class="fastcoding-card fade-in">
                        <div class="card-header">
                            <h3 class="card-title">快速导航</h3>
                        </div>
                        
                        <div class="quick-links">
                            <a href="./gaming/join.php" class="quick-link">
                                <i class="fas fa-play-circle"></i>
                                <span>开始游戏</span>
                            </a>
                            
                            <a href="./index.php" class="quick-link">
                                <i class="fas fa-home"></i>
                                <span>返回首页</span>
                            </a>
                            
                            <a href="./ranklist.php" class="quick-link">
                                <i class="fas fa-trophy"></i>
                                <span>查看排行榜</span>
                            </a>
                        </div>
                        
                        <div class="info-box">
                            <h5><i class="fas fa-info-circle"></i> 重要提示</h5>
                            <ul>
                                <li>请确保网络连接稳定</li>
                                <li>比赛开始后无法中途加入</li>
                                <li>违反规则可能导致成绩无效</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .rules-content {
                line-height: 1.6;
            }
            
            .rule-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .rule-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .rule-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
            }
            
            .rule-number {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                flex-shrink: 0;
            }
            
            .rule-text {
                flex: 1;
                line-height: 1.5;
            }
            
            .badge-custom {
                background: #6c757d;
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-block;
                margin: 0 2px;
            }
            
            .quick-links {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 25px;
            }
            
            .quick-link {
                display: flex;
                align-items: center;
                padding: 12px 15px;
                background: #f8f9fa;
                border-radius: 8px;
                text-decoration: none;
                color: #333;
                transition: all 0.3s ease;
            }
            
            .quick-link:hover {
                background: rgba(102, 126, 234, 0.1);
                color: var(--primary-color);
                transform: translateX(5px);
            }
            
            .quick-link i {
                margin-right: 10px;
                font-size: 1.2rem;
                width: 20px;
                text-align: center;
            }
            
            .info-box {
                background: rgba(102, 126, 234, 0.05);
                border-radius: 10px;
                padding: 20px;
                border-left: 4px solid var(--primary-color);
            }
            
            .info-box h5 {
                color: var(--primary-color);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }
            
            .info-box h5 i {
                margin-right: 8px;
            }
            
            .info-box ul {
                padding-left: 20px;
                margin-bottom: 0;
            }
            
            .info-box li {
                margin-bottom: 8px;
                line-height: 1.4;
            }
            
            .final-message {
                padding: 20px;
                background: rgba(102, 126, 234, 0.05);
                border-radius: 10px;
                text-align: center;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>