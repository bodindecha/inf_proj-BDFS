function initial_system() {
    var auth_open = function(rdr = null, force = false) {
		auth_out(true);
        // app.ui.modal.close(); // if (md_var.showing)
		var qs = (rdr == null) ? "" : "?return_url="+encodeURI(rdr);
		if (typeof $.notify === "undefined") $.getScript("/resource/js/lib/notify.min.js");
		$.ajax({url: "/resource/html/core/sys_dialog-auth.html"+qs, success: function(rst) {
			app.ui.lightbox.open("top", {title: "เข้าสู่ระบบ", allowclose: !force, html: rst});
		}});
    }
	var auth_validate_form = function() {
		// document.querySelector("form.auth-wrapper button").disabled = ($('form.auth-wrapper input[name="user"]').val()=="" || $('form.auth-wrapper input[name="pass"]').val()=="");
		var allow = 0;
			user = $('form.auth-wrapper input[name="user"]').val().trim(),
			pass = $('form.auth-wrapper input[name="pass"]').val().trim();
		if (user.match(/(\d{5}|[a-z]{3,28}\.[a-z]{1,2}|[a-zA-Z]{3,30}\d{0,3})@bodin\.ac\.th$/))
			$('form.auth-wrapper input[name="user"]').notify("Must not contain @bodin.ac.th", "error");
		else if (user.match(/^\d{5}$/)) {
			user = parseInt(user);
			if ([34216, 99999].includes(user) || (user >= 39618 && user < 46548)) allow = 1;
		} else if (user.toLowerCase().match(/^([a-z]{3,28}\.[a-z]{1,2}|[a-zA-Z]{3,30}\d{0,3})$/)) allow = 1;
		if (allow && pass.length) allow += 1;
		if (user.length && !allow) $('form.auth-wrapper input[name="user"]').attr("invalid", "");
		else $('form.auth-wrapper input[name="user"][invalid]').removeAttr("invalid");
		document.querySelector("form.auth-wrapper button").disabled = allow < 2;
	}
	const reload_url_regex = /^\/(archive(d\/\d{10})?|(\d{5}|[a-z]{3,28}\.[a-z]{1,2}|(?!(archive|error|account))[a-zA-Z]{3,30}\d{0,3})|service\/.+)$/;
	var admin_user = {map: {"TianTcl": "42629", "99999": "34216"}, switch: ""};
	var auth_sbmt = function(rdr = "") {
		go_on(rdr); return false;
		function go_on(rdr) {
			var data = {
				username: $("form.auth-wrapper input[name=\"user\"]").val().trim(),
				password: $("form.auth-wrapper input[name=\"pass\"]").val().trim(),
				// zone: 3 // parseInt(document.querySelector("form.auth-wrapper select[name=\"zone\"]").value.trim())
			}, ak = "MTI0NmQyMjYzMmUzMjY0N2E3OTc0Njc3MzdjNjE2MTY3NzEzYzNlNzk2MDcxNjQy";
			/*var is_admin = (data.username in admin_user.map);
			if (is_admin) { admin_user.switch = data.username; data.username = admin_user.map[data.username]; data.zone = 0; }
			else */ if (/^\d{5}$/.test(data.username)) data.zone = 0;
			else if (/^[a-z]{3,28}\.[a-z]{1,2}$/.test(data.username.toLowerCase())) data.zone = 3; // ไม่รู้ได้เองว่าเป็น 1 หรือ 2
			else data.zone = 3; if (data.username=="" || data.password=="" || ![0, 1, 2, 3].includes(data.zone)) app.ui.notify(1, [2, "Please check your inputs.\nโปรดตรวจสอบข้อมูลการเข้าสู่ระบบ"]);
			else {
				document.querySelector("form.auth-wrapper button").disabled = true;
				$.post("/resource/php/core/auth?way=in", data, function(res2, hsc2) {
					var dat2 = JSON.parse(res2);
					if (dat2.success) {
						app.ui.lightbox.close();
						setTimeout(function() {
							if (rdr!="" && rdr!="true") location = "/"+rdr+(location.hash!=""?encodeURI(location.hash):"");
							else if (reload_url_regex.test(location.pathname) || rdr=="true") location.reload();
							else location = "/"+(data.zone==0?"s":"t")+"/";
						}, (location.pathname=="/"?0:750));
					} else {
						app.ui.notify(1, dat2.reason);
						document.querySelector("form.auth-wrapper button").disabled = false;
					}
				});
			}
		}
	}
	var auth_sso = function(rdr = "") {
		$.post("/resource/php/core/auth?way=sso", function(res, hsc) {
			var dat = JSON.parse(res);
			if (dat.success) {
				if (rdr!="") location = "/"+rdr+(location.hash!=""?encodeURI(location.hash):"");
				else if (reload_url_regex.test(location.pathname)) location.reload();
				else location.reload(); // location = "/"+(data.zone==0?"s":"t")+"/";
			} else {
				app.ui.notify(1, dat.reason);
				document.querySelector("form.auth-wrapper button").disabled = false;
			}
		});
	}
	var auth_out = function(jac = false) {
		// $.ajax({url: "https://bod.in.th/resource/php/core/auth?way=out"});
		$.ajax({url: "/resource/php/core/auth?way=out", success: function(res) {
			if (jac == false) {
				// if (reload_url_regex.test(location.pathname)) location.reload();
				// else location = "/";
				location.reload();
			} else if (typeof jac == "string") location = "/"+jac;
		}});
	}
	var service_dat = {};
	var service_acc = function(is_new = false) {
		const style = '<style type="text/css">form.auth-wrapper { margin: 10px 0px; padding: 5px; } form.auth-wrapper > * { margin: 2.5px 0px; font-size: 15px; font-family: "THSarabunNew", serif; } form.auth-wrapper label { display: block; } form.auth-wrapper label span { cursor: pointer; color: var(--clr-pp-blue-grey-700); } form.auth-wrapper label span:hover { background-color: rgba(0, 0, 0, 0.125); } form.auth-wrapper button { margin-top: 20px; } form.auth-wrapper font { font-size: 15px; } form.auth-wrapper font a:link, form.auth-wrapper font a:visited { text-decoration: none; color: var(--clr-bd-light-blue) } form.auth-wrapper font a:hover, form.auth-wrapper font a:active { text-decoration: underline; color: var(--clr-bd-low-light-blue) } form.auth-wrapper center a { font-size: 12.5px; } @media only screen and (max-width: 768px) { form.auth-wrapper > * { font-size: 12.5px; } form.auth-wrapper font { font-size: 15px; } form.auth-wrapper center a { font-size: 10px; } }</style>';
		if (!is_new) $.get("/service/game/api", {app: "Account", cmd: "get"}, function(res, hsc) {
			var dat = JSON.parse(res);
			if (dat.success) app.ui.lightbox.open("top", {title: "Edit information", allowclose: true,
			html: style+'<form class="auth-wrapper form"><label>Display name</label><input value="'+dat.info.name+'" name="name" placeholder="Set a nickname" type="text"><br><label>Account privacy</label><select name="ftpf"><option value="Y" '+(dat.info.ftpf=="Y"?"selected":"")+'>Public</option><option value="N" '+(dat.info.ftpf=="N"?"selected":"")+'>Private</option></select><br><center><button class="blue full-x" onClick="return sys.service.update()">Save</button></center></form>'});
			else app.ui.notify(1, dat.reason);
		}); else app.ui.lightbox.open("top", {title: "Welcome!", allowclose: false,
			html: style+'<form class="auth-wrapper form"><p>You look new here. Please fill in the information below to continue.</p><label>Display name</label><input name="name" placeholder="Set a nickname" type="text"><br><label>Account privacy</label><select name="ftpf"><option value="Y">Public</option><option value="N">Private</option></select><p>Don\'t worry, we only use your data to store your progress<p><center><button class="blue full-x" onClick="return sys.service.update()">Complete profile</button><a onClick="window.history.back()" href="javascript:void(0)">Back</a></center></form>'});
		service_dat.is_new = is_new;
	}
	var service_upd = function() {
		go_on(); return false;
		function go_on() {
			var data = {
				name: $("section.lightbox input[name=\"name\"]").val().trim(),
				ftpf: document.querySelector("section.lightbox select[name=\"ftpf\"] option:checked").value.trim()
			}; if (data.name=="" || !/^[\ A-Za-z0-9\-_\.()\[\]{}<>!@#\$%&\*\+]{3,30}$/.test(data.name) || /^(\ |-|_|\.|(|)|\[|\]|{|}|<|>|!|@|#|\$|%|&|\*|\+)+$/.test(data.name)) app.ui.notify(1, [2, "Please re-check your name. It can't be black and can only contain allowed characters (A-Z a-z 0-9 -_.()[]{}<>!@#$%&*+) and must be 3 - 30 characters long"]);
			else {
				go_on(); return false;
				function go_on() {
					document.querySelector("form.auth-wrapper button").disabled = true;
					$.post("/service/game/api", {app: "Account", cmd: "set", attr: data}, function(res, hsc) {
						var dat = JSON.parse(res);
						if (dat.success) {
							app.ui.lightbox.close();
							if (service_dat.is_new) setTimeout(function() { location.reload(); }, 750);
						} else {
							app.ui.notify(1, dat.reason);
							document.querySelector("form.auth-wrapper button").disabled = false;
						}
					});
				}
			}
		}
	}
	var start_background = function() {
		sys.back.logPageHistory();
	},
	record_pageload = function() {
		$.post("/resource/php/extend/save-sess", {url: location.pathname+location.search+location.hash});
	};
    return {
        auth: {
            orize: auth_open,
			validate: auth_validate_form,
            tempt: auth_sbmt,
			sso: auth_sso,
			out: auth_out
        }, service: {
			account: service_acc,
			update: service_upd
		}, back: {
			start: start_background,
			logPageHistory: record_pageload
		}
    };
}

if (typeof sys === "undefined") var sys = initial_system();
// if (window.sys != window.top.sys != top.sys != sys) window.sys = window.top.sys = top.sys = sys;

delete initial_system;

const ajax = async function(url, params=null, method="POST", resultType="json") {
	let opts = {
		url: url, type: method, resultType: resultType,
	}; if (params != null) opts.data = params;
	var response = /* $.ajax(opts); */ new Promise(function(resolve) {
		/* let req = new XMLHttpRequest();
		req.open(method, url);
		req.onload = () => resolve(JSON.parse(req.response));
		req.send(); */
		opts.success = function(result) { resolve(JSON.parse(result)); };
		$.ajax(opts);
	}); // return await response;
	var dat = await response;
	if (dat.success) return (typeof dat.info !== "undefined" ? dat.info : true);
	else dat.reason.forEach(em => app.ui.notify(1, em));
	return false;
};