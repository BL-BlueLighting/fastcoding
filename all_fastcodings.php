<?php
require_once "./system/config.php";

// 分页设置
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// 获取总记录数
$total_count_result = pdo_query("SELECT COUNT(*) as total FROM fastcoding");
$total_count = $total_count_result[0]['total'] ?? 0;
$total_pages = ceil($total_count / $per_page);

// 调试信息
if (isset($_GET['debug'])) {
    echo "总记录数: " . $total_count . "<br>";
    echo "页码: " . $page . "<br>";
    echo "偏移量: " . $offset . "<br>";
}

// 获取当前页的记录 - 修复参数传递问题
$sql = "SELECT * FROM fastcoding ORDER BY ID DESC LIMIT $offset, $per_page";
$fastcodings = pdo_query($sql);

// 如果查询失败，尝试其他方法
if ($fastcodings === -1 || $fastcodings === false) {
    // 尝试使用不同的查询方式
    $sql = "SELECT * FROM fastcoding ORDER BY ID DESC";
    $all_fastcodings = pdo_query($sql);
    
    if (is_array($all_fastcodings)) {
        // 手动分页
        $fastcodings = array_slice($all_fastcodings, $offset, $per_page);
    } else {
        $fastcodings = [];
    }
}

// 确保 $fastcodings 是数组
if (!is_array($fastcodings)) {
    $fastcodings = [];
}

// 获取创建者昵称的函数
function getCreatorNick($joiners) {
    if (empty($joiners)) {
        return "未知用户";
    }
    
    $users = explode(",", $joiners);
    if (empty($users) || empty($users[0])) {
        return "未知用户";
    }
    
    $creator_id = trim($users[0]);
    if (empty($creator_id)) {
        return "未知用户";
    }
    
    $result = pdo_query("SELECT nick FROM users WHERE user_id = ?", $creator_id);
    
    if (!empty($result) && !empty($result[0]['nick'])) {
        return $result[0]['nick'];
    }
    
    return $creator_id; // 如果没有昵称，返回用户ID
}

// 获取用户昵称的函数
function getUserNick($user_id) {
    if (empty($user_id)) {
        return "未知用户";
    }
    
    $result = pdo_query("SELECT nick FROM users WHERE user_id = ?", $user_id);
    
    if (!empty($result) && !empty($result[0]['nick'])) {
        return $result[0]['nick'];
    }
    
    return $user_id; // 如果没有昵称，返回用户ID
}
?>

