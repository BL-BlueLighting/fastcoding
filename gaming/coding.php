<?php
include '../system/config.php';

/*
FastCoding 最核心部分 - coding.php
写代码的地方
*/

$RACE_ID = $_GET['race_id'] ?? 'default_race_1'; 
$problem_id = pdo_query("select problems from fastcoding where `join_id` = ?", $RACE_ID) [0] ["problems"];
$race = ["join_id" => $RACE_ID];

$rrace = pdo_query("select * from fastcoding where `join_id` = ?", $RACE_ID) [0];
$users = explode(",", $rrace ["joiners"]);
if (!in_array($un, $users) && !$DEBUGGING) {
    header("Location: ./join.php");
} // 如果没有参赛则直接跳转回加入页

$startTime = $rrace ["create_times"];
$startTimeO = strtotime($startTime);
$nowO = time();

if ( ($nowO - $startTimeO) / 60 < $WAIT_TIME && !$DEBUGGING) { // nowO 和 startTimeO 均为 timestamp，为秒数，除以 60 再除以 60 就是小时，而除以 60 为分钟。
    header("Location: ./game.php?race_id". $RACE_ID);
} // 如果时间还没到就跳转回游戏前页

if ($RACE_ID == "default_race_1" && !$DEBUGGING) header("Location: ./join.php");

$language_map_to_id = [
    'c' => 0,      
    'cpp' => 1,    
    'java' => 4,   
    'python' => 7  
];
$language_options = [
    'c' => 'C',
    'cpp' => 'C++',
    'java' => 'Java',
    'python' => 'Python'
];

