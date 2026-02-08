<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	$useRedirectRule = false;
	require($APP_RootDir."private/script/start/PHP.php");
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] .error *:not(path):not(button) { transition: var(--time-tst-medium) cubic-bezier(0.68, -0.6, 0.32, 1.6) /*easeInOutBack*/; }
	app[name=main] .error { display: flex; flex-direction: row; justify-content: center; }
	app[name=main] .error > * { margin: 5px; }
	app[name=main] .error .image .hexagon path {
		stroke: var(--btn-bgc); stroke-width: 7px;
		fill: none;
		animation: float_obj 1s infinite ease-in-out alternate;
		animation-delay: calc(0.2s * var(--i));
	}
	@keyframes float_obj {
		100% { transform: translateY(20px); }
	}
	app[name=main] .error .info {
		padding-left: 12.5px;
		display: flex; flex-direction: column; justify-content: space-evenly;
	}
	app[name=main] .error .info .intel {
		padding: 0px 5px;
		font-size: 1.5em;
	}
	app[name=main] .error .info .action {
		font-size: 1.5rem !important;
		display: flex; gap: 10px;
	}
	@media only screen and (max-width: 768px) {
		app[name=main] .error { flex-direction: column; justify-content: flex-start; }
		app[name=main] .error .image {
			height: 375px;
			text-align: right;
		}
		app[name=main] .error .image svg { transform: scale(0.75) translateY(-64.5px); }
		app[name=main] .error .info { padding-left: 2.5px; }
		app[name=main] .error .info .intel { font-size: 1.25rem !important; }
		app[name=main] .error .info .intel p { line-height: 1.25; }
		app[name=main] .error .info .action { justify-content: center; }
		app[name=main] .error .info .action { gap: 5px; }
	}
