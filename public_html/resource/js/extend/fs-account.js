function fsafx() {
    var fsas = {};
    var start = function(title, value, display, exclude="", mode="active") {
        fsas.val = document.querySelector(value); fsas.disp = document.querySelector(display); fsas.exc = exclude; fsas.mode = mode;
        $("input:focus").blur();
        app.ui.lightbox.open("top", {title: title, allowclose: true, html: '<style> div.fs-wrapper { padding: 5px 5px 0; width: 65vw; max-width: 100%; min-width: 40vw; } div.fs-wrapper input[name="fs-i"] { font-size: 20px !important; } div.fs-wrapper div.rs span { display: block; float: left; cursor: pointer; background-color: var(--clr-main-white-absolute); box-shadow: 0.25px 0.25px var(--shd-tiny) var(--fade-black-4); transition: var(--time-tst-xfast); padding: 5px 7.5px; border-radius: 2.5px; font-family: "Sarabun"; font-size: 18.75px; margin: 2.5px; } div.fs-wrapper div.rs span:hover { color: var(--clr-bs-blue); } </style><div class="fs-wrapper form"><input type="text" name="fs-i" onInput="fsa.find(this)" onChange="fsa.find(this)" placeholder="พิมพ์ชื่อบัญชีหรือชื่อผู้ใช้งาน..." autofocus><div class="rs"><span onClick="fsa.end(null, null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span></div></div>'});
        $('input[name="fs-i"]:not(:focus)').focus();
    };
    var find = function(me) {
        var search_for = me.value; if (/^([A-Za-zก-๛0-9]| ){1,50}$/.test(search_for))
        $.post("/resource/php/core/api?app=fs-account&cmd=find&q="+search_for+"&mode="+fsas.mode, {attr: fsas.exc}, function(res, hsc) {
            $("div.fs-wrapper div.rs").html(res);
        });
    };
    var end = function(who, me=null) {
        if (me == null) {
            fsas.val.removeAttribute("value");
            fsas.disp.value = "";
        } else {
            fsas.val.value = who;
            fsas.disp.value = me.innerText;
        }
        if (typeof validate_field !== "undefined") validate_field();
        if (fsas.mode == "log" && typeof exor.check !== "undefined") exor.check();
        else if (typeof sS !== "undefined" && typeof sS.complete !== "undefined") sS.complete();
        app.ui.lightbox.close(); fsas = {};
    };
    return {
        start: start,
        find: find,
        end: end
    };
} const fsa = fsafx(); delete fsafx;