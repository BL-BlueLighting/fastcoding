<?php
require_once "./config.php";

/*
fastcoding table:
ID: self-add value
problems: problem_id list, use ',' to split.
join_id: system auto generate, share it to another user to join a game.
joiners: players list, use ',' to split (no space)
Started: does this game started? 
started_times: if it started, this storage times game started.

*/

$err = false;

// 检测用户是否已经加入一场比赛
$all_races = pdo_query("select * from fastcoding");
foreach ($all_races as $race) {
    $all_users = explode(",", $race ["joiners"]);
    if (in_array($un, $all_users)) {
        header("Location: ./game.php?race_id=" . $race ["join_id"]);
        exit();
    }
}

if (isset($_GET ["race_id"])) {
    $raceid = $_GET ["race_id"];
    $race = [];

    // random
    if ($raceid == "random") {
        $races = pdo_query("select * FROM fastcoding WHERE `Started` = 0"); // 屏蔽已经开始的比赛

        if (empty($races)) {
            $err = true;
            $error_content = "当前没有可加入的比赛。";
        } else {
            $luck_index = array_rand($races);
            $race = $races [$luck_index];
        }
    }
    else if ($raceid != "new") {
        $race = pdo_query("select * FROM fastcoding WHERE `join_id` = ?", $raceid) [0];
    }
    else {
        header("Location: ./game.php?race_id=new");
    }

    if ($race ["Started"] || $err) {
        if (!$err) { // 防止两次判断
            $err = true;
            $error_content = "该比赛已经开始。";
        }
    }

    else {
        if ($race ["joiners"] == "") $race ["joiners"] . $un;
        else $race ["joiners"] . "," . $un;
        $Sql = "update SET `joiners` = ? WHERE `join_id` = ? ";
        pdo_query($Sql, $race ["joiners"], $raceid);

        header("Location: ./game.php?race_id=". $raceid);
    }
}

if (isset($_GET["err"])) {
    if ($_GET ["err"] == "nraceid") {
        // 不是正确的 raceid
        $err = true;
        $error_content = "Race ID 不正确。";
    }
    else if ($_GET ["err"] == "njoiner") {
        $err = true; // 没有加入
        $error_content = "您没有加入该场游戏的权限。";
    }
}

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
            <div class="left" style="margin-top: 27px;">
                <h1>FastCoding</h1>
                <h4>加入游戏 / Join game</h4>
                <br/>
                <?php if ($err) { ?>
                <div class="alert alert-danger" role="alert" style="width: 250px;">
                    <?php echo $error_content; ?>
                </div>
                <?php } ?>
                <div class="row g-3">
                    <div class="col-sm-5">
                        <div class="card">
                            <h5 class="card-header">通过 Race ID 加入游戏</h5>
                            <div class="card-body">
                                <form action="./join.php" method="GET">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="race_id_label">Race ID:</span>
                                        <input type="text" class="form-control" placeholder="在此处填写 Race ID | Input your Race ID here." aria-label="RaceID" aria-describedby="race_id_label" name="race_id">
                                    </div>
                                    <button type="submit" class="btn btn-outline-info">加入游戏</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <br/>

                    <div class="col-sm">
                        <div class="card" style="height: 167px;">
                            <h5 class="card-header">随机加入游戏</h5>
                            <div class="card-body">
                                <form action="./join.php" method="GET">
                                    <input type="text" value="random" name="race_id" hidden>
                                    <button type="submit" class="btn btn-outline-info">加入游戏</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm">
                        <div class="card" style="height: 167px; margin-right: 27px;">
                            <h5 class="card-header">新建一场游戏</h5>
                            <div class="card-body">
                                <form action="./join.php" method="GET">
                                    <input type="text" value="new" name="race_id" hidden>
                                    <button type="submit" class="btn btn-outline-info">新建游戏</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>