<!doctype html>
<html>
<head>
    <title><?php echo $OJ_NAME?> - FastCoding - 所有比赛</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./fastcoding.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fastcoding-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .fastcoding-table th,
        .fastcoding-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .fastcoding-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
        }
        
        .fastcoding-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .race-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .user-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        
        .user-badge {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .problem-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        
        .problem-badge {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .page-info {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-active {
            background: var(--success-color);
            color: white;
        }
        
        .status-finished {
            background: var(--secondary-color);
            color: white;
        }
        
        .status-waiting {
            background: var(--warning-color);
            color: #333;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .fastcoding-table {
                font-size: 0.9rem;
            }
            
            .fastcoding-table th,
            .fastcoding-table td {
                padding: 10px 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include "./nav.php"; ?>
    
    <div class="fastcoding-container">
        <!-- 头部 -->
        <div class="fastcoding-header fade-in">
            <h1 class="fastcoding-title">所有 FastCoding 比赛</h1>
            <p class="fastcoding-subtitle">查看所有已创建的 FastCoding 比赛记录</p>
        </div>

        <!-- 页面信息 -->
        <div class="page-info">
            共 <?php echo $total_count; ?> 场比赛，第 <?php echo $page; ?> 页 / 共 <?php echo $total_pages; ?> 页
        </div>

        <!-- 调试信息 -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="fastcoding-card">
            <div class="card-header">
                <h5>调试信息</h5>
            </div>
            <div class="card-body">
                <p>查询结果类型: <?php echo gettype($fastcodings); ?></p>
                <p>查询结果数量: <?php echo is_array($fastcodings) ? count($fastcodings) : 'N/A'; ?></p>
                <p>SQL: SELECT * FROM fastcoding ORDER BY ID DESC LIMIT <?php echo $offset; ?>, <?php echo $per_page; ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- 比赛列表 -->
        <div class="fastcoding-card fade-in">
            <?php if (empty($fastcodings)): ?>
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h3>暂无比赛记录</h3>
                    <p>还没有创建任何 FastCoding 比赛</p>
                    <a href="./gaming/join.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> 创建新比赛
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="fastcoding-table">
                        <thead>
                            <tr>
                                <th width="120">Race ID</th>
                                <th width="120">创建者</th>
                                <th width="150">创建时间</th>
                                <th width="120">状态</th>
                                <th width="200">参赛人员</th>
                                <th width="200">题目列表</th>
                                <th width="100">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fastcodings as $fc): 
                                // 安全处理数组数据
                                $joiners_str = $fc['joiners'] ?? '';
                                $problems_str = $fc['problems'] ?? '';
                                $finished_users_str = $fc['finished_users'] ?? '';
                                
                                $joiners_array = !empty($joiners_str) ? explode(",", $joiners_str) : [];
                                $problems_array = !empty($problems_str) ? explode(",", $problems_str) : [];
                                $finished_users_array = !empty($finished_users_str) ? explode(",", $finished_users_str) : [];
                                
                                // 过滤空值
                                $joiners_array = array_filter($joiners_array, function($value) {
                                    return !empty(trim($value));
                                });
                                $problems_array = array_filter($problems_array, function($value) {
                                    return !empty(trim($value));
                                });
                                $finished_users_array = array_filter($finished_users_array, function($value) {
                                    return !empty(trim($value));
                                });
                                
                                // 确定比赛状态
                                if ($fc['Started'] == 1) {
                                    $status = "进行中";
                                    $status_class = "status-active";
                                } else if (count($finished_users_array) > 0) {
                                    $status = "已结束";
                                    $status_class = "status-finished";
                                } else {
                                    $status = "等待中";
                                    $status_class = "status-waiting";
                                }
                                
                                // 获取当前用户ID
                                $current_user_id = $_SESSION[$OJ_NAME.'_'.'user_id'] ?? '';
                            ?>
                                <tr>
                                    <td>
                                        <span class="race-id"><?php echo htmlspecialchars($fc['join_id'] ?? '未知'); ?></span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(getCreatorNick($fc['joiners'] ?? '')); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($fc['create_times'] ?? '未知时间'); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-list">
                                            <?php if (!empty($joiners_array)): ?>
                                                <?php foreach ($joiners_array as $joiner): ?>
                                                    <span class="user-badge" title="<?php echo htmlspecialchars($joiner); ?>">
                                                        <?php echo htmlspecialchars(getUserNick($joiner)); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">无参赛者</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="problem-list">
                                            <?php if (!empty($problems_array)): ?>
                                                <?php foreach ($problems_array as $problem): ?>
                                                    <a href="/problem.php?id=<?php echo $problem; ?>" class="problem-badge" target="_blank">
                                                        #<?php echo $problem; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">无题目</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="./gaming/game.php?race_id=<?php echo $fc['join_id']; ?>" class="btn btn-primary btn-sm" title="查看比赛">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (($fc['Started'] ?? 0) == 0 && !empty($current_user_id) && in_array($current_user_id, $joiners_array)): ?>
                                                <a href="./gaming/game.php?race_id=<?php echo $fc['join_id']; ?>&act=exit" class="btn btn-danger btn-sm" title="退出比赛">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- 分页导航 -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <nav>
                <ul class="pagination">
                    <!-- 上一页 -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?p=<?php echo $page - 1; ?>">
                                <i class="fas fa-chevron-left"></i> 上一页
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i> 上一页
                            </span>
                        </li>
                    <?php endif; ?>

                    <!-- 页码 -->
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++): 
                        if ($i == $page): ?>
                            <li class="page-item active">
                                <span class="page-link"><?php echo $i; ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endif;
                    endfor; ?>

                    <!-- 下一页 -->
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?p=<?php echo $page + 1; ?>">
                                下一页 <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                下一页 <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <!-- 底部按钮 -->
        <div class="text-center mt-4 mb-5">
            <a href="./index.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> 返回首页
            </a>
            <a href="./gaming/join.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> 创建新比赛
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
