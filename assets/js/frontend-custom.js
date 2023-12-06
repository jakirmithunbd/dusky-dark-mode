!function (e) { const t = document.querySelectorAll(".dusky-toggle-wrap.dusky-active-auto"), o = dusky_localize, s = e(".dusky-toggle-wrap.dusky-floting.dusky-custom"); if (s.length > 0) { s.css("--dusky-toggle-bottom-offset", `${o.toggleBottomOffset}px`); const e = "left" === o.toggleCustomPosition ? o.toggleSideOffset + "px" : "inherit", t = "right" === o.toggleCustomPosition ? o.toggleSideOffset + "px" : "inherit"; s.css("--dusky-toggle-left-side-offset", e), s.css("--dusky-toggle-right-side-offset", t) } const d = e(".dusky-toggle-wrap.dusky-floting.dusky-toggle-custom"); function r() { const e = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light", t = document.querySelector("[data-dusky_mode]"); let s = u(); return s || (s = t.dataset.dusky_mode), o.autoOsMatch ? e : s } function a(e = !1) { let s; const { urlParameter: d, keyShortcut: a, flotingToggle: n } = o; function i() { if (e && d) { const e = window.location.search; if (e) { const t = new URLSearchParams(e), o = t.get("darkmode"), s = t.get("lightmode"); if (1 == o) return "dark"; if (1 == s) return "light" } return !1 } return !1 } i() && (s = i(), c("duskyf-mode", s, 1)), e && function () { const e = new Date, t = o.startDarkTimer.split(":"), s = o.endDarkTimer.split(":"), d = new Date(e); d.setHours(t[0], t[1], 0, 0); const r = new Date(e); r.setHours(s[0], s[1], 0, 0), s[0] - t[0] <= 0 && (d.getHours() <= e.getHours() ? r.setDate(r.getDate() + 1) : e.getHours() <= r.getHours() && d.setDate(d.getDate() - 1)); return e >= d && e <= r }() && o.timeBasedDark && !i() && (s = "dark", c("duskyf-mode", s, 1)); const l = u("duskyf-mode"); if (n && !l) { const e = `dusky-active-${r()}`; t?.forEach((t => { t.classList.remove("dusky-active-auto"), t.classList.add(e); document.querySelector("[data-dusky_mode]").dataset.dusky_mode = r(), s = r() })) } if (a && !s && !n && l && (s = l), l) { const e = `dusky-active-${l}`, t = document.querySelectorAll(".dusky-toggle-wrap"); t?.forEach((t => { t.classList.remove("dusky-active-auto", "dusky-active-dark", "dusky-active-light"), t.classList.add(e); document.querySelector("[data-dusky_mode]").dataset.dusky_mode = l, s = l })) } return s } function c(e, t, o) { var s = ""; if (o) { var d = new Date; d.setTime(d.getTime() + 24 * o * 60 * 60 * 1e3), s = "; expires=" + d.toUTCString() } document.cookie = e + "=" + t + s + "; path=/" } function u(e) { const t = `; ${document.cookie}`.split(`; ${e}=`); if (2 === t.length) return t.pop().split(";").shift() } d.length > 0 && d.css("--dusky-toggle-custom-size", o.customToggleSize + "px"), u("duskyf-mode") || c("duskyf-mode", r(), 1); const n = document.querySelector(".dusky-toggle-wrap"); function i(e = "", t = !1) { const { ColorBrightness: s, ColorContrast: d, ColorGrayscale: r, ColorSepia: a, excludeElements: c, isExcludeElements: u, urlParameter: n, presetTextColor: i, presetBGColor: l, colorMode: m, customTextColor: g, customBGColor: k, customCssLightMode: y, customCssDarkMode: f, changeFonts: h, fontFamily: S, customTextStroke: p } = o; if (n && t) { const t = window.location.search; if (t) { const o = new URLSearchParams(t), s = o.get("darkmode"), d = o.get("lightmode"); s ? e = "dark" : d && (e = "light") } } if ("dark" === e) { if (u && c.length > 0) { c.filter((e => "" !== e)).forEach((e => { document.querySelectorAll(e).forEach((e => { e.classList.add("dusky-ignore") })) })) } const e = { mode: 1, brightness: s, contrast: d, grayscale: r, sepia: a, useFont: h, fontFamily: S, textStroke: p, stylesheet: "", selectionColor: "auto", lightColorScheme: "Default", darkColorScheme: "Default", immediateModify: !0, excludes: ".dusky-ignore" }; "preset" === m ? (e.darkSchemeBackgroundColor = l, e.darkSchemeTextColor = i) : "custom" === m && (e.darkSchemeBackgroundColor = k, e.darkSchemeTextColor = g), DUSKY.enable(e) } else "light" === e && DUSKY.disable(); const C = document.getElementById("dusky-custom-css"); C && C.remove(); const w = document.createElement("style"); w.id = "dusky-custom-css", w.innerHTML = "dark" === e ? f : y, document.head.appendChild(w) } function l() { const e = a(), t = "dark" === e ? "light" : "dark", o = "dark" === e ? "dark" : "light", s = document.querySelector("[data-dusky_mode]"); if (s) { s.dataset.dusky_mode = t; const e = `dusky-active-${t}`; s.classList.remove(`dusky-active-${o}`), s.classList.add(e) } c("duskyf-mode", t, 1), i(t) } n && n.addEventListener("click", (() => l())), o.keyShortcut && window.addEventListener("keydown", (function (e) { "d" === e.key && e.altKey && e.ctrlKey && l() })), i(a(!0), !0) }(jQuery);