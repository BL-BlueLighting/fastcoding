<?php
include './config.php';

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
<html data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $OJ_NAME?> - FastCoding - 提交代码 (PID: <?php echo $problem_id; ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* 必须为 Monaco Editor 容器指定一个明确的高度 */
        #editor {
            width: 100%;
            height: 600px;
            border: 1px solid #495057; /* 匹配 Bootstrap 暗色主题的边框 */
        }
    </style>
</head>
<body>
    <?php include "./nav.php"; ?>

    <main style="margin-top: 27px; margin-left: 27px; margin-right: 27px;">

        <?php if ($DEBUGGING) :?>
            <script>
                console.log("startTimeO: <?php echo $startTimeO; ?>");
                console.log("nowTimeO: <?php echo $nowO?>");
                console.log("calcValue: <?php echo ($nowO - $startTimeO) / 60; ?>, <?php echo ($nowO - $startTimeO) / 60 / 60; ?>");
            </script>
        <?php endif ?>

        <div class="row">
            <div class="col-md-8">
                <h2>提交代码 - 题目 #<?php echo $problem_id; ?></h2>
                <p>当前用户: <?php echo htmlspecialchars($un); ?> | FASTCODING - HARD MODE | 请在下方选择语言，然后输入代码进行提交。</p>
                <p><a href="/problem.php?id=<?php echo $problem_id;?>">查看题目</a></p>

                <div class="submit">
                    
                    <div class="mb-3">
                        <label for="languageSelector" class="form-label">选择语言:</label>
                        <select name="language" id="languageSelector" class="form-select" style="max-width: 250px;">
                            <?php 
                            foreach ($language_options as $key => $name) {
                                $selected = ($key === 'cpp') ? 'selected' : '';
                                echo "<option value=\"{$key}\" data-hustoj-id=\"{$language_map_to_id[$key]}\" {$selected}>{$name}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div id="editor"></div>

                    <textarea name="code" id="codeText" style="display: none;"></textarea>

                    <div class="mt-3 mb-3">
                        <label for="fileUpload" class="form-label">或者，上传代码文件：</label>
                        <input type="file" name="codefile" id="fileUpload" class="form-control">
                    </div>

                    <button type="button" class="btn btn-primary mt-2" id="codeSubmit">提交</button>
                    <div id="submitMessage" class="mt-2 text-info"></div>
                </div>
            </div>

            <div class="col-md-4">
                
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-people-fill"></i> 用户列表
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 20%;">头像</th>
                                    <th scope="col" style="width: 50%;">名称</th>
                                    <th scope="col" style="width: 30%;">是否做完</th>
                                </tr>
                            </thead>
                            <tbody id="user-list-tbody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">正在加载用户数据...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-check-all"></i> 提交状态
                    </div>
                    <div class="card-body">
                        <ul class="list-group" id="ac-status-list">
                            <li class="list-group-item text-muted">等待提交...</li>
                        </ul>
                    </div>
                </div>

                <br/>
                
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-card-list"></i> 操作
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <a href="./game.php?act=exit&race_id=<?php echo $RACE_ID?>" class="list-group-item list-group-item-action list-group-item-danger">退出比赛</a>
                        </ul>
                    </div>
                </div>
            </div> 
        </div> 

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@latest/min/vs/loader.js"></script>
    
    

    <script>


        // ====================================================================
        // PHP 变量注入到 JavaScript
        // ====================================================================
        const API_ENDPOINT = 'judged_api.php'; // 你的提交 API 文件
        const RACE_API_ENDPOINT = 'api.php'; // 你的竞赛 API 文件
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
            const playerMoreNeed = document.getElementById("playerMoreNeed");

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
                    <tr class="${user.isCurrentUser ? 'table-primary' : ''}">
                        <td><img src="${user.avatar}" alt="${user.userId}" style="width: 30px; height: 30px; border-radius: 50%;"></td>
                        <td>
                            <a href="/userinfo.php?user=${user.userId}" class="${user.isCurrentUser ? 'fw-bold' : ''}">
                                ${user.userId} ${user.isCurrentUser ? '(你)' : ''}
                            </a>
                        </td>
                        <td>${user.isOver 
                            ? '<span class="badge text-bg-success">✅ 完成</span>'
                            : '<span class="badge text-bg-warning">🚧 进行中</span>'
                        }</td>
                    </tr>
                `).join('');

                // 5. 插入到 tbody
                userListBody.innerHTML = userRows;

            } catch (error) {
                console.error('Error rendering user list:', error);
                userListBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">加载用户列表失败</td></tr>';
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
                document.getElementById('submitMessage').innerHTML = `<span class="text-danger">API请求失败: ${error.message}</span>`;
                return { status: 'error', message: error.message };
            }
        }

        document.getElementById('codeSubmit').addEventListener('click', async () => {
            const submitBtn = document.getElementById('codeSubmit');
            const statusList = document.getElementById('ac-status-list');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '正在提交...';
            document.getElementById('submitMessage').innerHTML = '<span class="text-info">代码正在发送到评测系统...</span>';
            statusList.innerHTML = '<li class="list-group-item list-group-item-info">提交中，等待 Solution ID...</li>';

            const code = editor.getValue();
            const langSelect = document.getElementById('languageSelector');
            const langHustojId = langSelect.options[langSelect.selectedIndex].getAttribute('data-hustoj-id');
            
            if (code.trim().length === 0) {
                 document.getElementById('submitMessage').innerHTML = '<span class="text-warning">代码不能为空。</span>';
                 submitBtn.disabled = false;
                 submitBtn.innerHTML = '提交';
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
                document.getElementById('submitMessage').innerHTML = `<span class="text-success">提交成功! Solution ID: ${solutionId}</span>`;
                statusList.innerHTML = `<li class="list-group-item list-group-item-warning" id="status-${solutionId}">Solution ID ${solutionId}: 待评测 (Pending) ...</li>`;
                
                pollJudgeResult(solutionId);
                
            } else {
                document.getElementById('submitMessage').innerHTML = `<span class="text-danger">提交失败: ${submitResult.message}</span>`;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '提交';
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
                    if (judgeResult.result_name === '正确') {
                        statusClass = 'list-group-item list-group-item-success';
                        // 提交成功后，重新加载用户列表以更新 AC 状态
                        renderUserList(); 
                    } else if (judgeResult.is_finished) {
                        statusClass = 'list-group-item list-group-item-danger';
                    }

                    statusElement.className = `list-group-item ${statusClass}`;
                    statusElement.innerHTML = `
                        Solution ID ${solutionId}: ${resultName} <br>
                        时间: ${judgeResult.time} ms, 内存: ${judgeResult.memory} KB
                        ${judgeResult.pass_rate ? `, 通过率: ${judgeResult.pass_rate}` : ''}
                    `;

                    if (resultName == "正确") {
                        statusElement.className = `list-group-item list-group-item-success`;
                        statusElement.innerHTML = `Solution ID ${solutionId}: 通过！ACCEPTED!`;

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
                        submitBtn.innerHTML = '提交';
                        document.getElementById('submitMessage').innerHTML = '<span class="text-success">评测完成! 可重新提交。</span>';
                    }
                } else {
                    statusElement.className = 'list-group-item list-group-item-info';
                    statusElement.innerHTML = `Solution ID ${solutionId}: 还未判题结束，正在等待。`;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '提交';
                    document.getElementById('submitMessage').innerHTML = '<span class="text-danger">等待判题</span>';
                }
            }, POLL_INTERVAL_MS);
        }
        
    </script>

    <script src="./src/anti_cheat.js?ver=0.1"></script>
</body>
</html>