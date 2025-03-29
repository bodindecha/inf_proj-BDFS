let fsfx = function() {
    const _htmlStyle = {
        address: '.fs-wrapper { padding: 0.25rem; min-width: 300px; width: 400px; } .fs-wrapper div.rs { padding: 0.25rem; max-height: 70vh; } .fs-wrapper div.rs > span { margin-bottom: 7.5px; padding: 5px; display: block; border: 1px solid var(--clr-bs-gray-dark); border-radius: 2.5px; background-color: var(--clr-bs-light); font-size: 1rem; cursor: pointer; transition: calc(var(--time-tst-xfast) / 2); } .fs-wrapper div.rs > span:hover { background-color: var(--clr-pp-indigo-50); } .fs-wrapper div.rs > span:active, .fs-wrapper div.rs > span:focus { box-shadow: 0px 0px 0px 0.25rem rgb(13 110 253 / 25%); } .fs-wrapper div.rs > span:first-of-type { color: var(--clr-bs-red); text-align: center; }'
    };
    var _mode, _complete;
    function _htmlStruct(placeholder = "") {
        return '<style type="text/css">'+_htmlStyle[_mode]+'</style><div class="fs-wrapper"><div class="form"><input type="search" name="fs-search" onInput="fs.find(this)" placeholder="'+placeholder+'" autofocus style="margin: 0px auto 5px;"></div><div class="rs slider hscroll sscroll"><span onClick="fs.choose()">ลบออก</span></div></div>';
    }
    var find = function(me) {
        var search_for = me.value.trim(); if (/^[^%_+]{1,75}$/.test(search_for))
        $.post("/resource/php/core/fetch?app=fs&cmd="+_mode, {q: search_for}, function(res, hsc) {
            var dat = JSON.parse(res), dumper = $(".fs-wrapper div.rs");
            if (dat.success) {
                dumper.html('<span onClick="fs.choose()">ลบออก</span>');
                if (_mode == "address") dat.info.forEach((er) => { try {
                    let view = er.subdistrictN.replace(search_for, "<b>"+search_for+"</b>")+' → '+er.districtN.replace(search_for, "<b>"+search_for+"</b>")+' → '+er.provinceN.replace(search_for, "<b>"+search_for+"</b>");
                    dumper.append('<span onClick=\'fs.choose('+JSON.stringify(er)+')\'>'+view+'</span>');
                } catch(ex) {} });
            } else app.ui.notify(1, dat.reason);
        });
    };
    var choose = function(data = null) {
        if (_complete != undefined) _complete(data);
        app.ui.lightbox.close();
    }; // Custom begin
    var fs_addr = function(title = "ค้นหาที่อยู่ของท่าน", complete = undefined) {
        _mode = "address"; _complete = complete;
        $("input:focus").blur();
        app.ui.lightbox.open("top", { title: title, allowclose: true, html: _htmlStruct("พิมพ์ชื่ออำเภอ(แขวง)/ตำบล(เขต)/จังหวัด") });
        $('input[name="fs-search"]:not(:focus)').focus();
    };
    return {
        find: find, choose: choose,
        address: fs_addr
    }
}; const fs = fsfx(); delete fsfx;