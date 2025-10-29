<?php
require_once "./system/config.php";
?>

<!doctype html>
<html data-bs-theme="dark">
    <head>
        <title><?php echo $OJ_NAME?> - FastCoding - 首页</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include "./nav.php"; ?>
        <main style="margin-top: 27px; margin-left: 27px;">
            <center>
                <img src="./src/img.png" style="width: 640px; height: 360px;">
            </center>

            <div class="row" style="margin-top: 27px;">

                <div class="col">
                    <h1>FastCoding</h1>
                    <h3>-&nbsp;The&nbsp;&nbsp;race&nbsp;&nbsp;of&nbsp;&nbsp;Coding!&nbsp;-</h3>
                    <h5>这是什么？</h5>
                    <p>也许你是第一次来到这个陌生的界面，不过，没关系。</p>
                    <p>这，还是你熟悉的 Online Judge。</p>
                    <p>只不过，现在，我们要开始比拼速度了。</p>
                    <br/>
                    <h5>玩法是什么？</h5>
                    <p>参见 <a href="./rules.php">玩法说明</a></p>
                </div>
                
                <div class="col">
                    <h3>加入一场游戏</h3>
                    <br/>
                    <button type="button" class="btn btn-outline-info" onclick="javascript:window.location.href='./join.php';">开始游戏</button>
                </div>

            </div>

        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>