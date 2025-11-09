<?php
require_once "./system/config.php";
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($OJ_NAME)?> - FastCoding - 排行榜</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./fastcoding.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .ranking-table th,
        .ranking-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .ranking-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .ranking-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .rank-1 { 
            background-color: #fff9c4; 
            font-weight: bold;
        }
        .rank-2 { 
            background-color: #f5f5f5; 
        }
        .rank-3 { 
            background-color: #ffecb3; 
        }
        
        .rank-badge {
            display: inline-block;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .rank-1 .rank-badge {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: white;
        }
        
        .rank-2 .rank-badge {
            background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
            color: white;
        }
        
        .rank-3 .rank-badge {
            background: linear-gradient(135deg, #CD7F32, #A56C28);
            color: white;
        }
        
        .stats-highlight {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include "./nav.php"; ?>
    
    <div class="fastcoding-container">
        <!-- 头部 -->
        <div class="fastcoding-header fade-in">
            <h1 class="fastcoding-title">FastCoding 排行榜</h1>
            <p class="fastcoding-subtitle">- The race of coding -</p>
        </div>

        <!-- 排行榜卡片 -->
        <div class="fastcoding-card fade-in">
            <div class="card-header">
                <h2 class="card-title">🏆 玩家排名</h2>
                <span>前50名玩家</span>
            </div>
            
            <?php
            $sql = "select
                    f.user_id,
                    COALESCE(u.nick, '') AS nick,
                    COALESCE(f.joined_fastcodings, 0) AS joined_fastcodings,
                    COALESCE(f.cleared_fastcodings, 0) AS cleared_fastcodings
                FROM fastcoding_ranking AS f
                LEFT JOIN users AS u ON f.user_id = u.user_id
                ORDER BY f.cleared_fastcodings DESC, f.joined_fastcodings DESC, f.user_id ASC
                LIMIT 50
            ";
            $rows = pdo_query($sql);

            $rankList = [];
            $idx = 1;
            foreach ($rows as $r) {
                $rankList[] = [
                    'index' => $idx,
                    'user_id' => (string)$r['user_id'],
                    'nick' => (string)$r['nick'],
                    'joined_fastcodings' => (int)$r['joined_fastcodings'],
                    'cleared_fastcodings' => (int)$r['cleared_fastcodings'],
                ];
                $idx++;
                if ($DEBUGGING) echo "<script>console.log($idx); console.log(\"CHECKED\")</script>";
            }
            ?>

            <div class="table-responsive">
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th width="80">排名</th>
                            <th>玩家</th>
                            <th width="180">参与比赛</th>
                            <th width="180">获胜次数</th>
                            <th width="150">胜率</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($rankList) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                                    <i class="fas fa-trophy" style="font-size: 3rem; margin-bottom: 15px; display: block; color: #ddd;"></i>
                                    暂无排行榜数据
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rankList as $row): 
                                $win_rate = $row['joined_fastcodings'] > 0 ? 
                                    round(($row['cleared_fastcodings'] / $row['joined_fastcodings']) * 100, 1) : 0;
                                $rank_class = $row['index'] <= 3 ? 'rank-' . $row['index'] : '';
                            ?>
                                <tr class="<?php echo $rank_class; ?>">
                                    <td>
                                        <?php if ($row['index'] <= 3): ?>
                                            <span class="rank-badge"><?php echo $row['index']; ?></span>
                                        <?php else: ?>
                                            <span style="font-weight: 500;"><?php echo $row['index']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: left;">
                                        <div style="display: flex; align-items: center;">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($row['user_id'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;"><?php echo htmlspecialchars($row['user_id']); ?></div>
                                                <div style="font-size: 0.85rem; color: #666;">
                                                    <?php echo htmlspecialchars($row['nick'] ?: '(未命名)'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="stats-highlight"><?php echo (int)$row['joined_fastcodings']; ?></td>
                                    <td class="stats-highlight"><?php echo (int)$row['cleared_fastcodings']; ?></td>
                                    <td>
                                        <span style="color: <?php echo $win_rate >= 50 ? 'var(--success-color)' : 'var(--danger-color)'; ?>; font-weight: bold;">
                                            <?php echo $win_rate; ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!--
            <pre><?php // echo json_encode($rankList, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); ?></pre>
            -->
        </div>

        <!-- 底部按钮 -->
        <div class="text-center mt-4 mb-5">
            <a href="./index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> 返回首页
            </a>
            <a href="./gaming/join.php" class="btn btn-success">
                <i class="fas fa-gamepad"></i> 加入游戏
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>