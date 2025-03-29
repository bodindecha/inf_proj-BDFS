function schoolfx() {
    var schs = {};
    start = function(title, value, display, exclude="") {
        schs.val = document.querySelector(value); schs.disp = document.querySelector(display); schs.exc = exclude;
        $("input:focus").blur();
        app.ui.lightbox.open("top", {title: title, allowclose: true, html: '<style> div.fs-wrapper { padding: 5px 5px 0; width: 65vw; max-width: 100%; min-width: 40vw; } div.fs-wrapper input[name="fs-i"] { font-size: 20px !important; } div.fs-wrapper div.rs span { display: block; float: left; cursor: pointer; background-color: var(--clr-main-white-absolute); box-shadow: 0.25px 0.25px var(--shd-tiny) var(--fade-black-4); transition: var(--time-tst-xfast); padding: 5px 7.5px; border-radius: 2.5px; font-family: "Sarabun"; font-size: 18.75px; margin: 2.5px; } div.fs-wrapper div.rs span:hover { color: var(--clr-bs-blue); } div.fs-wrapper div.rs span sub { color: var(--clr-bs-gray); font-size: 0.5rem; } </style><div class="fs-wrapper form"><input type="text" name="fs-i" onInput="school.find(this)" onChange="school.find(this)" placeholder="พิมพ์ชื่อโรงเรียน..." autofocus><div class="rs"><span onClick="school.set(null)"><font style="color: var(--clr-bs-red);">ลบออก</font></span></div></div>'});
        $('input[name="fs-i"]:not(:focus)').focus();
    };
    find = function(me) {
        var search_for = me.value.trim(); if (search_for.length > 0)
        $.get("/resource/php/core/api?app=fs-school&cmd=find&q="+search_for+"&attr="+encodeURI(schs.exc), function(res, hsc) {
            $("div.fs-wrapper div.rs").html(res);
        });
    };
    end = function(who, me=null) {
        if (me == null) {
            schs.val.removeAttribute("value");
            schs.disp.value = "";
        } else {
            schs.val.value = who;
            let text = me.innerText.split(" ").reverse(); text.shift();
            schs.disp.value = text.reverse().join(" ");
        }
        if (typeof validate_field !== "undefined") validate_field();
        app.ui.lightbox.close(); schs = {};
    };
    return {
        choose: start,
        find: find,
        set: end
    };
} const school = schoolfx(); delete schoolfx;