if (!isset($un)) {
    $un = 'guest_user';
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $OJ_NAME?> - FastCoding - 提交代码 (PID: <?php echo $problem_id; ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../fastcoding.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* 必须为 Monaco Editor 容器指定一个明确的高度 */
        #editor {
            width: 100%;
            height: 600px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .coding-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .problem-info {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .user-status-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .user-avatar-small {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-accepted {
            background: var(--success-color);
            color: white;
        }
        
        .status-pending {
            background: var(--warning-color);
            color: #333;
        }
        
        .submission-status {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .action-card {
            text-align: center;
        }
        
        .action-card a {
            text-decoration: none;
            display: block;
            padding: 15px;
            transition: all 0.3s ease;
        }
        
        .action-card a:hover {
            background: rgba(220, 53, 69, 0.1);
        }
    </style>
</head>
<body>
    <?php include "../nav.php"; ?>
    
    <div class="coding-container">
        <!-- 头部信息 -->
        <div class="problem-info fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-code"></i> 提交代码 - 题目 #<?php echo $problem_id; ?></h2>
                    <p class="mb-0">当前用户: <?php echo htmlspecialchars($un); ?> | FASTCODING - HARD MODE</p>
                </div>
                <div class="col-md-4 text-end">
<<<<<<< HEAD
                    <button onclick="javascript:window.open('/problem.php?id=<?php echo $problem_id;?>', '_blank', 'popup=true')" class="btn btn-light">
                        <i class="fas fa-eye"></i> 查看题目
                    </button>
                    <div id="countdown" class="mt-2 text-muted small"></div>
=======
                    <a href="/problem.php?id=<?php echo $problem_id;?>" class="btn btn-light">
                        <i class="fas fa-eye"></i> 查看题目
                    </a>
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
                </div>
            </div>
        </div>

        <div class="row">
            <!-- 左侧代码编辑区域 -->
            <div class="col-md-8">
                <div class="fastcoding-card fade-in">
                    <div class="card-header">
                        <h3 class="card-title">代码编辑器</h3>
                        <div class="language-selector">
                            <label for="languageSelector" class="form-label mb-0">选择语言:</label>
                            <select name="language" id="languageSelector" class="form-select">
                                <?php 
                                foreach ($language_options as $key => $name) {
                                    $selected = ($key === 'cpp') ? 'selected' : '';
                                    echo "<option value=\"{$key}\" data-hustoj-id=\"{$language_map_to_id[$key]}\" {$selected}>{$name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div id="editor"></div>
                    
                    <div class="mt-3">
                        <label for="fileUpload" class="form-label">
                            <i class="fas fa-upload"></i> 或者，上传代码文件：
                        </label>
                        <input type="file" name="codefile" id="fileUpload" class="form-control">
                    </div>
                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-primary" id="codeSubmit">
                            <i class="fas fa-paper-plane"></i> 提交代码
                        </button>
                        <div id="submitMessage" class="text-info"></div>
                    </div>
                </div>
                
                <?php if ($DEBUGGING) :?>
                <div class="fastcoding-card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">调试信息</h5>
                    </div>
                    <div class="card-body">
                        <script>
                            console.log("startTimeO: <?php echo $startTimeO; ?>");
                            console.log("nowTimeO: <?php echo $nowO?>");
                            console.log("calcValue: <?php echo ($nowO - $startTimeO) / 60; ?>, <?php echo ($nowO - $startTimeO) / 60 / 60; ?>");
                        </script>
                    </div>
                </div>
                <?php endif ?>
            </div>

            <!-- 右侧信息区域 -->
            <div class="col-md-4">
                <!-- 用户列表卡片 -->
                <div class="user-status-card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                        <h5 class="mb-0"><i class="fas fa-users"></i> 参赛用户</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 20%;">头像</th>
                                        <th scope="col" style="width: 50%;">用户</th>
                                        <th scope="col" style="width: 30%;">状态</th>
                                    </tr>
                                </thead>
                                <tbody id="user-list-tbody">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-spinner fa-spin"></i> 正在加载用户数据...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 提交状态卡片 -->
                <div class="user-status-card fade-in">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i> 提交状态</h5>
                    </div>
                    <div class="card-body">
                        <div class="submission-status">
                            <ul class="list-group" id="ac-status-list">
                                <li class="list-group-item text-muted text-center py-3">
                                    <i class="fas fa-clock"></i> 等待提交...
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 操作卡片 -->
                <div class="user-status-card fade-in action-card">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--danger-color), #c82333); color: white;">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> 操作</h5>
                    </div>
                    <div class="card-body p-0">
                        <a href="./game.php?act=exit&race_id=<?php echo $RACE_ID?>" class="text-danger">
                            <i class="fas fa-sign-out-alt"></i> 退出比赛
                        </a>
                    </div>
                </div>
            </div> 
        </div> 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@latest/min/vs/loader.js"></script>
    
    <script>
        // ====================================================================
        // PHP 变量注入到 JavaScript
        // ====================================================================
        const API_ENDPOINT = './judged_api.php'; // 你的提交 API 文件
        const RACE_API_ENDPOINT = './api.php'; // 你的竞赛 API 文件
        const CURRENT_PROBLEM_ID = <?php echo $problem_id; ?>;
        const CURRENT_USER_ID = '<?php echo $un; ?>'; 
        const POLL_INTERVAL_MS = 2500; // 提交结果轮询间隔
        const RACE_ID = "<?php echo $race ["join_id"]; ?>"; // 竞赛 ID
        const DEBUGGING = <?php if ($DEBUGGING) { echo "true"; } else { echo "false"; }?>
        
        let currentLang = 'cpp'; 
        let userCount = 0; 

        // ====================================================================
        // 竞赛用户列表逻辑
        // ====================================================================
    
        async function getUserAvatar(userId) {
            const response = await fetch(`${RACE_API_ENDPOINT}?act=getAvatar&race_id=${RACE_ID}&user_id=${userId}`);
            const data = await response.json();
            return data.avatar ? data.avatar.replaceAll("\\", "") : 'https://ui-avatars.com/api/?name=' + userId + '&background=random';
        }

        async function doesHeOver(raceId, userId) {
            try {
                const response = await fetch(`${RACE_API_ENDPOINT}?act=doesHeOver&race_id=${raceId}&user_id=${userId}`);
                const data = await response.json();
                return data.is_over === true;
            } catch (error) {
                console.error(`Error querying completion status for ${userId}:`, error);
                return false; 
            }
        }

        async function callAC() {
            try {
                const response = await fetch(`${RACE_API_ENDPOINT}?act=callAccepted&user_id=${CURRENT_USER_ID}&race_id=${RACE_ID}`);
                const data = await response.json();
                return data.status == 1;
            } catch (error) {
                console.error(`Error to call accepted.`, error);
                return false;
            }
        }

        async function renderUserList() {
            const userListBody = document.getElementById("user-list-tbody");

            try {
                // 1. 获取用户列表
                const response = await fetch(`${RACE_API_ENDPOINT}?act=user_list&race_id=${RACE_ID}`);
                const data = await response.json();
                
                const joiners = data.joiners || ""; 
                let userList = joiners.split(",").filter(id => id.trim() !== "");
                
                userCount = userList.length;
                
                // 2. 创建 Promise 数组，同时获取头像和完成状态
                const userPromises = userList.map(async (userId) => {
                    const [avatar, isOver] = await Promise.all([
                        getUserAvatar(userId),
                        doesHeOver(RACE_ID, userId)
                    ]);
                    
                    return {
                        userId: userId,
                        avatar: avatar,
                        // 标记当前用户，以便在列表中突出显示
                        isCurrentUser: (userId === CURRENT_USER_ID),
                        isOver: isOver
                    };
                });

                // 3. 等待所有数据获取完成
                const usersData = await Promise.all(userPromises);
                
                // 4. 生成 HTML
                const userRows = usersData.map(user => `
                    <tr class="${user.isCurrentUser ? 'table-active' : ''}">
                        <td>
                            <img src="${user.avatar}" alt="${user.userId}" 
                                 class="user-avatar-small" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=${user.userId}&background=random'">
                        </td>
                        <td>
                            <a href="/userinfo.php?user=${user.userId}" class="${user.isCurrentUser ? 'fw-bold text-primary' : 'text-dark'}">
                                ${user.userId} ${user.isCurrentUser ? '<small>(你)</small>' : ''}
                            </a>
                        </td>
                        <td>
                            ${user.isOver 
                                ? '<span class="status-badge status-accepted"><i class="fas fa-check"></i> 已完成</span>'
                                : '<span class="status-badge status-pending"><i class="fas fa-spinner"></i> 进行中</span>'
                            }
                        </td>
                    </tr>
                `).join('');

                // 5. 插入到 tbody
                userListBody.innerHTML = userRows;

            } catch (error) {
                console.error('Error rendering user list:', error);
                userListBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle"></i> 加载用户列表失败</td></tr>';
            }
        }

        // 定时更新用户列表 (使用您提供的间隔 5000ms)
        setInterval(renderUserList, 5000); 

        // 初始加载
        renderUserList();
        
        // ====================================================================
        // 核心提交和轮询逻辑 (保持不变)
        // ====================================================================
        
        async function sendPostRequest(data) {
            const formData = new URLSearchParams();
            for (const key in data) {
                formData.append(key, data[key]);
            }

            try {
                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP Error! Status: ${response.status}`);
                }

                return await response.json();

            } catch (error) {
                console.error('API Request Failed:', error);
                document.getElementById('submitMessage').innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle"></i> API请求失败: ${error.message}</span>`;
                return { status: 'error', message: error.message };
            }
        }

        document.getElementById('codeSubmit').addEventListener('click', async () => {
            const submitBtn = document.getElementById('codeSubmit');
            const statusList = document.getElementById('ac-status-list');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 正在提交...';
            document.getElementById('submitMessage').innerHTML = '<span class="text-info"><i class="fas fa-sync fa-spin"></i> 代码正在发送到评测系统...</span>';
            statusList.innerHTML = '<li class="list-group-item list-group-item-info"><i class="fas fa-clock"></i> 提交中，等待 Solution ID...</li>';

            const code = editor.getValue();
            const langSelect = document.getElementById('languageSelector');
            const langHustojId = langSelect.options[langSelect.selectedIndex].getAttribute('data-hustoj-id');
            
            if (code.trim().length === 0) {
                 document.getElementById('submitMessage').innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> 代码不能为空。</span>';
                 submitBtn.disabled = false;
                 submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 提交代码';
                 return;
            }

            const submitData = {
                code: code,
                problem_id: CURRENT_PROBLEM_ID,
                language: langHustojId,
                user_id: CURRENT_USER_ID, 
                ip: '127.0.0.1' 
            };

            const submitResult = await sendPostRequest(submitData);

            if (submitResult.status === 'success' && submitResult.solution_id) {
                const solutionId = submitResult.solution_id;
                document.getElementById('submitMessage').innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> 提交成功! Solution ID: ${solutionId}</span>`;
                statusList.innerHTML = `<li class="list-group-item list-group-item-warning" id="status-${solutionId}"><i class="fas fa-hourglass-half"></i> Solution ID ${solutionId}: 待评测 (Pending) ...</li>`;
                
                pollJudgeResult(solutionId);
                
            } else {
                document.getElementById('submitMessage').innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> 提交失败: ${submitResult.message}</span>`;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 提交代码';
            }
        });

        // 轮询结果函数 (pollJudgeResult)
        async function pollJudgeResult(solutionId) {
            const statusElement = document.getElementById(`status-${solutionId}`);
            const submitBtn = document.getElementById('codeSubmit');

            const poll = setInterval(async () => {
                const queryData = {
                    getResult: solutionId.toString()
                };

                const result = await sendPostRequest(queryData);

                if (result.status === 'success' && result.results && result.results[solutionId]) {
                    const judgeResult = result.results[solutionId];
                    const resultName = judgeResult.result_name;

                    let statusClass = 'list-group-item list-group-item-warning'; 
                    let statusIcon = '<i class="fas fa-hourglass-half"></i>';
                    
                    if (judgeResult.result_name === '正确') {
                        statusClass = 'list-group-item list-group-item-success';
                        statusIcon = '<i class="fas fa-check-circle"></i>';
                        // 提交成功后，重新加载用户列表以更新 AC 状态
                        renderUserList(); 
                    } else if (judgeResult.is_finished) {
                        statusClass = 'list-group-item list-group-item-danger';
                        statusIcon = '<i class="fas fa-times-circle"></i>';
                    }

                    statusElement.className = statusClass;
                    statusElement.innerHTML = `
                        ${statusIcon} Solution ID ${solutionId}: ${resultName} <br>
                        <small>时间: ${judgeResult.time} ms, 内存: ${judgeResult.memory} KB
                        ${judgeResult.pass_rate ? `, 通过率: ${judgeResult.pass_rate}` : ''}</small>
                    `;

                    if (resultName == "正确") {
                        statusElement.className = `list-group-item list-group-item-success`;
                        statusElement.innerHTML = `<i class="fas fa-trophy"></i> Solution ID ${solutionId}: 通过！ACCEPTED!`;

                        if (callAC()) {
                            window.location.href=`./finish.php?race_id=${RACE_ID}`;
                        }
                        else {
                            alert("❌ 服务器拒绝了您的 AC 请求。请确认您没有作弊。");
                        }
                    }
                    
                    if (judgeResult.is_finished) {
                        clearInterval(poll); 
                        submitBtn.disabled = false; 
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 提交代码';
                        document.getElementById('submitMessage').innerHTML = '<span class="text-success"><i class="fas fa-flag-checkered"></i> 评测完成! 可重新提交。</span>';
                    }
                } else {
                    statusElement.className = 'list-group-item list-group-item-info';
                    statusElement.innerHTML = `<i class="fas fa-sync fa-spin"></i> Solution ID ${solutionId}: 还未判题结束，正在等待。`;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 提交代码';
                    document.getElementById('submitMessage').innerHTML = '<span class="text-danger"><i class="fas fa-clock"></i> 等待判题</span>';
                }
            }, POLL_INTERVAL_MS);
        }
<<<<<<< HEAD

        // 倒计时显示
        let countdownElement = document.getElementById("countdown");
        const WAIT_TIME = <?php echo $MAX_TIME; ?>; // 以分钟为单位
        let gamingTime = 0;

        // 异步更新倒计时：显示距离 WAIT_TIME（分钟）从 startTime 开始的剩余时间
        async function updateCountdown() {
            if (!countdownElement) return;
            try {
                const resp = await fetch(`${RACE_API_ENDPOINT}?act=startTime&race_id=${RACE_ID}`);
                if (!resp.ok) throw new Error('Network response was not ok');
                const json = await resp.json();

                // 解析服务器返回的起始时间
                const startTime = new Date(json.startTime);
                const now = new Date();

                const elapsedSeconds = Math.floor((now - startTime) / 1000);
                const waitSeconds = Number(WAIT_TIME) * 60;
                const remaining = waitSeconds - elapsedSeconds;

                if (isNaN(waitSeconds) || waitSeconds <= 0) {
                    countdownElement.innerHTML = `<span class="text-muted"><i class="fas fa-info-circle"></i> 错误：配置有误</span>`;
                    return;
                }

                if (remaining <= 0 && gamingTime >= WAIT_TIME * 60) {
                    // 倒计时结束
                    countdownElement.innerHTML = `<span class="text-success"><i class="fas fa-flag-checkered"></i> 比赛已结束</span>`;
                    // 汇报比赛结束，强制删除比赛
                    fetch(`${RACE_API_ENDPOINT}?act=callDeleteFastCoding&race_id=${RACE_ID}`);
                    // 切换 finish.php?stat=notfinish
                    window.location.href = `./finish.php?stat=notfinish&race_id=${RACE_ID}`;

                } else {
                    const m = Math.floor(remaining / 60);
                    const s = remaining % 60;
                    const em = Math.floor(elapsedSeconds / 60);
                    const es = elapsedSeconds % 60;

                    // 如果说 gamingTime < maxtime*60，那么请求 api，延长时间
                    if (gamingTime < WAIT_TIME * 60) {
                        fetch(`${RACE_API_ENDPOINT}?act=extendTime&race_id=${RACE_ID}`);
                        updateCountdown();
                        return;
                    }

                    countdownElement.innerHTML = `已用: ${em}:${String(es).padStart(2,'0')} | 剩余: ${m}:${String(s).padStart(2,'0')}`;
                }

            } catch (err) {
                console.error('updateCountdown error:', err);
                countdownElement.innerHTML = `<span class="text-muted"><i class="fas fa-exclamation-circle"></i> 无法获取时间</span>`;
            }

            gamingTime += 1; // 增加已用时间，单位为秒
        }

        // 定时更新倒计时
        setInterval(updateCountdown, 1000);
        // 初始加载
        updateCountdown();
=======
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
    </script>

    <!-- 隐藏的textarea用于表单提交 -->
    <textarea name="code" id="codeText" style="display: none;"></textarea>
    
    <!-- 反作弊脚本 -->
    <script src="../src/anti_cheat.js?ver=0.1"></script>
</body>
</html>
