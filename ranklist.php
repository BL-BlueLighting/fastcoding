<?php
require_once "./config.php";
?>
<!doctype html>
<html data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($OJ_NAME)?> - FastCoding - 排行榜</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; }
        main { margin-top: 27px; margin-left: 27px; margin-right: 27px; }
        table { margin-top: 20px; }
        h1, h5 { text-align: center; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", "Courier New", monospace; }
    </style>
</head>
<body>
    <?php include "./nav.php"; ?>

    <main>
        <center>
            <h1>🏆 FastCoding 排行榜</h1>
            <h5>- The race of coding -</h5>
        </center>

        <div class="container mt-4">
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
            $ridx = "";
            foreach ($rows as $r) {
                if ($idx === 1) {
                    $ridx = "<font style=\"color: yellow\">NUMBER 1</font>";
                }
                else if ($idx === 2) {
                    $ridx = "<font style=\"color: gray\">NUMBER 2</font>";
                }
                else if ($idx === 3) {
                    $ridx = "<font style=\"color: brown\">NUMBER 3</font>";
                }

                $rankList[] = [
                    'index' => $ridx,
                    'user_id' => (string)$r['user_id'],
                    'nick' => (string)$r['nick'],
                    'joined_fastcodings' => (int)$r['joined_fastcodings'],
                    'cleared_fastcodings' => (int)$r['cleared_fastcodings'],
                ];
                $idx++;
                if ($DEBUGGING) echo "<script>console.log($idx); console.log(\"CHECKED\")</script>";
            }
            ?>

            <table class="table table-dark table-striped table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">用户 ID</th>
                        <th scope="col">用户昵称</th>
                        <th scope="col">参加过的 FastCoding 数</th>
                        <th scope="col">AC 过的 FastCoding 数</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rankList) === 0): ?>
                        <tr><td colspan="5">暂无数据</td></tr>
                    <?php else: ?>
                        <?php foreach ($rankList as $row): ?>
                            <tr>
                                <td class="mono"><?php echo $row['index']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['nick'] ?: '(未命名)'); ?></td>
                                <td><?php echo (int)$row['joined_fastcodings']; ?></td>
                                <td><?php echo (int)$row['cleared_fastcodings']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!--
            <pre><?php // echo json_encode($rankList, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); ?></pre>
            -->
        </div>

        <div class="text-center mt-4 mb-5">
            <a href="./index.php" class="btn btn-outline-info">返回首页</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
