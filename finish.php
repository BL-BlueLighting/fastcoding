<?php
require_once "./config.php";

if (!isset($_GET["race_id"])) {
    header(
        "Location: ./index.php"
    );

    exit;
}

$race_id = $_GET["race_id"];

$sql = "select joiners from `fastcoding` where `join_id` = ?";
$_joiners = pdo_query($sql, $race_id) [0] ["joiners"];
$joiners = explode(",", $_joiners);

// 查 email 数据
function getAvatar ($__un) {
    $sql = "select email from `users` where `user_id` = ?";
    $email = pdo_query($sql, $__un) [0] ["email"];

    //if (isset($_GET ["debugging"])) echo $email;

    $is_qq = false;
    $qq=stripos($email,"@qq.com");
    $grav_url = "";

    if($qq>0){
        $qq=urlencode(substr($email,0,$qq));
        $grav_url="https://q1.qlogo.cn/g?b=qq&nk=$qq&s=5";
        $is_qq = true;
    }
    else {
        $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?size=100";
    }

    return $grav_url;
}

// 查询是否已经结束
function getEnd($__un)
{
    $sql = "select finished_users from fastcoding where `join_id` = ?";
    $__result = pdo_query($sql, $_GET ["race_id"]) [0] ["finished_users"];
    $result = explode(",", $__result);

    if (in_array($__un, $result)) {
        return "<font style=\"color: green;\">ACCEPTED</font>";
    }
    else {
        return "<font style=\"color: red;\">WRITING</font>";
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

            <div class="row" style="margin-top: 27px;">

                <div class="col">
                    <h1>FastCoding</h1>
                    <h3>-&nbsp;The&nbsp;&nbsp;race&nbsp;&nbsp;of&nbsp;&nbsp;Coding!&nbsp;-</h3>
                    <h5>游戏结束。</h5>
                    <h5>您的 RaceID: <?php echo $race_id; ?></h5>
                    <?php foreach ($joiners as $joiner):?>
                        <div class="col-md-3 mb-4">
                            <div class="card">
                                <img src="<?php echo getAvatar($joiner); ?>" class="card-img-top" alt="用户头像">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="/userinfo.php?user=<?php echo $joiner; ?>"><?php echo $joiner;?> - <?php echo getEnd($joiner); ?></a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    <?php endforeach?>
                </div>
                
                <div class="col">
                    <h3>继续新一场？</h3>
                    <br/>
                    <button type="button" class="btn btn-outline-info" onclick="javascript:window.location.href='./join.php';">开始游戏</button>
                </div>

            </div>

        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>