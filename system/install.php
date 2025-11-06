<?php
include "../system/config.php";

if (file_exists("./install.lock")) {
    echo "为了安全起见，每次运行该脚本会生成 install.lock 文件，请删除该文件再试。";
    exit();
}

if (isset($_GET ["ok"])) {
    $sql = file_get_contents("./src/install.sql");
    pdo_query($sql);
    echo "已成功构建 fastcoding 表。";
    echo "已成功构建 fastcoding_ranking 表。";

    file_put_contents("./install.lock", "installation finished");
    exit();
}
?>

<head>
    <title>FastCoding - Installation</title>
</head>

<body>
    <h1>FastCoding - Installation</h1>
    <p>CLICK BUTTON UNDER THIS TEXT TO BEGIN INSTALLATION.</p>
    <button onclick="javascript:window.location.href='./install.php?ok=true'" style="border: 1px solid skyblue; background-color: black; color: white; width: 150px; height: 27px; ">Install</button>
</body>