</style>
<script type="text/javascript">
	const TRANSLATION = "error";
	$(document).ready(function() {
		hsc.init();
	});
	const hsc = (function(d) {
		const cv = {
			http_status_codes: { // HSC REF : iana.org/assignments/http-status-codes/http-status-codes.xhtml
				// Informational
				100: "Continue",
				101: "Switching Protocols",
				102: "Processing",
				103: "Checkpoint",
				// 104-109: "Unassigned",
				110: "Response is Stale",
				111: "Revalidation Failed",
				112: "Disconnected Operation",
				113: "Heuristic Expiration",
				// 104-198: "Unassigned",
				199: "Miscellaneous Warning",
				
				// Success
				200: "OK",
				201: "Created",
				202: "Accepted",
				203: "Non-Authoritative Information",
				204: "No Content",
				205: "Reset Content",
				206: "Partial Content",
				207: "Multi-Status",
				208: "Already Reported",
				// 209-213: "Unassigned",
				214: "Transformation Applied",
				// 215-217: "Unassigned",
				218: "This is fine", // Apache
				// 219-225: "Unassigned",
				226: "IM Used",
				// 227-298: "Unassigned",
				299: "Miscellaneous Persistent Warning",
				
				// Redirect
				300: "Multiple Choices",
				301: "Moved Permanently",
				302: "Found",
				303: "See Other",
				304: "Not Modified",
				305: "Use Proxy",
				306: "Switch Proxy",
				307: "Temporary Redirect",
				308: "Permanent Redirect",
				// 309-399: "Unassigned",
				
				// Client error
				400: "Bad Request",
				401: "Unauthorized",
				402: "Payment Required",
				403: "Forbidden",
				404: "Not Found",
				405: "Method Not Allowed",
				406: "Not Acceptable",
				407: "Proxy Authentication Required",
				408: "Request Timeout",
				409: "Conflict",
				410: "Gone",
				411: "Length Required",
				412: "Precondition Failed",
				413: "Request Entity Too Large",
				414: "Request-URI Too Long",
				415: "Unsupported Media Type",
				416: "Requested Range Not Satisfiable",
				417: "Expectation Failed",
				418: "I'm a teapot",
				419: "Client Error", // Laravel: Page Expired
				420: "Enhance Your Calm", // Twitter // Spring: Method Failure
				421: "Misdirected Request",
				422: "Unprocessable Entity",
				423: "Locked",
				424: "Failed Dependency",
				425: "Unordered Collection",
				426: "Upgrade Required",
				// 427: "Unassigned",
				428: "Precondition Required",
				429: "Too Many Requests",
				430: "Request Header Fields Too Large", // Shopify
				// 431-439: "Unassigned",
				440: "Login Time-out", // IIS
				// 441-443: "Unassigned",
				444: "Connection Closed Without Response", // nginx: No Response
				// 445-448: "Unassigned",
				449: "Retry With", // IIS
				450: "Blocked by Windows Parental Controls", // Microsoft // IIS: Redirect
				451: "Unavailable For Legal Reasons",
				// 452-459: "Unassigned",
				460: "Client closed the connection with the load balancer before the idle timeout period elapsed", // AWS
				// 461-462: "Unassigned",
				463: "The load balancer received an X-Forwarded-For request header with more than 30 IP addresses", // AWS
				// 464-493: "Unassigned",
				494: "Request header too large", // nginx
				495: "SSL Certificate Error", // nginx
				496: "SSL Certificate Required", // nginx
				497: "HTTP Request Sent to HTTPS Port", // nginx
				498: "Invalid Token", // Esri
				499: "Client Closed Request", // nginx
				
				// Server error
				500: "Internal Server Error",
				501: "Not Implemented",
				502: "Bad Gateway",
				503: "Service Unavailable",
				504: "Gateway Timeout",
				505: "HTTP Version Not Supported",
				506: "Variant Also Negotiates",
				507: "Insufficient Storage",
				509: "Bandwidth Limit Exceeded", // Apache
				510: "Not Extended",
				511: "Network Authentication Required",
				// 512-519: "Unassigned",
				520: "Web Server Returned an Unknown Error", // Cloudflare
				521: "Web Server Is Down", // Cloudflare
				522: "Connection Timed Out", // Cloudflare
				523: "Origin Is Unreachable", // Cloudflare
				524: "A Timeout Occurred", // Cloudflare
				525: "SSL Handshake Failed", // Cloudflare
				526: "Invalid SSL certificate", // Cloudflare
				527: "Railgun Error", // Cloudflare
				// 528: "Unassigned",
				529: "Site is overloaded", // Qualys
				530: "Site is frozen", // Pantheon
				// 531-560: "Unassigned",
				561: "Unauthorized", // AWS
				// 562-597: "Unassigned",
				598: "Network read timeout error", // Informal convention
				599: "Network Connect Timeout Error",
				
				// System HSC
				900: "Not Found!",
				901: "No Permission",
				902: "Wrong!",
				903: "Page Under Construction",
				904: "JS Disabled",
				905: "Server Error",
				906: "Safari Not Supported",
				907: "Unauthorized",
				908: "Unavailable",
				909: "Updating Page",
				910: "Unknown Error",
				911: "...",
				912: "~&emsp;¯\_(ツ)_/¯&emsp;~",
				913: "No Error",
				914: "Not Allowed",
				915: "Incorrect",
				916: "Cannot Be Shown",
				917: "Invalid Request",
				918: "Missing Attributes",
				919: "Required Parameters",
				920: "DevTool Disabled",

				// Cloudflare errors
				1000: "DNS points to prohibited IP",
				1001: "DNS resolution error", // Unable to resolve
				1002: "DNS points to Prohibited IP",
				1002: "Restricted",
				1003: "Access Denied: Direct IP Access Not Allowed", // Bad Host header
				1004: "Host Not Configured to Serve Web Traffic",
				1006: "Access Denied: Your IP address has been banned", // Quota exceeded. You are currently allowed 5 monitors. Please re-use or delete any unused monitors.
				1007: "Access Denied: Your IP address has been banned",
				1008: "Access Denied: Your IP address has been banned",
				1009: "Access Denied: Country or region banned",
				1010: "The owner of this website has banned your access based on your browser's signature",
				1011: "Access Denied (Hotlinking Denied)",
				1012: "Access Denied",
				1013: "HTTP hostname and TLS SNI hostname mismatch",
				1014: "CNAME Cross-User Banned",
				1015: "You are being rate limited",
				1016: "Origin DNS error",
				1018: "Could not find host", // Unable to resolve because of ownership lookup failure
				1019: "Compute server error",
				1020: "Access denied",
				1023: "Could not find host", // Unable to resolve because of feature lookup failure
				1025: "Please check back later",
				1033: "Argo Tunnel error",
				1035: "Invalid request rewrite (invalid URI path)",
				1036: "Invalid request rewrite (maximum length exceeded)",
				1037: "Invalid rewrite rule (failed to evaluate expression)",
				1040: "Invalid request rewrite (header modification not allowed)",
				1041: "Invalid request rewrite (invalid header value)",
				1101: "Rendering error",
				1102: "Rendering error",
				1104: "A variation of this email address is already taken in our system. Only one variation is allowed.",
				1106: "Access Denied: Your IP address has been banned",
				1200: "Cache connection limit"
			},
			loggable: (typeof sys.log !== "undefined" && typeof sys.log.pageHistory !== "undefined")
		};
		var sv = { hsc: 0, controls: false };
		var getError = function() {
			function setDefaultHSC() {
				setLocation(900);
			}
			if (/\/error\/?$/.test(location.pathname)) setDefaultHSC();
			else {
				sv.hsc = /\/error\/\d{3,4}$/.test(location.pathname) ? parseInt(location.pathname.split("/").at(-1)) : <?=($_REQUEST["code"] ?? 0)?>;
				if (!Object.keys(cv.http_status_codes).includes(sv.hsc.toString())) setDefaultHSC();
			}
			return sv.hsc;
		},
		setError = function() {
			var last = {
				code: "Error: " + sv.hsc.toString() + " | " + (/\ \|\ /.test(d.title) ? d.title.split(" | ")[1] : d.title),
				text: cv.http_status_codes[sv.hsc]
			}; $('app[name=main] .control select[name="hsc"]').val(sv.hsc);
			$('app[name=main] .control select[name="hsc"] + button').attr("disabled", "");
			d.querySelector("app[name=main] .error .info .intel h2").innerText = sv.hsc.toString();
			d.title = last.code;
			$('head meta[name="twitter:title"], head meta[property="og:title"]').attr("content", last.code);
			d.querySelector("app[name=main] .error .info .intel p").innerHTML = last.text;
			$('head meta[name="description"], head meta[property="og:description"]').attr("content", last.code);
		},
		seek_param = function() {
			app.IO.URL.getHashQuery().then(hash => {
				if (!hash) return;
				if (hash.has("ref")) {
					var ref = decodeURIComponent(hash.get("ref")).replace(/^\//, "");
					history.replaceState(null, null, location.pathname.replace(/\/error\/\d{3,4}$/, AppConfig.baseURL) + ref);
					var signInButton = document.querySelector('app[name=main] > header a[href*="#next=error%2F"]');
					if (signInButton != null) $(signInButton).attr("href", $(signInButton).attr("href").replace(/error%2F\d{3,4}/, encodeURIComponent(ref)));
					else console.warn("Couldn't find the sign-in button to update its referrer link.");
				}
			});
		},
		customError = function(hsc, showNotification=true) {
			hsc = parseInt(hsc);
			if (hsc == sv.hsc) app.UI.notify(2, "Code not changed.");
			else if (Object.keys(cv.http_status_codes).includes(hsc.toString())) {
				setLocation(hsc);
				if (cv.loggable) sys.log.pageHistory();
				setError();
				if (showNotification) app.UI.notify(0, "New HTTP status code set ("+sv.hsc.toString()+")");
			} else if (showNotification) app.UI.notify(1, "There is no HTTP status code "+sv.hsc.toString());
		},
		setLocation = function(hsc) {
			sv.hsc = parseInt(hsc);
			let newLocation = location.pathname.match(/^(\/[A-Z0-9a-z\-_]+)*\/error/)[0];
			newLocation = newLocation.substring(0, newLocation.length-6);
			history.replaceState(null, null, newLocation+"/error/"+sv.hsc.toString());
		},
		initialize = async function() {
			// Form
			const topics = ["Informational", "Success", "Redirect", "Client error", "Server error", "System HSC", "Cloudflare errors"];
			var pos = 0,
				firstDigit = "", optHTML = '';
			Object.keys(cv.http_status_codes).forEach(ec => {
				ec = ec.toString();
				if (ec.substring(0, 1) != firstDigit) {
					firstDigit = ec.substring(0, 1);
					if (pos) optHTML += '</optgroup>';
					optHTML += '<optgroup label="'+topics[pos++]+'">';
				} optHTML += '<option value="'+ec+'">'+ec+'</option>'
			}); $('app[name=main] .control select[name="hsc"]').html(optHTML+'</optgroup>');
			// Start
			await getError();
			setError();
			seek_param();
		},
		moveError = function(forward) {
			const hscList = Object.keys(cv.http_status_codes);
			var pos = hscList.indexOf(sv.hsc.toString());
			pos += (forward ? 1 : -1);
			if (pos > 0 && pos < hscList.length) hsc.set(hscList[pos]);
			else app.UI.notify(1, "You've reached either end of the HSC list.");
		},
		showControls = function() {
			if (!sv.controls) {
				sv.controls = true;
				$("app[name=main] .control").fadeIn(750, app.UI.refineElements);
			}
		},
		customByForm = function() {
			var newHSC = $('app[name=main] .control select[name="hsc"]').val();
			hsc.set(newHSC);
		};
		return {
			init: initialize,
			set: customError,
			next: () => moveError(true),
			prev: () => moveError(false),
			showControls: showControls,
			form: customByForm
		};
	}(document));
	const goHome = function() {
		var home = top.document.querySelector("header div.head-item.logo > a:first-child");
		if (/^#(&?[A-Za-z\-]+=[A-Z0-9a-z\-_]+&)*home=\/.*(&[A-Za-z\-]+=[A-Z0-9a-z\-_])*$/.test(location.hash)) {
			home = location.hash.match(/home=\/.*&/)[0].substring(5);
			home = home.substring(0, home.length-1);
			top.location.assign(home);
		} else if (home != null) home.click();
		else top.location.assign("<?=$APP_CONST["baseURL"]?>");
	}, allowForm = function() {
		$('app[name=main] .control select[name="hsc"] + button').removeAttr("disabled");
	};
</script>
<?php $APP_PAGE -> print -> nav(); ?>
<main class="bg-rainbow">
	<section class="container">
		<div class="error">
			<div class="image">
				<svg width="380px" height="500px" viewBox="0 0 837 1045" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g class="hexagon">
						<path d="M353,9 L626.664028,170 L626.664028,487 L353,642 L79.3359724,487 L79.3359724,170 L353,9 Z" style="--i: 0;" class="blue" -stroke="#007FB2"></path>
						<g><a onDblClick="hsc.showControls()">
							<path d="M78.5,529 L147,569.186414 L147,648.311216 L78.5,687 L10,648.311216 L10,569.186414 L78.5,529 Z" style="--i: 1;" class="red" -stroke="#EF4A5B"></path>
						</a></g>
						<path d="M773,186 L827,217.538705 L827,279.636651 L773,310 L719,279.636651 L719,217.538705 L773,186 Z" style="--i: 2;" class="purple" -stroke="#795D9C"></path>
						<path d="M639,529 L773,607.846761 L773,763.091627 L639,839 L505,763.091627 L505,607.846761 L639,529 Z" style="--i: 3;" class="yellow" -stroke="#F2773F"></path>
						<path d="M281,801 L383,861.025276 L383,979.21169 L281,1037 L179,979.21169 L179,861.025276 L281,801 Z" style="--i: 4;" class="green" -stroke="#36B455"></path>
					</g>
				</svg>
			</div>
			<div class="info">
				<div class="intel">
					<h2></h2>
					<p></p>
				</div>
				<div class="action">
					<button onClick="goHome()" class="primary ripple-click">Home</button>
					<button onClick="top.history.back()" class="secondary ripple-click">Back</button>
					<button onClick="top.location.reload()" class="secondary ripple-click">Reload</button>
				</div>
			</div>
		</div>
		<div class="control form form-bs" style="display: none;"><div class="group spread">
			<button class="icon teal hollow pill ripple-click" onClick="hsc.prev()"><i class="material-icons">chevron_left</i></button>
			<div class="group">
				<select name="hsc" onChange="allowForm()"></select>
				<button class="pink hollow ripple-click" onClick="hsc.form()" disabled>Set</button>
			</div>
			<button class="icon teal hollow pill ripple-click" onClick="hsc.next()"><i class="material-icons">chevron_right</i></button>
		</div></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer();
?>