<?php
require_once "./config.php";
?>

<!doctype html>
<html data-bs-theme="dark">
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 玩法说明</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include "./nav.php"; ?>
        <main style="margin-top: 27px; margin-left: 27px;">
            <center>
                <img src="./src/img.png" style="width: 640px; height: 360px;">
            </center>
            <div class="left">
                <h1>FastCoding</h1>
                <h3>游戏玩法说明 / Game rules</h3>
                <p>欢迎！这里是 BL.BlueLighting。</p>
                <p>如果你没有玩过 <a href="https://www.codingame.com/multiplayer/clashofcode">Clash of Code</a>，那么这是对于你的玩法说明。如果你玩过，最好也看看，有些差别。</p>
                <h4>如何游玩</h4>
                <p>点击主页的 <button type="button" class="btn btn-outline-info" disabled>开始游戏</button> 按钮，或者点击菜单栏的加入，跳转到另一个页面。</p>
                <p>如果您有您的好友分享的 RaceID，请使用 Race ID 加入游戏。</p>
                <p>如果您没有 Race ID，请点击随机加入一场游戏。</p>
                <h4>游戏规则</h4>
                <p>1. 不允许使用生成式 AI，页面会监测你是否切出页面，除了由游戏页面发起的请求外，不允许切出比赛页面。</p>
                <p>2. 您的比赛代码将在结束后<b>被公开展示</b>，也就意味着即使您使用了 AI 模型但逃避了监管，但由社区审查，可以取消您的比赛成绩，严重可以禁止排行榜排名。</p>
                <p>3. 本游戏是谁最先完成代码的编写，并获得 <?php echo $AGREE_ACC;?> 及以上的 ACC 视为通过，若您做完了所有题目，且符合条件，最先获胜。</p>
                <p>4. 本游戏会随机从题库中抽取 <?php echo $NEED_TITLES?> 道题目，并打乱，<b>并且不会同步您之前写过的任何代码</b>。</p>
                <p>5. 本游戏的最长时间为 <?php echo $MAX_TIME?> 分钟，请在时间内完成所有题目。（当然每个人有不同的实力，你可以做不完。）</p>
                <p>6. 不允许使用 <span class="badge bg-secondary">Ctrl + C</span> <span class="badge bg-secondary">Ctrl + V</span> 等复制快捷键，任何试图复制粘贴的内容均会被记录。<p>
                <h3>最后，祝您玩的愉快！</h3>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>