<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	require_once($APP_RootDir."private/script/function/autoCDN.php");
	// Header setup
	header("Content-Type: text/javascript");
	// Caching
	$cacheDur = 31536000; // 1 year
	$cacheExp = gmdate("D, d M Y H:i:s", time() + $cacheDur)." GMT";
	header("Cache-Control: public, max-age=$cacheDur");
	header("Expires: $cacheExp");
	header("Pragma: public");
?>
const AppConfig = {
	name: "<?=$APP_CONST["name"]?>",
	baseURL: "<?=$APP_CONST["baseURL"]?>",
	APIbase: "/api/",
	cdnURL: "<?=$APP_CONST["cdnURL"]?>",
	tab_color: { light: "#15499A", dark: "#113A7B" },
	default: {
		language: "EN", theme: "auto",
		darkModeCustom: {
			brightness: 100,
			contrast: 100,
			sepia: 0,
			grayscale: 0
		}, position: {
			modal: "bottom",
			lightbox: "top"
		}
	}, languageUniversalPacks: ["@materials", "@header"],
	installApp: {delay: 180, duration: 15}
};
AppConfig["blockDevTool"] = {
	enabled: true,
	bypass: {
		field: "grantDevTool", // Parameter to get bypass key
		md5password: "f4bd9b6156b8d28edac4e13068c0be8c" // Key to bypass (use DisableDevtool.md5("___"))
	},
	checkFrequency: 1.5e3, // Check every 1.5s
	allowContextMenu: true,
	onDevToolOpen: function() {
		debugger;
		// Record to log
	}, whitelist: (function() {
		if (window.CURRENT_APP_USER_IS_DEVELOPER) return () => true; // Is developer or in user allow list
		else return [ // Base on URL
			AppConfig.baseURL + "d/sandbox/",
			/\/_resx\/service\/dev\/.+$/
		];
	}()),
	redirectOnOpenTo: null // AppConfig.baseURL + "" // Leave null for default (error/920)
};
var after_init = function() {
	/* Function to execute after initialization */
	if (location.pathname != AppConfig.baseURL + "d/sandbox/ambient") SeasonalEffect.init();

	sys.back.start();
	$('app[name=main] > .navigator-tab section.nav ul > li > a[href="'+location.pathname+'"]').parent().addClass("this-page");
	$("app[name=main] > .navigator-tab section.nav details:has(li.this-page)").attr("open", "");
	function activeMenu() {
		document.querySelectorAll('app[name=main] > header .item a[href^="/"]:not([name=logo])').forEach(menu => {
			if ($(menu).attr("href").split("?")[0].split("#")[0] == location.pathname) menu.classList.add("current");
		}); $("app[name=main] > header.item.super .menu").has("a.current").addClass("current");
		app.UI.refineElements();
	}
	if (location.pathname.startsWith(AppConfig.baseURL + "service/4/SC/")) $('<div></div>').load(AppConfig.baseURL + "_resx/static/block/ext/header-service-SC.html", function() {
		$($(this).children()[0]).insertBefore("app[name=main] > header .section:nth-child(2) .item:last-child");
		activeMenu();
	}); else activeMenu();
};