<?php

/**
 *
 * 用于把 FastCoding 和 hustoj 的 judged 对接
 */

// 引入 hustoj 的配置和常量文件
include '../system/config.php';
include '../include/const.inc.php';

// Array($MSG_Pending,$MSG_Pending_Rejudging,$MSG_Compiling,$MSG_Running_Judging,$MSG_Accepted,$MSG_Presentation_Error,$MSG_Wrong_Answer,$MSG_Time_Limit_Exceed,$MSG_Memory_Limit_Exceed,$MSG_Output_Limit_Exceed,$MSG_Runtime_Error,$MSG_Compile_Error,$MSG_Compile_OK,$MSG_TEST_RUN,$MSG_MANUAL_CONFIRMATION,$MSG_SUBMITTING,$MSG_REMOTE_PENDING,$MSG_REMOTE_JUDGING);
$jr = $judge_result;

header('Content-Type: application/json');

// --- 检查是否为查询判题结果请求 ---
if (isset($_POST["getResult"])) {
    $solution_ids_input = $_POST["getResult"];
    $results = [];

    $sql = 'SELECT solution_id, result, time, memory, pass_rate FROM solution WHERE solution_id = ?';
    $solution_rows = pdo_query($sql, $solution_ids_input);
    foreach ($solution_rows as $row) {
        $solution_id = $row['solution_id'];
        $result_code = $row['result'];

        $results[$solution_id] = [
            'solution_id' => $solution_id,
            'result_code' => $result_code,
            // 使用 $jr 数组将数字结果码转换为可读的字符串
            'result_name' => $jr[$result_code] ?? 'Unknown Result',
            'time' => $row['time'],
            'memory' => $row['memory'],
            'pass_rate' => $row['pass_rate'] ?? null, // 假设存在 pass_rate 字段
            'is_finished' => ($result_code >= 4) // 大于等于 4 则判题完毕，4 是 AC 线，4 以下是判题运行中
        ];
    }

    echo json_encode([
        'status' => 'success',
        'results' => $results
    ]);
    exit();
}


// --- 提交代码的逻辑 (当 getResult 不存在时执行) ---
$code = $_POST ["code"] ?? null;

if (is_null($code)) {
    die(json_encode(['status' => 'error', 'message' => 'Missing code or getResult parameter.']));
}

$problem_id = $_POST["problem_id"] ?? null;
$language = $_POST["language"] ?? null;
$user_id = $_POST["user_id"] ?? "FastCodingUser";
$ip = $_POST["ip"];

if (empty($code) || !is_numeric($problem_id) || !is_numeric($language)) {
    die(json_encode(['status' => 'error', 'message' => 'Missing or invalid required submission parameters.']));
}

// 确保代码长度符合限制
$code_len = strlen($code);
if ($code_len > 65536) {
    die(json_encode(['status' => 'error', 'message' => 'Code is too long.']));
}

// 使用 $sqlesc 进行 SQL 转义 (假设在 my_func.inc.php 中定义)
$sql_code = $code;
$problem_id = intval($problem_id);
$language = intval($language);

// --- 插入 solution 表 (提交记录) ---
$sql_solution = "INSERT INTO solution
                    (problem_id, user_id, time, memory, in_date, result, language, ip, code_length)
                VALUES
                    (?, ?, 0, 0, NOW(), 14, ?, ?, ?)";

// 执行插入操作
pdo_query($sql_solution, $problem_id, $user_id, $language, $ip, $code_len);

// 获取最后一个插入的 solution_id，用 sql
$query = "SELECT solution_id FROM solution 
          WHERE user_id = ? 
          AND in_date = NOW()
          ORDER BY in_date DESC 
          LIMIT 1";
$insert_id = pdo_query($query, $user_id) [0] ["solution_id"];


// --- 插入 source_code 表 (代码内容) ---
$sql_source = "INSERT INTO source_code (solution_id, source) VALUES ($insert_id, ?)";
pdo_query($sql_source, $sql_code);

$sql_source = "INSERT INTO source_code_user (solution_id, source) VALUES ($insert_id, ?)";
pdo_query($sql_source, $sql_code);

$sql = "UPDATE solution SET result=0 WHERE solution_id=?";
pdo_query($sql, $insert_id);

// --- 返回结果给 FastCoding ---
$response = [
    'status' => 'success',
    'solution_id' => $insert_id,
    'message' => "Submission accepted. Solution ID: $insert_id"
];

// 发送 udp 字符串给 judged，这段代码是 c+c, c+v 的
trigger_judge(intval($insert_id));

echo json_encode($response);

?>