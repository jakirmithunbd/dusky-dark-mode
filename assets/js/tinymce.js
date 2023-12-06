(function () {
    window.tinyMCE &&
        tinyMCE.PluginManager.add("dusky_tinymce_js", function (editor) {
            const isAdminDark = dusky_localize.admin_settings.adminDark;
            const isClassicEditorDarkMode =
                dusky_localize.admin_settings.classicEditorDarkMode;

            if (isAdminDark && isClassicEditorDarkMode) {
                editor.on("init", function () {
                    document.addEventListener("dusky:enable", function () {
                        const body = editor.getBody();
                        body.parentNode.setAttribute(
                            "data-dusky-dark-mode",
                            "dark",
                        );
                        // body.style.setProperty("--dusky-background", "#000");
                        // body.style.setProperty("--dusky-text", "#fff");
                    });
                    document.addEventListener("dusky:disable", function () {
                        editor
                            .getBody()
                            .parentNode.removeAttribute("data-dusky-dark-mode");
                    });
                });
            }
        });
})();
