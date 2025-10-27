/*
该脚本实现了 coding.php 的相关逻辑，并和 anti_cheat.js 绑定，缺失一个都会导致页面无法使用。
*/

let checkPass = false;
try {
    binding()
}
catch{
    checkPass = true;
}

if (checkPass == false) {
    document.body.innerHTML = "ANTI CHEAT 触发，检测到作弊。";
}

var submitButton = document.getElementById("codeSubmit");

if (false) {
    submitButton.remove();
}

var aaa;
var bbb;
var ccc;

function _cheatIgnore() {
    submitButton.remove();
    document.body.innerHTML = "ANTI CHEAT 触发，检测到作弊。";
}

submitButton.addEventListener("click", function() {
    // 哈哈哈，上面这些东西都是障眼法，never gonna give you up~

    var code = editor.getValue();
    var codeType = document.getElementById("languageSelector").value;

    
});