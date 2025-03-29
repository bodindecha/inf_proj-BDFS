function fstfx() {
    var fsts = {};
    start = function(title, value, display, exclude="") {
        fsts.val = document.querySelector(value); fsts.disp = document.querySelector(display); fsts.exc = exclude;
        $("input:focus").blur();
        app.ui.lightbox.open("top", {title: title, allowclose: true, html: '<style> div.fs-wrapper { padding: 5px 5px 0; width: 65vw; max-width: 100%; min-width: 40vw; } div.fs-wrapper input[name="fs-i"] { font-size: 20px !important; } div.fs-wrapper div.rs span { display: block; float: left; cursor: pointer; background-color: var(--clr-main-white-absolute); box-shadow: 0.25px 0.25px var(--shd-tiny) var(--fade-black-4); transition: var(--time-tst-xfast); padding: 5px 7.5px; border-radius: 2.5px; font-family: "Sarabun"; font-size: 18.75px; margin: 2.5px; } div.fs-wrapper div.rs span:hover { color: var(--clr-bs-blue); } </style><div class="fs-wrapper form"><input type="text" name="fs-i" onInput="fst.find(this)" onChange="fst.find(this)" placeholder="พิมพ์ชื่อครู..." autofocus><div class="rs"><span onClick="fst.end(null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span></div></div>'});
        $('input[name="fs-i"]:not(:focus)').focus();
    };
    find = function(me) {
        var search_for = me.value; if (/^[A-Za-zก-๛]{1,30}$/.test(search_for))
        $.get("/resource/php/core/api?app=fs-teacher&cmd=find&q="+search_for+"&attr="+encodeURI(fsts.exc), function(res, hsc) {
            $("div.fs-wrapper div.rs").html(res);
        });
    };
    end = function(who, me=null) {
        if (me == null) {
            fsts.val.removeAttribute("value");
            fsts.disp.value = "";
        } else {
            fsts.val.value = who;
            fsts.disp.value = me.innerText;
        }
        if (typeof validate_field !== "undefined") validate_field();
        app.ui.lightbox.close(); fsts = {};
    };
    return {
        start: start,
        find: find,
        end: end
    };
} const fst = fstfx(); delete fstfx;