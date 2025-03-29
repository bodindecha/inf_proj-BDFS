const Spotlight_background = (function(d) {
	const cv = {
		controlKey: "displaySpotlightBg",
		controlClass: "bg-spotlight",
		range: [1, 57],
		myStylesheet: AppConfig.baseURL + "_resx/static/style/core/spotlight-background.css",
		applyTo: "app[name=main] > main"
	};
	var sv = {inited: false};
	var initialize = function() {
		if (sv.inited) return;
		// Add preference
		if (!app.preferences.has(cv.controlKey)) app.preferences.set(cv.controlKey, {
			on: true,
			apply: (Math.floor(Math.random()*cv.range[1])+cv.range[0]).toString().padStart(3, "0")
		}); d.head.appendChild($(`<link rel="stylesheet" href="${cv.myStylesheet}" />`)[0]);
		// Add controls
		sv.userControlStatus = $('<span></span>')
			.text(app.preferences[cv.controlKey].on ? "On" : "off");
		sv.userControl = $(`<button name="pref-${cv.controlKey}" class="pill large ripple-click">Spotlight: </button>`)
			.on("click", function() {
				$(cv.applyTo).toggleClass(cv.controlClass + " " + sv.preferredBG);
				app.preferences.set(cv.controlKey, {
					on: !app.preferences[cv.controlKey].on,
					apply: app.preferences[cv.controlKey].apply
				}); sv.userControl.toggleClass("green red");
				sv.userControlStatus.text(app.preferences[cv.controlKey].on ? "On" : "Off");
			})
			.addClass(app.preferences[cv.controlKey].on ? "green" : "red")
			.append(sv.userControlStatus);
		$("app[name=main] > header .menu[name=settings] .form").append(sv.userControl);
		app.UI.refineElements();
		// Apply settings
		sv.preferredBG = Array.from(d.querySelector(cv.applyTo).classList).filter(c => /^bg-[A-Z0-9a-z\-]+$/.test(c)).join(" ");
		$(cv.applyTo).addClass(`${cv.controlClass}-${app.preferences[cv.controlKey].apply}`);
		// Check autostart
		if (app.preferences[cv.controlKey].on)
			$(cv.applyTo).addClass(cv.controlClass).removeClass(sv.preferredBG);
		sv.inited = true;
	}
	return {
		init: initialize,
		currentDisplay: () => app.preferences[cv.controlKey].apply
	};
}(document));