(function () {
    var c = {
        getSelection: function () {
            var e = this.jquery ? this[0] : this;
            return(('selectionStart'in e && function () {
                var l = e.selectionEnd - e.selectionStart;
                return{
                    start: e.selectionStart,
                    end: e.selectionEnd,
                    length: l,
                    text: e.value.substr(e.selectionStart, l)
                };
            }) || (document.selection && function () {
                e.focus();
                var r = document.selection.createRange();
                if (r === null) {
                    return{
                        start: 0,
                        end: e.value.length,
                        length: 0
                    };
                }
                var a = e.createTextRange();
                var b = a.duplicate();
                a.moveToBookmark(r.getBookmark());
                b.setEndPoint('EndToStart', a);
                return{
                    start: b.text.length,
                    end: b.text.length + r.text.length,
                    length: r.text.length,
                    text: r.text
                };
            }) || function () {
                return{
                    start: 0,
                    end: e.value.length,
                    length: 0
                };
            })();
        },
        replaceSelection: function () {
            var e = this.jquery ? this[0] : this;
            var a = arguments[0] || '';
            return(('selectionStart'in e && function () {
                e.value = e.value.substr(0, e.selectionStart) + a + e.value.substr(e.selectionEnd, e.value.length);
                return this;
            }) || (document.selection && function () {
                e.focus();
                document.selection.createRange().text = a;
                return this;
            }) || function () {
                e.value += a;
                return this;
            })()
        }
    };

    jQuery.each(c, function (i) {
        jQuery.fn[i] = this
    })
})();

