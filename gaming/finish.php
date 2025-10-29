<?php
require_once "../system/config.php";

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
        <style>
            a {
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <?php include "../nav.php"; ?>
        <main style="margin-top: 27px; margin-left: 27px;">
            <center>
                <img src="../src/img.png" style="width: 640px; height: 360px;">
                <div class="content" style="width: 1000px;">
                    <h1>FastCoding</h1>
                    <h3>-&nbsp;The&nbsp;&nbsp;race&nbsp;&nbsp;of&nbsp;&nbsp;Coding!&nbsp;-</h3>
                    <h5>游戏结束。</h5>
                    <h5>您的 RaceID: <?php echo $race_id; ?></h5>
                    <div class="row">
                        <?php foreach ($joiners as $joiner):?>
                            <div class="col-md-3 mb-4">
                                <div class="card" style="width: 200px;">
                                    <img src="<?php echo getAvatar($joiner); ?>" class="card-img-top" alt="用户头像" style="width: 200px; height: 200px;">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="/userinfo.php?user=<?php echo $joiner; ?>"><?php echo $joiner;?><br/><?php echo getEnd($joiner); ?></a>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
            </center>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            async function sendPostRequest(api, data) {
                const formData = new URLSearchParams();
                for (const key in data) {
                    formData.append(key, data[key]);
                }

                try {
                    const response = await fetch(api, {
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


            let intervalEntity = setInterval(function() {
                let race_id = "<?php echo $race_id ?>";
                let result = sendPostRequest(`./api/api.php?act=callDeleteFastCoding&race_id=${race_id}`);

                if (result.status == 1) {
                    clearInterval(intervalEntity);
                }
            },1000)
        </script>
    </body>
</html>