// anti_cheat.js (重构版)

// 开发模式禁用 anticheat
if (typeof DEBUGGING !== 'undefined' && DEBUGGING) throw Error("deving no anti cheat");

let editor; // will be set after monaco init

// ----------------- monaco 初始化（你原来的代码） -----------------
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@latest/min/vs' } });
require(['vs/editor/editor.main'], function() {
    // create editor and expose globally
    editor = window.editor = monaco.editor.create(document.getElementById('editor'), {
        value: "Hello, World!",
        language: 'cpp',
        theme: 'vs-dark',
        automaticLayout: true
    });

    // 语言选择器仍然可以在这里绑定（保证 editor 已存在）
    const langSelectEl = document.getElementById('languageSelector');
    if (langSelectEl) {
        langSelectEl.addEventListener('change', function() {
            const selectedLang = this.value;
            const monacoLang = languageModeMap[selectedLang] || 'plaintext';
            monaco.editor.setModelLanguage(editor.getModel(), monacoLang);
            if (editor.getValue().trim() === defaultCodeTemplates[currentLang].trim() || editor.getValue().trim() === "") {
                editor.setValue(defaultCodeTemplates[selectedLang]);
            }
            currentLang = selectedLang;
        });
    }

    // 在 editor 创建后初始化 anti-cheat
    initAntiCheat(editor);
});

// ----------------- anti-cheat 初始化函数 -----------------
function initAntiCheat(ed) {
    if (!ed) {
        console.warn('initAntiCheat: editor not ready');
        return;
    }
    // 防止多次初始化
    if (ed._antiCheatInited) return;
    ed._antiCheatInited = true;

    // 开发者工具检测（保持原逻辑）
    (function detectDev() {
        function detectDevTools() {
            const minTimeThreshold = 100;
            let startTime = new Date();
            debugger;
            let endTime = new Date();
            if (endTime - startTime > minTimeThreshold) {
                if (!window.devtoolsDetected) {
                    window.devtoolsDetected = true;
                    alert("检测到开发者工具！为保证竞赛公平，将清空页面。");
                    document.body.innerHTML = "<div style=\"margin-top: 27px; margin-left: 27px;\"><h1>检测到违规操作</h1><p>请关闭开发者工具并刷新页面。</p></div>";
                }
            }
        }
        setInterval(detectDevTools, 1500);
    })();

    // 禁用右键/快捷键的全局监听（你原来的）
    document.addEventListener('contextmenu', function(e){ e.preventDefault(); });
    document.addEventListener('keydown', function(e){
        if (e.key === 'F12' || e.keyCode === 123) e.preventDefault();
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.keyCode === 73)) e.preventDefault();
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'J' || e.keyCode === 74)) e.preventDefault();
        if ((e.ctrlKey || e.metaKey) && (e.key === 'U' || e.keyCode === 85)) e.preventDefault();
    });

    // --------- Monaco 层面的拦截（只有在 editor 就绪时才注册） ---------
    try {
        // 覆盖快捷键：Ctrl/Cmd+V/C/X
        ed.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyV, function() {
            alert("不允许粘贴！如果您需要提交本地代码，请选择上传。");
        });
        ed.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyC, function() {
            // 阻止复制：什么也不做
        });
        ed.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyX, function() {
            // 阻止剪切：什么也不做
        });

        // 禁用编辑器右键菜单（通过 monaco 提供的 hook 或 DOM）
        if (ed.onContextMenu) {
            ed.onContextMenu(function(e) {
                e.event.preventDefault();
                return false;
            });
        }

        // 禁止 DOM 粘贴/复制/剪切事件（保底）
        const dom = ed.getDomNode && ed.getDomNode();
        if (dom) {
            dom.addEventListener('paste', function(e){
                e.preventDefault();
                alert("不允许粘贴！如果您需要提交本地代码，请选择上传。");
                return false;
            });
            dom.addEventListener('copy', function(e){
                e.preventDefault();
                return false;
            });
            dom.addEventListener('cut', function(e){
                e.preventDefault();
                return false;
            });
        }
    } catch (err) {
        console.error('initAntiCheat: failed to bind editor handlers', err);
    }

    // binding() 交互：建议不要在这里直接 throw
    window.binding = function() {
        // 若需要与其它脚本做连通性检查，可以抛出特定值或返回状态而非直接 throw
        // throw Error("ANTI_CHEAT_RUNED");
        return { status: 'anti_cheat_active' };
    };
}