function Translator() {
    var currentMousePos = {x: -1, y: -1};
    var hoverMousePos = {x: -1, y: -1};
    var orphanScript = '/translator/orphans';
    var beginautotranslateScript = '/translator/begin_autotranslate';
    var endautotranslateScript = '/translator/end_autotranslate';
    var dataUrl = '/translator/save_site_params';
    var translatorUrl = '/translator/save_translations';
    var machineTranslateUrl = '/translator/translate';
    var sConfirmSave = _jsb('Do you want to save your translations?');
    var sSave = _jsb('Save');
    var sClose = _jsb('Close');
    var sParameter = _jsb('Replace selection with a parameter');
    //var sUpLevel = _jsb("Translate the containing element");
    var sNextSibling = _jsb("Translate the next element");
    var sPrevSibling = _jsb("Translate the previous element");
    var sTextView = _jsb("Text view");
    var sHtmlView = _jsb("Html view");
    var sMoveNext = _jsb("Move after the following element");
    var sMoveBefore = _jsb("Move before the previous element");
    var sSkipTranslated = _jsb("You are viewing only not yet translated items");
    var sIncludeTranslated = _jsb("You are viewing both translated and not translated items");
    var sTranslating = _jsb("Current translate unit: ");
    var sNoTranslations = _jsb("This element has nothing to translate.");
    var sNoTransHint = _jsb("If you were expecting something, perhaps all translations have been done and you are filtering translated items, or maybe you are filtering nested items.");
    var sPopup = _jsb("Show items to translate in a separate window");
    var sNoPopup = _jsb("Show items to translate in the web page window");
    var sAlertPopup = _jsb("Before using this functionality please be sure that your browser doesn't block popups for this site; do you want to continue?");
    var sIgnoreTranslation = _jsb("This translation unit is NOT to translate");
    var sDoNotIgnoreTranslation = _jsb("This translation unit is to translate");
    var sPageTranslation = _jsb("This translation unit si valid only for this page");
    var sNoPageTranslation = _jsb("This translation unit is valid for the entire site");
    var sOrphanWindowTitle = _jsb("Orphan translations");
    var sNoOrphans = _jsb("There are no orphan translations");
    var sAutoSave = _jsb("Autosave enabled");
    var sNoAutoSave = _jsb("Autosave disabled");
    var sManage = _jsb("Manage site translations");
    var sLogoff = _jsb("Log off");
    var sMachineTranslation = _jsb("Machine translation");
    var skipTranslatedCookie = "wlSkipTranslated";
    var trHeightCookie = "wlTranslatorHeight";
    var autosaveCookie = "wlAutosave";
    var popupCookie = "wlPopup";
    var handle = null;
    var btnSave = null;
    var btnSkip = null;
    var btnNext = null;
    var btnIgnore = null;
    var btnPageSpecific = null;
    var btnAutoSave = null;
    var btnPopup = null;
    var modified = false;
    var accelerators = [];
    var currentTU = null;
    var tr = this;
    var translatorWindow = null;
    var translatorFrame = null;
    var jOrphansTable = null;
    var orphansWindow = null;
    var flasher = null;
    var addedBodySpace = 0;
    var skip = _jsbGetCookie(skipTranslatedCookie) == "true";
    var autosave = _jsbGetCookie(autosaveCookie) == "true";
    var popup = _jsbGetCookie(popupCookie) == "true";
    var jqueryuicss = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css";
    this.onManage = function () {
        alert("Ciao");
    };
    tr.addCss(jqueryuicss);
    tr.showButton('jsb_login', false);
    tr.setThis(this); // per farlo puntare all'oggetto derivato

    var inlines = {
        "a": true,
        "abbr": true,
        "acronym": true,
        "address": false,
        "applet": true,
        "area": false,
        "b": true,
        "base": false,
        "basefont": false,
        "bdo": true,
        "bgsound": false,
        "big": true,
        "blink": true,
        "blockquote": false,
        "body": false,
        "br": true,
        "button": true,
        "caption": false,
        "center": false,
        "cite": true,
        "code": true,
        "col": false,
        "colgroup": false,
        "dd": false,
        "del": true,
        "dfn": true,
        "dir": false,
        "div": false,
        "dl": false,
        "dt": false,
        "em": true,
        "embed": true,
        "face": true,
        "fieldset": false,
        "font": true,
        "form": false,
        "frame": false,
        "frameset": false,
        "h1": false,
        "h2": false,
        "h3": false,
        "h4": false,
        "h5": false,
        "h6": false,
        "head": false,
        "hr": false,
        "html": false,
        "i": true,
        "ia": false,
        "iframe": true,
        "img": true,
        "input": true,
        "ins": true,
        "isindex": false,
        "kbd": true,
        "label": true,
        "legend": false,
        "li": false,
        "link": false,
        "listing": false,
        "map": true,
        "marquee": false,
        "menu": false,
        "meta": false,
        "nobr": true,
        "noembed": false,
        "noframes": false,
        "noscript": false,
        "object": true,
        "ol": false,
        "optgroup": false,
        "option": false,
        "p": false,
        "param": true,
        "plaintext": false,
        "pre": false,
        "q": true,
        "rb": true,
        "rbc": true,
        "rp": true,
        "rt": true,
        "rtc": true,
        "ruby": true,
        "s": true,
        "samp": true,
        "script": false,
        "select": true,
        "small": true,
        "spacer": true,
        "span": true,
        "strike": true,
        "strong": true,
        "style": false,
        "sub": true,
        "sup": true,
        "symbol": true,
        "table": false,
        "tbody": false,
        "td": false,
        "textarea": true,
        "tfoot": false,
        "th": false,
        "thead": false,
        "title": false,
        "tr": false,
        "tt": true,
        "u": true,
        "ul": false,
        "var": true,
        "wbr": true,
        "xml": false,
        "xmp": false,
        "#text": true};
    this.closestTUElement = function (el)
    {
        if (!tr.isInline(el))
        {
            return getTranslationUnit(el) ? el : null;
        }
        return tr.closestTUElement(el.parentNode);
    };
    function FlashAnimator(e)
    {
        var flashingPeriod = 350;
        var el = e;
        var stopped = false;
        var timer = null;
        this.stop = function () {
            stopped = true;
            clearTimeout(timer);
            el.removeClass("jsb_translateCurrent");
        };
        function pulse()
        {
            //la aggiungo e la rimuovo
            el.switchClass("dummy", "jsb_translateCurrent", flashingPeriod, null, function () {
                el.switchClass("jsb_translateCurrent", "dummy", flashingPeriod);
            });
            //se non mi devo fermare, richiamo il metodo ricorsivamente
            if (!stopped)
                timer = setTimeout(pulse, flashingPeriod * 2);
        }

        pulse();
    }
    function Accelerator(f, c, a, s, k)
    {
        var ctrl = c;
        var alt = a;
        var shift = s;
        var keyCode = k;
        var fn = f;
        this.execute = function (e) {
            if (e.altKey == alt && e.ctrlKey == ctrl && e.shiftKey == shift && keyCode == e.keyCode) {
                fn();
                return true;
            }
            return false;
        }
        this.getDescription = function () {
            var s = "(";
            if (ctrl)
                s += "CTRL+";
            if (alt)
                s += "ALT+";
            if (shift)
                s += "SHIFT+"
            s += getKeyDescription();
            s += ")";
            return s;
        }
        function getKeyDescription()
        {
            switch (keyCode)
            {
                case 27:
                    return 'ESC';
                case 37:
                    return 'LEFT';
                case 38:
                    return 'UP';
                case 39:
                    return 'RIGHT';
                case 40:
                    return 'DOWN';
            }
            return String.fromCharCode((96 <= keyCode && keyCode <= 105) ? keyCode - 48 : keyCode);
        }
    }

    this.translatorTableOpened = function () {
        return handle;
    };
    function isAutoSave() {
        return autosave;
    }
    function getTranslatorWindow()
    {
        if (translatorWindow && !translatorWindow.closed)
            return translatorWindow;
        if (translatorFrame)
            return translatorFrame.contentWindow;
        return null;
    }
    function getTranslatorDocument() {
        var wnd = getTranslatorWindow();
        return wnd ? wnd.document : null;
    }

    function changeTargetText() {
        var baseText = this.rowObjs.baseText;
        var targetText = this;
        targetText.value = adjustTranslation(baseText.value, targetText.value);
        var tuEl = getTranslationElement(this.rowObjs.targetEl, baseText.value);
        if (tuEl)
        {
            tuEl.target = targetText.value;
            tuEl.tu.saveTranslations(tr.getTranslations(), tr.getIgnores());
        }
        jQuery("textarea.baseInput", handle).each(function () {
            if (this == baseText)
                return;
            if (this.value == baseText.value)
                this.rowObjs.targetText.value = targetText.value;
        });
        tr.applyTranslations();
        if (!isAutoSave())
            setModified(true);
    }
    function toggleIgnoreTU() {
        if (!currentTU)
            return;
        currentTU.ignore = !currentTU.ignore;
        currentTU.saveTranslations(tr.getTranslations(), tr.getIgnores());
        tr.applyTranslations();
        setIgnoreStateProperties();
        if (!isAutoSave())
            setModified(true);
    }
    function togglePageSpecificTU()
    {
        if (!currentTU)
            return;
        currentTU.specific = !currentTU.specific;
        setPageSpecificStateProperties();
        if (!isAutoSave())
            setModified(true);
    }

    function setBtnSaveImage()
    {
        if (btnSave) {
            if (modified)
                btnSave.attr('src', tr.getJsbDomain() + '/img/savered.png');
            else
                btnSave.attr('src', tr.getJsbDomain() + '/img/savegreen.png');
        }
    }
    function setModified(bSet) {
        modified = bSet != false;
        setBtnSaveImage();
    }


    function createTranslatorTable() {
        function getLanguage(locale) {
            return locale.split('-')[0];
        }
        if (handle)
        {
            if (!handle.is(':visible')) {
                handle.show();
            }
            return handle;
        }
        var trnH = parseInt(_jsbGetCookie(trHeightCookie));
        if (!trnH || isNaN(trnH))
            trnH = 200;
        if (popup)
        {
            translatorWindow = window.open("", "", "location=no, menubar=no");
            var h = getTranslatorDocument().getElementsByTagName("head")[0];
            jQuery("<title class='inlineTranslatorTitle'/>", getTranslatorDocument()).appendTo(h);
            jQuery(translatorWindow).unload(function () {
                if (flasher)
                    flasher.stop();
                if (handle && handle[0].ownerDocument == translatorWindow.document)
                    handle = null;
                translatorWindow = null;
            });
        } else
        {
            var f = jQuery("<div id='jsbabelTranslatorFrame' style='z-index:10000;position: fixed;width: 100%;bottom: 0px;background-color: orange;'><iframe style='height: 100%;width: 100%;'/></div>")
                    .appendTo(jQuery(document.body));
            f
                    .height(trnH)
                    .resizable({
                        handles: "n",
                        start: function () {
                            jQuery(translatorFrame).hide();
                        },
                        resize: function () {
                            var $this = jQuery(this);
                            $this.css({
                                top: "auto",
                                width: "100%"
                            });
                        },
                        stop: function () {
                            jQuery(translatorFrame).show();
                            _jsbSetCookie(trHeightCookie, jQuery(this).height(), 365);
                        }
                    });
            translatorFrame = jQuery("iframe", f)[0];
            var doc = getTranslatorDocument();
            doc.open(); // altrimenti in IE il body è null
            doc.close();
        }

        tr.addCss("/css/translator.css", getTranslatorDocument());
        tr.addCss("/css/babel.css", getTranslatorDocument());
        tr.addCss(jqueryuicss, getTranslatorDocument());
        handle = jQuery(
                '<div class="jsb_notranslate inlineTranslator">' +
                '<table border="0" cellpadding="0" cellspacing="0">' +
                '<thead><tr><td colspan=3 class="inlineTranslatorTitle"/></tr><tr><td class="buttons" colspan=3/></tr></thead>' +
                '</table>' +
                '<div class="translatorcontentcontainer">' +
                '<div class="notranslations"><h2/><p/></div>' +
                '<table border="0" cellpadding="0" cellspacing="0" class="translatorcontent"><tbody/></table></div>' +
                '</div>', getTranslatorDocument())
                .appendTo(getTranslatorDocument().body);
        var cnt = jQuery(".translatorcontentcontainer", handle);
        jQuery(getTranslatorWindow()).resize(function () {
            cnt.height(jQuery(this).height() - 80);
        });
        handle
                .attr('lang', getLanguage(tr.getBaseLocale()))
                .defaultView = doTextView;
        jQuery('.notranslations h2', handle).text(sNoTranslations);
        jQuery('.notranslations p', handle).text(sNoTransHint);
        var tdButtons = jQuery('.buttons', handle);
        handle.keydown(function (e) {
            for (var i = 0; i < accelerators.length; i++) {
                var accelerator = accelerators[i];
                if (accelerator.execute(e))
                {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
            }
        });
        function addToolSeparator()
        {
            jQuery('<span>&nbsp;</span>', getTranslatorDocument()).appendTo(tdButtons);
        }
        function addToolButton(id, image, title, f, ctrl, alt, shift, key)
        {
            var img = jQuery('<img id="' + id + '"/>', getTranslatorDocument()).appendTo(tdButtons);
            if (image)
                img.attr('src', tr.getJsbDomain() + '/img/' + image + '.png');
            img.click(f);
            if (key)
            {
                var acc = new Accelerator(f, ctrl, alt, shift, key);
                accelerators.push(acc);
                img.accelerator = acc;
                title += (" " + acc.getDescription());
            }
            img.attr('title', title);
            return img;
        }
        function addTab(id, title, f, left)
        {
            var anchor = jQuery('<a id="' + id + '"/>', getTranslatorDocument()).appendTo(tdButtons);
            anchor.attr('title', title);
            anchor.text(title);
            anchor.click(f);
            anchor.css("left", left + 'px');
        }
        addTab("textView", sTextView, doTextView, 0);
        addTab("htmlView", sHtmlView, doHtmlView, 90);
        addToolButton("jsbclose", "close", sClose, closeTranslatorTable, false, false, false, 27);
        //addToolButton("jsbparam", "parameter", sParameter, addParameter, true, false, false, 80);
        addToolButton("jsbmachine", "automatic", sMachineTranslation, machineTranslation, true, false, false, 76);
        addToolButton("jsbprevSibling", "prev", sPrevSibling, prevSibling, true, false, false, 37);
        btnNext = addToolButton("jsbnextSibling", "next", sNextSibling, nextSibling, true, false, false, 39);
        btnSkip = addToolButton("jsbskip", "", "", skipTranslated, true, false, false, 75);
        setSkipButtonProperties();
        btnPopup = addToolButton("jsbpopup", "", "", togglePopupWindow, true, false, false, 85);
        setPopupButtonProperties();
        if (!tr.isDemoMode())
        {
            addToolButton("jsbOrphans", "orphans", sOrphanWindowTitle, showOrphans, true, false, false, 79)
            btnAutoSave = addToolButton("jsbautosave", "autosave", sAutoSave, toggleAutosave, true, false, false, 65);
            btnSave = addToolButton("jsbsave", "savegreen", sSave, tr.globalSave, true, false, false, 83);
            setAutosaveButtonProperties();
            setBtnSaveImage();
            addToolButton("jsbmanage", "edit", sManage, tr.onManage, true, false, false, 77);
            addToolButton("jsblogoff", "logoff", sLogoff, tr.logoff, true, false, false, 27);
        }
        addToolSeparator();
        btnIgnore = addToolButton("jsbIgnore", "translate", sIgnoreTranslation, toggleIgnoreTU, true, false, false, 73);
        btnPageSpecific = addToolButton("jsbSpecific", "page", sPageTranslation, togglePageSpecificTU, true, false, false, 81);
        var jBody = jQuery(document.body);
        if (popup)
        {
            if (addedBodySpace)
                jBody.height(jBody.height() - addedBodySpace);
        } else
        {
            jBody.height(jBody.height() + trnH);
            addedBodySpace = trnH;
        }
        return handle;
    }

    function doHtmlView()
    {
        jQuery('.textRow', handle).hide();
        jQuery('#textView', handle).removeClass('selected');
        jQuery('.htmlRow', handle).show();
        jQuery('#htmlView', handle).addClass('selected');
        handle.defaultView = doHtmlView;
        // do il fuoco al primo textarea
        jQuery('textarea:first', handle).focus();
    }
    function doTextView()
    {
        jQuery('.htmlRow', handle).hide();
        jQuery('#htmlView', handle).removeClass('selected');
        jQuery('.textRow', handle).show();
        jQuery('#textView', handle).addClass('selected');
        handle.defaultView = doTextView;
        jQuery('textarea.targetInput:visible', handle).first().focus();
    }
    function hideUnwantedMoveArrows()
    {
        var jRows = jQuery("tr.textRow", handle);
        //the last row is for 'tab' arrow, not for translations
        for (var i = 0; i < jRows.length - 1; i++) {
            jQuery('.moveprev', jRows[i]).show();
            jQuery('.movenext', jRows[i]).show();
            if (i === 0)
            {
                jQuery('.moveprev', jRows[i]).hide();
            }
            if (i === jRows.length - 2)
            {
                jQuery('.movenext', jRows[i]).hide();
            }
        }
    }
    function movePrev()
    {
        var jRow = jQuery(this).closest("tr.textRow");
        var elementToMove = jRow[0].associatedEl;
        if (!elementToMove)
            return;
        var jPrevRow = jRow.prev();
        if (jPrevRow.size() === 0)
            return;
        var prevEl = jPrevRow[0].associatedEl;
        if (!prevEl)
            return;
        while (true)
        {
            var jTmp = jPrevRow.prev();
            if (jTmp.size() === 0 || !jTmp[0] || !jTmp[0].associatedEl || jTmp[0].associatedEl != prevEl)
                break;
            jPrevRow = jTmp;
        }
        jRow.insertBefore(jPrevRow);
        adjustTUPositions(jRow.parent());
        if (!isAutoSave())
            setModified(true);
        hideUnwantedMoveArrows();
    }
    function moveNext()
    {
        var jRow = jQuery(this).closest("tr.textRow");
        var elementToMove = jRow[0].associatedEl;
        if (!elementToMove)
            return;
        var jNextRow = jRow.next();
        if (jNextRow.size() === 0)
            return;
        var nextEl = jNextRow[0].associatedEl;
        if (!nextEl)
            return;
        while (true)
        {
            var jTmp = jNextRow.next();
            if (jTmp.size() === 0 || !jTmp[0] || !jTmp[0].associatedEl || jTmp[0].associatedEl != nextEl)
                break;
            jNextRow = jTmp;
        }
        jRow.insertAfter(jNextRow);
        adjustTUPositions(jRow.parent());
        if (!isAutoSave())
            setModified(true);
        hideUnwantedMoveArrows();
    }
    function machineTranslation()
    {
        jQuery(".targetInput", handle).each(function (n) {
            var jThis = jQuery(this);
            if (jThis.val() || jThis.is('[readonly]'))
                return;
            var base = jQuery(this.rowObjs.baseText).val();
            if (!base)
                return;
            
            var url = tr.getJsbDomain() + machineTranslateUrl + "?jsoncallback=?";
            jQuery.ajax({
                dataType: "jsonp",
                url: url,
                data: {
                    text: base,
                    from: tr.getBaseLocale(),
                    to: tr.getTargetLocale()
                },
                success: function (data) {
                    if (data && data.success)
                        jThis.val(data.value).trigger("change");
                    endWait();
                }, beforeSend: beginWait
            });


        });
    }
    function beginWait()
    {
        var s = '<img id = "_jsbWait" src="' + tr.getJsbDomain() + '/img/wait.gif" />';
        jQuery(s).appendTo(jQuery(".buttons", handle));
    }
    function endWait()
    {
        jQuery("#_jsbWait", handle).remove();
    }
    function move(ar, old_index, new_index) {
        if (new_index >= ar.length) {
            var k = new_index - ar.length;
            while ((k--) + 1) {
                ar.push(undefined);
            }
        }
        ar.splice(new_index, 0, ar.splice(old_index, 1)[0]);
    }

    function adjustTUPositions(jTable)
    {
        var jRows = jQuery("tr.textRow", jTable);
        for (var originalIdx = 0; originalIdx < currentTU.length; originalIdx++)
        {
            var tuEl = currentTU[originalIdx];
            var newIdx = -1;
            for (var i = 0; i < jRows.length; i++) {
                if (jRows[i].associatedEl === tuEl.getRootElement())
                {
                    newIdx = i;
                    break;
                }
            }
            tuEl.position = (newIdx === originalIdx) ? null : newIdx;
        }
        currentTU.saveTranslations(tr.getTranslations(), tr.getIgnores());
        tr.applyTranslations();
    }
    function setSkipButtonProperties()
    {
        btnSkip.attr("src", tr.getJsbDomain() + (skip ? "/img/skip.png" : "/img/noskip.png"));
        btnSkip.attr("title", (skip ? sSkipTranslated : sIncludeTranslated) + (" " + btnSkip.accelerator.getDescription()));
    }
    function setIgnoreStateProperties()
    {
        if (!currentTU)
            return;
        btnIgnore.attr("src", tr.getJsbDomain() + (currentTU.ignore ? "/img/notranslate.png" : "/img/translate.png"));
        btnIgnore.attr("title", (currentTU.ignore ? sIgnoreTranslation : sDoNotIgnoreTranslation) + (" " + btnIgnore.accelerator.getDescription()));
        jQuery(".targetInput", handle).each(function (n) {
            var jThis = jQuery(this);
            if (jQuery(this.rowObjs.tuItem.getRootElement()).hasClass('jsb_var'))
                jThis.prop('readonly', true).css("background-color", '#D5D5FF');
            else
                jThis.prop('readonly', currentTU.ignore).css("background-color", currentTU.ignore ? '#D5D5D5' : '');
        });
    }

    function setPageSpecificStateProperties()
    {
        if (!currentTU)
            return;
        btnPageSpecific.attr("src", tr.getJsbDomain() + (currentTU.specific ? "/img/page.png" : "/img/site.png"));
        btnPageSpecific.attr("title", (currentTU.specific ? sPageTranslation : sNoPageTranslation) + (" " + btnPageSpecific.accelerator.getDescription()));
    }
    function skipTranslated()
    {
        skip = !skip;
        _jsbSetCookie(skipTranslatedCookie, skip, 365);
        setSkipButtonProperties();
        if (currentTU)
            translateTU(currentTU);
    }
    function setAutosaveButtonProperties()
    {
        btnAutoSave.attr("src", tr.getJsbDomain() + (autosave ? "/img/autosave.png" : "/img/noautosave.png"));
        btnAutoSave.attr("title", (autosave ? sAutoSave : sNoAutoSave) + (" " + btnAutoSave.accelerator.getDescription()));
        if (autosave)
            btnSave.hide();
        else
            btnSave.show();
    }

    function toggleAutosave()
    {
        autosave = !autosave;
        _jsbSetCookie(autosaveCookie, autosave, 365);
        setAutosaveButtonProperties();
    }

    function setPopupButtonProperties()
    {
        btnPopup.attr("src", tr.getJsbDomain() + (popup ? "/img/nopopup.png" : "/img/popup.png"));
        btnPopup.attr("title", (popup ? sNoPopup : sPopup) + (" " + btnPopup.accelerator.getDescription()));
    }
    function togglePopupWindow()
    {
        if (!popup && !confirm(sAlertPopup))
            return;
        var tu = currentTU ? currentTU : null;
        closeTranslatorTable();
        popup = !popup;
        setPopupButtonProperties();
        if (tu)
            translateTU(tu, function ()
            {
                _jsbSetCookie(popupCookie, popup, 365); //solo se tutto va bene, imposto il cookie per rendere persistente lo stato
            });
    }

    function translateNext(offset)
    {
        if (!currentTU)
            return;
        var units = tr.getTranslationUnits();
        var i = tr.getTranslationUnits().indexOf(currentTU) + offset;
        if (i >= 0 && i < units.length)
        {
            translateTU(units[i]);
        } else if (offset < 1) //moving backward, when I reach first a go to last
        {
            translateTU(units[units.length - 1]);
        } else
        {
            translateTU(units[0]);
        }
    }
    function nextSibling()
    {
        translateNext(1);
    }
    function prevSibling()
    {
        translateNext(-1);
    }


    function adjustTranslation(b, t)
    {
        return t;
        var beginningSpaces = /^(\s*)/gm;
        var endingSpaces = /(\s*)$/gm;
        var adjust = false;
        var prefix = "";
        var matches = b.match(beginningSpaces);
        if (matches && matches.length > 0) {
            prefix = matches[0];
            var targetMatches = t.match(beginningSpaces);
            if (!targetMatches) {
                adjust = true;
            } else if (targetMatches.length == 0) {
                adjust = true;
            } else if (targetMatches[0] != prefix)
            {
                adjust = true;
            }
        }

        matches = b.match(endingSpaces);
        var suffix = "";
        if (matches && matches.length > 0) {
            suffix = matches[0];
            var targetMatches = t.match(endingSpaces);
            if (!targetMatches) {
                adjust = true;
            }
            if (targetMatches.length == 0) {
                adjust = true;
            } else if (targetMatches[0] != suffix)
            {
                adjust = true;
            }
        }

        return adjust
                ? prefix + jQuery.trim(t) + suffix
                : t;
    }

    function showOrphans()
    {
        if (orphansWindow && !orphansWindow.closed)
        {
            orphansWindow = window.open("", "orphans", "location=no, menubar=no");
        } else
        {
            orphansWindow = window.open("", "orphans", "location=no, menubar=no");
            orphansWindow.document.title = sOrphanWindowTitle;
            tr.addCss("/css/common.css", orphansWindow.document);
            tr.addCss("/css/translator.css", orphansWindow.document);
            tr.addCss("/css/babel.css", orphansWindow.document);
            tr.addCss(jqueryuicss, orphansWindow.document);
            jQuery(orphansWindow).unload(function () {
                jOrphansTable = null;
                orphansWindow = null;
            });
            jOrphansTable = jQuery(
                    '<div class="jsb_notranslate inlineTranslator">' +
                    '<h1></h1>' +
                    '<div class="translatorcontentcontainer">' +
                    '<div class="notranslations"><h2/><p/></div>' +
                    '<table border="0" cellpadding="0" cellspacing="0" class="translatorcontent"><tbody/></table></div>' +
                    '</div>', orphansWindow.document)
                    .appendTo(orphansWindow.document.body);
            jQuery('h1', jOrphansTable).html(sOrphanWindowTitle);
        }

        tr.addScript(orphanScript + '?src='
                + encodeURIComponent(tr.getPageUrl()) + '&loc='
                + encodeURIComponent(tr.getTargetLocale()));
    }
    this.setOrphanData = function (strings)
    {
        if (!jOrphansTable)
            return;
        var localTranslations = [];
        var localIgnores = [];
        tr.parseTranslations(strings, localTranslations, localIgnores);
        var table = jQuery('.translatorcontent>tbody', jOrphansTable);
        table.empty();
        if (localIgnores.length == 0 && localTranslations.length == 0)
        {
            jQuery('.notranslations h2', jOrphansTable).text(sNoOrphans);
        } else
        {
            for (var i = 0; i < localIgnores.length; i++)
            {
                addOrphanRow(localIgnores[i], false);
            }
            for (var i = 0; i < localTranslations.length; i++)
            {
                addOrphanRow(localTranslations[i], false);
            }
        }
        function addOrphanRow(trn, ignore)
        {
            var html = "<tr class='textRow'>" +
                    "<td class='copyColumn'></td>" +
                    "<td class='baseColumn'><textarea class='baseInput' readonly='readonly'/></td>" +
                    "<td class='targetColumn'><textarea class='targetInput' readonly='readonly'/></td>" +
                    //"<td class='ignoreColumn'><input type='checkbox' tabindex='-1' class='ignoreInput'/ disabled='true'></td>" +
                    //"<td class='specificColumn'><input type='checkbox' tabindex='-1' class='specificInput' disabled='true'/></td>" +
                    "</tr>";
            var row = jQuery(html, jOrphansTable).appendTo(table);
            jQuery('.baseInput', row).text(trn.getBase());
            jQuery('.targetInput', row).text(trn.getTarget());
            jQuery('.ignoreInput', row).attr('checked', ignore);
            jQuery('.targetInput', row).attr('checked', trn.isPageSpecific());
        }
    }
    function readParameter(s)
    {
        var matches = s.match(/%\d+%/gm);
        if (matches && matches.length == 1 && matches[0] == s)
            return parseInt(s.substring(1, s.length - 1), 10);
        return null;
    }
    function addParameter() {
        var modified = false;
        var baseParamIndex = 0;
        var els = jQuery('textarea.baseInput', handle);
        els.each(function () {
            var el = jQuery(this);
            var sel = el.getSelection();
            if (sel.length === 0)
            {
                var matches = el.val().match(/(%\d+%)/gm);
                if (matches)
                    baseParamIndex += matches.length;
                return;
            }
            var i = readParameter(sel.text);
            var baseLocalIndex = baseParamIndex;
            var targetLocalIndex = baseParamIndex;
            if (i)
            {
                var base = this.rowObjs.tuItem.originalBase ? this.rowObjs.tuItem.originalBase : this.rowObjs.targetEl.nodeValue;
                var pattern = tr.createBasePattern(el.val());
                var matches = base.match(pattern);
                el.replaceSelection(matches[i - baseLocalIndex], true);
            } else
            {
                el.replaceSelection('%' + 0 + '%', true);
            }
            var b = el.val();
            b = b.replace(/(%\d+%)/gm, function (m) {
                return '%' + ++baseParamIndex + '%';
            });
            el.val(b);
            var tgtEl = jQuery(this.rowObjs.targetText);
            var t = tgtEl.val();
            t = t.replace(sel.text, '%' + 0 + '%');
            t = t.replace(/(%\d+%)/gm, function (m) {
                return '%' + ++targetLocalIndex + '%';
            });
            tgtEl.val(t);
            var tuItem = this.rowObjs.tuItem;
            tuItem.tu.removeTranslations(tr.getTranslations(), tr.getIgnores());
            tuItem.base = b;
            tuItem.target = t;
            tuItem.tu.saveTranslations(tr.getTranslations(), tr.getIgnores());
            modified = true;
        });
        if (modified)
        {
//if (isAutoSave()) TODO MARCO
//    tr.specificSave([trn], [], [], true);
            tr.applyTranslations();
            if (!isAutoSave())
                setModified(true);
        }
    }

    function isValid(element) {
        if (element == null)
            return true;
        if (tr.skipTranslate(element))
            return false;
        return isValid(element.parentNode);
    }
    function getElementSafeId(el) {
        var s = "";
        if (el && el.nodeName != '#document') {
            if (el.id)
                return el.nodeName + "#" + el.id;
            s = getElementSafeId(el.parentNode);
            if (el.nodeName == "#text")
            {
                s += "-" + tr.prepareString(el.nodeValue);
            } else
            {
                if (s.length > 0)
                    s += '>';
                s += el.nodeName;
                if (el.nodeName != 'HTML' && el.nodeName != 'HEAD' && el.nodeName != 'BODY')
                    s += ":eq(" + jQuery(el).prevAll(el.nodeName).not('.jsb_notranslate').length + ")";
            }
        }
        return s;
    }
    function getFullPath(el) {
        var s = "";
        if (el && el.nodeName != '#document') {
            s = getFullPath(el.parentNode);
            if (s.length > 0)
                s += '>';
            s += el.nodeName;
        }
        return s;
    }
    function fitToContent(text, maxHeight) {
        text.style.height = "0px";
        var adjustedHeight = text.clientHeight;
        if (!maxHeight || maxHeight > adjustedHeight) {
            adjustedHeight = Math.max(text.scrollHeight + 20, adjustedHeight);
            if (maxHeight)
                adjustedHeight = Math.min(maxHeight, adjustedHeight);
            if (adjustedHeight > text.clientHeight) {
                text.style.height = adjustedHeight + "px";
                return adjustedHeight;
            }
        }
        return text.clientHeight;
    }

    var currentHooked = null;
    var jPop = jQuery("<div class='jsb_notranslate' style='cursor:pointer;position:absolute;display:inline-block;'><img></img></div>", getTranslatorDocument())
            .appendTo(document.body)
            .click(function () {
                if (currentHooked)
                    translateTU(getTranslationUnit(currentHooked));
            });
    jQuery("img", jPop)
            .attr("src", tr.getJsbDomain() + '/img/dotranslate.png');
    var popupTimeoutId = 0;
    this.onHover = function (evt)
    {
        clearTimeout(popupTimeoutId);
        evt.preventDefault();
        evt.stopPropagation();
        var overEl = tr.closestTUElement(evt.target);
        if (!overEl || tr.isBaseLocale() || !isValid(overEl) || currentHooked === overEl)
            return;
        popupTimeoutId = setTimeout(popupHook, 1000); //un secondo
        hoverMousePos.x = currentMousePos.x;
        hoverMousePos.y = currentMousePos.y;
        function popupHook()
        {
            currentHooked = overEl;
            jPop.offset({
                top: hoverMousePos.y , //offset.top, 
                left: hoverMousePos.x //offset.left

            });
            jQuery("img", jPop)
                    .attr("title", getFullPath(currentHooked) + "\n" + _jsb("Click here to translate this element"));
            tr.moveToTop(jPop);
        }
    };
    function isInViewPort(jEl)
    {
        var win = jQuery(window);
        var viewport = {
            top: win.scrollTop(),
            left: win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();
        var bounds = jEl.offset();
        bounds.right = bounds.left + jEl.outerWidth();
        bounds.bottom = bounds.top + jEl.outerHeight();
        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
    }
    function getTranslationUnit(el)
    {
        for (var i = 0; i < tr.getTranslationUnits().length; i++)
        {
            var tu = tr.getTranslationUnits()[i];
            if (tu.owner === el)
            {
                return tu;
            }
        }
        return null;
    }
    function getTranslationElement(el, baseText)
    {
        for (var i = 0; i < tr.getTranslationUnits().length; i++)
        {
            var tu = tr.getTranslationUnits()[i];
            for (var j = 0; j < tu.length; j++)
            {
                var tuEl = tu[j];
                if (tuEl.owner == el && tuEl.base === baseText)
                    return tuEl;
            }

        }
        return null;
    }

    function translateTU(tu, callback)
    {
        try
        {
            if (!tu)
                return;
            currentTU = tu;
            var el = currentTU.owner;
            if (!el || !isValid(el))
                return;
            if (el.scrollIntoView && !isInViewPort(jQuery(el)))
                el.scrollIntoView();
            createTranslatorTable();
            forceChange(); //scateno il change sul campo che stavo editando per recepirne i cambiamenti
            if (flasher)
                flasher.stop();
            flasher = new FlashAnimator(jQuery(el));
            var rootText = getFullPath(el);
            var titleEl = jQuery('.inlineTranslatorTitle', getTranslatorDocument());
            titleEl.text(sTranslating + rootText);
            var table = jQuery('.translatorcontent>tbody', handle);
            table.empty();
            function addRow(tuItem)
            {
                var innerEl = tuItem.owner;
                var base = tuItem.base;
                var target = tuItem.target;
                var propName = tuItem.propertyName;
                var html = "<tr class='textRow'>" +
                        "<td class='jsb_notranslate titleColumn'><span class='rowtitle'/></td>" +
                        "<td class='baseColumn'><textarea tabindex=-1 class='baseInput' readonly='readonly'/></td>" +
                        "<td class='jsb_notranslate arrowColumn'></td>" +
                        "<td class='targetColumn'><textarea class='targetInput'/></td>" +
                        // "<td class ='ignoreColumn'><input type='checkbox' tabindex='-1' class='ignoreInput'/></td>" +
                        // "<td class ='specificColumn'><input type='checkbox' tabindex='-1' class='specificInput'/></td>" +
                        "</tr>";
                var objs = {};
                objs.targetEl = innerEl;
                objs.tuItem = tuItem;
                var row = jQuery(html, getTranslatorDocument())
                        .appendTo(table).each(function () {
                    this.associatedEl = tuItem.getRootElement();
                });
                var jArrowCol = jQuery(".arrowColumn", row);
                jQuery("<img class='moveprev'/>", getTranslatorDocument())
                        .appendTo(jArrowCol)
                        .attr("src", tr.getJsbDomain() + '/img/moveup.png')
                        .click(movePrev)
                        .attr("title", sMoveBefore)
                        .each(function () {
                            this.rowObjs = objs;
                        });
                jQuery("<img class='movenext'/>", getTranslatorDocument())
                        .appendTo(jArrowCol)
                        .attr("src", tr.getJsbDomain() + '/img/movedown.png')
                        .attr("title", sMoveNext)
                        .click(moveNext)
                        .each(function () {
                            this.rowObjs = objs;
                        });
                var jBase = jQuery('textarea.baseInput', row)
                        .text(base)
                        .each(function () {
                            this.rowObjs = objs;
                        });
                objs.baseText = jBase[0];
                var jTarget = jQuery('textarea.targetInput', row)
                        .text(target)
                        .each(function () {
                            this.rowObjs = objs;
                        })
                        .change(changeTargetText);
                objs.targetText = jTarget[0];
                var col = jQuery('.rowtitle', row);
                var lbl = getFullPath(innerEl).substr(rootText.length);
                if (propName) {
                    lbl += '@' + propName;
                }
                col.text(lbl);
            }


            if (!currentTU)
            {
                jQuery('.notranslations', handle).show();
            } else
            {
                jQuery('.notranslations', handle).hide();
                for (var i = 0; i < currentTU.length; i++)
                    addRow(currentTU[i]);
            }

            var row = jQuery("<tr class='htmlRow'><td colspan=5><textarea tabindex=-1 class='htmlInput' readonly='readonly'/></td></tr>", getTranslatorDocument())
                    .appendTo(table);
            var jInput = jQuery('textarea.htmlInput', row);
            jInput.text(el.outerHTML);
            jInput.each(function () {
                fitToContent(this);
            });
            var baseTexts = jQuery('textarea.baseInput', handle);
            var targetTexts = jQuery('textarea.targetInput', handle);
            baseTexts.each(function (n) {
                var l = fitToContent(this);
                var tgt = targetTexts[n];
                tgt.style.height = l + "px";
            });
            row = jQuery("<tr class='textRow'><td class='jsb_notranslate'></td><td></td><td><a href='#' title='Translate next'><img/></a></td></tr>", getTranslatorDocument())
                    .appendTo(table);
            jQuery('td', row).last().css("text-align", "right");
            jQuery('a', row).focus(nextSibling);
            jQuery('img', row)
                    .attr("title", btnNext.attr("title"))
                    .attr("src", btnNext.attr("src"))
                    .width(16)
                    .height(16);
            hideUnwantedMoveArrows();
            setPageSpecificStateProperties();
            setIgnoreStateProperties();
            handle.defaultView();
        } catch (e)
        {
            alert(e);
        } finally
        {
            if (callback)
                callback();
        }
    }

    this.saveModified = function () {
        if (modified && confirm(sConfirmSave)) {
            tr.globalSave();
            return true;
        }

        return !modified;
    };
    this.globalSave = function () {
        if (tr.isBaseLocale())
            return;
        var localTranslations = [];
        var localIgnores = [];
        for (var i = 0; i < tr.getTranslationUnits().length; i++) {
            var tu = tr.getTranslationUnits()[i];
            tu.saveTranslations(localTranslations, localIgnores);
        }

        tr.specificSave(localTranslations, localIgnores, false)
    }
    this.specificSave = function (translations, ignores, appendToExisting) {
        var data = {};
        data.src = tr.getPageUrl();
        data.targetLocale = tr.getTargetLocale();
        data.baseLocale = tr.getBaseLocale();
        data.appendToExisting = appendToExisting;
        var index = 0;
        for (var i = 0; i < translations.length; i++) {
            var trn = translations[i];
            data["b" + index] = trn.getBase();
            data["t" + index] = trn.getTarget();
            data["p" + index] = trn.isPageSpecific();
            index++;
        }
        index = 0;
        for (var i = 0; i < ignores.length; i++) {
            var trn = ignores[i];
            data["bi" + index] = trn.getBase();
            data["ti" + index] = trn.getTarget();
            data["pm" + index] = trn.isPageSpecific();
            index++;
        }
        sendData(tr.getJsbDomain() + translatorUrl, data);
        setModified(false);
    };
    function forceChange() {
        jQuery('textarea:first', handle).focus(); // forza l'onchange
    }
    function closeTranslatorTable() {
        if (flasher)
            flasher.stop();
        if (popup)
        {
            if (translatorWindow)
            {
                forceChange();
                translatorWindow.close();
                translatorWindow = null;
            }
        } else
        {
            if (handle) {
                forceChange();
                jQuery("#jsbabelTranslatorFrame").remove();
            }
        }
        handle = null;
    }
    this.autoTranslate = function () {
        if (tr.isBaseLocale())
            return;
        var missingTranslations = [];
        tr.getTranslationUnits().forEach(function (tu) {
            var b = tu.toBaseString();
            b = tr.prepareString(b);
            if (b.length === 0 || b === " ")
                return null;
            if (skipAutomaticTranslation(b))
                return null;
            for (var i = 0; i < missingTranslations.length; i++) {
                var trn = missingTranslations[i];
                if (trn.getBase() === b) {
                    continue;
                }
            }
            missingTranslations.push(tr.createTranslation(b, "", false));
        });
        function getSerializedTranslations() {
            var s = new String();
            for (var i = 0; i < missingTranslations.length; i++) {
                var t = missingTranslations[i];
                if (t.getTarget())
                    continue;
                s += t.toString(tr.TypeTextChar);
            }

            return s;
        }

        sendData(tr.getJsbDomain() + beginautotranslateScript,
                {
                    "targetLocale": tr.getTargetLocale(),
                    "baseLocale": tr.getBaseLocale(),
                    "src": tr.getPageUrl(),
                    "strings": getSerializedTranslations()
                },
                function () {
                    tr.addScript(endautotranslateScript + '?src='
                            + encodeURIComponent(tr.getPageUrl()) + '&loc='
                            + encodeURIComponent(tr.getTargetLocale()));
                });
    };
    function skipAutomaticTranslation(b)
    {
        // sia che sia nell'array delle traduzione, sia in quello da ignorare, non devo
        //applicare la traduzione automatica
        var existing = tr.getTranslation(b);
        if (existing && existing.getTarget())
            return true;
        existing = tr.getIgnore(b);
        if (existing)
            return true;
        return false;
    }
    this.addAutomaticTranslations = function (strings)
    {
        var localTranslations = [];
        var localIgnores = [];
        tr.parseTranslations(strings, localTranslations, localIgnores);
        for (var i = 0; i < localTranslations.length; i++)
        {
            var trn = localTranslations[i];
            if (skipAutomaticTranslation(trn.getBase()))
                continue;
            tr.getTranslations().push(trn);
            setModified(true);
        }
        for (var j = 0; j < localIgnores.length; j++)
        {
            var ign = localIgnores[j];
            if (skipAutomaticTranslation(ign.getBase()))
                continue;
            tr.getIgnores().push(ign);
            setModified(true);
        }
        tr.applyTranslations();
        openTranslatorAtFirst();
    }
    function openTranslatorAtFirst()
    {
        var tu = tr.getTranslationUnits().length == 0 ? null : tr.getTranslationUnits()[0];
        if (tu)
            translateTU(tu);
    }
    this.sendNewToolbarPosition = function () {
        var pos = tr.getPos();
        sendData(tr.getJsbDomain() + dataUrl, {
            "left": pos.left,
            "top": pos.top,
            "src": tr.getPageUrl()
        });
    };
    function sendData(url, obj, callback) {
        jQuery.post(url, obj, callback);
    }

    jQuery('*:not(".jsb_notranslate")').hover(tr.onHover);
    $(document).mousemove(function (event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });
    jQuery(window).unload(function () {
        if (translatorWindow && !translatorWindow.closed)
            translatorWindow.close();
        tr.saveModified();
    });
    jQuery(tr.getTranslatorToolbar())
            .draggable(
                    {
                        drag: tr.adjustFlagZIndex,
                        stop: function () {
                            tr.calculateOffset();
                            tr.adjustFlagZIndex();
                            tr.sendNewToolbarPosition();
                        }
                    })
            .addClass('translateMode');
    if (tr.isBaseLocale())
        return;
    tr.translating = true;
    if (tr.isDemoMode())
        openTranslatorAtFirst(); //apro subito il traduttore, senza traduzione automatica
    else
        tr.autoTranslate(); //la risposta chiamerà poi la openTranslatorAtFirst


}
jQuery(function () {
    try {
        Translator.prototype = window.__babel;
        window.__babel = new Translator();
        jQuery("form").submit(function (e) {
            if (window.__babel.translatorTableOpened())
                e.preventDefault();
        });
    } catch (e) {
        alert(e);
    }
}
);
