$(document).ready(function() {
    seek_param(); sizeup_mbtn();
    $(window).on("resize", function() {
        setTimeout(sizeup_menu, 500);
    }).trigger("resize");
    $('html body main div.container input[type="checkbox"]').on("change", last_menu);
});
function seek_param() { if (location.hash!="") {
    // Extract hashes
    var hash = {}; location.hash.substring(1, location.hash.length).split("&").forEach((ehs) => {
        let ths = ehs.split("=");
        hash[ths[0]] = ths[1];
    });
    // Let's see
    if (typeof hash.menu !== "undefined") { setTimeout(function() {
        try { open_menu(hash.menu); }
        catch(error) {}
    }, 750); }
	// history.replaceState(null, null, location.pathname);
} }
function sizeup_mbtn() {
    document.querySelectorAll("html body main div.container label").forEach((emb) => {
        $(emb).attr("style", "--w: "+(parseInt($(emb).innerWidth())-7.5).toString()+"px;");
    });
}
function sizeup_menu() {
    document.querySelectorAll("html body main div.container ul").forEach((eml) => {
        var height = 0;
        $(eml).children().each(function() { if (!$(this).is("[hidden]")) height += $(this).outerHeight(); });
        $(eml).attr("style", "--h: "+height.toString()+"px;");
    });
}
function last_menu(e) {
    if (e.target.checked) history.replaceState(null, null, location.pathname+"#menu="+e.target.name);
}
function open_menu(name) {
    var target = document.querySelector('html body main div.container input[type="checkbox"][name="'+name+'"]');
    if (!target.checked) target.click();
}
function openManual(doc, e) {
    var viewurl = "/resource/viewer?mode=Manual&doc="+doc;
    if (ppa.ctrling()) window.open(viewurl, "_blank");
    // if (ppa.ctrling()) open(viewurl, "_blank", "width=540,height=720");
    else app.ui.lightbox.open("mid", {title: "คู่มือการใช้งาน"+e.target.innerText, allowclose: true, html: '<iframe src="'+viewurl+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'});
}