
function _jsbHexDecode(s)
{
    var r = '';
    for (var i = 0; i < s.length; i += 2)
    {
        r += unescape('%' + s.substr(i, 2));
    }
    return r;
}
function _jsbSetCookie(n, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value)
            + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = n + "=" + c_value;
}
function _jsbGetCookie(n) {
    var x, y, cookies = document.cookie.split(";");
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var idx = cookie.indexOf("=");
        x = cookie.substr(0, idx);
        y = cookie.substr(idx + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == n) {
            return unescape(y);
        }
    }
    return "";
}

function Babel() {
    this.PositionChar = '%';
    this.TypeTextChar = '*';
    this.TypeImageChar = '!';
    this.TypeIgnoreChar = ':';

    var jsbHost = "http://localhost:610/jsbabel";
    var dropUpImg = '/img/dropup.gif';
    var dropDownImg = '/img/dropdown.gif';
    var stringScript = '/translator/get_translations';
    var getLoginScript = '/translator/get_login_page';
    var translatorScript = '/js/translator.js';
    var getLogoffScript = '/translator/do_logoff';
    var translations = [];
    var missingTranslations = [];
    var translationUnits = [];
    var ignores = [];
    var jsbDomain = jsbHost;
    var trnCnt = null;
    var jTrnCnt = null;
    var tr = this;
    var trimRegExp = /[\r\n\s]+/gm;
    var baseRegExp = /[-[\]{}()*+?.,\\^$|#]|(\%\d+\%)/gm;
    var offset = 0;
    var anchor = 'C';
    var persistencyManager = typeof openDatabase === "undefined" ? null : new PersistenceManager();
    var trnDataVersion = 0;
    var trnStringsVersion = 0;
    var jqueryscript = "http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js";
    var jqueryuiscript = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js";
    var demoMode = false;
    var pageDomain = null;
    var pageUrl = null;
    var imgH = 20;
    var dropdownHeight = 0;
    this.translating = false;
    var SiteAdmin = 1;
    var SiteOwner = 2;
    var SiteTranslator = 3;
    var userRole;
    function elementFromPoint(x, y)
    {
        if (!document.elementFromPoint)
            return null;
        var doc = jQuery(document);
        x -= doc.scrollLeft();
        y -= doc.scrollTop();
        return document.elementFromPoint(x, y);
    }


    var targetLocale = _jsbGetCookie("wltargetLocale");
    var baseLocale = _jsbGetCookie("wlbaseLocale");
    this.getTranslations = function ()
    {
        return translations;
    }
    this.getIgnores = function ()
    {
        return ignores;
    }
    this.isDemoMode = function () {
        return demoMode;
    };
    this.logoff = function () {
        tr.addScript(getLogoffScript);
    };
    this.createBasePattern = function (s)
    {
        return s.replace(baseRegExp, function (m) {
            return m.length == 1 ? '\\' + m : "(.*)";
        });
    };
    function Translation(aBase, aTarget, isPageSpecific) {
        var base = aBase;
        var target = aTarget;
        var pageSpecific = isPageSpecific;
        var pattern = null;
        this.getBase = function () {
            return base;
        };
        this.setBase = function (aBase) {
            base = aBase;
            pattern = null;
        };
        this.isPageSpecific = function ()
        {
            return pageSpecific;
        };
        this.setPageSpecific = function (isPageSpecific)
        {
            pageSpecific = isPageSpecific;
        };
        this.getBasePattern = function () {
            if (!pattern)
                pattern = tr.createBasePattern(base);
            return pattern;
        };
        this.getTarget = function () {
            return target;
        };
        this.setTarget = function (aTarget) {
            target = aTarget;
        };
        this.toString = function (separator) {
            var b = encodeURIComponent(base);
            var t = encodeURIComponent(target);
            return b.length + separator + b + t.length + separator + t + (pageSpecific ? '1' : '0');
        };
    }
    function TranslationUnit()
    {
        var paramRegExp = /(\%\d+\%)/gm;
        this.specific = false;
        this.ignore = false;
        this.isTranslated = function ()
        {
            for (var i = 0; i < this.length; i++) {
                if (this[i].target)
                    return true;
            }
            return false;
        }
        this.toBaseString = function () //may differ from original because of parameters
        {
            var ret = "";
            for (var i = 0; i < this.length; i++) {
                var tuEl = this[i];
                if (jQuery(tuEl.getRootElement()).hasClass('jsb_var'))
                    ret += "%0%";
                else
                    ret += tuEl.base;
            }
            return ret;
        };
        this.toOriginalBaseString = function ()
        {
            var ret = "";
            for (var i = 0; i < this.length; i++) {
                ret += this[i].originalBase;
            }
            return ret;
        };
        this.toTargetString = function ()
        {
            var ret = "";
            for (var i = 0; i < this.length; i++) {
                var tuEl = this[i];
                if (tuEl.position != null)
                {
                    ret += tuEl.position;
                    ret += tr.PositionChar;
                }
                ret += tuEl.target.length;
                ret += tr.TypeTextChar;
                ret += tuEl.target;
            }
            return ret;
        };
        this.isScript = function ()
        {
            return (this.owner.nodeName === "SCRIPT");
        }
        this.applyTranslation = function ()
        {
            if (this.isScript())
                return;
            var trn = this.translation;
            var needMove = false;
            for (var i = 0; i < this.length; i++) {
                var tuEl = this[i];
                var translated = tuEl.target;//.replace(paramRegExp, replaceParam);
                if (tuEl.propertyName)
                {
                    if (!tuEl.originalBase)
                        tuEl.originalBase = tuEl.owner[tuEl.propertyName];

                    tuEl.owner[tuEl.propertyName] = translated ? translated : tuEl.originalBase;
                } else
                {
                    if (!tuEl.originalBase)
                        tuEl.originalBase = tuEl.owner.nodeValue;

                    tuEl.owner.nodeValue = translated ? translated : tuEl.originalBase;
                    ;
                }

                if (tuEl.position != null)
                {
                    needMove = true;
                    tuEl.sortingPosition = tuEl.position;
                } else
                {
                    tuEl.sortingPosition = i;
                }
            }

            if (needMove)
            {
                var itemsArr = [];
                for (var i = 0; i < this.length; i++)
                    itemsArr.push(this[i]);
                itemsArr.sort(function (a, b) {
                    return a.sortingPosition === b.sortingPosition
                            ? 0
                            : (a.sortingPosition > b.sortingPosition ? 1 : -1);
                });
                for (i = 0; i < itemsArr.length; ++i) {
                    this.owner.appendChild(itemsArr[i].getRootElement());
                }

            }

            function replaceParam(m) {
                try {
                    var idx = parseInt(m.substring(1, m.length - 1), 10);
                    if (idx >= trn.matches.length || idx <= 0)
                        return m;
                    return trn.matches[idx];
                } catch (e) {
                    return m;
                }
            }
        }
        this.parseTargetString = function (s)
        {
            var currentIndex = 0, currentTUIndex = 0;
            while (currentIndex < s.length) {
                if (currentTUIndex >= this.length)
                    return;
                var tu = this[currentTUIndex++];
                var pos = null;
                tu.target = parseString(s);
                tu.position = pos;
            }

            function parseString(s) {
                var ch;
                var sb = "";
                while (true) {
                    if (currentIndex >= s.length)
                        throw "Invalid translation unit string:\r\n" + s;
                    ch = s.charAt(currentIndex++);
                    if (ch == tr.PositionChar)
                    {
                        pos = parseInt(sb);
                        sb = "";
                        continue;
                    } else if (ch == tr.TypeTextChar)
                    {
                        break;
                    }
                    sb += ch;
                }
                var l = parseInt(sb);
                return s.substring(currentIndex, currentIndex = currentIndex + l);
            }
        }
        this.removeTranslations = function (localTranslations, localIgnores)
        {
            if (this.ignore)
            {
                removeFromArray(this.toBaseString(), localIgnores);
            } else
            {
                removeFromArray(this.toBaseString(), localTranslations);
            }

        }

        this.saveTranslations = function (localTranslations, localIgnores)
        {
            if (this.ignore)
            {
                removeFromArray(this.toBaseString(), localTranslations);
                addToArray(this.toBaseString(), "1", this.specific, localIgnores);
            } else
            {
                removeFromArray(this.toBaseString(), localIgnores);
                if (this.isTranslated())
                    addToArray(this.toBaseString(), this.toTargetString(), this.specific, localTranslations);
            }
        }
        function addToArray(b, t, specific, ar) {
            if (b.length == 0 || b == " ")
                return null;
            for (var i = 0; i < ar.length; i++) {
                var trn = ar[i];
                if (trn.getBase() == b) {
                    trn.setTarget(t);
                    trn.setPageSpecific(specific);
                    return trn;
                }
            }
            var trn = tr.createTranslation(b, t ? t : "", specific);
            ar.push(trn);
            return trn;
        }
        function removeFromArray(b, ar)
        {
            if (b.length == 0 || b == " ")
                return false;
            for (var i = 0; i < ar.length; i++) {
                var trn = ar[i];
                if (trn.getBase() == b) {
                    ar.splice(i, 1);
                    return true;
                }
            }
            return false;
        }

    }
    TranslationUnit.prototype = [];
    function TranslationElement(tu, owner, base, propertyName)
    {
        this.tu = tu;
        this.owner = owner;
        this.base = base;//may change when adding parameters
        this.originalBase = base;
        this.target = "";
        this.position = null;
        this.propertyName = propertyName;
        this.getRootElement = function ()
        {
            var rootEl = owner;
            while (rootEl && rootEl.parentNode !== tu.owner)
                rootEl = rootEl.parentNode;
            return rootEl;
        }

    }
    this.setTranslationData = function (data, dataVersion, strings, stringsVersion) {

        missingTranslations.length = 0;
        var persist = (trnDataVersion != dataVersion) || (trnStringsVersion != stringsVersion);
        trnDataVersion = dataVersion;
        trnStringsVersion = stringsVersion;
        if (data)
            tr.applyTranslationData(data);
        if (strings)
        {
            tr.parseTranslations(strings, translations, ignores);
            tr.applyTranslations();
        }

        if (persist && persistencyManager)
            persistencyManager.persistTranslationData(
                    location.pathname, tr.getTargetLocale(), data, trnDataVersion, strings, trnStringsVersion);
    };
    this.applyTranslationData = function (trnData) {
        tr.setBaseLocale(trnData.bl);
        tr.clearFlags();
        trnData.ld.sort(function (a, b) {
            if (a.l == targetLocale)
                return -1;
            if (b.l == targetLocale)
                return 1;
            return 0;
        });
        //first one is target locale
        for (var i = 0; i < trnData.ld.length; i++) {
            var flag = trnData.ld[i];
            if (!flag.l)
                continue;
            if (!targetLocale)
                targetLocale = flag.l;
            var loc = flag.l;
            tr.addButton('jsb_' + loc, flag.u, flag.t, function () {
                tr.setTargetLocale(loc);
                location.reload();
            });
            if (i == 0)
                this.addDropdown();
            trnCnt.appendChild(document.createElement('br'));
        }
        tr.addButton('jsb_login', "/img/login.png", _jsb("Edit translations"), tr.login);
        trnCnt.style.display = "block";
        tr.setPos(trnData.a, trnData.x, trnData.y);
    };
    this.showButton = function (id, show)
    {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    };
    this.addButton = function (id, imgUrl, title, fn) {

        var img = document.createElement("img");
        img.id = id;
        img.src = jsbDomain + imgUrl;
        img.title = decodeURIComponent(title) + _jsb(" - Powered by JSBABEL");
        img.className = "jsb_imageButton jsb_notranslate";
        img.onclick = fn;
        dropdownHeight += imgH + 4;
        trnCnt.appendChild(img);
    };
    this.login = function () {
        var jLogin = jQuery("#_jsbLoginModal");
        if (jLogin.length > 0)
            jLogin.modal();
        else
            tr.addScript(getLoginScript); //will call modal() after load

    };
    this.setThis = function (th) {
        tr = th;
    };
    this.getTranslatorToolbar = function () {
        return trnCnt;
    };
    this.getTranslationUnits = function () {
        return translationUnits;
    };
    this.getJsbDomain = function () {
        return jsbDomain;
    };
    this.clearFlags = function ()
    {
        jTrnCnt.empty();
        dropdownHeight = 0;
    };
    this.addAndWait = function (condFunc, script, endFunc, targetDoc) {
        try {
            if (!condFunc())
                tr.addScript(script, targetDoc);
            function execEndFunc() {
                if (condFunc())
                    endFunc();
                else
                    setTimeout(execEndFunc, 1);
            }
            execEndFunc();
        } catch (e) {
            alert(e);
        }
    };
    this.addTranslatorScripts = function (role, demo) {
        demoMode = demo;
        userRole = role;
        tr.addAndWait(
                function () {
                    return typeof (jQuery) !== 'undefined';
                },
                jqueryscript,
                function () {
                    tr.addAndWait(
                            function () {
                                return typeof (jQuery.ui) !== 'undefined'
                                        && typeof (jQuery.ui.draggable) != 'undefined';
                            },
                            jqueryuiscript,
                            function () {
                                tr.addScript(translatorScript);
                            });
                });
    };
    this.addScript = function (src, targetDoc) {
        if (!targetDoc)
            targetDoc = document;
        var h = targetDoc.getElementsByTagName("head")[0];
        var scriptNode = targetDoc.createElement('script');
        scriptNode.type = 'text/javascript';
        scriptNode.src = src.indexOf("http") === 0 ? src : jsbDomain + src;
        h.appendChild(scriptNode);
    };
    this.addCss = function (src, targetDoc) {
        if (!targetDoc)
            targetDoc = document;
        var h = targetDoc.getElementsByTagName("head")[0];
        var cssNode = targetDoc.createElement('link');
        cssNode.type = "text/css";
        cssNode.rel = "stylesheet";
        cssNode.href = src.indexOf("http") === 0 ? src : jsbDomain + src;
        h.appendChild(cssNode);
    };
    this.setPos = function (aAnchor, left, top) {
        anchor = aAnchor;
        if ((anchor != 'L' || left > 0) && top > 0) {
            offset = left;
            trnCnt.style.top = top + "px";
        }
        onResize();
    };
    this.getPos = function () {
        return {
            "left": offset,
            "top": jTrnCnt.offset().top
        };
    };
    this.addDropdown = function () {
        var isOpen = false;
        var isOpening = false;
        var isClosing = false;
        function increase() {
            if (!isOpening)
                return;
            var ddHeight = jTrnCnt.height();
            if (ddHeight < dropdownHeight) {
                ddHeight += 10;
                jTrnCnt.height(ddHeight);
                setTimeout(increase, 10);
            } else {
                isOpen = true;
                isOpening = false;
                img.attr("src", jsbDomain + dropUpImg);
            }
        }
        function decrease() {
            if (!isClosing)
                return;
            var ddHeight = jTrnCnt.height();
            if (ddHeight > imgH) {
                ddHeight -= 10;
                jTrnCnt.height(ddHeight);
                setTimeout(decrease, 10);
            } else {
                isOpen = false;
                img.attr("src", jsbDomain + dropDownImg);
            }
        }
        function open() {
            if (isOpening)
                return;
            isOpening = true;
            if (isClosing)
                isClosing = false;
            setTimeout(increase, 2);
        }
        function close() {
            if (isClosing)
                return;
            isClosing = true;
            if (isOpening)
                isOpening = false;
            setTimeout(decrease, 2);
        }
        var img = jQuery("<img class='jsb_dropdown jsb_notranslate jsb_clickable'/>")
                .attr("src", jsbDomain + dropDownImg)
                .click(function () {
                    if (isOpen)
                        close();
                    else
                        open();
                })
                .hover(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    open();
                })
                .appendTo(trnCnt);
        jTrnCnt.mouseleave(close);
    };
    this.getBaseLocale = function () {
        return baseLocale;
    };
    this.setBaseLocale = function (l) {
        baseLocale = l;
        _jsbSetCookie("wlbaseLocale", baseLocale, 365);
    };
    this.setTargetLocale = function (l) {
        targetLocale = l;
        _jsbSetCookie("wltargetLocale", l, 365);
    };
    this.getTargetLocale = function () {
        return targetLocale;
    };
    this.isBaseLocale = function () {
        return baseLocale == targetLocale;
    };
    this.getTranslations = function () {
        return translations;
    };
    this.getIgnores = function () {
        return ignores;
    };
    this.parseTranslations = function (s, localTranslations, localIgnores) {
        localTranslations.length = 0;
        localIgnores.length = 0;
        var currentIndex = 0;
        while (currentIndex < s.length) {
            var typeChar = null;
            var b = parseString(s);
            var t = parseString(s);
            var specific = s.charAt(currentIndex++) == '1';
            if (typeChar == tr.TypeTextChar)
                localTranslations.push(tr.createTranslation(decodeURIComponent(b), decodeURIComponent(t), specific));
            else if (typeChar == tr.TypeIgnoreChar)
                localIgnores.push(tr.createTranslation(decodeURIComponent(b), decodeURIComponent(t), specific));
        }

        function parseString(s) {
            var ch;
            var sb = "";
            while (true) {
                if (currentIndex >= s.length)
                    throw "Invalid translation string:\r\n" + s;
                ch = s.charAt(currentIndex++); //il separatore è * per le traduzioni, ? per gli spostamenti di dom
                if (ch == tr.TypeTextChar || ch == tr.TypeIgnoreChar)
                {
                    typeChar = ch;
                    break;
                }
                sb += ch;
            }
            var l = parseInt(sb);
            return s.substring(currentIndex, currentIndex = currentIndex + l);
        }
    };
    this.getIgnore = function (s) {
        var toTranslate = tr.prepareString(s);
        if (toTranslate.length == 0 || toTranslate == " ")
            return null;
        for (var i = 0; i < ignores.length; i++) {
            var t = ignores[i];
            var matches = toTranslate.match(t.getBasePattern());
            if (matches != null && matches.length > 0
                    && matches[0] == toTranslate) {
                t.matches = matches;
                return t;
            }
        }
        return null;
    };
    this.getTranslation = function (s) {
        var toTranslate = tr.prepareString(s);
        if (toTranslate.length == 0 || toTranslate == " ")
            return null;
        for (var i = 0; i < translations.length; i++) {
            var t = translations[i];
            var matches = toTranslate.match(t.getBasePattern());
            if (matches != null && matches.length > 0
                    && matches[0] == toTranslate) {
                t.matches = matches;
                return t;
            }
        }

        missingTranslations.push(toTranslate);
        return null;
    };
    window._jsb = function (s) {
        for (var i = 0; i < tr.getTranslationUnits().length; i++) {
            var tu = tr.getTranslationUnits()[i];
            if (!tu.isScript())
                return s;
            for (var j = 0; j < tu.length; j++) {
                var tuEl = tu[j];
                if (tuEl.base == s)
                    return tuEl.target ? tuEl.target : s;
            }
        }
        return s;
    };
    this.skipTranslate = function (el) {
        function isNoTranslate(jEl)
        {
            return jEl.size() !== 0 && (jEl.hasClass('jsb_notranslate') || isNoTranslate(jEl.parent()));
        }

        return isNoTranslate(jQuery(el));
    };
    this.isInline = function (el)
    {
        var display = jQuery(el).css("display");
        return display === "inline";
    }
    this.extractTranslationUnits = function (el, tuCurrent) {
        try
        {
            if (tr.skipTranslate(el))
                return tuCurrent;
            var name = el.nodeName;
            if (!name)
                return tuCurrent;
            if (name === "STYLE" || name == "TEXTAREA")
                return tuCurrent;
            if (name === "#text")
            {
                var toTranslate = tr.prepareString(el.nodeValue);
                if (toTranslate.length !== 0 && toTranslate !== " ")
                {
                    if (!tuCurrent)
                    {
                        tuCurrent = new TranslationUnit();
                    }
                    tuCurrent.push(new TranslationElement(tuCurrent, el, toTranslate, null));
                    tuCurrent.owner = tuCurrent.owner ? commonAncestor(tuCurrent.owner, el.parentNode) : el.parentNode;
                }

            } else if (name == "SCRIPT")
            {
                if (tuCurrent)//closes current TU
                {
                    translationUnits.push(tuCurrent);
                    tuCurrent = null;
                }
                var text;
                var src;
                if (el.src)
                {
                    //parso solo gli script che provengono dal dominio del sito
                    if (el.src.indexOf(tr.getPageDomain(), 0) == 0)
                    {
                        src = el.src.substr(tr.getPageDomain().length)
                        jQuery.ajax({
                            url: el.src,
                            success: function (data) {
                                text = data;
                            },
                            async: false,
                            cache: true,
                            dataType: "text"
                        });
                    }
                } else
                {
                    src = location.href.substr(tr.getPageDomain().length)
                    text = el.innerHTML;
                }
                if (text)
                {
                    var reg = /_jsb\s*\(\s*"((.(?!"\s*\)))*.)/gm;
                    var tokens;
                    while ((tokens = reg.exec(text)))
                    {
                        var tu = new TranslationUnit();
                        tu.push(new TranslationElement(tu, el, tokens[1], null));
                        tu.owner = el;
                        translationUnits.push(tu);
                    }
                }


            } else if (name == "INPUT") {
                if (tuCurrent)//closes current TU
                {
                    translationUnits.push(tuCurrent);
                    tuCurrent = null;
                }
                var type = el.type;
                if (type == "hidden" || type == "text" || type == "password"
                        || type == "file")
                    return null;
                //I'm interested in value attribute: will be extracted later

            } else
            {
                var inline = tr.isInline(el);
                if (!inline)
                {
                    if (tuCurrent)//a new non inline elemente starts: close current TU if existing
                    {
                        translationUnits.push(tuCurrent);
                        tuCurrent = null;
                    }
                }

                for (var i = 0; i < el.childNodes.length; i++)
                {
                    var child = el.childNodes[i];
                    tuCurrent = tr.extractTranslationUnits(child, tuCurrent);
                }
                if (tuCurrent && !inline)//a non inline elemente ends: close current TU if existing
                {
                    translationUnits.push(tuCurrent);
                    tuCurrent = null;
                }
            }

            function isValidAttribute(attr)
            {
                if ("alt" === attr
                        || "title" === attr
                        || "abbr" === attr
                        || "abbr" === attr
                        || "accesskey" === attr
                        || "label" === attr
                        || "placeholder" === attr
                        || "prompt" === attr
                        || "standby" === attr
                        || "summary" === attr)
                    return true;
                if ("content" === attr) {
                    if ("META" !== el.nodeName) {
                        return false;
                    }

                    var httpEquiv = el.httpEquiv;
                    if (httpEquiv)
                        return "keywords" === httpEquiv.toLowerCase();
                    var aName = el["name"];
                    if (!aName)
                        return false;
                    aName = aName.toLowerCase();
                    if ("generator" === aName || "author" === aName || "progid" === aName || "date" === aName)
                        return false;
                    return true;
                }

                if ("value" === attr) {
                    return "BUTTON" === el.nodeName || "INPUT" === el.nodeName;
                }
                return false;
            }
            if (el.attributes)
            {
                for (var i = 0, attrs = el.attributes, l = attrs.length; i < l; i++) {
                    var attr = attrs.item(i).nodeName;
                    if (isValidAttribute(attr))
                    {
                        var tu = new TranslationUnit();
                        tu.owner = el; //the element which caused ths creation of this TU
                        tu.push(new TranslationElement(tu, el, el[attr], attr));
                        translationUnits.push(tu);
                    }
                }
            }

        } catch (e)
        {

        }

        return tuCurrent;


        function commonAncestor(node1, node2) {
            while (null !== node1)
            {
                while (null !== node2)
                {
                    if (node1 === node2)
                        return node1;
                    node2 = node2.parentNode;
                }
                node1 = node1.parentNode;
            }
            return null;
        }
    };
    this.prepareString = function (s) {
        s = s.replace(trimRegExp, " ");
        return s;
    };
    this.createTranslation = function (b, t, specific)
    {
        return new Translation(b, t, specific);
    };
    function onResize() {
        if (jTrnCnt && jTrnCnt.is(':visible')) {
            if (anchor == 'C') {
                var left = Math.round((getWindowWidth() / 2)) - offset;
                trnCnt.style.left = left + "px";
            } else if (anchor == 'R') {
                var left = getWindowWidth() - offset;
                trnCnt.style.right = left + "px";
            } else {
                trnCnt.style.left = offset + "px";
            }
            tr.adjustFlagZIndex();
        }
    }
    this.moveToTop = function (jElToMove)
    {
        var max = 0;
        function calcMax(x, y)
        {
            function calcMaxForEl(el)
            {
                var n = parseInt(el.style.zIndex);
                if (!isNaN(n))
                    max = Math.max(max, n);
                n = parseInt(jQuery(el).css("z-index"));
                if (!isNaN(n))
                    max = Math.max(max, n);
                if (el.parentNode && el.parentNode.style)
                    calcMaxForEl(el.parentNode);
            }
            var el = elementFromPoint(x, y);
            if (el && el.style)
            {
                calcMaxForEl(el);
            }
        }

        var pos = jElToMove.offset();
        var w = jElToMove.width();
        var h = jElToMove.height();
        jElToMove.hide(); //lo nascondo, altrimenti la elementFromPoint lo becca

        //calcMax(pos.left, pos.top);//topleft
        //calcMax(pos.left + w, pos.top);//topright
        //calcMax(pos.left, pos.top + h);//bottomleft
        //calcMax(pos.left + w, pos.top + h);//bottomright

        calcMax(pos.left + w / 2, pos.top + h / 2); //center
        jElToMove[0].style.zIndex = max + 1;
        jElToMove.show();
    };
    this.adjustFlagZIndex = function ()
    {
        tr.moveToTop(jTrnCnt);
    };
    function onMessage(e) {
        if (e.originalEvent.data == "reload")
            location.reload();
    }

    this.applyTranslations = function () {
        for (var i = 0; i < translationUnits.length; i++)
        {
            var tu = translationUnits[i];
            if (tr.getIgnore(tu.toOriginalBaseString()))
            {
                tu.ignore = true;
            } else
            {
                tu.ignore = false;
                var trn = tr.getTranslation(tu.toOriginalBaseString());
                if (trn)
                {
                    tu.specific = trn.isPageSpecific();
                    tu.parseTargetString(trn.getTarget());
                    tu.translation = trn;
                    tu.applyTranslation();
                }
            }

        }


    };
    this.getPageDomain = function ()
    {
        if (!pageDomain)
            calculatePageInfo();
        return pageDomain;
    };
    this.getPageUrl = function () {
        if (!pageUrl)
            calculatePageInfo();
        return pageUrl;
    };
    function calculatePageInfo()
    {
        //la variabile demoMode non è ancora inizializzata
        var idx = location.href.indexOf("htmlInjector?", 0);
        if (idx > -1)//ho trovato la parolina che mi dice che sono in demo
            idx = location.href.indexOf("src=", idx); // 4 caratteri
        if (idx > -1)
        {
            pageUrl = location.href.substring(idx + 4);
            var l = pageUrl.indexOf('/', 7); //salto http://
            if (l == -1)
                l = pageUrl.length;
            pageDomain = pageUrl.substr(0, l);
        } else
        {
            pageUrl = location.href;
            pageDomain = "http://" + location.host;
        }
    }


    this.calculateOffset = function () {
        var left = parseInt(trnCnt.style.left);
        if (anchor == 'C')
            offset = Math.round((getWindowWidth() / 2) - left);
        else if (anchor == 'R')
            offset = getWindowWidth() - left;
        else
            offset = left;
    };
    function getWindowWidth() {
        return jQuery(window).width();
    }

    function attachTranslator() {

        jQuery(window)
                .bind("message", onMessage)
                .bind("resize", onResize);
        trnCnt = document.createElement("div");
        trnCnt.style.display = "none";
        document.body.appendChild(trnCnt);
        trnCnt.style.left = "10px";
        trnCnt.style.top = "10px";
        trnCnt.className = "jsb_translator jsb_notranslate";
        jTrnCnt = jQuery(trnCnt);
        if (persistencyManager) {
            persistencyManager.open(function () {
                persistencyManager.loadTranslationData(location.pathname, targetLocale,
                        function (data, dataVersion, strings, stringsVersion) {
                            tr.setTranslationData(data, dataVersion, strings, stringsVersion);
                            tr.addStringScript();
                        });
            });
        } else {
            tr.addStringScript();
        }
        tr.addCss("/css/babel.css");
        tr.extractTranslationUnits(document.documentElement);
    }
    this.addStringScript = function () {
        tr.addScript(stringScript + '?src='
                + encodeURIComponent(tr.getPageUrl())
                + '&loc=' + encodeURIComponent(targetLocale)
                + '&vData=' + trnDataVersion
                + '&vStrings=' + trnStringsVersion);
    };
    this.addAndWait(function () {
        return typeof (jQuery) !== 'undefined';
    },
            jqueryscript,
            function () {
                jQuery(attachTranslator);
            });
    function PersistenceManager() {
        var data = "";
        var strings = "";
        var db = null;
        function errorHandler(transaction, error) {
            if (error.code == 1) {
                console.log("JSBABEL ERROR: DB Table already exists")
            } else {
                // Error is a human-readable string.
                console.log('JSBABEL ERROR: ' + error.message + ' (Code ' + error.code + ')');
            }
            return false;
        }
        this.open = function (readyCallback) {
            db = openDatabase('JSBABELDATA', '1.0', 'JSBABEL translations', 2097152); //2 * 1024 * 1024

            db.transaction(function (transaction) {
                transaction
                        .executeSql(
                                "CREATE TABLE IF NOT EXISTS TRANSLATIONS (PAGE TEXT NOT NULL, LOCALE TEXT NOT NULL, VERSION INTEGER NOT NULL, STRINGS TEXT NOT NULL, PRIMARY KEY(PAGE, LOCALE))",
                                [], createSettings, errorHandler);
                function createSettings() {
                    transaction
                            .executeSql(
                                    "CREATE TABLE IF NOT EXISTS SETTINGS (ID INTEGER NOT NULL, VERSION INTEGER NOT NULL, DATA TEXT NOT NULL, PRIMARY KEY(ID))",
                                    [], readyCallback, errorHandler);
                }

            });
        };
        this.loadTranslationData = function (page, locale, callback) {
            // prima seleziono i settings
            db.transaction(function (transaction) {
                transaction.executeSql(
                        'SELECT DATA, VERSION FROM SETTINGS WHERE ID=0', [],
                        onSelectSettings, errorHandler);
            });
            // se trovo i settings, seleziono le traduzioni
            function onSelectSettings(transaction, results) {
                if (results.rows.length == 1) {
                    try
                    {
                        data = JSON.parse(results.rows.item(0).DATA);
                        trnDataVersion = results.rows.item(0).VERSION;
                    } catch (e)
                    {
                        data = "";
                        trnDataVersion = 0;
                    }
                    transaction.executeSql(
                            'SELECT STRINGS, VERSION FROM TRANSLATIONS WHERE PAGE=? AND LOCALE=?',
                            [page, locale], onSelectTranslations,
                            errorHandler);
                } else
                    callback("", 0, "", 0);
            }

            // se trovo le traduzioni, chiamo la callback
            function onSelectTranslations(transaction, results) {
                if (results.rows.length == 1)
                {
                    strings = results.rows.item(0).STRINGS;
                    trnStringsVersion = results.rows.item(0).VERSION;
                }
                callback(data, trnDataVersion, strings, trnStringsVersion);
            }

            function errorHandler(transaction, error) {
                console.log(error);
                callback("", 0, "", 0);
                return false;
            }
        };
        function nullDataHandler() {

        }
        this.persistTranslationData = function (page, locale, newData, newDataVersion, newStrings, newStringsVersion) {
            if (newData)//solo se ho nuovi dati
            {
                if (data) {
                    updateSettings();
                } else {
                    insertSettings();
                }
            }
            // le stringhe le salvo solo per le lingue da tradurre, quelle della
            // lingua base sono vuote
            if (locale == tr.getBaseLocale())
                return;
            if (newStrings)
            {
                if (strings) {
                    updateTranslations();
                } else {
                    insertTranslations();
                }
            }
            function insertSettings() {
                db.transaction(function (transaction) {
                    delete Array.prototype.toJSON; // elimina buco della
                    // stringify con gli array
                    transaction.executeSql(
                            'INSERT INTO SETTINGS (ID, VERSION, DATA) VALUES(0, ?, ?)',
                            [newDataVersion, JSON.stringify(newData)],
                            nullDataHandler, errorHandler);
                });
            }
            function updateSettings() {
                db.transaction(function (transaction) {
                    delete Array.prototype.toJSON; // elimina buco della
                    // stringify con gli array
                    transaction.executeSql(
                            'UPDATE SETTINGS SET VERSION=?, DATA=? WHERE ID=0',
                            [newDataVersion,
                                JSON.stringify(newData)],
                            nullDataHandler, errorHandler);
                });
            }

            function insertTranslations() {
                db
                        .transaction(function (transaction) {
                            transaction
                                    .executeSql(
                                            'INSERT INTO TRANSLATIONS (PAGE, LOCALE, VERSION, STRINGS) VALUES(?, ?, ?, ?)',
                                            [page, locale, newStringsVersion, newStrings],
                                            nullDataHandler, errorHandler);
                        });
            }
            function updateTranslations() {
                db
                        .transaction(function (transaction) {
                            transaction
                                    .executeSql(
                                            'UPDATE TRANSLATIONS SET VERSION=?, STRINGS=? WHERE PAGE=? AND LOCALE=?',
                                            [newStringsVersion, newStrings, page, locale],
                                            nullDataHandler, errorHandler);
                        });
            }
        };
    }

}

__babel = new Babel();
