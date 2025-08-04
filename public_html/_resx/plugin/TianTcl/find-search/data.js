const fs = (function() {
	const cv = {
		API_URL: AppConfig.baseURL + "_resx/plugin/TianTcl/find-search/options", // AppConfig.APIbase + "v1/search/options",
		maxRetryAttempts: 5,
		retrySpeed: 1.5e3,
		delayDuration: .8,
		_htmlStyle: '.fs-wrapper { padding: 0.25rem; min-width: 300px; width: 400px; } .fs-wrapper input[type="search"] { margin: 0 auto 5px; } .fs-wrapper div.rs { margin-bottom: 7.5px; padding: 0.25rem; max-height: calc(var(--window-height) - 245px); display: flex; flex-direction: column; gap: 7.5px; } .fs-wrapper div.rs > span { padding: 5px; display: block; border: 1px solid var(--clr-bs-gray-dark); border-radius: 2.5px; background-color: var(--clr-bs-light); cursor: pointer; transition: calc(var(--time-tst-xfast) / 2); white-space: pre-wrap; } .fs-wrapper div.rs > span:hover { background-color: var(--fade-black-8); } .fs-wrapper div.rs > span:active, .fs-wrapper div.rs > span:focus { box-shadow: 0 0 0 0.25rem #828A9180; } .fs-wrapper div.rs > span var.tag { font-size: .75em; border-radius: 1em; font-style: normal; padding: .25px 2.5px; } .fs-wrapper div.rs > span .chip-tag { gap: 0;} .fs-wrapper > button { width: 100%; }'
	};
	var sv = {queue: {empty: true, loading: false}, query: "", delayed: {timeout: false, load: false}};
	const _htmlStruct = (placeholder="") => `<style type="text/css">${cv._htmlStyle}</style><div class="fs-wrapper"><div class="form form-bs" data-clarity-unmask="true"><input type="search" name="fs-search" onInput="fs.find(this)" placeholder="${placeholder}" maxlength="256" autofocus /></div><div class="rs slider hscroll sscroll"></div><button class="red hollow full-x" onClick="fs.choose()">ลบออก</button></div>`;
	var find = async function(me, bypassValidation=false) {
		let tmp = typeof me === "string" ? me : (me == null ? document.querySelector("app[name=main] > .lightbox input[name=fs-search]") : me).value.trim();
		if (!bypassValidation && (!tmp.length || tmp.length > 256 || /^[_%+.]+$/.test(tmp) || sv.query == tmp)) return;
		if (sv.queue.loading) {
			if (!sv.queue.empty) return;
			sv.queue.attempt = 0;
			sv.queue.reload = setInterval(function() {
				if (sv.queue.attempt >= cv.maxRetryAttempts && sv.queue.loading) {
					sv.queue.empty = true;
					clearInterval(sv.queue.reload);
				} else if (!sv.queue.loading) {
					sv.queue.empty = true;
					clearInterval(sv.queue.reload);
					fs.find(null, bypassValidation);
				} else sv.queue.attempt += 1;
			}, cv.retrySpeed);
			sv.queue.empty = false;
		} else if (sv.delayed.timeout) {
			sv.delayed.load = true;
		} else {
			sv.queue.loading = true;
			sv.delayed.timeout = true;
			setTimeout(function() {
				sv.delayed.timeout = false;
				if (!sv.delayed.load) return;
				fs.find(null, bypassValidation);
				sv.delayed.load = false;
			}, cv.delayDuration * 1e3);
			sv.query = tmp;
			await app.Util.ajax(cv.API_URL, {act: sv._type, cmd: sv._mode, param: {q: sv.query, ...sv._param, bypassValidation}}).then(function(dat) {
				sv.queue.loading = false;
				sv.index = 1000; sv.dumper.html("");
				if (typeof dat !== "object" || !dat.length) return;
				switch (sv._mode) {
					case "name-prefix": { dat.forEach(er => {
						sv.view = er.namepth;
						addOption('"'+er["ID"]+'", {TH: "'+er["namepth"]+'", EN: "'+er["namepen"]+'"}');
					}); break; }
					case "address": { dat.forEach(er => {
						sv.view = `${er.subdistrictN} → ${er.districtN} → ${er.provinceN}`;
						addOption(JSON.stringify(er));
					}); break; }
					case "school": { dat.forEach(er => {
						sv.view = `${er["name"]} <var class="tag">${er["province"]}</var>`;
						delete er["province"];
						addOption(JSON.stringify(er));
					}); break; }
					case "account": { dat.forEach(er => {
						sv.view = `${er.display} (${er["username"]})`;
						let optionalParam = sv._complete.length >= 2 ? `, "${er.display} (${er["username"]})"` : "";
						addOption(`"${er["ID"]}"${optionalParam}`);
					}); break; }
					case "teacher": { dat.forEach(er => {
						sv.view = er["display"];
						addOption(`"${er["idcode"]}", "${er.display}"`);
					}); break; }
					case "subject": { dat.forEach(er => {
						sv.view = `${er.name} (${er.code})`;
						delete er["name"];
						addOption(JSON.stringify(er));
					}); break; }
					/* case "___": { dat.forEach(er => {
						sv.view = er["___"];
						addOption(JSON.stringify(er));
					}); break; } */
				}
			});
		}
	},
	choose = function(...data) {
		if (sv._complete != undefined) sv._complete(...data);
		sv.query = "";
		app.UI.lightbox.close();
	},
	addOption = function(data) {
		// sv.view = sv.view.replaceAll(sv.query, `<b>${sv.query}</b>`);
		if (sv.query.length) sv.view = sv.view.replace(new RegExp(`(?!<[^>]*?)(${sv.query})(?![^<]*?>)`, "gi"), "<b>$1</b>");
		sv.dumper.append(`<span onClick='fs.choose(${data.replaceAll("'", "&#x0027;")})' tabindex="${++sv.index}">${sv.view}</span>`);
	}
	// Custom begin
	start = function(isConst, mode, title, complete, param, placeholder) {
		if (typeof param["except"] === "object") param["except"] = param["except"].join(",");
		sv._mode = mode; sv._complete = complete; sv._param = param; sv._type = (isConst ? "constants" : "app");
		$("app[name=main] input:focus").blur();
		app.UI.lightbox("top", {title: title, allowClose: true, exitTap: param["exitTap"] ?? false}, _htmlStruct(placeholder));
		$('app[name=main] > .lightbox input[name="fs-search"]:not(:focus)').focus();
		sv.dumper = $("app[name=main] > .lightbox .fs-wrapper div.rs");
		fs.find("", true);
	};
	return {
		find, choose,
		name_prefix: (title="เลือกคำนำหน้าชื่อ", complete=undefined, except=[], exitTap=true) => start(true, "name-prefix", title, complete, {except, exitTap}, "พิมพ์ชื่อคำนำหน้าชื่อ"),
		address: (title="เลือกพื้นที่", complete=undefined, exitTap=false) => start(true, "address", title, complete, {exitTap}, "พิมพ์ชื่ออำเภอ(แขวง)/ตำบล(เขต)/จังหวัด"),
		school: (title="ค้นหาสถานศึกษา", complete=undefined, except=[], exitTap=false) => start(true, "school", title, complete, {except, exitTap}, "พิมพ์ชื่อสถานศึกษา"),
		account: (title="ค้นหาบัญชี", complete=undefined, except=[], exitTap=true) => start(false, "account", title, complete, {except, exitTap}, "คำค้นหา"),
		teacher: (title="เลือกครู", complete=undefined, except=[], exitTap=false) => start(false, "teacher", title, complete, {except, exitTap}, "พิมพ์ชื่อครู"),
		subject: (title="ค้นหาวิชา", complete=undefined, except=[]) => start(false, "subject", title, complete, {except}, "พิมพ์ชื่อ/รหัสวิชา")
		// ___: (title="___", complete=undefined, except=[]) => start(false, "___", title, complete, {except}, "___")
	}
}());