<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Youtube Downloader</title>
        <meta name="keywords" content="Video downloader, download youtube, video download, youtube video, youtube downloader, download youtube FLV, download youtube MP4, download youtube 3GP, php video downloader" />
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
            <style type="text/css">
                body {
                    padding-top: 40px;
                    padding-bottom: 40px;
                    background-color: #f5f5f5;
                }

                .form-download {
                    max-width: 300px;
                    padding: 19px 29px 29px;
                    margin: 0 auto 20px;
                    background-color: #fff;
                    border: 1px solid #e5e5e5;
                    -webkit-border-radius: 5px;
                    -moz-border-radius: 5px;
                    border-radius: 5px;
                    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
                }
                .form-download .form-download-heading,
                .form-download .checkbox {
                    margin-bottom: 10px;
                    text-align: center;
                }
                .form-download input[type="text"] {
                    font-size: 16px;
                    height: auto;
                    margin-bottom: 15px;
                    padding: 7px 9px;
                }
                .userscript {
                    float: right;
                    margin-top: 5px
                }

            </style>
    </head>
    <body>
        <form class="form-download" method="get" id="download" action="getvideo">
            <h1 class="form-download-heading">Youtube Downloader</h1>
            <input type="text" name="videoid" id="videoid" size="40" placeholder="VideoID" />
            <input class="btn btn-primary" type="submit" name="type" id="type" value="Download" />
            <p>Put in just the ID bit, the part after v=.</p>
            <p>Example: http://www.youtube.com/watch?v=<b>Fw-BM-Mqgeg</b></p>

            <!-- @TODO: Prepend the base URI -->
            <?PHP
            /*             * ********|| Thumbnail Image Configuration ||************** */
            # $config['ThumbnailImageMode']=0;   // don't show thumbnail image
            # $config['ThumbnailImageMode']=1;   // show thumbnail image directly from YouTube
            $config['ThumbnailImageMode'] = 2;    // show thumbnail image by proxy from this server

            /*             * ********|| Video Download Link Configuration ||************** */
           $config['VideoLinkMode']='direct'; // show only direct download link
            #$config['VideoLinkMode']='proxy'; // show only by proxy download link
            #$config['VideoLinkMode'] = 'both'; // show both direct and by proxy download links

            /*             * ********|| features ||************** */
            $config['feature']['browserExtensions'] = true; // show links for install browser extensions? true or false

            /*             * ********|| Other ||************** */
            // Set your default timezone
            // use this link: http://php.net/manual/en/timezones.php
            date_default_timezone_set("Asia/Tehran");

            // Debug mode
            #$debug=true; // debug mode on
            $debug = false; // debug mode off

            function curlGet($URL) {
                $ch = curl_init();
                $timeout = 3;
                curl_setopt($ch, CURLOPT_URL, $URL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                /* if you want to force to ipv6, uncomment the following line */
                //curl_setopt( $ch , CURLOPT_IPRESOLVE , 'CURLOPT_IPRESOLVE_V6');
                $tmp = curl_exec($ch);
                curl_close($ch);
                return $tmp;
            }

            /*
             * function to use cUrl to get the headers of the file 
             */

            function get_location($url) {
                $my_ch = curl_init();
                curl_setopt($my_ch, CURLOPT_URL, $url);
                curl_setopt($my_ch, CURLOPT_HEADER, true);
                curl_setopt($my_ch, CURLOPT_NOBODY, true);
                curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($my_ch, CURLOPT_TIMEOUT, 10);
                $r = curl_exec($my_ch);
                foreach (explode("\n", $r) as $header) {
                    if (strpos($header, 'Location: ') === 0) {
                        return trim(substr($header, 10));
                    }
                }
                return '';
            }

            function get_size($url) {
                $my_ch = curl_init();
                curl_setopt($my_ch, CURLOPT_URL, $url);
                curl_setopt($my_ch, CURLOPT_HEADER, true);
                curl_setopt($my_ch, CURLOPT_NOBODY, true);
                curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($my_ch, CURLOPT_TIMEOUT, 10);
                $r = curl_exec($my_ch);
                foreach (explode("\n", $r) as $header) {
                    if (strpos($header, 'Content-Length:') === 0) {
                        return trim(substr($header, 16));
                    }
                }
                return '';
            }

            function get_description($url) {
                $fullpage = curlGet($url);
                $dom = new DOMDocument();
                @$dom->loadHTML($fullpage);
                $xpath = new DOMXPath($dom);
                $tags = $xpath->query('//div[@class="info-description-body"]');
                foreach ($tags as $tag) {
                    $my_description .= (trim($tag->nodeValue));
                }

                return utf8_decode($my_description);
            }

            function is_chrome() {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                if (preg_match("/like\sGecko\)\sChrome\//", $agent)) { // if user agent is google chrome
                    if (!strstr($agent, 'Iron')) // but not Iron
                        return true;
                }
                return false; // if isn't chrome return false
            }

            if (($config['feature']['browserExtensions'] == true) && (is_chrome()))
                echo '<a href="ytdl.user.js" class="userscript btn btn-mini" title="Install chrome extension to view a \'Download\' link to this application on Youtube video pages."> Install Chrome Extension </a>';
            ?>
        </form>
    </body>
</html>
<script>
/*! Projekktor v1.2.37 -jarisflash | http://www.projekktor.com | Copyright 2010, 2011, Sascha Kluger, Spinning Airwhale Media, http://www.spinningairwhale.com | GNU General Public License - http://www.projekktor.com/license/
 */
jQuery(function($) {
    var projekktors = [];
    document.createElement("video").canPlayType && function() {
        return;
        var t;
        var e
    }();

    function Iterator(t) {
        this.length = t.length, this.each = function(e) {
            $.each(t, e)
        }, this.size = function() {
            return t.length
        }
    }
    $.fn.prop || ($.fn.prop = function(t, e) {
        return $(this).attr(t, e)
    }), projekktor = $p = function() {
        var arg = arguments[0],
            instances = [],
            plugins = [];
        if (!arguments.length) return projekktors[0] || null;
        if ("number" == typeof arg) return projekktors[arg];
        if ("string" == typeof arg) {
            if ("*" == arg) return new Iterator(projekktors);
            for (var i = 0; projekktors.length > i; i++) {
                try {
                    if (projekktors[i].getId() == arg.id) {
                        instances.push(projekktors[i]);
                        continue
                    }
                } catch (e) {}
                try {
                    for (var j = 0; $(arg).length > j; j++) projekktors[i].env.playerDom.get(0) != $(arg).get(j) || instances.push(projekktors[i])
                } catch (e) {}
                try {
                    if (projekktors[i].getParent() == arg) {
                        instances.push(projekktors[i]);
                        continue
                    }
                } catch (e) {}
                try {
                    if (projekktors[i].getId() == arg) {
                        instances.push(projekktors[i]);
                        continue
                    }
                } catch (e) {}
            }
            if (instances.length > 0) return 1 == instances.length ? instances[0] : new Iterator(instances)
        }
        if (0 === instances.length) {
            var cfg = arguments[1] || {},
                callback = arguments[2] || {},
                count = 0,
                playerA;
            if ("string" == typeof arg) return $.each($(arg), function() {
                playerA = new PPlayer($(this), cfg, callback), projekktors.push(playerA), count++
            }), count > 1 ? new Iterator(projekktors) : playerA;
            if (arg) return projekktors.push(new PPlayer(arg, cfg, callback)), new Iterator(projekktors)
        }
        return null;

        function PPlayer(srcNode, cfg, onReady) {
            return this.config = new projekktorConfig("1.3.09"), this.env = {
                muted: !1,
                playerDom: null,
                mediaContainer: null,
                agent: "standard",
                mouseIsOver: !1,
                loading: !1,
                className: "",
                onReady: onReady
            }, this.media = [], this._plugins = [], this._pluginCache = {}, this._queue = [], this._cuePoints = {}, this.listeners = [], this.playerModel = {}, this._isReady = !1, this._maxElapsed = 0, this._currentItem = null, this._playlistServer = "", this._id = "", this._reelUpdate = function(obj) {
                var ref = this,
                    itemIdx = null,
                    data = obj || [{}],
                    files = data.playlist || data;
                this.env.loading = !0, this.media = [];
                try {
                    for (var props in data.config)
                        if (data.config.hasOwnProperty(props)) {
                            if (typeof data.config[props].indexOf("objectfunction") > -1) continue;
                            this.config[props] = eval(data.config[props])
                        }
                    null != data.config && ($p.utils.log("Updated config var: " + props + " to " + this.config[props]), this._promote("configModified"), delete data.config)
                } catch (e) {}
                $.each(files, function() {
                    itemIdx = ref._addItem(ref._prepareMedia({
                        file: this,
                        config: this.config || {},
                        errorCode: this.errorCode || 0
                    })), $.each(this.cuepoints || [], function() {
                        this.item = itemIdx, ref.setCuePoint(this)
                    })
                }), null === itemIdx && this._addItem(this._prepareMedia({
                    file: "",
                    config: {},
                    errorCode: 97
                })), this.env.loading = !1, this._promote("scheduled", this.getItemCount()), this._syncPlugins(function() {
                    ref.setActiveItem(0)
                })
            }, this._addItem = function(t, e, i) {
                var s = 0;
                return 1 === this.media.length && "NA" == this.media[0].mediaModel && (this._detachplayerModel(), this.media = []), void 0 === e || 0 > e || e > this.media.length - 1 ? (this.media.push(t), s = this.media.length - 1) : (this.media.splice(e, i === !0 ? 1 : 0, t), s = e), this.env.loading === !1 && this._promote("scheduleModified", this.getItemCount()), s
            }, this._removeItem = function(t) {
                var e = 0;
                return 1 === this.media.length ? "NA" == this.media[0].mediaModel ? 0 : (this.media[0] = this._prepareMedia({
                    file: ""
                }), 0) : (void 0 === t || 0 > t || t > this.media.length - 1 ? (this.media.pop(), e = this.media.length) : (this.media.splice(t, 1), e = t), this.env.loading === !1 && this._promote("scheduleModified", this.getItemCount()), e)
            }, this._canPlay = function(t, e, i) {
                var s = this,
                    n = [],
                    a = [],
                    r = i || "http",
                    o = "object" == typeof e ? e : [e],
                    l = t ? t.replace(/x-/, "") : void 0,
                    h = s._testMediaSupport();
                if ($.each(o, function(t, e) {
                        $.each($.extend(h[r], h["*"] || []) || [], function(t, i) {
                            return null != e && t != e ? !0 : (n = $.merge(n, this), !0)
                        })
                    }), 0 === n.length) return !1;
                switch (typeof l) {
                    case "undefined":
                        return n.length > 0;
                    case "string":
                        if ("*" == l) return n;
                        a.push(l);
                        break;
                    case "array":
                        a = l
                }
                for (var u in a)
                    if ($p.mmap.hasOwnProperty(u)) {
                        if ("string" != typeof a[u]) break;
                        if ($.inArray(a[u], n) > -1) return !0
                    }
                return !1
            }, this._prepareMedia = function(t) {
                var e = this,
                    i = [],
                    s = [],
                    n = [],
                    a = {},
                    r = {},
                    o = [],
                    l = {},
                    h = [],
                    u = 0;
                for (var c in $p.mmap) $p.mmap.hasOwnProperty(c) && (platforms = "object" == typeof $p.mmap[c].platform ? $p.mmap[c].platform : [$p.mmap[c].platform], $.each(platforms, function(i, s) {
                    var n = 0,
                        o = "http";
                    for (var l in t.file)
                        if (t.file.hasOwnProperty(l)) {
                            if ("config" === l) continue;
                            if (o = t.file[l].streamType || e.getConfig("streamType") || "http", e._canPlay($p.mmap[c].type, s, o) && n++, 0 === n) continue;
                            if ($p.mmap[c].level = $.inArray(s, e.config._platforms), $p.mmap[c].level = 0 > $p.mmap[c].level ? 100 : $p.mmap[c].level, h.push("." + $p.mmap[c].ext), a[$p.mmap[c].ext] || (a[$p.mmap[c].ext] = []), a[$p.mmap[c].ext].push($p.mmap[c]), null === $p.mmap[c].streamType || "*" == $p.mmap[c].streamType || $.inArray(o || [], $p.mmap[c].streamType || "") > -1) {
                                r[$p.mmap[c].type] || (r[$p.mmap[c].type] = []), n = -1;
                                for (var u = 0, d = r[$p.mmap[c].type].length; d > u; u++)
                                    if (r[$p.mmap[c].type][u].model == $p.mmap[c].model) {
                                        n = u;
                                        break
                                    } - 1 === n && r[$p.mmap[c].type].push($p.mmap[c])
                            }
                        } else;
                    return !0
                }));
                h = "^.*.(" + h.join("|") + ")$", "string" == typeof t.file && (t.file = [{
                    src: t.file
                }], "string" == typeof t.type && (t.file = [{
                    src: t.file,
                    type: t.type
                }])), ($.isEmptyObject(t) || t.file === !1 || null === t.file) && (t.file = [{
                    src: null
                }]);
                for (var d in t.file)
                    if (t.file.hasOwnProperty(d)) {
                        if ("config" == d) continue;
                        if ("string" == typeof t.file[d] && (t.file[d] = {
                                src: t.file[d]
                            }), null == t.file[d].src) continue;
                        if (null != t.file[d].type && "" !== t.file[d].type) try {
                            var p = t.file[d].type.split(" ").join("").split(/[\;]codecs=.([a-zA-Z0-9\,]*)[\'|\"]/i);
                            null != p[1] && (t.file[d].codec = p[1]), t.file[d].type = p[0].replace(/x-/, ""), t.file[d].originalType = p[0]
                        } catch (m) {} else t.file[d].type = this._getTypeFromFileExtension(t.file[d].src);
                        r[t.file[d].type] && r[t.file[d].type].length > 0 && (r[t.file[d].type].sort(function(t, e) {
                            return t.level - e.level
                        }), o.push(r[t.file[d].type][0]))
                    }
                0 === o.length ? o = r["none/none"] : (o.sort(function(t, e) {
                    return t.level - e.level
                }), u = o[0].level, o = $.grep(o, function(t) {
                    return t.level == u
                })), i = [], $.each(o || [], function() {
                    i.push(this.type)
                });
                var f = o && o.length > 0 ? o[0] : {
                    type: "none/none",
                    model: "NA",
                    errorCode: 11
                };
                i = $p.utils.unique(i);
                for (d in t.file)
                    if (t.file.hasOwnProperty(d)) {
                        if (null == t.file[d].type) continue;
                        if (0 > $.inArray(t.file[d].type.replace(/x-/, ""), i) && "none/none" != f.type) continue;
                        ($.isEmptyObject(t.config) || null == t.config.streamType || -1 == t.config.streamType.indexOf("rtmp")) && (t.file[d].src = $p.utils.toAbsoluteURL(t.file[d].src)), null == t.file[d].quality && (t.file[d].quality = "default"), n.push(t.file[d].quality), s.push(t.file[d])
                    }
                0 === s.length && s.push({
                    src: null,
                    quality: "default"
                });
                var g = [];
                return $.each(this.getConfig("playbackQualities"), function() {
                    g.push(this.key || "default")
                }), l = {
                    ID: t.config.id || $p.utils.randomId(8),
                    cat: t.config.cat || "clip",
                    file: s,
                    platform: f.platform,
                    platforms: platforms,
                    qualities: $p.utils.intersect($p.utils.unique(g), $p.utils.unique(n)),
                    mediaModel: f.model || "NA",
                    errorCode: f.errorCode || t.errorCode || 7,
                    config: t.config || {}
                }
            }, this._modelUpdateListener = function(t, e) {
                var i = this,
                    s = this.playerModel;
                if (this.playerModel.init) switch ("time" != t && "progress" != t && $p.utils.log("Update: '" + t, this.playerModel.getSrc(), this.playerModel.getModelName(), e), t) {
                    case "state":
                        this._promote("state", e);
                        var n = $.map(this.getDC().attr("class").split(" "), function(t) {
                            return -1 === t.indexOf(i.getConfig("ns") + "state") ? t : ""
                        });
                        switch (n.push(this.getConfig("ns") + "state" + e.toLowerCase()), this.getDC().attr("class", n.join(" ")), e) {
                            case "AWAKENING":
                                this._syncPlugins(function() {
                                    s.getState("AWAKENING") && s.displayItem(!0)
                                });
                                break;
                            case "ERROR":
                                this._addGUIListeners();
                                break;
                            case "PAUSED":
                                this.getConfig("disablePause") === !0 && this.playerModel.applyCommand("play", 0);
                                break;
                            case "COMPLETED":
                                if (this._currentItem + 1 >= this.media.length && !this.getConfig("loop") && (this._promote("done", {}), this.getConfig("leaveFullscreen"))) return this.reset(), void 0;
                                this.setActiveItem("next")
                        }
                        break;
                    case "modelReady":
                        this._maxElapsed = 0, this._promote("item", i._currentItem);
                        break;
                    case "displayReady":
                        this._promote("displayReady", !0), this._syncPlugins(function() {
                            i._promote("ready"), i._addGUIListeners(), s.getState("IDLE") || s.start()
                        });
                        break;
                    case "availableQualitiesChange":
                        this.media[this._currentItem].qualities = e, this._promote("availableQualitiesChange", e);
                        break;
                    case "qualityChange":
                        this.setConfig({
                            playbackQuality: e
                        }), this._promote("qualityChange", e);
                        break;
                    case "volume":
                        this.setConfig({
                            volume: e
                        }), this._promote("volume", e), 0 >= e ? (this.env.muted = !0, this._promote("mute", e)) : this.env.muted === !0 && (this.env.muted = !1, this._promote("unmute", e));
                        break;
                    case "playlist":
                        this.setFile(e.file, e.type);
                        break;
                    case "config":
                        this.setConfig(e);
                        break;
                    case "time":
                        if (e > this._maxElapsed) {
                            var a = Math.round(100 * e / this.getDuration()),
                                r = !1;
                            25 > a && (a = 25), a > 25 && 50 > a && (r = "firstquartile", a = 50), a > 50 && 75 > a && (r = "midpoint", a = 75), a > 75 && 100 > a && (r = "thirdquartile", a = 100), r !== !1 && this._promote(r, e), this._maxElapsed = this.getDuration() * a / 100
                        }
                        this._promote(t, e);
                        break;
                    case "fullscreen":
                        e === !0 ? (this.getDC().addClass("fullscreen"), this._enterFullViewport()) : (this.getDC().removeClass("fullscreen"), this._exitFullViewport()), this._promote(t, e);
                        break;
                    case "error":
                        this._promote(t, e), this.getConfig("skipTestcard") && this.getItemCount() > 1 ? this.setActiveItem("next") : (this.playerModel.applyCommand("error", e), this._addGUIListeners());
                        break;
                    case "streamTypeChange":
                        "dvr" == e && this.getDC().addClass(this.getNS() + "dvr"), this._promote(t, e);
                        break;
                    default:
                        this._promote(t, e)
                }
            }, this._syncPlugins = function(t) {
                var e = this;
                this.env.loading = !0,
                    function() {
                        try {
                            if (e._plugins.length > 0)
                                for (var i = 0; e._plugins.length > i; i++)
                                    if (!e._plugins[i].isReady()) return setTimeout(arguments.callee, 50), void 0;
                            e.env.loading = !1, e._promote("pluginsReady", {});
                            try {
                                t()
                            } catch (s) {}
                        } catch (s) {}
                    }()
            }, this._MD = function(t) {
                projekktor("#" + t.currentTarget.id.replace(/_media$/, ""))._playerFocusListener(t)
            }, this._addGUIListeners = function() {
                var t = this;
                this._removeGUIListeners(), this.getDC().get(0).addEventListener ? this.getDC().get(0).addEventListener("mousedown", this._MD, !0) : this.getDC().mousedown(function(e) {
                    t._playerFocusListener(e)
                }), this.getDC().mousemove(function(e) {
                    t._playerFocusListener(e)
                }).mouseenter(function(e) {
                    t._playerFocusListener(e)
                }).mouseleave(function(e) {
                    t._playerFocusListener(e)
                }).focus(function(e) {
                    t._playerFocusListener(e)
                }).blur(function(e) {
                    t._playerFocusListener(e)
                }), $(window).bind("resize.projekktor" + this.getId(), function() {
                    t.setSize()
                }).bind("touchstart", function() {
                    t._windowTouchListener(event)
                }), this.config.enableKeyboard === !0 && ($(document).unbind("keydown.pp" + this._id), $(document).bind("keydown.pp" + this._id, function(e) {
                    t._keyListener(e)
                }))
            }, this._removeGUIListeners = function() {
                $("#" + this.getId()).unbind(), this.getDC().unbind(), this.getDC().get(0).removeEventListener ? this.getDC().get(0).removeEventListener("mousedown", this._MD, !0) : this.getDC().get(0).detachEvent("onmousedown", this._MD), $(window).unbind("resize.projekktor" + this.getId())
            }, this._registerPlugins = function() {
                var plugins = $.merge($.merge([], this.config._plugins), this.config._addplugins),
                    pluginName = "",
                    pluginObj = null;
                if (!(this._plugins.length > 0 || 0 === plugins.length))
                    for (var i = 0; plugins.length > i; i++) {
                        pluginName = "projekktor" + plugins[i].charAt(0).toUpperCase() + plugins[i].slice(1);
                        try {
                            typeof eval(pluginName)
                        } catch (e) {
                            alert("Projekktor Error: Plugin '" + plugins[i] + "' malicious or not available.");
                            continue
                        }
                        pluginObj = $.extend(!0, {}, new projekktorPluginInterface, eval(pluginName).prototype), pluginObj.name = plugins[i].toLowerCase(), pluginObj.pp = this, pluginObj.playerDom = this.env.playerDom, pluginObj._init(this.config["plugin_" + plugins[i].toLowerCase()] || {}), null == this.config["plugin_" + pluginObj.name] && (this.config["plugin_" + pluginObj.name] = {}), this.config["plugin_" + pluginObj.name] = $.extend(!0, {}, pluginObj.config || {});
                        for (var propName in pluginObj) propName.indexOf("Handler") > 1 && (null == this._pluginCache[propName] && (this._pluginCache[propName] = []), this._pluginCache[propName].push(pluginObj));
                        this._plugins.push(pluginObj)
                    }
            }, this.removePlugins = function(t) {
                if (0 != this._plugins.length) {
                    var e = t || $.merge($.merge([], this.config._plugins), this.config._addplugins),
                        i = this._plugins.length;
                    for (var s = 0; e.length > s; s++)
                        for (var n = 0; i > n; n++)
                            if (void 0 != this._plugins[n] && this._plugins[n].name == e[s].toLowerCase()) {
                                this._plugins[n].deconstruct(), this._plugins.splice(n, 1);
                                for (var a in this._pluginCache)
                                    for (var r = 0; this._pluginCache[a].length > r; r++) this._pluginCache[a][r].name == e[s].toLowerCase() && this._pluginCache[a].splice(r, 1)
                            }
                }
            }, this.getPlugins = function() {
                var t = [];
                return $.each(this._plugins, function() {
                    t.push({
                        name: this.name,
                        ver: this.version || "unknown"
                    })
                }), t
            }, this._promote = function(t, e) {
                var i = this;
                this._enqueue(function() {
                    try {
                        i.__promote(t, e)
                    } catch (s) {}
                })
            }, this.__promote = function(t, e) {
                var i = t,
                    s = {};
                if ("object" == typeof i) {
                    if (!i._plugin) return;
                    i = "plugin_" + i._plugin + $p.utils.capitalise(i._event.toUpperCase())
                }
                if ("time" != i && "progress" != i && "mousemove" != i && $p.utils.log("Event: [" + i + "]", e, this.listeners), this._pluginCache[i + "Handler"] && this._pluginCache[i + "Handler"].length > 0)
                    for (var n = 0; this._pluginCache[i + "Handler"].length > n; n++)
                        if (this.getConfig("debug")) try {
                            this._pluginCache[i + "Handler"][n][i + "Handler"](e, this)
                        } catch (a) {
                            $p.utils.log(a)
                        } else this._pluginCache[i + "Handler"][n][i + "Handler"](e, this);
                if (this._pluginCache.eventHandler && this._pluginCache.eventHandler.length > 0)
                    for (var n = 0; this._pluginCache.eventHandler.length > n; n++)
                        if (this.getConfig("debug")) try {
                            this._pluginCache.eventHandler[n].eventHandler(i, e, this)
                        } catch (a) {
                            $p.utils.log(a)
                        } else this._pluginCache.eventHandler[n].eventHandler(i, e, this);
                if (this.listeners.length > 0)
                    for (var n = 0; this.listeners.length > n; n++)
                        if (this.listeners[n].event == i || "*" == this.listeners[n].event)
                            if (this.getConfig("debug")) try {
                                this.listeners[n].callback(e, this)
                            } catch (a) {
                                $p.utils.log(a)
                            } else this.listeners[n].callback(e, this)
            }, this._detachplayerModel = function() {
                this._removeGUIListeners();
                try {
                    this.playerModel.destroy(), this._promote("detach", {})
                } catch (t) {}
            }, this._windowTouchListener = function(t) {
                t.touches && t.touches.length > 0 && (($(document.elementFromPoint(t.touches[0].clientX, t.touches[0].clientY)).attr("id") || "").indexOf(this.getDC().attr("id")) > -1 ? (0 == this.env.mouseIsOver && this._promote("mouseenter", {}), this.env.mouseIsOver = !0, this._promote("mousemove", {}), t.stopPropagation()) : this.env.mouseIsOver && (this._promote("mouseleave", {}), this.env.mouseIsOver = !1))
            }, this._playerFocusListener = function(t) {
                var e = t.type.toLowerCase();
                switch (e) {
                    case "mousedown":
                        if (0 == this.env.mouseIsOver) break;
                        if ("|TEXTAREA|INPUT".indexOf("|" + t.target.tagName.toUpperCase()) > -1) return;
                        if (3 == t.which) {
                            if ($(t.target).hasClass("context")) break;
                            $(document).bind("contextmenu", function(t) {
                                return $(document).unbind("contextmenu"), !1
                            })
                        }
                        break;
                    case "mousemove":
                        if (this.env.mouseX != t.clientX && this.env.mouseY != t.clientY && (this.env.mouseIsOver = !0), this.env.clientX == t.clientX && this.env.clientY == t.clientY) return;
                        this.env.clientX = t.clientX, this.env.clientY = t.clientY;
                        break;
                    case "focus":
                    case "mouseenter":
                        this.env.mouseIsOver = !0;
                        break;
                    case "blur":
                    case "mouseleave":
                        this.env.mouseIsOver = !1
                }
                this._promote(e, t)
            }, this._keyListener = function(t) {
                if (this.env.mouseIsOver && !("|TEXTAREA|INPUT".indexOf("|" + t.target.tagName.toUpperCase()) > -1)) {
                    var e = this,
                        i = this.getConfig("keys").length > 0 ? this.getConfig("keys") : [{
                            27: function(t) {
                                t.getInFullscreen() ? t.setFullscreen(!1) : t.setStop()
                            },
                            32: function(t, e) {
                                t.setPlayPause(), e.preventDefault()
                            },
                            70: function(t) {
                                t.setFullscreen()
                            },
                            39: function(t, e) {
                                t.setPlayhead("+5"), e.preventDefault()
                            },
                            37: function(t, e) {
                                t.setPlayhead("-5"), e.preventDefault()
                            },
                            38: function(t, e) {
                                t.setVolume("+0.05"), e.preventDefault()
                            },
                            40: function(t, e) {
                                t.setVolume("-0.05"), e.preventDefault()
                            },
                            68: function(t) {
                                t.setDebug()
                            },
                            67: function(t) {
                                $p.utils.log("Config Dump", t.config)
                            },
                            80: function(t) {
                                $p.utils.log("Schedule Dump", t.media)
                            },
                            84: function(t) {
                                $p.utils.log("Cuepoints Dump", t.getCuePoints())
                            }
                        }];
                    this._promote("key", t), $.each(i || [], function() {
                        try {
                            this[t.keyCode](e, t)
                        } catch (i) {}
                        try {
                            this["*"](e)
                        } catch (i) {}
                    })
                }
            }, this._enterFullViewport = function(t) {
                var e = this.getIframeParent() || $(window),
                    i = this.getIframe() || this.getDC(),
                    s = $(e[0].document.body).css("overflow");
                t && (e = $(window), i = this.getDC()), i.data("fsdata", {
                    scrollTop: e.scrollTop() || 0,
                    scrollLeft: e.scrollLeft() || 0,
                    targetStyle: i.attr("style") || "",
                    targetWidth: i.width(),
                    targetHeight: i.height(),
                    bodyOverflow: "visible" == s ? "auto" : s,
                    bodyOverflowX: $(e[0].document.body).css("overflow-x"),
                    bodyOverflowY: $(e[0].document.body).css("overflow-y"),
                    iframeWidth: i.attr("width") || 0,
                    iframeHeight: i.attr("height") || 0
                }).css({
                    position: "absolute",
                    display: "block",
                    top: 0,
                    left: 0,
                    width: "100%",
                    height: "100%",
                    zIndex: 99999,
                    margin: 0,
                    padding: 0
                }), e.scrollTop(0).scrollLeft(0), $(e[0].document.body).css({
                    overflow: "hidden",
                    overflowX: "hidden",
                    overflowY: "hidden"
                })
            }, this._exitFullViewport = function(t) {
                var e = this.getIframeParent() || $(window),
                    i = this.getIframe() || this.getDC(),
                    s = i.data("fsdata") || null;
                t && (e = $(window), i = this.getDC()), null != s && (e.scrollTop(s.scrollTop).scrollLeft(s.scrollLeft), $(e[0].document.body).css("overflow", s.bodyOverflow), $(e[0].document.body).css("overflow-x", s.bodyOverflowX), $(e[0].document.body).css("overflow-y", s.bodyOverflowY), s.iframeWidth > 0 && !t ? i.attr("width", s.iframeWidth + "px").attr("height", s.iframeHeight + "px") : i.width(s.targetWidth).height(s.targetHeight), i.attr("style", null == s.targetStyle ? "" : s.targetStyle).data("fsdata", null))
            }, this.pluginAPI = function() {
                var t = Array.prototype.slice.call(arguments) || null,
                    e = t.shift(),
                    i = t.shift();
                if (null != e && null != i)
                    for (var s = 0; this._plugins.length > s; s++)
                        if (this._plugins[s].name == e) return this._plugins[s][i](t[0]), void 0
            }, this.getPlayerVer = function() {
                return this.config._version
            }, this.getIsLastItem = function() {
                return this._currentItem == this.media.length - 1 && this.config._loop !== !0
            }, this.getIsFirstItem = function() {
                return 0 == this._currentItem && this.config._loop !== !0
            }, this.getItemConfig = function(t, e) {
                return this.getConfig(t, e)
            }, this.getConfig = function(t, e) {
                var i = e || this._currentItem,
                    s = null != this.config["_" + t] ? this.config["_" + t] : this.config[t];
                if (null == t) return this.media[i].config;
                if (null == this.config["_" + t]) try {
                    void 0 !== this.media[i].config[t] && (s = this.media[i].config[t])
                } catch (n) {}
                if (t.indexOf("plugin_") > -1) try {
                    this.media[i].config[t] && (s = $.extend(!0, {}, this.config[t], this.media[i].config[t]))
                } catch (n) {}
                if (null == s) return null;
                if ("object" == typeof s && null === s.length ? s = $.extend(!0, {}, s || {}) : "object" == typeof s && (s = $.extend(!0, [], s || [])), "string" == typeof s) switch (s) {
                    case "true":
                        s = !0;
                        break;
                    case "false":
                        s = !1;
                        break;
                    case "NaN":
                    case "undefined":
                    case "null":
                        s = null
                }
                return s
            }, this.getDC = function() {
                return this.env.playerDom
            }, this.getState = function(t) {
                var e = "IDLE";
                try {
                    e = this.playerModel.getState()
                } catch (i) {}
                return null != t ? e == t.toUpperCase() : e
            }, this.getLoadProgress = function() {
                try {
                    return this.playerModel.getLoadProgress()
                } catch (t) {
                    return 0
                }
            }, this.getKbPerSec = function() {
                try {
                    return this.playerModel.getKbPerSec()
                } catch (t) {
                    return 0
                }
            }, this.getItemCount = function() {
                return 1 == this.media.length && "na" == this.media[0].mediaModel ? 0 : this.media.length
            }, this.getItemId = function(t) {
                return this.media[t || this._currentItem].ID || null
            }, this.getItemIdx = function() {
                return this._currentItem
            }, this.getPlaylist = function() {
                return this.getItem("*")
            }, this.getItem = function() {
                if (1 == this.media.length && "na" == this.media[0].mediaModel) return null;
                switch (arguments[0] || "current") {
                    case "next":
                        return $.extend(!0, {}, this.media[this._currentItem + 1] || {});
                    case "prev":
                        return $.extend(!0, {}, this.media[this._currentItem - 1] || {});
                    case "current":
                        return $.extend(!0, {}, this.media[this._currentItem] || {});
                    case "*":
                        return $.extend(!0, [], this.media || []);
                    default:
                        return $.extend(!0, {}, this.media[arguments[0] || this._currentItem] || {})
                }
            }, this.getVolume = function() {
                return this.getConfig("fixedVolume") === !0 ? this.config.volume : this.getConfig("volume")
            }, this.getTrackId = function() {
                return this.getConfig("trackId") ? this.config.trackId : null != this._playlistServer ? "pl" + this._currentItem : null
            }, this.getLoadPlaybackProgress = function() {
                try {
                    return this.playerModel.getLoadPlaybackProgress()
                } catch (t) {
                    return 0
                }
            }, this.getSource = function() {
                try {
                    return this.playerModel.getSource()[0].src
                } catch (t) {
                    return !1
                }
            }, this.getDuration = function() {
                try {
                    return this.playerModel.getDuration()
                } catch (t) {
                    return 0
                }
            }, this.getPosition = function() {
                try {
                    return this.playerModel.getPosition() || 0
                } catch (t) {
                    return 0
                }
            }, this.getMaxPosition = function() {
                try {
                    return this.playerModel.getMaxPosition() || 0
                } catch (t) {
                    return 0
                }
            }, this.getFrame = function() {
                try {
                    return this.playerModel.getFrame()
                } catch (t) {
                    return 0
                }
            }, this.getTimeLeft = function() {
                try {
                    return this.playerModel.getDuration() - this.playerModel.getPosition()
                } catch (t) {
                    return this.media[this._currentItem].duration
                }
            }, this.getInFullscreen = function() {
                return this.getNativeFullscreenSupport().isFullScreen()
            }, this.getMediaContainer = function() {
                return null == this.env.mediaContainer && (this.env.mediaContainer = $("#" + this.getMediaId())), 0 == this.env.mediaContainer.length && (this.env.mediaContainer = this.env.playerDom.find("." + this.getNS() + "display").length > 0 ? $(document.createElement("div")).attr({
                    id: this.getId() + "_media"
                }).css({
                    overflow: "hidden",
                    height: "100%",
                    width: "100%",
                    top: 0,
                    left: 0,
                    padding: 0,
                    margin: 0,
                    display: "block"
                }).appendTo(this.env.playerDom.find("." + this.getNS() + "display")) : $(document.createElement("div")).attr({
                    id: this.getMediaId()
                }).css({
                    width: "1px",
                    height: "1px"
                }).appendTo($(document.body))), this.env.mediaContainer
            }, this.getMediaId = function() {
                return this.getId() + "_media"
            }, this.getMediaType = function() {
                try {
                    return this._getTypeFromFileExtension(this.playerModel.getSrc()) || "na/na"
                } catch (t) {
                    return "na/na"
                }
            }, this.getUsesFlash = function() {
                return this.playerModel.modelId.indexOf("FLASH") > -1
            }, this.getModel = function() {
                try {
                    return this.media[this._currentItem].mediaModel.toUpperCase()
                } catch (t) {
                    return "NA"
                }
            }, this.getIframeParent = this.getIframeWindow = function() {
                try {
                    var t = !1;
                    return this.config._iframe && (t = parent.location.host || !1), t === !1 ? !1 : $(parent.window)
                } catch (e) {
                    return !1
                }
            }, this.getIframe = function() {
                try {
                    var t = [];
                    return this.config._iframe && (t = window.$(frameElement) || []), 0 == t.length ? !1 : t
                } catch (e) {
                    return !1
                }
            }, this.getIframeAllowFullscreen = function() {
                var t = null;
                try {
                    t = window.frameElement.attributes.allowFullscreen || window.frameElement.attributes.mozallowFullscreen || window.frameElement.attributes.webkitallowFullscreen || null
                } catch (e) {
                    t = !0
                }
                return null != t ? !0 : !1
            }, this.getPlaybackQuality = function() {
                var t = "default";
                try {
                    t = this.playerModel.getPlaybackQuality()
                } catch (e) {}
                return "default" == t && (t = this.getConfig("playbackQuality")), ("default" == t || -1 == $.inArray(t, this.getPlaybackQualities())) && (t = this.getAppropriateQuality()), -1 == $.inArray(t, this.getPlaybackQualities()) && (t = "default"), t
            }, this.getPlaybackQualities = function() {
                try {
                    return $.extend(!0, [], this.media[this._currentItem].qualities || [])
                } catch (t) {}
                return []
            }, this.getIsMobileClient = function(t) {
                var e = navigator.userAgent.toLowerCase();
                var i = ["android", "windows ce", "blackberry", "palm", "mobile"];
                for (var s = 0; i.length > s; s++)
                    if (e.indexOf(i[s]) > -1) return t ? i[s].toUpperCase() == t.toUpperCase() : !0;
                return !1
            }, this.getCanPlay = function(t, e, i) {
                return this._canPlay(t, e, i)
            }, this.getCanPlayNatively = function(t) {
                return this._canPlay(t, "native")
            }, this.getPlatform = function() {
                return this.media[this._currentItem].platform || "error"
            }, this.getPlatforms = function() {
                var t = this,
                    e = this._testMediaSupport(!0),
                    i = this.getConfig("platforms"),
                    s = [],
                    n = [];
                try {
                    for (var a in this.media[this._currentItem].file)
                        if (this.media[this._currentItem].file.hasOwnProperty(a))
                            for (var r in e) this._canPlay(this.media[this._currentItem].file[a].type.replace(/x-/, ""), e[r].toLowerCase(), this.getConfig("streamType")) && -1 == $.inArray(e[r].toLowerCase(), n) && n.push(e[r].toLowerCase())
                } catch (o) {}
                return n.sort(function(t, e) {
                    return $.inArray(t, i) - $.inArray(e, i)
                }), n
            }, this.getNativeFullscreenSupport = function() {
                var t = this,
                    e = {
                        supportsFullScreen: "viewport",
                        isFullScreen: function() {
                            try {
                                return t.getDC().hasClass("fullscreen")
                            } catch (e) {
                                return !1
                            }
                        },
                        requestFullScreen: function() {
                            t.playerModel.applyCommand("fullscreen", !0)
                        },
                        cancelFullScreen: function() {
                            t.playerModel.applyCommand("fullscreen", !1)
                        },
                        prefix: "",
                        ref: this
                    },
                    i = "webkit moz o ms khtml".split(" ");
                if (document.cancelFullScreen !== void 0) e.supportsFullScreen = !0;
                else
                    for (var s = 0, n = i.length; n > s; s++)
                        if (e.prefix = i[s], document.createElement("video")[e.prefix + "EnterFullscreen"] !== void 0 && (e.supportsFullScreen = "mediaonly"), document[e.prefix + "CancelFullScreen"] !== void 0 && (e.supportsFullScreen = "dom", "moz" == e.prefix && document[e.prefix + "FullScreenEnabled"] === void 0 && (e.supportsFullScreen = "viewport")), e.supportsFullScreen !== !1 && "viewport" !== e.supportsFullScreen) break; return "viewport" == e.supportsFullScreen || "dom" == e.supportsFullScreen && this.getConfig("forceFullViewport") ? e : "mediaonly" == e.supportsFullScreen ? (e.requestFullScreen = function(e) {
                    t.playerModel.getMediaElement().get(0)[this.prefix + "EnterFullscreen"]()
                }, e.dest = {}, e.cancelFullScreen = function() {}, e) : (e.isFullScreen = function(e) {
                    var i = t.getIframe() ? parent.window.document : document;
                    switch (this.prefix) {
                        case "":
                            return i.fullScreen;
                        case "webkit":
                            return i.webkitIsFullScreen;
                        case "moz":
                            return i[this.prefix + "FullScreen"] || t.getDC().hasClass("fullscreen") && e !== !0;
                        default:
                            return i[this.prefix + "FullScreen"]
                    }
                }, e.requestFullScreen = function() {
                    if (!this.isFullScreen()) {
                        var e = t.getIframeParent() || $(window),
                            i = t.getIframe() ? t.getIframe().get(0) : null || t.getDC().get(0),
                            s = this,
                            n = t.getIframe() ? parent.window.document : document,
                            e = t.getIframeParent() || $(window);
                        e.data("fsdata", {
                            scrollTop: e.scrollTop(),
                            scrollLeft: e.scrollLeft()
                        }), $(n).unbind(this.prefix + "fullscreenchange.projekktor"), "" === this.prefix ? i.requestFullScreen() : i[this.prefix + "RequestFullScreen"](), s.ref.playerModel.applyCommand("fullscreen", !0), $(n).bind(this.prefix + "fullscreenchange.projekktor", function(t) {
                            if (s.isFullScreen(!0)) s.ref.playerModel.applyCommand("fullscreen", !0);
                            else {
                                s.ref.playerModel.applyCommand("fullscreen", !1);
                                var e = s.ref.getIframeParent() || $(window),
                                    i = e.data("fsdata");
                                null != i && (e.scrollTop(i.scrollTop), e.scrollLeft(i.scrollLeft))
                            }
                        })
                    }
                }, e.cancelFullScreen = function() {
                    var e = t.getIframe() ? parent.window.document : document,
                        i = t.getIframeParent() || $(window),
                        s = i.data("fsdata");
                    $(t.getIframe() ? parent.window.document : document).unbind(this.prefix + "fullscreenchange.projekktor"), e.exitFullScreen ? e.exitFullScreen() : "" == this.prefix ? e.cancelFullScreen() : e[this.prefix + "CancelFullScreen"](), null != s && (i.scrollTop(s.scrollTop), i.scrollLeft(s.scrollLeft)), t.playerModel.applyCommand("fullscreen", !1)
                }, e)
            }, this.getId = function() {
                return this._id
            }, this.getHasGUI = function() {
                try {
                    return this.playerModel.getHasGUI()
                } catch (t) {
                    return !1
                }
            }, this.getCssPrefix = this.getNS = function() {
                return this.config._cssClassPrefix || this.config._ns || "pp"
            }, this.getPlayerDimensions = function() {
                return {
                    width: this.getDC().width(),
                    height: this.getDC().height()
                }
            }, this.getMediaDimensions = function() {
                return this.playerModel.getMediaDimensions() || {
                    width: 0,
                    height: 0
                }
            }, this.getAppropriateQuality = function(t) {
                var e = t || this.getPlaybackQualities() || [];
                if (0 == e.length) return [];
                var i = this.env.playerDom.width(),
                    s = this.env.playerDom.height(),
                    n = $p.utils.roundNumber(i / s, 2),
                    a = {};
                return $.each(this.getConfig("playbackQualities") || [], function() {
                    if (0 > $.inArray(this.key, e)) return !0;
                    if ((this.minHeight || 0) > s && s >= a.minHeight) return !0;
                    if ((a.minHeight || 0) > this.minHeight) return !0;
                    if ("number" == typeof this.minWidth) {
                        if (0 === this.minWidth && this.minHeight > s) return !0;
                        if (this.minWidth > i) return !0;
                        a = this
                    } else if ("object" == typeof this.minWidth) {
                        var t = this;
                        $.each(this.minWidth, function() {
                            return (this.ratio || 100) > n ? !0 : this.minWidth > i ? !0 : (a = t, !0)
                        })
                    }
                    return !0
                }), $.inArray("auto", this.getPlaybackQualities()) > -1 ? "auto" : a.key || "default"
            }, this.getFromUrl = function(t, e, i, s, n) {
                var a = null,
                    r = this,
                    o = !this.getIsMobileClient();
                if (e == r && "_reelUpdate" == i && this._promote("scheduleLoading", 1 + this.getItemCount()), "_" != i.substr(0, 1) ? window[i] = function(t) {
                        try {
                            delete window[i]
                        } catch (s) {}
                        e[i](t)
                    } : n.indexOf("jsonp") > -1 && (this["_jsonp" + i] = function(t) {
                        e[i](t)
                    }), n) {
                    if (void 0 == $.parseJSON && n.indexOf("json") > -1) return this._raiseError("Projekktor requires at least jQuery 1.4.2 in order to handle JSON playlists."), this;
                    n = n.indexOf("/") > -1 ? n.split("/")[1] : n
                }
                var l = {
                    url: t,
                    complete: function(t, r) {
                        if (void 0 == n) try {
                            t.getResponseHeader("Content-Type").indexOf("xml") > -1 && (n = "xml"), t.getResponseHeader("Content-Type").indexOf("json") > -1 && (n = "json"), t.getResponseHeader("Content-Type").indexOf("html") > -1 && (n = "html")
                        } catch (o) {}
                        a = $p.utils.cleanResponse(t.responseText, n);
                        try {
                            a = s(a, t.responseText, e)
                        } catch (o) {}
                        if ("error" != r && "jsonp" != n) try {
                            e[i](a)
                        } catch (o) {}
                    },
                    error: function(t) {
                        e[i] && "jsonp" != n && e[i](!1)
                    },
                    cache: !0,
                    async: o,
                    dataType: n,
                    jsonpCallback: "_" != i.substr(0, 1) ? !1 : "projekktor('" + this.getId() + "')._jsonp" + i,
                    jsonp: "_" != i.substr(0, 1) ? !1 : "callback"
                };
                return l.xhrFields = {
                    withCredentials: !0
                }, l.beforeSend = function(t) {
                    t.withCredentials = !0
                }, $.support.cors = !0, $.ajax(l), this
            }, this.setActiveItem = function(t) {
                var e = 0,
                    i = this._currentItem,
                    s = this,
                    n = !1;
                if ("string" == typeof t) switch (t) {
                    case "same":
                        e = this._currentItem;
                        break;
                    case "previous":
                        e = this._currentItem - 1;
                        break;
                    case "next":
                        e = this._currentItem + 1
                } else e = "number" == typeof t ? parseInt(t) : 0;
                if (e != this._currentItem && 1 == this.getConfig("disallowSkip") && !this.getState("COMPLETED") && !this.getState("IDLE")) return this;
                this._detachplayerModel(), this.env.loading = !1, 0 !== e || null != i && i != e || !(this.config._autoplay === !0 || "DESTROYING|AWAKENING".indexOf(this.getState()) > -1) ? this.getItemCount() > 1 && e != i && null != i && this.config._continuous === !0 && this.getItemCount() > e && (n = !0) : n = !0, (e >= this.getItemCount() || 0 > e) && (n = this.config._loop, e = 0), this._currentItem = e;
                var a = this.getDC().hasClass("fullscreen");
                this.getDC().attr("class", this.env.className), a && this.getDC().addClass("fullscreen");
                var r = this.media[this._currentItem].mediaModel.toUpperCase();
                return $p.models[r] ? (null != this.getConfig("className", null) && this.getDC().addClass(this.getNS() + this.getConfig("className")), this.getDC().addClass(this.getNS() + (this.getConfig("streamType") || "http")), $p.utils.cssTransitions() || this.getDC().addClass("notransitions"), this.getIsMobileClient() && this.getDC().addClass("mobile")) : (r = "NA", this.media[this._currentItem].mediaModel = r, this.media[this._currentItem].errorCode = 8), this.playerModel = new playerModel, $.extend(this.playerModel, $p.models[r].prototype), this._promote("syncing", "display"), this._enqueue(function() {
                    try {
                        s._applyCuePoints()
                    } catch (t) {}
                }), this.playerModel._init({
                    media: $.extend(!0, {}, this.media[this._currentItem]),
                    model: r,
                    pp: this,
                    environment: $.extend(!0, {}, this.env),
                    autoplay: n,
                    quality: this.getPlaybackQuality(),
                    fullscreen: this.getInFullscreen()
                }), this
            }, this.setPlay = function() {
                var t = this;
                return this.getConfig("thereCanBeOnlyOne") && projekktor("*").each(function() {
                    this.getId() !== t.getId() && this.setStop()
                }), this._enqueue("play", !1), this
            }, this.setPause = function() {
                return this._enqueue("pause", !1), this
            }, this.setStop = function(t) {
                var e = this;
                return this.getState("IDLE") ? this : (t ? this._enqueue(function() {
                    e._currentItem = 0, e.setActiveItem(0)
                }) : this._enqueue("stop", !1), this)
            }, this.setPlayPause = function() {
                return this.getState("PLAYING") ? this.setPause() : this.setPlay(), this
            }, this.setVolume = function(t, e) {
                var i = this.getVolume();
                if (1 == this.getConfig("fixedVolume")) return this;
                switch (typeof t) {
                    case "string":
                        var s = t.substr(0, 1);
                        switch (t = parseFloat(t.substr(1)), s) {
                            case "+":
                                t = this.getVolume() + t;
                                break;
                            case "-":
                                t = this.getVolume() - t;
                                break;
                            default:
                                t = this.getVolume()
                        }
                    case "number":
                        t = parseFloat(t), t = t > 1 ? 1 : t, t = 0 > t ? 0 : t;
                        break;
                    default:
                        return this
                }
                if (t > i && e) {
                    if (t - i > .03) {
                        for (var n = i; t >= n; n += .03) this._enqueue("volume", n, e);
                        return this._enqueue("volume", t, e), this
                    }
                } else if (i > t && e && i - t > .03) {
                    for (var n = i; n >= t; n -= .03) this._enqueue("volume", n, e);
                    return this._enqueue("volume", t, e), this
                }
                return this._enqueue("volume", t), this
            }, this.setPlayhead = this.setSeek = function(t) {
                if (1 == this.getConfig("disallowSkip")) return this;
                if ("string" == typeof t) {
                    var e = t.substr(0, 1);
                    t = parseFloat(t.substr(1)), t = "+" == e ? this.getPosition() + t : "-" == e ? this.getPosition() - t : this.getPosition()
                }
                return "number" == typeof t && this._enqueue("seek", Math.round(100 * t) / 100), this
            }, this.setFrame = function(t) {
                if (null == this.getConfig("fps")) return this;
                if (1 == this.getConfig("disallowSkip")) return this;
                if ("string" == typeof t) {
                    var e = t.substr(0, 1);
                    t = parseFloat(t.substr(1)), t = "+" == e ? this.getFrame() + t : "-" == e ? this.getFrame() - t : this.getFrame()
                }
                return "number" == typeof t && this._enqueue("frame", t), this
            }, this.setPlayerPoster = function(t) {
                var e = this;
                return this._enqueue(function() {
                    e.setConfig({
                        poster: t
                    }, 0)
                }), this._enqueue(function() {
                    e.playerModel.setPosterLive()
                }), this
            }, this.setConfig = function() {
                var t = this,
                    e = arguments;
                return this._enqueue(function() {
                    t._setConfig(e[0] || null, e[1] || null)
                }), this
            }, this._setConfig = function() {
                if (!arguments.length) return result;
                var confObj = arguments[0],
                    dest = "*",
                    value = !1;
                if ("object" != typeof confObj) return this;
                dest = "string" == arguments[1] || "number" == arguments[1] ? arguments[1] : this._currentItem;
                for (var i in confObj)
                    if (null == this.config["_" + i]) {
                        try {
                            value = eval(confObj[i])
                        } catch (e) {
                            value = confObj[i]
                        }
                        if ("*" != dest) {
                            if (void 0 == this.media[dest]) return this;
                            null == this.media[dest].config && (this.media[dest].config = {}), this.media[dest].config[i] = value
                        } else $.each(this.media, function() {
                            null == this.config && (this.config = {}), this.config[i] = value
                        })
                    }
                return this
            } /*this.setFullscreen=function(t){var e=this.getNativeFullscreenSupport();return t=null==t?!e.isFullScreen():t,t===!0?e.requestFullScreen():e.cancelFullScreen(),this}*/ , this.setSize = function(t) {
                var e = this.getIframe() || this.getDC(),
                    i = e.data("fsdata") || null,
                    s = t && null != t.width ? t.width : null != this.getConfig("width") ? this.getConfig("width") : !1,
                    n = t && null != t.height ? t.height : null == this.getConfig("height") && this.getConfig("ratio") ? Math.round((s || this.getDC().width()) / this.getConfig("ratio")) : null != this.getConfig("height") ? this.getConfig("height") : !1;
                this.getInFullscreen() && null != i ? (i.targetWidth = s, i.targetHeight = n, e.data("fsdata", i)) : (s && e.css({
                    width: s + "px"
                }), n && e.css({
                    height: n + "px"
                }));
                try {
                    this.playerModel.applyCommand("resize")
                } catch (a) {}
            }, this.setLoop = function(t) {
                this.config._loop = t || !this.config._loop
            }, this.setDebug = function(t) {
                $p.utils.logging = t || !$p.utils.logging, $p.utils.logging && $p.utils.log("DEBUG MODE for player #" + this.getId())
            }, this.addListener = function(t, e) {
                var i = this;
                return this._enqueue(function() {
                    i._addListener(t, e)
                }), this
            }, this._addListener = function(t, e) {
                var i = t.indexOf(".") > -1 ? t.split(".") : [t, "default"];
                return this.listeners.push({
                    event: i[0],
                    ns: i[1],
                    callback: e
                }), this
            }, this.removeListener = function(t, e) {
                var i = this.listeners.length,
                    s = t.indexOf(".") > -1 ? t.split(".") : [t, "*"];
                for (var n = 0; i > n; n++) void 0 != this.listeners[n] && (this.listeners[n].event == s[0] || "*" === s[0]) && (this.listeners[n].ns != s[1] && "*" !== s[1] || this.listeners[n].callback != e && null != e || this.listeners.splice(n, 1));
                return this
            }, this.setItem = function() {
                var t = arguments[0];
                var e = 0;
                return this._clearqueue(), null == t ? (e = this._removeItem(arguments[1]), e === this._currentItem && this.setActiveItem("previous")) : (e = this._addItem(this._prepareMedia({
                    file: t,
                    config: t.config || {}
                }), arguments[1], arguments[2]), e === this._currentItem && this.setActiveItem(this._currentItem)), this
            }, this.setFile = function() {
                var t = arguments[0] || "",
                    e = arguments[1] || this._getTypeFromFileExtension(t),
                    i = [];
                return this.env.loading === !0 ? this : (this._clearqueue(), this.env.loading = !0, this._detachplayerModel(), "object" == typeof t ? ($p.utils.log("Applying incoming JS Object", t), this._reelUpdate(t), this) : (i[0] = {}, i[0].file = {}, i[0].file.src = t || "", i[0].file.type = e || this._getTypeFromFileExtension(splt[0]), i[0].file.type.indexOf("/xml") > -1 || i[0].file.type.indexOf("/json") > -1 ? ($p.utils.log("Loading external data from " + i[0].file.src + " supposed to be " + i[0].file.type), this._playlistServer = i[0].file.src, this.getFromUrl(i[0].file.src, this, "_reelUpdate", this.getConfig("reelParser"), i[0].file.type), this) : ($p.utils.log("Applying incoming single file:" + i[0].file.src, i), this._reelUpdate(i), this)))
            }, this.setPlaybackQuality = function(t) {
                var e = t || this.getAppropriateQuality();
                return $.inArray(e, this.media[this._currentItem].qualities || []) > -1 && (this.playerModel.applyCommand("quality", e), this.setConfig({
                    playbackQuality: e
                })), this
            }, this.openUrl = function(t) {
                return t = t || {
                    url: "",
                    target: "",
                    pause: !1
                }, "" == t.url ? this : (t.pause === !0 && this.setPause(), window.open(t.url, t.target).focus(), this)
            }, this.selfDestruct = this.destroy = function() {
                var t = this;
                return this._enqueue(function() {
                    t._destroy()
                }), this
            }, this._destroy = function() {
                var t = this;
                return $(this).unbind(), this.removePlugins(), this.playerModel.destroy(), this._removeGUIListeners(), $.each(projekktors, function(e) {
                    try {
                        if (this.getId() == t.getId() || this.getId() == t.getId() || this.getParent() == t.getId()) return projekktors.splice(e, 1), void 0
                    } catch (i) {}
                }), this.env.playerDom.replaceWith(this.env.srcNode), this._promote("destroyed"), this.removeListener("*"), this
            }, this.reset = function() {
                var t = this;
                return this._clearqueue(), this._enqueue(function() {
                    t._reset()
                }), this
            }, this._reset = function() {
                var t = {},
                    e = this;
                this.setFullscreen(!1), $(this).unbind(), $(this.getIframe() ? parent.window.document : document).unbind(".projekktor"), $(window).unbind(".projekktor" + this.getId()), this.playerModel.destroy(), this.playerModel = {}, this.removePlugins(), this._removeGUIListeners(), this.env.mediaContainer = null, this._currentItem = null;
                for (var i in this.config) t["_" == i.substr(0, 1) ? i.substr(1) : i] = this.config[i];
                return t.autoplay = !1, "function" == typeof this.env.onReady && this._enqueue(e.env.onReady(e)), this._init(this.env.playerDom, t), this
            }, this.setCuePoint = function(t, e) {
                var i = void 0 !== t.item ? t.item : this.getItemIdx(),
                    s = $.extend(!0, {
                        offset: 0
                    }, e),
                    n = this,
                    a = {
                        id: t.id || $p.utils.randomId(8),
                        group: t.group || $p.utils.randomId(8),
                        item: i,
                        on: ($p.utils.toSeconds(t.on) || 0) + s.offset,
                        off: ($p.utils.toSeconds(t.off) || $p.utils.toSeconds(t.on) || 0) + s.offset,
                        value: t.value || null,
                        callback: t.callback || function() {},
                        precision: null == t.precision ? 1 : t.precision,
                        title: null == t.title ? "" : t.title,
                        _listeners: [],
                        _unlocked: !1,
                        _active: !1,
                        _lastTime: 0,
                        isAvailable: function() {
                            return this._unlocked
                        },
                        _stateListener: function(t, e) {
                            if ("STOPPED|COMPLETED|DESTROYING".indexOf(t) > -1) {
                                if (this._active) try {
                                    this.callback(!1, this, e)
                                } catch (i) {}
                                this._active = !1, this._lastTime = -1
                            }
                        },
                        _timeListener: function(t, e) {
                            if (e.getItemIdx() === this.item || "*" == this.item) {
                                var i = 0 == this.precision ? Math.round(t) : $p.utils.roundNumber(t, this.precision),
                                    s = this;
                                if (this._unlocked === !1) {
                                    var n = e.getDuration() * e.getLoadProgress() / 100;
                                    if (!(n >= this.on || i >= this.on)) return;
                                    $.each(this._listeners.unlock || [], function() {
                                        this(s, e)
                                    }), this._unlocked = !0
                                }
                                if (this._lastTime != i) {
                                    var a = 1 >= i - this._lastTime && i - this._lastTime > 0;
                                    if ((i >= this.on && this.off >= i || i >= this.on && this.on == this.off && this.on + 1 >= i) && this._active !== !0) {
                                        this._active = !0, $p.utils.log("Cue Point: [ON " + this.on + "] at " + i, this);
                                        try {
                                            this.callback({
                                                id: this.id,
                                                enabled: !0,
                                                value: this.value,
                                                seeked: !a,
                                                player: e
                                            })
                                        } catch (r) {}
                                    } else if ((this.on > i || i > this.off) && this.off != this.on && 1 == this._active) {
                                        this._active = !1, $p.utils.log("Cue Point: [OFF] at " + this.off, this);
                                        try {
                                            this.callback({
                                                id: this.id,
                                                enabled: !1,
                                                value: this.value,
                                                seeked: !a,
                                                player: e
                                            })
                                        } catch (r) {}
                                    }
                                    this.off == this.on && this._active && new Number(i - this.on).toPrecision(this.precision) >= 1 && (this._active = !1), this._lastTime = i
                                }
                            }
                        },
                        addListener: function(t, e) {
                            null == this._listeners[t] && (this._listeners[t] = []), this._listeners[t].push(e || function() {})
                        }
                    };
                return null != t.unlockCallback && a.addListener("unlock", t.unlockCallback), null == this._cuePoints[i] && (this._cuePoints[i] = []), this._cuePoints[i].push(a), this.getState("IDLE") || this._promote("cuepointAdded"), this
            }, this.setGotoCuePoint = function(t, e) {
                var i = this.getCuePoints(e);
                return this.setPlayhead(i[t].on), this
            }, this.getCuePoints = function(t) {
                return this._cuePoints[t || this.getItemIdx()] || this._cuePoints || {}
            }, this.getCuePointById = function(t, e) {
                var i = !1,
                    s = this.getCuePoints(e);
                for (var n = 0; s.length > n; n++)
                    if (s.id == t) {
                        i = this;
                        break
                    }
                return i
            }, this.removeCuePoints = function(t, e) {
                var i = this.getCuePoints(t || 0) || {},
                    s = [];
                for (var n = 0; i.length > n; n++) i[n].group == e && (this.removeListener("time", i[n].timeEventHandler), this.removeListener("state", i[n].stateEventHandler), s.push(n));
                for (var a = 0; s.length > a; a++) i.splice(s[a] - a, 1);
                return this
            }, this.syncCuePoints = function() {
                var t = this;
                return this._enqueue(function() {
                    try {
                        t._applyCuePoints()
                    } catch (e) {}
                }), this
            }, this._applyCuePoints = function(t) {
                var e = this;
                (null != this._cuePoints[this._currentItem] || null != this._cuePoints["*"]) && $.each($.merge(this._cuePoints[this._currentItem] || [], this._cuePoints["*"] || []), function(t, i) {
                    try {
                        e.removeListener("time", i.timeEventHandler), e.removeListener("state", i.stateEventHandler)
                    } catch (s) {}
                    i.timeEventHandler = function(t, e) {
                        try {
                            i._timeListener(t, e)
                        } catch (s) {}
                    }, i.stateEventHandler = function(t, e) {
                        try {
                            i._stateListener(t, e)
                        } catch (s) {}
                    }, e.addListener("time", i.timeEventHandler), e.addListener("state", i.stateEventHandler), e.addListener("item", function() {
                        e.removeListener("time", i.timeEventHandler), e.removeListener("state", i.stateEventHandler)
                    })
                })
            }, this._enqueue = function(t, e, i) {
                null != t && (this._queue.push({
                    command: t,
                    params: e,
                    delay: i
                }), this._processQueue())
            }, this._clearqueue = function(t, e) {
                this._isReady === !0 && (this._queue = [])
            }, this._processQueue = function() {
                var t = this,
                    e = !1;
                this._processing !== !0 && this.env.loading !== !0 && (this._processing = !0, function() {
                    try {
                        e = t.playerModel.getIsReady()
                    } catch (i) {}
                    if (t.env.loading !== !0 && e) {
                        try {
                            var s = t._queue.shift();
                            null != s && ("string" == typeof s.command ? s.delay > 0 ? setTimeout(function() {
                                t.playerModel.applyCommand(s.command, s.params)
                            }, s.delay) : t.playerModel.applyCommand(s.command, s.params) : s.command(t))
                        } catch (i) {
                            $p.utils.log("ERROR:", i)
                        }
                        return 0 == t._queue.length ? (t._isReady === !1 && (t._isReady = !0), t._processing = !1, void 0) : (arguments.callee(), void 0)
                    }
                    setTimeout(arguments.callee, 100)
                }())
            }, this._getTypeFromFileExtension = function(t) {
                var e = "",
                    i = [],
                    s = {},
                    i = [],
                    n = null,
                    a = !0;
                for (var r in $p.mmap)
                    if ($p.mmap.hasOwnProperty(r)) {
                        n = $p.mmap[r].platform, "object" != typeof n && (n = [n]), a = !0;
                        for (var o = 0; n.length > o; o++) null != n[o] && (this.getConfig("enable" + n[o].toUpperCase() + "Platform") === !1 || -1 === $.inArray(n[o], this.getConfig("platforms"))) && (a = !1);
                        if (a === !1) continue;
                        i.push("\\." + $p.mmap[r].ext), s[$p.mmap[r].ext] = $p.mmap[r]
                    }
                i = "^.*.(" + i.join("|") + ")";
                try {
                    e = t.match(RegExp(i))[1], e = e ? e.replace(".", "") : "NaN"
                } catch (l) {
                    e = "NaN"
                }
                return s[e].type
            }, this._testMediaSupport = function(t) {
                var e = {},
                    i = [],
                    s = "",
                    n = this;
                if (t) {
                    if (null != $p._platformTableCache) return $p._platformTableCache
                } else if (null != $p._compTableCache) return $p._compTableCache;
                for (var a = 0; $p.mmap.length > a; a++) $p.mmap.hasOwnProperty(a) && (platforms = "object" == typeof $p.mmap[a].platform ? $p.mmap[a].platform : [$p.mmap[a].platform], $.each(platforms, function(t, r) {
                    return null == r ? !0 : (s = $p.mmap[a].streamType || ["http"], $.each(s, function(t, s) {
                        if (null == e[s] && (e[s] = {}), null == e[s][r] && (e[s][r] = []), $.inArray($p.mmap[a].type, e[s][r]) > -1) return !0;
                        var o = "" + ($p.models[$p.mmap[a].model.toUpperCase()].prototype[r.toLowerCase() + "Version"] || "1");
                        try {
                            if ($p.utils.versionCompare($p.platforms[r.toUpperCase()]($p.mmap[a].type), o)) return 0 != n.getConfig("enable" + r.toUpperCase() + "Platform") && $.inArray(r.toLowerCase(), n.getConfig("platforms")) > -1 && (e[s][r].push($p.mmap[a].type), -1 == $.inArray(r.toUpperCase(), i) && i.push(r.toUpperCase())), !0
                        } catch (l) {
                            $p.utils.log("ERROR", "platform " + r + " not defined")
                        }
                        return !0
                    }), !0)
                }));
                return $p._compTableCache = e, $p._platformTableCache = i, t ? $p._platformTableCache : $p._compTableCache
            }, this._readMediaTag = function(t) {
                var e = {},
                    i = "",
                    s = [],
                    n = this;
                if (-1 == "VIDEOAUDIO".indexOf(t[0].tagName.toUpperCase())) return !1;
                this.getConfig("ignoreAttributes") || (e = {
                    autoplay: void 0 === t.attr("autoplay") && void 0 === t.prop("autoplay") || t.prop("autoplay") === !1 ? !1 : !0,
                    controls: void 0 === t.attr("controls") && void 0 === t.prop("controls") || t.prop("controls") === !1 ? !1 : !0,
                    loop: void 0 === t.attr("autoplay") && void 0 === t.prop("loop") || t.prop("loop") === !1 ? !1 : !0,
                    title: void 0 !== t.attr("title") && t.attr("title") !== !1 ? t.attr("title") : "",
                    poster: void 0 !== t.attr("poster") && t.attr("poster") !== !1 ? t.attr("poster") : "",
                    width: void 0 !== t.attr("width") && t.attr("width") !== !1 ? t.attr("width") : null,
                    height: void 0 !== t.attr("height") && t.attr("height") !== !1 ? t.attr("height") : null
                }), i = $($("<div></div>").html($(t).clone())).html(), s = ["autoplay", "controls", "loop"];
                for (var a = 0; s.length > a; a++) - 1 != i.indexOf(s[a]) && (e[s[a]] = !0);
                if (e.playlist = [], e.playlist[0] = [], e.playlist[0].config = {
                        tracks: []
                    }, t.attr("src") && e.playlist[0].push({
                        src: t.attr("src"),
                        type: t.attr("type") || this._getTypeFromFileExtension(t.attr("src"))
                    }), !$("<video/>").get(0).canPlayType) {
                    var r = t;
                    do
                        if (r = r.next("source,track"), r.attr("src")) switch (r.get(0).tagName.toUpperCase()) {
                            case "SOURCE":
                                e.playlist[0].push({
                                    src: r.attr("src"),
                                    type: r.attr("type") || this._getTypeFromFileExtension(r.attr("src")),
                                    quality: r.attr("data-quality") || ""
                                });
                                break;
                            case "TRACK":
                                $(this).attr("src") && e.playlist[0].config.tracks.push({
                                    src: r.attr("src"),
                                    kind: r.attr("kind") || "subtitle",
                                    lang: r.attr("srclang") || null,
                                    label: r.attr("label") || null
                                })
                        }
                        while (r.attr("src"))
                }
                return 0 == e.playlist[0].length && t.children("source,track").each(function() {
                    if ($(this).attr("src")) switch ($(this).get(0).tagName.toUpperCase()) {
                        case "SOURCE":
                            e.playlist[0].push({
                                src: $(this).attr("src"),
                                type: $(this).attr("type") || n._getTypeFromFileExtension($(this).attr("src")),
                                quality: $(this).attr("data-quality") || ""
                            });
                            break;
                        case "TRACK":
                            e.playlist[0].config.tracks.push({
                                src: $(this).attr("src"),
                                kind: $(this).attr("kind") || "subtitle",
                                lang: $(this).attr("srclang") || null,
                                label: $(this).attr("label") || null
                            })
                    }
                }), e
            }, this._raiseError = function(t) {
                this.env.playerDom.html(t).css({
                    color: "#fdfdfd",
                    backgroundColor: "#333",
                    lineHeight: this.config.height + "px",
                    textAlign: "center",
                    display: "block"
                }), this._promote("error")
            }, this._init = function(t, e) {
                var i = t || srcNode,
                    s = e || cfg,
                    n = this._readMediaTag(i);
                if (this.env.srcNode = i.wrap("<div></div>").parent().html(), i.unwrap(), this.env.className = i.attr("class") || "", this._id = i[0].id || $p.utils.randomId(8), n !== !1) {
                    this.env.playerDom = $("<div/>").attr({
                        "class": i[0].className,
                        style: i.attr("style")
                    }), i.replaceWith(this.env.playerDom), i.empty().removeAttr("type").removeAttr("src");
                    try {
                        i.get(0).pause(), i.get(0).load()
                    } catch (a) {}
                    $("<div/>").append(i).get(0).innerHTML = "", i = null
                } else this.env.playerDom = i;
                s = $.extend(!0, {}, n, s);
                for (var r in s) null != this.config["_" + r] ? this.config["_" + r] = s[r] : this.config[r] = r.indexOf("plugin_") > -1 ? $.extend(this.config[r], s[r]) : s[r];
                if ($p.utils.logging = this.config._debug, this.setSize(), this.getIsMobileClient() && (this.config._autoplay = !1, this.config.fixedVolume = !0), this.env.playerDom.attr("id", this._id), this.config._theme) switch (typeof this.config._theme) {
                    case "string":
                        break;
                    case "object":
                        this._applyTheme(this.config._theme)
                } else this._start(!1);
                return this
            }, this._start = function(t) {
                var e = this,
                    i = this.getIframeParent();
                this._registerPlugins(), this.config._iframe === !0 && (i ? i.ready(function() {
                    e._enterFullViewport(!0)
                }) : e._enterFullViewport(!0)), i === !1 && (this.config._isCrossDomain = !0), this.getIframeAllowFullscreen() || (this.config.enableFullscreen = !1), "function" == typeof onReady && this._enqueue(function() {
                    onReady(e)
                });
                for (var s in this.config._playlist[0])
                    if (this.config._playlist[0][s].type && (this.config._playlist[0][s].type.indexOf("/json") > -1 || this.config._playlist[0][s].type.indexOf("/xml") > -1)) return this.setFile(this.config._playlist[0][s].src, this.config._playlist[0][s].type), this;
                return this.setFile(this.config._playlist), this
            }, this._applyTheme = function(data) {
                var ref = this;
                if (data === !1) return this._raiseError("The Projekktor theme-set specified could not be loaded."), !1;
                if ("string" == typeof data.css && $("head").append('<style type="text/css">' + $p.utils.parseTemplate(data.css, {
                        rp: data.baseURL
                    }) + "</style>"), "string" == typeof data.html && this.env.playerDom.html($p.utils.parseTemplate(data.html, {
                        p: this.getNS()
                    })), this.env.playerDom.addClass(data.id).addClass(data.variation), this.env.className = this.env.className && 0 !== this.env.className.length ? this.env.className + " " + data.id : data.id, data.variation && 0 !== data.variation.length && (this.env.className += " " + data.variation), "object" == typeof data.config) {
                    for (var i in data.config) null != this.config["_" + i] ? this.config["_" + i] = data.config[i] : this.config[i] = i.indexOf("plugin_") > -1 ? $.extend(!0, {}, this.config[i], data.config[i]) : data.config[i];
                    if ("object" == typeof data.config.plugins)
                        for (var i = 0; data.config.plugins.length > i; i++) try {
                            typeof eval("projekktor" + data.config.plugins[i])
                        } catch (e) {
                            return this._raiseError("The applied theme requires the following Projekktor plugin(s): <b>" + data.config.plugins.join(", ") + "</b>"), !1
                        }
                }
                return data.onReady && this._enqueue(function(player) {
                    eval(data.onReady)
                }), this._start()
            }, this._init()
        }
    }, $p.mmap = [], $p.models = {}, $p.newModel = function(t, e) {
        if ("object" != typeof t) return i;
        if (!t.modelId) return i;
        var i = !1,
            s = $p.models[e] && void 0 != e ? $p.models[e].prototype : {};
        if ($p.models[t.modelId]) return i;
        $p.models[t.modelId] = function() {}, $p.models[t.modelId].prototype = $.extend({}, s, t), t.setiLove && t.setiLove(), $p.mmap = $.grep($p.mmap, function(e) {
            var i = e.model != (t.replace ? t.replace.toLowerCase() : ""),
                s = e.replaces != t.modelId;
            return i && s
        });
        for (var n = 0; t.iLove.length > n; n++) t.iLove[n].model = t.modelId.toLowerCase(), t.iLove[n].replaces = t.replace ? t.replace.toLowerCase() : "", $p.mmap.push(t.iLove[n]);
        return !0
    }
});
var projekktorConfig = function(t) {
    this._version = t
};
projekktorConfig.prototype = {
    _playerName: "Projekktor",
    _playerHome: "http://www.projekktor.com?via=context",
    _cookieName: "projekktor",
    _cookieExpiry: 356,
    _plugins: ["display", "controlbar"],
    _addplugins: [],
    _reelParser: null,
    _ns: "pp",
    _platforms: ["browser", "android", "ios", "native", "flash", "vlc"],
    _iframe: !1,
    _ignoreAttributes: !1,
    _loop: !1,
    _autoplay: !1,
    _continuous: !0,
    _thereCanBeOnlyOne: !0,
    _leaveFullscreen: !0,
    _playlist: [],
    _theme: !1,
    _themeRepo: !1,
    _messages: {
        0: "#0 An (unknown) error occurred.",
        1: "#1 You aborted the media playback. ",
        2: "#2 A network error caused the media download to fail part-way. ",
        3: "#3 The media playback was aborted due to a corruption problem. ",
        4: "#4 The media (%{title}) could not be loaded because the server or network failed.",
        5: "#5 Sorry, your browser does not support the media format of the requested file.",
        6: "#6 Your client is in lack of the Flash Plugin V%{flashver} or higher.",
        7: "#7 No media scheduled.",
        8: "#8 ! Invalid media model configured !",
        9: "#9 File (%{file}) not found.",
        10: "#10 Invalid or missing quality settings for %{title}.",
        11: "#11 Invalid streamType and/or streamServer settings for %{title}.",
        12: "#12 Invalid or inconsistent quality setup for %{title}.",
        80: "#80 The requested file does not exist or is delivered with an invalid content-type.",
        97: "No media scheduled.",
        98: "Invalid or malformed playlist data!",
        99: "Click display to proceed. ",
        100: "Keyboard Shortcuts",
        500: "This Youtube video has been removed or set to private",
        501: "The Youtube user owning this video disabled embedding.",
        502: "Invalid Youtube Video-Id specified."
    },
    _debug: !1,
    _width: null,
    _height: null,
    _ratio: !1,
    _keys: [],
    _isCrossDomain: !1,
    _forceFullViewport: !1,
    id: 0,
    title: null,
    cat: "clip",
    poster: null,
    controls: !0,
    start: !1,
    stop: !1,
    volume: .5,
    cover: "",
    disablePause: !1,
    disallowSkip: !1,
    fixedVolume: !1,
    imageScaling: "aspectratio",
    videoScaling: "aspectratio",
    playerFlashMP4: "",
    playerFlashMP3: "",
    streamType: "http",
    streamServer: "",
    startParameter: "start",
    useYTIframeAPI: !0,
    enableKeyboard: !0,
    enableFullscreen: !0,
    playbackQuality: "default",
    _playbackQualities: [{
        key: "small",
        minHeight: 240,
        minWidth: 240
    }, {
        key: "medium",
        minHeight: 360,
        minWidth: [{
            ratio: 1.77,
            minWidth: 640
        }, {
            ratio: 1.33,
            minWidth: 480
        }]
    }, {
        key: "large",
        minHeight: 480,
        minWidth: [{
            ratio: 1.77,
            minWidth: 853
        }, {
            ratio: 1.33,
            minWidth: 640
        }]
    }, {
        key: "hd1080",
        minHeight: 1080,
        minWidth: [{
            ratio: 1.77,
            minWidth: 1920
        }, {
            ratio: 1.33,
            minWidth: 1440
        }]
    }, {
        key: "hd720",
        minHeight: 720,
        minWidth: [{
            ratio: 1.77,
            minWidth: 1280
        }, {
            ratio: 1.33,
            minWidth: 960
        }]
    }, {
        key: "highres",
        minHeight: 1081,
        minWidth: 0
    }],
    enableTestcard: !0,
    skipTestcard: !1,
    duration: 0,
    className: ""
}, jQuery(function(t) {
    $p.utils = {
        imageDummy: function() {
            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABBJREFUeNpi/v//PwNAgAEACQsDAUdpTjcAAAAASUVORK5CYII="
        },
        capitalise: function(t) {
            return t.charAt(0).toUpperCase() + t.slice(1).toLowerCase()
        },
        blockSelection: function(t) {
            return t && t.css({
                "-khtml-user-select": "none",
                "-webkit-user-select": "none",
                MozUserSelect: "none",
                "user-select": "none"
            }).attr("unselectable", "on").bind("selectstart", function() {
                return !1
            }), t
        },
        unique: function(e) {
            var i = [];
            for (var s = e.length; s--;) {
                var n = e[s]; - 1 === t.inArray(n, i) && i.unshift(n)
            }
            return i
        },
        intersect: function(e, i) {
            var s = [];
            return t.each(e, function(n) {
                try {
                    t.inArray(i, e[n]) > -1 && s.push(e[n])
                } catch (a) {}
                try {
                    t.inArray(e[n], i) > -1 && s.push(e[n])
                } catch (a) {}
            }), s
        },
        roundNumber: function(t, e) {
            return 0 >= t || isNaN(t) ? 0 : Math.round(t * Math.pow(10, e)) / Math.pow(10, e)
        },
        randomId: function(t) {
            var e = "abcdefghiklmnopqrstuvwxyz",
                i = "";
            for (var s = 0; t > s; s++) {
                var n = Math.floor(Math.random() * e.length);
                i += e.substring(n, n + 1)
            }
            return i
        },
        toAbsoluteURL: function(t) {
            var e = location,
                i, s, n, a;
            if (null == t || "" == t) return "";
            if (/^\w+:/.test(t)) return t;
            if (i = e.protocol + "//" + e.host, 0 === t.indexOf("/")) return i + t;
            if (s = e.pathname.replace(/\/[^\/]*$/, ""), n = t.match(/\.\.\//g))
                for (t = t.substring(3 * n.length), a = n.length; a--;) s = s.substring(0, s.lastIndexOf("/"));
            return i + s + "/" + t
        },
        strip: function(t) {
            return t.replace(/^\s+|\s+$/g, "")
        },
        toSeconds: function(t) {
            var e = 0;
            if ("string" != typeof t) return t;
            if (t) {
                var s = t.split(":");
                for (s.length > 3 && (s = s.slice(0, 3)), i = 0; s.length > i; i++) e = 60 * e + parseFloat(s[i].replace(",", "."))
            }
            return parseFloat(e)
        },
        toTimeString: function(t, e) {
            var i = Math.floor(t / 3600),
                s = t % 3600,
                n = Math.floor(s / 60),
                a = s % 60,
                r = Math.floor(a);
            return 10 > i && (i = "0" + i), 10 > n && (n = "0" + n), 10 > r && (r = "0" + r), e === !0 ? i + ":" + n : i + ":" + n + ":" + r
        },
        embeddFlash: function(e, i, s, n) {
            var a = i.FlashVars || {},
                r = "",
                o = "",
                l = "",
                h = "",
                u = e,
                c = "";
            i.src += -1 == i.src.indexOf("?") ? "?" : "&";
            for (var d in a) "function" != typeof a[d] && (h = a[d], i.src += d + "=" + encodeURIComponent(h) + "&");
            i.src.replace(/&$/, ""), o = '<object id="' + i.id + '" codebase="https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"  name="' + i.name + '" width="' + i.width + '" height="' + i.height + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">' + '<param name="movie" value="' + i.src + '"></param>' + '<param name="allowScriptAccess" value="' + i.allowScriptAccess + '"></param>' + '<param name="allowFullScreen" value="' + i.allowFullScreen + '"></param>' + '<param name="wmode" value="' + i.wmode + '"></param>', l = "<embed ";
            for (var d in i) "FLASHVARS" !== d.toUpperCase() && "function" != typeof i[d] && (l += d + '="' + i[d] + '" ');
            return l += ' pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>', r = o + l, r += "</object>", (!document.all || window.opera) && (r = l), null === u ? r : (u.get(0).innerHTML = r, s !== !1 && u.append(t("<div/>").attr("id", i.id + "_cc").css({
                width: n ? "1px" : "100%",
                height: n ? "1px" : "100%",
                backgroundColor: 9 > $p.utils.ieVersion() ? "#000" : "transparent",
                filter: "alpha(opacity = 0.1)",
                position: "absolute",
                top: 0,
                left: 0
            })), t("#" + i.id))
        },
        ieVersion: function() {
            var t = 3,
                e = document.createElement("div"),
                i = e.getElementsByTagName("i");
            while (e.innerHTML = "<!--[if gt IE " + ++t + "]><i></i><![endif]-->", i[0]);
            return t > 4 ? t : void 0
        },
        parseTemplate: function(t, e, i) {
            if (void 0 === e || 0 == e.length || "object" != typeof e) return t;
            for (var s in e) t = t.replace(RegExp("%{" + s + "}", "gi"), i === !0 ? window.encodeURIComponent(e[s]) : e[s]);
            return t = t.replace(/%{(.*?)}/gi, "")
        },
        stretch: function(e, i, s, n, a, r) {
            if (null == i) return !1;
            i instanceof t == !1 && (i = t(i)), i.data("od") || i.data("od", {
                width: i.width(),
                height: i.height()
            });
            var o = void 0 !== a ? a : i.data("od").width,
                l = void 0 !== r ? r : i.data("od").height,
                h = s / o,
                u = n / l,
                c = s,
                d = n;
            switch (e) {
                case "none":
                    c = o, d = l;
                    break;
                case "fill":
                    h > u ? (c = o * h, d = l * h) : u > h && (c = o * u, d = l * u);
                    break;
                case "aspectratio":
                default:
                    h > u ? (c = o * u, d = l * u) : u > h && (c = o * h, d = l * h)
            }
            return s = $p.utils.roundNumber(100 * (c / s), 0), n = $p.utils.roundNumber(100 * (d / n), 0), 0 === s || 0 === n ? !1 : (i.css({
                margin: 0,
                padding: 0,
                width: s + "%",
                height: n + "%",
                left: (100 - s) / 2 + "%",
                top: (100 - n) / 2 + "%"
            }), i.data("od").width != i.width() || i.data("od").height != i.height() ? !0 : !1)
        },
        parseUri: function(t) {
            var e = {
                    strictMode: !1,
                    key: ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"],
                    q: {
                        name: "queryKey",
                        parser: /(?:^|&)([^&=]*)=?([^&]*)/g
                    },
                    parser: {
                        strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                        loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
                    }
                },
                i = e.parser[e.strictMode ? "strict" : "loose"].exec(t),
                s = {},
                n = 14;
            while (n--) s[e.key[n]] = i[n] || "";
            return s[e.q.name] = {}, s[e.key[12]].replace(e.q.parser, function(t, i, n) {
                i && (s[e.q.name][i] = n)
            }), s
        },
        log: function() {
            this.logging !== !1 && (this.history = this.history || [], this.history.push(arguments), window.console && console.log(Array.prototype.slice.call(arguments)))
        },
        cleanResponse: function(e, i) {
            var s = !1;
            switch (i) {
                case "html":
                case "xml":
                    window.DOMParser ? (s = new DOMParser, s = s.parseFromString(e, "text/xml")) : (s = new ActiveXObject("Microsoft.XMLDOM"), s.async = "false", s.loadXML(e));
                    break;
                case "json":
                    s = e, "string" == typeof s && (s = t.parseJSON(s));
                    break;
                case "jsonp":
                    break;
                default:
                    s = e
            }
            return s
        },
        cssTransitions: function() {
            var t = document.createElement("z"),
                e = t.style;

            function i(t) {
                for (var i in t)
                    if (null != e[t[i]]) return !0;
                return !1
            }

            function s(t) {
                var e = "Webkit Moz O ms Khtml".split(" "),
                    s = t.charAt(0).toUpperCase() + t.substr(1),
                    n = (t + " " + e.join(s + " ") + s).split(" ");
                return i(n)
            }
            return s("animationName")
        },
        versionCompare: function(t, e) {
            var i = t.split("."),
                s = e.split("."),
                n = 0;
            for (n = 0; i.length > n; ++n) i[n] = Number(i[n]);
            for (n = 0; s.length > n; ++n) s[n] = Number(s[n]);
            return 2 == i.length && (i[2] = 0), i[0] > s[0] ? !0 : i[0] < s[0] ? !1 : i[1] > s[1] ? !0 : i[1] < s[1] ? !1 : i[2] > s[2] ? !0 : i[2] < s[2] ? !1 : !0
        },
        stringify: function(t) {
            if ("JSON" in window) return JSON.stringify(t);
            var e = typeof t;
            if ("object" != e || null === t) return "string" == e && (t = '"' + t + '"'), t + "";
            var i, s, n = [],
                a = t && t.constructor == Array;
            for (i in t) t.hasOwnProperty(i) && (s = t[i], e = typeof s, t.hasOwnProperty(i) && ("string" == e ? s = '"' + s + '"' : "object" == e && null !== s && (s = $p.utils.stringify(s)), n.push((a ? "" : '"' + i + '":') + (s + ""))));
            return (a ? "[" : "{") + (n + "") + (a ? "]" : "}")
        },
        logging: !1
    }
}), jQuery(function(t) {
    $p.platforms = {
        VLC: function() {
            if (navigator.plugins && navigator.plugins.length > 0) {
                for (var t = 0; navigator.plugins.length > t; ++t)
                    if (-1 != navigator.plugins[t].name.indexOf("VLC")) {
                        if (null != navigator.plugins[t].version) return navigator.plugins[t].version || "0";
                        if (null != navigator.plugins[t].description && navigator.plugins[t].description.match(/\d{1,}\.\d{1,}\.\d{1,}/i)[0]) return navigator.plugins[t].description.match(/\d{1,}\.\d{1,}\.\d{1,}/i)[0]
                    }
            } else try {
                return new ActiveXObject("VideoLAN.VLCPlugin.2"), "0"
            } catch (e) {}
            return "0"
        },
        FLASH: function(t) {
            try {
                try {
                    var e = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    try {
                        e.AllowScriptAccess = "always"
                    } catch (i) {
                        return "6.0.0"
                    }
                } catch (i) {}
                return "" + new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version").replace(/\D+/g, ",").match(/^,?(.+),?$/)[1].match(/\d+/g)[0]
            } catch (i) {
                try {
                    if (navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin) return "" + (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1].match(/\d+/g)[0]
                } catch (i) {}
            }
            return "0"
        },
        FLASHNA: function(t) {
            try {
                try {
                    var e = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
                    try {
                        e.AllowScriptAccess = "always"
                    } catch (i) {
                        return "6.0.0"
                    }
                } catch (i) {}
                return "" + new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version").replace(/\D+/g, ",").match(/^,?(.+),?$/)[1].match(/\d+/g)[0]
            } catch (i) {
                try {
                    if (navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin) return "" + (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1].match(/\d+/g)[0]
                } catch (i) {}
            }
            return "0"
        },
        ANDROID: function(t) {
            try {
                return "" + navigator.userAgent.toLowerCase().match(/android\s+(([\d\.]+))?/)[1]
            } catch (e) {}
            return "0"
        },
        IOS: function(t) {
            var e = navigator.userAgent.toLowerCase(),
                i = e.indexOf("os ");
            return (e.indexOf("iphone") > -1 || e.indexOf("ipad") > -1) && i > -1 ? "" + e.substr(i + 3, 3).replace("_", ".") : "0"
        },
        NATIVE: function(e) {
            try {
                var i = t(e.indexOf("video") > -1 ? "<video/>" : "<audio/>").get(0);
                if (null != i.canPlayType) {
                    if ("*" === e) return "1";
                    switch (i.canPlayType(e)) {
                        case "no":
                        case "":
                            return "0";
                        default:
                            return "1"
                    }
                }
            } catch (s) {}
            return "0"
        },
        BROWSER: function(t) {
            return "1"
        }
    }
});
var projekktorPluginInterface = function() {};
jQuery(function($) {
    projekktorPluginInterface.prototype = {
        pluginReady: !1,
        reqVer: null,
        name: "",
        pp: {},
        config: {},
        playerDom: null,
        _appliedDOMObj: [],
        _pageDOMContainer: {},
        _childDOMContainer: {},
        _init: function(t) {
            return this.config = $.extend(!0, this.config, t), null == this.reqVer || $p.utils.versionCompare(this.pp.getPlayerVer(), this.reqVer) ? (this.initialize(), void 0) : (alert("Plugin '" + this.name + "' requires Projekktor v" + this.reqVer + " or later! Please visit http://www.projekktor.com and get the most recent version."), this.pluginReady = !0, void 0)
        },
        getConfig: function(t, e) {
            var i = null,
                s = e || null;
            return null != this.pp.getConfig("plugin_" + this.name) && (i = this.pp.getConfig("plugin_" + this.name)[t]), null == i && (i = this.pp.getConfig(t)), null == i && (i = this.config[t]), "object" == typeof i && null === i.length ? i = $.extend(!0, {}, i, this.config[t]) : "object" == typeof i && (i = $.extend(!0, [], this.config[t] || [], i || [])), null == i ? s : i
        },
        getDA: function(t) {
            return "data-" + this.pp.getNS() + "-" + this.name + "-" + t
        },
        getCN: function(t) {
            return this.pp.getNS() + t
        },
        sendEvent: function(t, e) {
            this.pp._promote({
                _plugin: this.name,
                _event: t
            }, e)
        },
        deconstruct: function() {
            this.pluginReady = !1, $.each(this._appliedDOMObj, function() {
                $(this).unbind()
            })
        },
        applyToPlayer: function(t, e, i) {
            if (!t) return null;
            var s = e || "container",
                n = "",
                a = this;
            try {
                n = t.attr("class") || this.name
            } catch (r) {
                n = this.name
            }
            return this._pageDOMContainer[s] = $("[" + this.getDA("host") + "='" + this.pp.getId() + "'][" + this.getDA("func") + "='" + s + "']"), this._childDOMContainer[s] = this.playerDom.find("[" + this.getDA("func") + "='" + s + "'],." + this.getCN(n) + ":not([" + this.getDA("func") + "=''])"), this._pageDOMContainer[s].length > 0 ? (this._pageDOMContainer[s].removeClass("active").addClass("inactive"), $.each(this._pageDOMContainer[s], function() {
                a._appliedDOMObj.push($(this))
            }), this._pageDOMContainer[s]) : 0 == this._childDOMContainer[s].length ? (t.removeClass(n).addClass(this.pp.getNS() + n).removeClass("active").addClass("inactive").attr(this.getDA("func"), s).appendTo(this.playerDom), this._childDOMContainer[s] = t, this._appliedDOMObj.push(t), i === !0 && t.addClass("active").removeClass("inactive"), t) : ($.each(this._childDOMContainer[s], function() {
                $(this).attr(a.getDA("func"), s), a._appliedDOMObj.push($(this))
            }), i === !0 && this._childDOMContainer[s].addClass("active").removeClass("inactive"), $(this._childDOMContainer[s][0]))
        },
        getElement: function(t) {
            return this.pp.env.playerDom.find("." + this.pp.getNS() + t)
        },
        setInactive: function() {
            $(this._pageDOMContainer.container).removeClass("active").addClass("inactive"), $(this._childDOMContainer.container).removeClass("active").addClass("inactive"), this.sendEvent("inactive", $.extend(!0, {}, this._pageDOMContainer.container, this._childDOMContainer.container))
        },
        setActive: function(t, e) {
            var i = "object" == typeof t ? t : this.getElement(t);
            return null == t ? (this._pageDOMContainer.container.removeClass("inactive").addClass("active"), this._childDOMContainer.container.removeClass("inactive").addClass("active"), this.sendEvent("active", $.extend(!0, {}, this._pageDOMContainer.container, this._childDOMContainer.container)), i) : (0 != e ? i.addClass("active").removeClass("inactive") : i.addClass("inactive").removeClass("active"), i.css("display", ""), i)
        },
        getActive: function(t) {
            return $(t).hasClass("active")
        },
        initialize: function() {},
        isReady: function() {
            return this.pluginReady
        },
        clickHandler: function(t) {
            try {
                this.pp[this.getConfig(t + "Click").callback](this.getConfig(t + "Click").value)
            } catch (e) {
                try {
                    this.getConfig(t + "Click")(this.getConfig(t + "Click").value)
                } catch (e) {}
            }
            return !1
        },
        cookie: function(key, value, ttl) {
            if (void 0 === document.cookie || document.cookie === !1) return null;
            if (null == key && null != value) return null;
            if (0 == this.pp.getConfig("cookieExpiry")) return null;
            var t = new Date,
                result = null,
                cookieString = "",
                tmp = storedData = jQuery.parseJSON(eval(result = RegExp("(?:^|; )" + encodeURIComponent(this.getConfig("cookieName") + "_" + this.name) + "=([^;]*)").exec(document.cookie)) ? decodeURIComponent(result[1]) : null);
            return ("object" != typeof storedData || null == storedData) && (storedData = {}, null != key && (storedData[key] = tmp)), null == key ? storedData : 1 == arguments.length ? storedData[key] : (null != value ? storedData[key] = value : delete storedData[key], $.isEmptyObject(storedData) ? (ttl = 0, storedData = "") : storedData = $p.utils.stringify(storedData), t.setDate(t.getDate() + (ttl || this.getConfig("cookieExpiry", 0))), cookieString = encodeURIComponent(this.getConfig("cookieName", "projekktor") + "_" + this.name) + "=" + encodeURIComponent(storedData) + "; expires=" + (0 == ttl ? "Thu, 01 Jan 1970 00:00:01 GMT" : t.toUTCString()), this.getConfig("cookieDomain", !1) && (cookieString += "; domain=" + options.domain), document.cookie = cookieString, value)
        },
        eventHandler: function() {}
    }
});
var playerModel = function() {};
jQuery(function(t) {
    playerModel.prototype = {
        modelId: "player",
        iLove: [],
        _currentState: null,
        _currentBufferState: null,
        _currentSeekState: null,
        _ap: !1,
        _volume: 0,
        _quality: "default",
        _displayReady: !1,
        _isPlaying: !1,
        _id: null,
        _KbPerSec: 0,
        _bandWidthTimer: null,
        _isPoster: !1,
        _isFullscreen: !1,
        hasGUI: !1,
        allowRandomSeek: !1,
        flashVerifyMethod: "api_get",
        mediaElement: null,
        pp: {},
        media: {
            duration: 0,
            position: 0,
            maxpos: 0,
            offset: 0,
            file: !1,
            poster: "",
            ended: !1,
            loadProgress: 0,
            errorCode: 0
        },
        _init: function(e) {
            this.pp = e.pp || null, this.media = t.extend(!0, {}, this.media, e.media), this._ap = e.autoplay, this._isFullscreen = e.fullscreen, this._id = $p.utils.randomId(8), this._quality = e.quality || this._quality, this._volume = this.pp.getVolume(), this._playbackQuality = this.pp.getPlaybackQuality(), this.init()
        },
        init: function(t) {
            this.ready()
        },
        ready: function() {
            this.sendUpdate("modelReady"), this._ap ? (this.sendUpdate("autostart", !0), this._setState("awakening")) : this.displayItem(!1)
        },
        displayItem: function(e) {
            return this._displayReady = !1, this._isPoster = !1, this.pp.removeListener("fullscreen.poster"), this.pp.removeListener("resize.poster"), e !== !0 || this.getState("STOPPED") ? (this._setState("idle"), this.applyImage(this.getPoster(), this.pp.getMediaContainer().html("")), this._isPoster = !0, this.displayReady(), void 0) : (t("#" + this.pp.getMediaId() + "_image").remove(), t("#" + this.pp.getId() + "_testcard_media").remove(), this.applyMedia(this.pp.getMediaContainer()), void 0)
        },
        applyMedia: function() {},
        sendUpdate: function(t, e) {
            "ERROR" != this._currentState && ("error" == t && this._setState("error"), this.pp._modelUpdateListener(t, e))
        },
        displayReady: function() {
            this._displayReady = !0, this.pp._modelUpdateListener("displayReady")
        },
        start: function() {
            var t = this;
            (null != this.mediaElement || "PLAYLIST" == this.modelId) && (this.getState("STARTING") || (this._setState("STARTING"), this.getState("STOPPED") || this.addListeners(), this.pp.getIsMobileClient("ANDROID") && !this.getState("PLAYING") && setTimeout(function() {
                t.setPlay()
            }, 500), this.setPlay()))
        },
        addListeners: function() {},
        removeListeners: function() {
            try {
                this.mediaElement.unbind(".projekktor" + this.pp.getId())
            } catch (t) {}
        },
        detachMedia: function() {},
        destroy: function(e) {
            this.removeListeners(), this.getState("IDLE") || this._setState("destroying"), this.detachMedia();
            try {
                t("#" + this.mediaElement.id).empty()
            } catch (i) {}
            if (!this.pp.getIsMobileClient()) {
                try {
                    t("#" + this.mediaElement.id).remove()
                } catch (i) {}
                try {
                    this.mediaElement.remove()
                } catch (i) {}
                this.pp.getMediaContainer().html("")
            }
            this.mediaElement = null, this.media.loadProgress = 0, this.media.playProgress = 0, this.media.frame = 0, this.media.position = 0, this.media.duration = 0
        },
        reInit: function() {
            this.flashVersion === !1 && this._isFF() && !this.getState("ERROR") && this.pp.getConfig("bypassFlashFFFix") !== !0 && (this.sendUpdate("FFreinit"), this.removeListeners(), this.displayItem(!this.getState("IDLE")))
        },
        applyCommand: function(t, e) {
            switch (t) {
                case "quality":
                    this.setQuality(e);
                    break;
                case "error":
                    this._setState("error"), this.setTestcard(e);
                    break;
                case "play":
                    if (this.getState("ERROR")) break;
                    if (this.getState("IDLE")) {
                        this._setState("awakening");
                        break
                    }
                    this.setPlay();
                    break;
                case "pause":
                    if (this.getState("ERROR")) break;
                    this.setPause();
                    break;
                case "volume":
                    if (this.getState("ERROR")) break;
                    this.setVolume(e) || (this._volume = e, this.sendUpdate("volume", e));
                    break;
                case "stop":
                    this.setStop();
                    break;
                case "frame":
                    this.setFrame(e);
                    break;
                case "seek":
                    if (this.getState("ERROR")) break;
                    if (this.getSeekState("SEEKING")) break;
                    if (this.getState("IDLE")) break;
                    if (-1 == this.media.loadProgress) break;
                    this._setSeekState("seeking", e), this.setSeek(e);
                    break;
                case "fullscreen":
                    if (e == this._isFullscreen) break;
                    this._isFullscreen = e, this.sendUpdate("fullscreen", this._isFullscreen), this.reInit(), this.setFullscreen();
                    break;
                case "resize":
                    this.setResize(), this.sendUpdate("resize", e)
            }
        },
        setFrame: function(t) {
            var e = t / this.pp.getConfig("fps") + 1e-5;
            this.setSeek(e)
        },
        setSeek: function(t) {},
        setPlay: function() {},
        setPause: function() {},
        setStop: function() {
            this.detachMedia(), this._setState("stopped"), this.displayItem(!1)
        },
        setVolume: function(t) {},
        setFullscreen: function(t) {
            this.setResize()
        },
        setResize: function() {
            var t = this.pp.getMediaContainer();
            this.sendUpdate("scaled", {
                realWidth: this.media.videoWidth || null,
                realHeight: this.media.videoHeight || null,
                displayWidth: t.width(),
                displayHeight: t.height()
            })
        },
        setPosterLive: function() {},
        setQuality: function(t) {
            if (this._quality != t) {
                this._quality = t;
                try {
                    this.applySrc()
                } catch (e) {}
                this.qualityChangeListener()
            }
        },
        getQuality: function() {
            return this._quality
        },
        getVolume: function() {
            return null == this.mediaElement ? this._volume : this.mediaElement.prop("muted") === !0 ? 0 : this.mediaElement.prop("volume")
        },
        getLoadProgress: function() {
            return this.media.loadProgress || 0
        },
        getLoadPlaybackProgress: function() {
            return this.media.playProgress || 0
        },
        getPosition: function() {
            return this.media.position || 0
        },
        getFrame: function() {
            return this.media.frame || 0
        },
        getDuration: function() {
            return this.media.duration || 0
        },
        getMaxPosition: function() {
            return this.media.maxpos || 0
        },
        getPlaybackQuality: function() {
            return t.inArray(this._quality, this.media.qualities) > -1 ? this._quality : "default"
        },
        getInFullscreen: function() {
            return this.pp.getInFullscreen()
        },
        getKbPerSec: function() {
            return this._KbPerSec
        },
        getState: function(t) {
            var e = null == this._currentState ? "IDLE" : this._currentState;
            return null != t ? e == t.toUpperCase() : e
        },
        getBufferState: function(t) {
            var e = null == this._currentBufferState ? "NONE" : this._currentBufferState;
            return null != t ? e == t.toUpperCase() : e
        },
        getSeekState: function(t) {
            var e = null == this._currentSeekState ? "NONE" : this._currentSeekState;
            return null != t ? e == t.toUpperCase() : e
        },
        getSrc: function() {
            try {
                return this.mediaElement.get(0).currentSrc
            } catch (t) {}
            try {
                return this.media.file[0].src
            } catch (t) {}
            try {
                return this.getPoster()
            } catch (t) {}
            return null
        },
        getModelName: function() {
            return this.modelId || null
        },
        getHasGUI: function() {
            return this.hasGUI && !this._isPoster
        },
        getIsReady: function() {
            return this._displayReady
        },
        getPoster: function() {
            var t = "poster",
                e = null,
                i = this.pp.getConfig(t),
                s = "default",
                n = [];
            if ("object" != typeof i) return i;
            for (var a in i) i[a].quality && n.push(i[a].quality);
            s = this.pp.getAppropriateQuality(n);
            for (var r in i)(i[r].quality == s || "" == e || "default" == s) && (e = i[r].src);
            return e
        },
        getMediaElement: function() {
            return this.mediaElement || t("<video/>")
        },
        getMediaDimensions: function() {
            return {
                width: this.media.videoWidth || 0,
                height: this.media.videoHeight || 0
            }
        },
        getSource: function() {
            var e = [],
                i = this.media.offset || this.media.position || !1,
                s = this,
                n = "pseudo" == this.pp.getConfig("streamType") ? this.pp.getConfig("startParameter") : !1;
            return t.each(this.media.file || [], function() {
                if (s._quality != this.quality && null !== s._quality) return !0;
                if (!n || !i) return e.push(this), !0;
                var a = $p.utils.parseUri(this.src),
                    r = a.protocol + "://" + a.host + a.path,
                    o = [];
                return t.each(a.queryKey, function(t, e) {
                    t != n && o.push(t + "=" + e)
                }), r += o.length > 0 ? "?" + o.join("&") + "&" + n + "=" + i : "?" + n + "=" + i, this.src = r, e.push(this), !0
            }), 0 === e.length ? this.media.file : e
        },
        timeListener: function(t) {
            if (null != t) {
                var e = parseFloat((t.position || t.currentTime || this.media.position || 0).toFixed(2)),
                    i = parseFloat((t.duration || 0).toFixed(2));
                isNaN(i + e) || ((0 != i && i != this.media.duration && !this.isPseudoStream || this.isPseudoStream && 0 == this.media.duration) && (this.media.duration = i, this.sendUpdate("durationChange", i)), e != this.media.position && (this.media.position = this.isPseudoStream && Math.round(100 * e) / 100 == Math.round(100 * this.media.offset) / 100 ? this.media.offset : this.media.offset + e, this.media.maxpos = Math.max(this.media.maxpos || 0, this.media.position || 0), this.media.playProgress = parseFloat(this.media.position > 0 && this.media.duration > 0 ? 100 * this.media.position / this.media.duration : 0), this.media.frame = this.media.position * this.pp.getConfig("fps"), this.sendUpdate("time", this.media.position), this.loadProgressUpdate()))
            }
        },
        loadProgressUpdate: function() {
            var t = this.mediaElement.get(0),
                e = 0;
            0 !== this.media.duration && "object" == typeof t.buffered && (0 !== t.buffered.length || 0 !== t.seekable.length) && 100 != this.media.loadProgress && (e = t.seekable && t.seekable.length > 0 ? Math.round(100 * t.seekable.end(0) / this.media.duration) : Math.round(100 * t.buffered.end(t.buffered.length - 1)) / this.media.duration, this.media.loadProgress > e || (this.media.loadProgress = this.allowRandomSeek === !0 ? 100 : -1, this.media.loadProgress = 100 > this.media.loadProgress || void 0 === this.media.loadProgress ? e : 100, this.sendUpdate("progress", this.media.loadProgress)))
        },
        progressListener: function(t, e) {
            if (this.mediaElement instanceof jQuery && "object" == typeof this.mediaElement.get(0).buffered && this.mediaElement.get(0).buffered.length > 0) return this.mediaElement.unbind("progress"), void 0;
            null == this._bandWidthTimer && (this._bandWidthTimer = (new Date).getTime());
            var i = 0,
                s = 0;
            try {
                isNaN(e.loaded / e.total) ? e.originalEvent && !isNaN(e.originalEvent.loaded / e.originalEvent.total) && (i = e.originalEvent.loaded, s = e.originalEvent.total) : (i = e.loaded, s = e.total)
            } catch (n) {
                t && !isNaN(t.loaded / t.total) && (i = t.loaded, s = t.total)
            }
            var a = i > 0 && s > 0 ? 100 * i / s : 0;
            Math.round(a) > Math.round(this.media.loadProgress) && (this._KbPerSec = i / 1024 / (((new Date).getTime() - this._bandWidthTimer) / 1e3)), a = 100 !== this.media.loadProgress ? a : 100, a = this.allowRandomSeek === !0 ? 100 : 5 * Math.round(a / 5), this.media.loadProgress != a && (this.media.loadProgress = a, this.sendUpdate("progress", a)), this.media.loadProgress >= 100 && this.allowRandomSeek === !1 && this._setBufferState("full")
        },
        qualityChangeListener: function() {
            this.sendUpdate("qualityChange", this._quality)
        },
        endedListener: function(t) {
            null !== this.mediaElement && (0 >= this.media.maxpos || "STARTING" != this.getState() && this._setState("completed"))
        },
        waitingListener: function(t) {
            this._setBufferState("empty")
        },
        canplayListener: function(t) {
            this._setBufferState("full")
        },
        canplaythroughListener: function(t) {
            this._setBufferState("full")
        },
        suspendListener: function(t) {
            this._setBufferState("full")
        },
        playingListener: function(t) {
            this._setState("playing")
        },
        startListener: function(t) {
            this.applyCommand("volume", this.pp.getConfig("volume")), this.isPseudoStream || this.setSeek(this.media.position || 0), this._setState("playing")
        },
        pauseListener: function(t) {
            this._setState("paused")
        },
        seekedListener: function(t) {
            this._setSeekState("SEEKED", this.media.position)
        },
        volumeListener: function(t) {
            this.sendUpdate("volume", this.getVolume())
        },
        flashReadyListener: function() {
            this._displayReady = !0
        },
        errorListener: function(t, e) {},
        metaDataListener: function(t) {
            try {
                this.media.videoWidth = t.videoWidth, this.media.videoHeight = t.videoHeight
            } catch (e) {}
            this._scaleVideo()
        },
        setTestcard: function(e, i) {
            var s = this.pp.getMediaContainer().html("").css({
                    width: "100%",
                    height: "100%"
                }),
                n = t.extend(this.pp.getConfig("messages"), this.pp.getConfig("msg")),
                a = null == n[e] ? 0 : e,
                r = void 0 !== i && "" !== i ? i : n[a];
            this.removeListeners(), this.detachMedia(), this.pp.getItemCount() > 1 && (r += " " + n[99]), 3 > r.length && (r = "ERROR"), 100 == a && (r = i), r = $p.utils.parseTemplate(r, t.extend({}, this.media, {
                title: this.pp.getConfig("title")
            })), this.mediaElement = t("<div/>").addClass(this.pp.getNS() + "testcard").attr("id", this.pp.getId() + "_testcard_media").html("<p>" + r + "</p>").appendTo(s), null != this.pp.getConfig("msg")[a] && this.mediaElement.addClass(this.pp.getNS() + "customtestcard")
        },
        applySrc: function() {},
        applyImage: function(e, i) {
            var s = t("<img/>").hide(),
                n = this;
            if ($p.utils.blockSelection(s), null == e || e === !1) return t("<span/>").attr({
                id: this.pp.getMediaId() + "_image"
            }).appendTo(i);
            s.html("").appendTo(i).attr({
                id: this.pp.getMediaId() + "_image",
                alt: this.pp.getConfig("title") || ""
            }).css({
                position: "absolute"
            }), s.error(function(e) {
                t(this).remove()
            }), s.load(function(t) {
                var e = t.currentTarget;
                s.data("od") || s.data("od", {
                    width: e.naturalWidth,
                    height: e.naturalHeight
                }), s.show(), $p.utils.stretch(n.pp.getConfig("imageScaling"), s, i.width(), i.height())
            }), s.attr("src", e);
            var a = function(t, e) {
                e.is(":visible") === !1 && n.pp.removeListener("fullscreen", arguments.callee);
                var i = e.width(),
                    s = e.height(),
                    a = t.width(),
                    r = t.height();
                if ($p.utils.stretch(n.pp.getConfig("imageScaling"), t, e.width(), e.height())) try {
                    n.sendUpdate("scaled", {
                        realWidth: t._originalDimensions.width,
                        realHeight: t._originalDimensions.height,
                        displayWidth: n.mediaElement.width(),
                        displayHeight: n.mediaElement.height()
                    })
                } catch (o) {}
                t.attr("src") != n.getPoster() && t.attr("src", n.getPoster())
            };
            return this.pp.addListener("fullscreen.poster", function() {
                a(s, i)
            }), this.pp.addListener("resize.poster", function() {
                a(s, i)
            }), s
        },
        createFlash: function(t, e, i) {
            this.mediaElement = $p.utils.embeddFlash(e.html(""), t, i, !0), this._waitforPlayer()
        },
        _waitforPlayer: function() {
            var e = this,
                i = 0;
            this._displayReady !== !0 && (this._setBufferState("empty"), function() {
                if (i > 6 && e._isFF()) {
                    i = 0;
                    var s = t(e.mediaElement).parent(),
                        n = t(e.mediaElement).clone();
                    s.html("").append(n), e.mediaElement = n
                }
                s = e.mediaElement;
                try {
                    if (t(s).attr("id").indexOf("testcard") > -1) return
                } catch (a) {
                    console.log(a)
                }
                i++;
                try {
                    void 0 === s ? setTimeout(arguments.callee, 200) : void 0 === s.get(0)[e.flashVerifyMethod] ? setTimeout(arguments.callee, 200) : (e._setBufferState("full"), e.flashReadyListener(), t("#" + t(e.mediaElement).attr("id") + "_cc").css({
                        width: "100%",
                        height: "100%"
                    }))
                } catch (a) {
                    setTimeout(arguments.callee, 200)
                }
            }())
        },
        _setState: function(t) {
            var e = this;
            t = t.toUpperCase(), this._currentState != t && "ERROR" != this._currentState && ("PAUSED" == this._currentState && "PLAYING" == t && (this.sendUpdate("resume", this.media), this._isPlaying = !0), "IDLE" != this._currentState && "STARTING" != this._currentState || "PLAYING" != t || (this.sendUpdate("start", this.media), this._isPlaying = !0), "PAUSED" == t && (this._isPlaying = !1), "ERROR" == t && (this.setPlay = this.setPause = function() {
                e.sendUpdate("start")
            }), this._currentState = t.toUpperCase(), this.sendUpdate("state", this._currentState))
        },
        _setBufferState: function(t) {
            this._currentBufferState != t.toUpperCase() && (this._currentBufferState = t.toUpperCase(), this.sendUpdate("buffer", this._currentBufferState))
        },
        _setSeekState: function(t, e) {
            this._currentSeekState != t.toUpperCase() && (this._currentSeekState = t.toUpperCase(), this.sendUpdate("seek", this._currentSeekState))
        },
        _scaleVideo: function(t) {
            var e = this.pp.getMediaContainer();
            if (!this.pp.getIsMobileClient()) try {
                var i = e.width(),
                    s = e.height(),
                    n = this.media.videoWidth,
                    a = this.media.videoHeight;
                $p.utils.stretch(this.pp.getConfig("videoScaling"), this.mediaElement, i, s, n, a) && this.sendUpdate("scaled", {
                    realWidth: n,
                    realHeight: a,
                    displayWidth: i,
                    displayHeight: s
                })
            } catch (r) {}
        },
        _isFF: function() {
            return navigator.userAgent.toLowerCase().indexOf("firefox") > -1
        }
    }
}), jQuery(function(t) {
    $p.newModel({
        modelId: "NA",
        iLove: [{
            ext: "NaN",
            type: "none/none",
            platform: "browser"
        }],
        hasGUI: !0,
        applyMedia: function(t) {
            var e = this;
            t.html("");
            var i = function(t, i) {
                i.getState("AWAKENING") || (e.pp.removeListener("mousedown", arguments.callee), e._setState("completed"))
            };
            this.displayReady(), this.pp.getConfig("enableTestcard") && !this.pp.getIsMobileClient() ? (this.pp.addListener("mousedown", i), this._setState("error"), this.setTestcard(null != this.media.file[0].src && 7 === this.media.errorCode ? 5 : this.media.errorCode)) : (this.applyCommand("stop"), window.location.href = this.media.file[0].src)
        },
        detachMedia: function() {
            this.pp.removeListener("leftclick", this.mouseClick)
        }
    })
}), jQuery(function(t) {
    $p.newModel({
        modelId: "VIDEO",
        androidVersion: "2",
        iosVersion: "3",
        nativeVersion: "1",
        iLove: [{
            ext: "mp4",
            type: "video/mp4",
            platform: ["ios", "android", "native"],
            streamType: ["http", "pseudo", "httpVideo"],
            fixed: "maybe"
        }, {
            ext: "m4v",
            type: "video/mp4",
            platform: ["ios", "android", "native"],
            streamType: ["http", "pseudo", "httpVideo"],
            fixed: "maybe"
        }, {
            ext: "ogv",
            type: "video/ogg",
            platform: "native",
            streamType: ["http", "httpVideo"]
        }, {
            ext: "webm",
            type: "video/webm",
            platform: "native",
            streamType: ["http", "httpVideo"]
        }, {
            ext: "ogg",
            type: "video/ogg",
            platform: "native",
            streamType: ["http", "httpVideo"]
        }, {
            ext: "anx",
            type: "video/ogg",
            platform: "native",
            streamType: ["http", "httpVideo"]
        }],
        _eventMap: {
            pause: "pauseListener",
            play: "playingListener",
            volumechange: "volumeListener",
            progress: "progressListener",
            timeupdate: "timeListener",
            ended: "_ended",
            waiting: "waitingListener",
            canplaythrough: "canplayListener",
            canplay: "canplayListener",
            error: "errorListener",
            suspend: "suspendListener",
            seeked: "seekedListener",
            loadedmetadata: "metaDataListener",
            loadstart: null
        },
        isGingerbread: !1,
        isAndroid: !1,
        allowRandomSeek: !1,
        videoWidth: 0,
        videoHeight: 0,
        wasPersistent: !0,
        isPseudoStream: !1,
        init: function() {
            var t = navigator.userAgent;
            t.indexOf("Android") >= 0 && (this.isAndroid = !0, 3 > parseFloat(t.slice(t.indexOf("Android") + 8)) && (this.isGingerbread = !0)), this.ready()
        },
        applyMedia: function(e) {
            0 === t("#" + this.pp.getMediaId() + "_html").length && (this.wasPersistent = !1, e.html("").append(t("<video/>").attr({
                id: this.pp.getMediaId() + "_html",
                poster: $p.utils.imageDummy(),
                loop: !1,
                autoplay: !1,
                preload: "none",
                "x-webkit-airplay": "allow"
            }).prop({
                controls: !1,
                volume: this.getVolume()
            }).css({
                width: "100%",
                height: "100%",
                position: "absolute",
                top: 0,
                left: 0
            }))), this.mediaElement = t("#" + this.pp.getMediaId() + "_html"), this.applySrc()
        },
        applySrc: function() {
            var e = this,
                i = this.getSource(),
                s = e.getState("AWAKENING");
            this.mediaElement.attr("src", i[0].src), this.isGingerbread || this.mediaElement.attr("type", i[0].originalType), this.mediaElement.bind("mousedown.projekktorqs" + this.pp.getId(), this.disableDefaultVideoElementActions), this.mediaElement.bind("click.projekktorqs" + this.pp.getId(), this.disableDefaultVideoElementActions);
            var n = function() {
                return e.mediaElement.unbind("loadstart.projekktorqs" + e.pp.getId()), e.mediaElement.unbind("loadeddata.projekktorqs" + e.pp.getId()), e.mediaElement.unbind("canplay.projekktorqs" + e.pp.getId()), e.addListeners("error"), e.addListeners("play"), e.addListeners("canplay"), e.mediaElement = t("#" + e.pp.getMediaId() + "_html"), s ? (e.displayReady(), void 0) : e.getSeekState("SEEKING") ? (e._isPlaying && e.setPlay(), e.seekedListener(), void 0) : (e.isPseudoStream || e.setSeek(e.media.position || 0), e._isPlaying && e.setPlay(), void 0)
            };
            this.mediaElement.bind("loadstart.projekktorqs" + this.pp.getId(), n), this.mediaElement.bind("loadeddata.projekktorqs" + this.pp.getId(), n), this.mediaElement.bind("canplay.projekktorqs" + this.pp.getId(), n), this.mediaElement[0].load(), this.isGingerbread && n()
        },
        detachMedia: function() {
            try {
                this.removeListener("error"), this.removeListener("play"), this.removeListener("canplay"), this.mediaElement.unbind("mousedown.projekktorqs" + this.pp.getId()), this.mediaElement.unbind("click.projekktorqs" + this.pp.getId()), this.mediaElement[0].pause(), this.mediaElement.attr("src", ""), this.mediaElement[0].load()
            } catch (t) {}
        },
        addListeners: function(e, i) {
            if (null != this.mediaElement) {
                var s = null != i ? ".projekktor" + i + this.pp.getId() : ".projekktor" + this.pp.getId(),
                    n = this,
                    a = null == e ? "*" : e;
                t.each(this._eventMap, function(t, e) {
                    t != a && "*" != a || null == e || n.mediaElement.bind(t + s, function(t) {
                        n[e](this, t)
                    })
                })
            }
        },
        removeListener: function(e, i) {
            if (null != this.mediaElement) {
                var s = null != i ? ".projekktor" + i + this.pp.getId() : ".projekktor" + this.pp.getId(),
                    n = this;
                t.each(this._eventMap, function(t, i) {
                    t == e && n.mediaElement.unbind(t + s)
                })
            }
        },
        _ended: function() {
            var t = this.mediaElement[0].duration,
                e = Math.round(this.media.position) === Math.round(t),
                i = 2 > t - this.media.maxpos && 0 === this.media.position || !1;
            e || i || this.isPseudoStream ? this.endedListener(this) : this.pauseListener(this)
        },
        playingListener: function(t) {
            var e = this;
            this.isGingerbread || function() {
                try {
                    if (0 === e.getDuration()) return "" !== e.mediaElement.get(0).currentSrc && e.mediaElement.get(0).networkState == e.mediaElement.get(0).NETWORK_NO_SOURCE ? (e.sendUpdate("error", 80), void 0) : (setTimeout(arguments.callee, 500), void 0)
                } catch (t) {}
            }(), this._setState("playing")
        },
        errorListener: function(t, e) {
            try {
                switch (e.target.error.code) {
                    case e.target.error.MEDIA_ERR_ABORTED:
                        this.sendUpdate("error", 1);
                        break;
                    case e.target.error.MEDIA_ERR_NETWORK:
                        this.sendUpdate("error", 2);
                        break;
                    case e.target.error.MEDIA_ERR_DECODE:
                        this.sendUpdate("error", 3);
                        break;
                    case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                        this.sendUpdate("error", 4);
                        break;
                    default:
                        this.sendUpdate("error", 5)
                }
            } catch (i) {}
        },
        canplayListener: function(e) {
            var i = this;
            "pseudo" == this.pp.getConfig("streamType") && t.each(this.media.file, function() {
                return this.src.indexOf(i.mediaElement[0].currentSrc) > -1 && "video/mp4" == this.type ? (i.isPseudoStream = !0, i.allowRandomSeek = !0, i.media.loadProgress = 100, !1) : !0
            }), this._setBufferState("full")
        },
        disableDefaultVideoElementActions: function(t) {
            t.preventDefault(), t.stopPropagation()
        },
        setPlay: function() {
            try {
                this.mediaElement[0].play()
            } catch (t) {}
        },
        setPause: function() {
            try {
                this.mediaElement[0].pause()
            } catch (t) {}
        },
        setVolume: function(t) {
            this._volume = t;
            try {
                this.mediaElement.prop("volume", t)
            } catch (e) {
                return !1
            }
            return t
        },
        setSeek: function(t) {
            var e = this;
            return this.isPseudoStream ? (this.media.position = 0, this.media.offset = t, this.applySrc(), void 0) : (function() {
                try {
                    e.mediaElement[0].currentTime = t, e.timeListener({
                        position: t
                    })
                } catch (i) {
                    null != e.mediaElement && setTimeout(arguments.callee, 100)
                }
            }(), void 0)
        },
        setFullscreen: function(t) {
            "audio" != this.element && this._scaleVideo()
        },
        setResize: function() {
            "audio" != this.element && this._scaleVideo(!1)
        }
    }), $p.newModel({
        modelId: "AUDIO",
        iLove: [{
            ext: "ogg",
            type: "audio/ogg",
            platform: "native",
            streamType: ["http", "httpAudio"]
        }, {
            ext: "oga",
            type: "audio/ogg",
            platform: "native",
            streamType: ["http", "httpAudio"]
        }, {
            ext: "mp3",
            type: "audio/mp3",
            platform: ["ios", "android", "native"],
            streamType: ["http", "httpAudio"]
        }, {
            ext: "mp3",
            type: "audio/mpeg",
            platform: ["ios", "android", "native"],
            streamType: ["http", "httpAudio"]
        }],
        imageElement: {},
        applyMedia: function(e) {
            $p.utils.blockSelection(e), this.imageElement = this.applyImage(this.getPoster("cover") || this.getPoster("poster"), e), this.imageElement.css({
                border: "0px"
            }), 0 === t("#" + this.pp.getMediaId() + "_html").length && (this.wasPersistent = !1, e.html("").append(t(this.isGingerbread ? "<video/>" : "<audio/>").attr({
                id: this.pp.getMediaId() + "_html",
                poster: $p.utils.imageDummy(),
                loop: !1,
                autoplay: !1,
                preload: "none",
                "x-webkit-airplay": "allow"
            }).prop({
                controls: !1,
                volume: this.getVolume()
            }).css({
                width: "1px",
                height: "1px",
                position: "absolute",
                top: 0,
                left: 0
            }))), this.mediaElement = t("#" + this.pp.getMediaId() + "_html"), this.applySrc()
        },
        setPosterLive: function() {
            if (this.imageElement.parent) {
                var e = this.imageElement.parent(),
                    i = this;
                if (this.imageElement.attr("src") == this.getPoster("cover") || this.getPoster("poster")) return;
                this.imageElement.fadeOut("fast", function() {
                    t(this).remove(), i.imageElement = i.applyImage(i.getPoster("cover") || i.getPoster("poster"), e)
                })
            }
        }
    }, "VIDEO")
}), jQuery(function(t) {
    $p.newModel({
        modelId: "VIDEOHLS",
        androidVersion: 4,
        iosVersion: 3,
        iLove: [{
            ext: "m3u8",
            type: "application/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }, {
            ext: "m3u",
            type: "application/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }, {
            ext: "m3u8",
            type: "application/vnd.apple.mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }, {
            ext: "m3u",
            type: "application/vnd.apple.mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }, {
            ext: "m3u8",
            type: "application/x-mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }, {
            ext: "m3u",
            type: "application/x-mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpVideo", "httpVideoLive"]
        }]
    }, "VIDEO"), $p.newModel({
        modelId: "AUDIOHLS",
        androidVersion: 4,
        iosVersion: 3,
        iLove: [{
            ext: "m3u8",
            type: "application/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u",
            type: "application/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u8",
            type: "application/vnd.apple.mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u",
            type: "application/vnd.apple.mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u8",
            type: "application/x-mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u",
            type: "application/x-mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u8",
            type: "audio/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }, {
            ext: "m3u",
            type: "audio/mpegURL",
            platform: ["ios", "android"],
            streamType: ["http", "httpAudio", "httpAudioLive"]
        }]
    }, "AUDIO")
}), jQuery(function(t) {
    $p.newModel({
        modelId: "VIDEOVLC",
        vlcVersion: "2.0.6.0",
        iLove: [],
        _eventMap: {
            MediaPlayerPaused: "pauseListener",
            MediaPlayerPlaying: "playingListener",
            MediaPlayerTimeChanged: "_timeListener",
            MediaPlayerEndReached: "endedListener",
            MediaPlayerBuffering: "waitingListener",
            MediaPlayerEncounteredError: "errorListener",
            MediaPlayerSeekableChanged: "seekableListener"
        },
        allowRandomSeek: !1,
        videoWidth: 0,
        videoHeight: 0,
        isPseudoStream: !1,
        setiLove: function() {
            var e = this;
            if (navigator.plugins && navigator.plugins.length > 0)
                for (var i = 0; navigator.plugins.length > i; ++i)
                    if (navigator.plugins[i].name.indexOf("VLC") > -1) {
                        for (var s = 0; navigator.plugins[i].length > s; s++) {
                            var n = navigator.plugins[i][s];
                            null != n.suffixes && null != n.type && t.each(n.suffixes.split(","), function(t, i) {
                                e.iLove.push({
                                    ext: i,
                                    type: n.type.replace(/x-/, ""),
                                    platform: ["vlc"],
                                    streamType: ["rtsp", "http", "pseudo", "httpVideo", "multipart"]
                                })
                            })
                        }
                        break
                    }
        },
        applyMedia: function(e) {
            e.html("").append(t("<embed/>").attr({
                id: this.pp.getMediaId() + "_vlc",
                type: "application/x-vlc-plugin",
                pluginspage: "http://www.videolan.org",
                width: "100%",
                height: "100%",
                events: !0,
                controls: !1,
                toolbar: !1,
                windowless: !0,
                allowfullscreen: !0,
                autoplay: !1
            }).css({
                position: "absolute",
                top: 0,
                left: 0
            })), this.mediaElement = t("#" + this.pp.getMediaId() + "_vlc"), this.applySrc()
        },
        applySrc: function() {
            var t = this,
                e = this.getSource();
            this.mediaElement.get(0).playlist.add(e[0].src, "item 1"), this.getState("PLAYING") ? (this.setPlay(), t.isPseudoStream !== !0 && this.setSeek(this.media.position || 0)) : this.displayReady()
        },
        detachMedia: function() {
            try {
                this.mediaElement.get(0).playlist.stop(), this.mediaElement.html("")
            } catch (t) {}
        },
        addListeners: function() {
            var e = this;
            t.each(this._eventMap, function(t, i) {
                try {
                    e.mediaElement.get(0).attachEvent ? e.mediaElement.get(0).attachEvent(t, function(t) {
                        e[i](this, t)
                    }) : e.mediaElement.get(0).addEventListener ? e.mediaElement.get(0).addEventListener(t, function(t) {
                        e[i](this, t)
                    }, !1) : e.mediaElement.get(0)["on" + t] = function(t) {
                        e[i](this, t)
                    }
                } catch (s) {}
            })
        },
        removeListener: function(e, i) {
            if (null != this.mediaElement) {
                var s = null != i ? ".projekktor" + i + this.pp.getId() : ".projekktor" + this.pp.getId(),
                    n = this;
                t.each(this._eventMap, function(t, i) {
                    t == e && n.mediaElement.unbind(t + s)
                })
            }
        },
        _timeListener: function(t) {
            this.timeListener({
                position: this.mediaElement.get(0).input.time / 1e3,
                duration: this.mediaElement.get(0).input.length / 1e3
            })
        },
        seekableListener: function() {
            this.allowRandomSeek = !0, this.media.loadProgress = 100
        },
        errorListener: function(t, e) {
            try {
                switch (event.target.error.code) {
                    case event.target.error.MEDIA_ERR_ABORTED:
                        this.sendUpdate("error", 1);
                        break;
                    case event.target.error.MEDIA_ERR_NETWORK:
                        this.sendUpdate("error", 2);
                        break;
                    case event.target.error.MEDIA_ERR_DECODE:
                        this.sendUpdate("error", 3);
                        break;
                    case event.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                        this.sendUpdate("error", 4);
                        break;
                    default:
                        this.sendUpdate("error", 5)
                }
            } catch (i) {}
        },
        setPlay: function() {
            this.mediaElement.get(0).playlist.play()
        },
        setPause: function() {
            this.mediaElement.get(0).playlist.pause()
        },
        setVolume: function(t) {
            this._volume = t, this.mediaElement.get(0).audio.volume = 100 * t, this.volumeListener(t)
        },
        setSeek: function(t) {
            this.mediaElement.get(0).input.position = t / this.media.duration, this._setSeekState("seeked", t)
        },
        setFullscreen: function() {
            pos = this.mediaElement.get(0).input.position, this.mediaElement.get(0).playlist.stop(), this.setPlay(), this.mediaElement.get(0).input.position = pos, this.getState("PAUSED") && this.setPause()
        },
        setResize: function() {
            this._scaleVideo(!1)
        }
    })
}), jQuery(function(t) {
    $p.newModel({
        modelId: "PLAYLIST",
        iLove: [{
            ext: "json",
            type: "text/json",
            platform: "browser"
        }, {
            ext: "jsonp",
            type: "text/jsonp",
            platform: "browser"
        }, {
            ext: "xml",
            type: "text/xml",
            platform: "browser"
        }, {
            ext: "json",
            type: "application/json",
            platform: "browser"
        }, {
            ext: "jsonp",
            type: "application/jsonp",
            platform: "browser"
        }, {
            ext: "xml",
            type: "application/xml",
            platform: "browser"
        }],
        applyMedia: function(t) {
            this.displayReady()
        },
        setPlay: function() {
            this.sendUpdate("playlist", this.media)
        }
    })
}), jQuery(function(t) {
    $p.newModel({
        browserVersion: "1",
        modelId: "IMAGE",
        iLove: [{
            ext: "jpg",
            type: "image/jpeg",
            platform: "browser",
            streamType: ["http"]
        }, {
            ext: "gif",
            type: "image/gif",
            platform: "browser",
            streamType: ["http"]
        }, {
            ext: "png",
            type: "image/png",
            platform: "browser",
            streamType: ["http"]
        }],
        allowRandomSeek: !0,
        _position: 0,
        _duration: 0,
        applyMedia: function(t) {
            this.mediaElement = this.applyImage(this.media.file[0].src, t.html("")), this._duration = this.pp.getConfig("duration") || 1, this._position = -1, this.displayReady(), this._position = -.5
        },
        setPlay: function() {
            var t = this;
            return this._setBufferState("full"), this.progressListener(100), this.playingListener(), 0 == this._duration ? (t._setState("completed"), void 0) : (function() {
                return t._position >= t._duration ? (t._setState("completed"), void 0) : (t.getState("PLAYING") && (t.timeListener({
                    duration: t._duration,
                    position: t._position
                }), setTimeout(arguments.callee, 200), t._position += .2), void 0)
            }(), void 0)
        },
        detachMedia: function() {
            this.mediaElement.remove()
        },
        setPause: function() {
            this.pauseListener()
        },
        setSeek: function(t) {
            this._duration > t && (this._position = t, this.seekedListener())
        }
    }), $p.newModel({
        modelId: "HTML",
        iLove: [{
            ext: "html",
            type: "text/html",
            platform: "browser",
            streamType: ["http"]
        }],
        applyMedia: function(e) {
            var i = this;
            this.mediaElement = t(document.createElement("iframe")).attr({
                id: this.pp.getMediaId() + "_iframe",
                name: this.pp.getMediaId() + "_iframe",
                src: this.media.file[0].src,
                scrolling: "no",
                frameborder: "0",
                width: "100%",
                height: "100%"
            }).css({
                overflow: "hidden",
                border: "0px",
                width: "100%",
                height: "100%"
            }).appendTo(e.html("")), this.mediaElement.load(function(t) {
                i.success()
            }), this.mediaElement.error(function(t) {
                i.remove()
            }), this._duration = this.pp.getConfig("duration")
        },
        success: function() {
            this.displayReady()
        },
        remove: function() {
            this.mediaElement.remove()
        }
    }, "IMAGE")
}), jQuery(function(t) {
    $p.newModel({
        modelId: "OSMFVIDEO",
        replace: "VIDEOFLASH",
        flashVersion: "10.2",
        flashVerifyMethod: "addEventListener",
        iLove: [{
            ext: "flv",
            type: "video/flv",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "mp4",
            type: "video/mp4",
            platform: "flash",
            streamType: ["*"]
        }, {
            ext: "f4v",
            type: "video/mp4",
            platform: "flash",
            streamType: ["*"]
        }, {
            ext: "mov",
            type: "video/quicktime",
            platform: "flash",
            streamType: ["*"]
        }, {
            ext: "m4v",
            type: "video/mp4",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "f4m",
            type: "application/f4m+xml",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "m3u8",
            type: "application/mpegURL",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "m3u8",
            type: "application/x-mpegURL",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "m3u8",
            type: "application/vnd.apple.mpegurl",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "manifest",
            type: "application/vnd.ms-ss",
            platform: "flash",
            fixed: !0,
            streamType: ["*"]
        }],
        hasGUI: !1,
        allowRandomSeek: !1,
        isPseudoStream: !1,
        streamType: "http",
        availableQualities: {},
        _hardwareAcceleration: !0,
        _isStream: !1,
        _isDVR: !1,
        _isMuted: !1,
        _isStarted: !1,
        _qualitySwitching: !1,
        _isDynamicStream: !1,
        _volume: 0,
        _eventMap: {
            mediaPlayerCapabilityChange: "OSMF_PlayerCapabilityChange",
            durationChange: "OSMF_durationChange",
            currentTimeChange: "OSMF_currentTimeChange",
            loadStateChange: "OSMF_loadStateChange",
            bufferingChange: "OSMF_bufferingChange",
            bytesLoadedChange: "OSMF_bytesLoadedChange",
            playStateChange: "OSMF_playerStateChange",
            seekingChange: "OSMF_seekingChange",
            canPlayChange: "OSMF_seekingChange",
            isRecordingChange: "OSMF_isRecordingChange",
            complete: "endedListener",
            volumeChange: "volumeListener",
            mediaError: "errorListener",
            MBRItemChange: "OSMF_universal",
            isDynamicStreamChange: "OSMF_updateDynamicStream",
            autoSwitchChange: "OSMF_updateDynamicStream",
            switchingChange: "OSMF_updateDynamicStream"
        },
        applyMedia: function(e) {
            var i = this;
            window["projekktorOSMFReady" + this.pp.getId()] = function() {
                projekktor(i.pp.getId()).playerModel._OSMFListener(arguments)
            };
            var s = {
                id: this.pp.getMediaId() + "_flash",
                name: this.pp.getMediaId() + "_flash",
                src: this.pp.getConfig("playerFlashMP4"),
                width: "100%",
                height: "100%",
                allowScriptAccess: "always",
                quality: "high",
                menu: !1,
                allowFullScreen: "true",
                wmode: $p.utils.ieVersion() ? "transparent" : "opaque",
                SeamlessTabbing: "false",
                bgcolor: "#000000",
                FlashVars: t.extend({
                    enableStageVideo: this._hardwareAcceleration,
                    disableHardwareAcceleration: !this._hardwareAcceleration,
                    javascriptCallbackFunction: "window.projekktorOSMFReady" + this.pp.getId()
                }, this.pp.getConfig("OSMFVars"))
            };
            this.createFlash(s, e)
        },
        flashReadyListener: function() {
            this.applySrc(), this.displayReady()
        },
        removeListeners: function() {},
        loadProgressUpdate: function() {},
        addListeners: function() {},
        applySrc: function() {
            var t = this,
                e = this.getSource();
            this.mediaElement.get(0).setMediaResourceURL(e[0].src), this.streamType = e[0].streamType || this.pp.getConfig("streamType") || "http", this.getState("PLAYING") && (this.setPlay(), t.isPseudoStream !== !0 && this.media.position > 0 && this.setSeek(this.media.position)), "pseudo" == this.streamType && (this.isPseudoStream = !0, this.allowRandomSeek = !0, this.media.loadProgress = 100), this.streamType.indexOf("live") > -1 && (this.allowRandomSeek = !0, this.media.loadProgress = 100)
        },
        _OSMFListener: function() {
            var e = arguments[0][1],
                i = arguments[0][2],
                s = this;
            switch (this.mediaElement = t("#" + this.pp.getMediaId() + "_flash"), e) {
                case "onJavaScriptBridgeCreated":
                    null !== this.mediaElement && this.getState("AWAKENING") && (t.each(this._eventMap, function(t, e) {
                        s.mediaElement.get(0).addEventListener(t, "projekktor('" + s.pp.getId() + "').playerModel." + e)
                    }), this.flashReadyListener());
                    break;
                case "loadedmetadata":
                    this.metaDataListener(i);
                    break;
                case "progress":
            }
        },
        OSMF_universal: function() {},
        OSMF_isRecordingChange: function() {},
        OSMF_PlayerCapabilityChange: function(t) {},
        OSMF_bytesLoadedChange: function() {
            var t = this.mediaElement.get(0),
                e = 0;
            e = 100 * t.getBytesLoaded() / t.getBytesTotal(), this.media.loadProgress > e || (this.media.loadProgress = this.allowRandomSeek === !0 ? 100 : -1, this.media.loadProgress = 100 > this.media.loadProgress || void 0 === this.media.loadProgress ? e : 100, this.sendUpdate("progress", this.media.loadProgress))
        },
        OSMF_durationChange: function(t) {
            isNaN(t) || (this.timeListener({
                position: this.media.position,
                duration: t || 0
            }), this.seekedListener())
        },
        OSMF_currentTimeChange: function(t) {
            this._isDVR && this.sendUpdate("isLive", t + 20 >= this.media.duration), this.timeListener({
                position: t,
                duration: this.media.duration || 0
            })
        },
        OSMF_seekingChange: function(t) {
            this.seekedListener()
        },
        OSMF_bufferingChange: function(t) {
            t === !0 ? this.waitingListener() : this.canplayListener()
        },
        OSMF_loadStateChange: function(t) {
            switch (t) {
                case "loading":
                    this.waitListener();
                    break;
                case "ready":
                    this.getState("awakening") && this.displayReady(), this.getState("starting") && this.setPlay(), this.mediaElement.get(0).getStreamType().indexOf("dvr") > -1 && (this.allowRandomSeek = !0, this.media.loadProgress = 100);
                    break;
                case "loadError":
            }
        },
        OSMF_playerStateChange: function(t) {
            var e = this;
            switch (this._isDVR || "dvr" != this.mediaElement.get(0).getStreamType() || (this._isDVR = !0, this.sendUpdate("streamTypeChange", "dvr")), t) {
                case "playing":
                    this.playingListener();
                    break;
                case "paused":
                    this.pauseListener(), this._isDVR && function() {
                        e.getState("PAUSED") && e.media.position >= .5 && (e.timeListener({
                            position: e.media.position - .5,
                            duration: e.media.duration || 0
                        }), setTimeout(arguments.callee, 500))
                    }();
                    break;
                case "stopped":
                    this.getSeekState("SEEKING") || this.endedListener()
            }
        },
        OSMF_updateDynamicStream: function() {
            var e = this.mediaElement.get(0).getStreamItems(),
                i = "",
                s = [];
            for (var n in e) e.hasOwnProperty(n) && void 0 !== e[n].bitrate && (i = e[n].width + "x" + e[n].height, this.pp.getConfig("OSMFQualityMap") && this.pp.getConfig("OSMFQualityMap")[i] && (this.availableQualities[this.pp.getConfig("OSMFQualityMap")[i]] = n));
            $p.utils.log(e), t.each(this.availableQualities, function(t, e) {
                s.push(t)
            }), s.push("auto"), this._isDynamicStream = !0, this.sendUpdate("availableQualitiesChange", s)
        },
        switchDynamicStreamIndex: function(t) {
            -1 == t ? this.mediaElement.get(0).setAutoDynamicStreamSwitch(!0) : (this.mediaElement.get(0).getAutoDynamicStreamSwitch() && this.mediaElement.get(0).setAutoDynamicStreamSwitch(!1), this.mediaElement.get(0).switchDynamicStreamIndex(t))
        },
        errorListener: function() {
            switch (arguments[0]) {
                case 15:
                    this.sendUpdate("error", 5);
                    break;
                case 80:
                    this.sendUpdate("error", 80);
                    break;
                default:
            }
        },
        detachMedia: function() {
            try {
                this.mediaElement.get(0).remove()
            } catch (t) {}
        },
        volumeListener: function(t) {
            this._volume = t
        },
        endedListener: function(t) {
            null !== this.mediaElement && (0 >= this.media.maxpos || "STARTING" != this.getState() && this._qualitySwitching !== !0 && this._setState("completed"))
        },
        setSeek: function(t) {
            return this.isPseudoStream ? (this._setSeekState("seeking"), this.media.offset = t, this.applySrc(), void 0) : (-1 == t && (t = this.getDuration()), this.mediaElement.get(0).seek(t), void 0)
        },
        setVolume: function(t) {
            null === this.mediaElement ? this.volumeListener(t) : this.mediaElement.get(0).setVolume(t)
        },
        setPause: function(t) {
            this.mediaElement.get(0).pause()
        },
        setPlay: function(t) {
            this.mediaElement.get(0).play2()
        },
        setQuality: function(t) {
            if (this._quality != t) {
                if (this._quality = t, this._isDynamicStream === !0) return this.switchDynamicStreamIndex("auto" == t ? -1 : this.availableQualities[t]), void 0;
                this._qualitySwitching = !0, this.applySrc(), this._qualitySwitching = !1, this.qualityChangeListener()
            }
        },
        getVolume: function() {
            return this._isMuted === !0 ? 0 : null === this.mediaElement ? this.media.volume : this._volume
        },
        getSrc: function() {
            try {
                return this.mediaElement.get(0).getCurrentSrc()
            } catch (t) {
                return null
            }
        },
        getQuality: function() {
            return this._quality
        },
        _scaleVideo: function() {}
    }), $p.newModel({
        modelId: "OSMFVIDEONA",
        iLove: [{
            ext: "flv",
            type: "video/flv",
            platform: "flashna",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "mp4",
            type: "video/mp4",
            platform: "flashna",
            streamType: ["*"]
        }, {
            ext: "f4v",
            type: "video/mp4",
            platform: "flashna",
            streamType: ["*"]
        }, {
            ext: "mov",
            type: "video/quicktime",
            platform: "flashna",
            streamType: ["*"]
        }, {
            ext: "m4v",
            type: "video/mp4",
            platform: "flashna",
            fixed: !0,
            streamType: ["*"]
        }, {
            ext: "f4m",
            type: "application/f4m+xml",
            platform: "flashna",
            fixed: !0,
            streamType: ["*"]
        }],
        _hardwareAcceleration: !1
    }, "OSMFVIDEO"), $p.newModel({
        modelId: "OSMFAUDIO",
        replace: "AUDIOFLASH",
        hasGUI: !1,
        iLove: [{
            ext: "mp3",
            type: "audio/mp3",
            platform: "flash",
            streamType: ["*"]
        }, {
            ext: "m4a",
            type: "audio/mp4",
            platform: "flash",
            streamType: ["*"]
        }, {
            ext: "m4a",
            type: "audio/mpeg",
            platform: "flash",
            streamType: ["*"]
        }],
        applyMedia: function(e) {
            var i = this;
            $p.utils.blockSelection(e), this.imageElement = this.applyImage(this.getPoster("cover") || this.getPoster("poster"), e);
            var s = t("#" + this.pp.getMediaId() + "_flash_container");
            0 === s.length && (s = t(document.createElement("div")).css({
                width: "1px",
                height: "1px"
            }).attr("id", this.pp.getMediaId() + "_flash_container").prependTo(this.pp.getDC())), window["projekktorOSMFReady" + this.pp.getId()] = function() {
                projekktor(i.pp.getId()).playerModel._OSMFListener(arguments)
            };
            var n = {
                id: this.pp.getMediaId() + "_flash",
                name: this.pp.getMediaId() + "_flash",
                src: this.pp.getConfig("playerFlashMP4"),
                width: "100%",
                height: "100%",
                allowScriptAccess: "always",
                quality: "height",
                menu: !1,
                allowFullScreen: "true",
                wmode: "opaque",
                seamlesstabbing: "false",
                bgcolor: "#ccc",
                FlashVars: t.extend({
                    javascriptCallbackFunction: "window.projekktorOSMFReady" + this.pp.getId()
                }, this.pp.getConfig("OSMFVars"))
            };
            this.createFlash(n, s, !1)
        }
    }, "OSMFVIDEO")
}), jQuery(function(t) {
    $p.newModel({
        modelId: "YTVIDEO",
        iLove: [{
            ext: "youtube.com",
            type: "video/youtube",
            platform: "flash",
            fixed: "maybe"
        }],
        allowRandomSeek: !0,
        useIframeAPI: !0,
        flashVerifyMethod: "cueVideoById",
        _ffFix: !1,
        _updateTimer: null,
        init: function(e) {
            var i = this;
            if (this.useIframeAPI = this.pp.getConfig("useYTIframeAPI") || this.pp.getIsMobileClient(), this.hasGUI = this.pp.getIsMobileClient(), !this.useIframeAPI) return this.requiresFlash = 8, this.ready(), void 0;
            var s = this.pp.getId();
            window.ProjekktorYoutubePlayerAPIReady !== !0 ? (t.getScript("http://www.youtube.com/player_api"), function() {
                try {
                    if (1 == window.ProjekktorYoutubePlayerAPIReady) return i.ready(), void 0;
                    setTimeout(arguments.callee, 50)
                } catch (t) {
                    setTimeout(arguments.callee, 50)
                }
            }()) : this.ready(), window.onYouTubePlayerAPIReady = function() {
                window.ProjekktorYoutubePlayerAPIReady = !0
            }
        },
        applyMedia: function(e) {
            this._setBufferState("empty");
            var i = this,
                s = "YTAUDIO" == this.modelId ? 1 : "100%",
                n = "YTAUDIO" == this.modelId ? 1 : "100%";
            if ("YTAUDIO" == this.modelId && (this.imageElement = this.applyImage(this.pp.getPoster(), e)), this.useIframeAPI) {
                e.html("").append(t("<div/>").attr("id", this.pp.getId() + "_media_youtube").css({
                    width: "100%",
                    height: "100%",
                    position: "absolute",
                    top: 0,
                    left: 0
                }));
                var a = t("<div/>").attr("id", this.pp.getId() + "_media_youtube_cc").css({
                    width: "100%",
                    height: "100%",
                    backgroundColor: $p.utils.ieVersion() ? "#000" : "transparent",
                    filter: "alpha(opacity = 0.1)",
                    position: "absolute",
                    top: 0,
                    left: 0
                });
                e.append(a), this.mediaElement = new YT.Player(this.pp.getId() + "_media_youtube", {
                    width: this.pp.getIsMobileClient() ? this.pp.config._width : s,
                    height: this.pp.getIsMobileClient() ? this.pp.config._height : n,
                    playerVars: {
                        autoplay: 0,
                        disablekb: 0,
                        version: 3,
                        start: 0,
                        controls: this.pp.getIsMobileClient() ? 1 : 0,
                        showinfo: 0,
                        enablejsapi: 1,
                        start: this.media.position || 0,
                        origin: window.location.href,
                        wmode: "transparent",
                        modestbranding: 1
                    },
                    videoId: this.youtubeGetId(),
                    events: {
                        onReady: function(t) {
                            i.onReady(t)
                        },
                        onStateChange: function(t) {
                            i.stateChange(t)
                        },
                        onError: function(t) {
                            i.errorListener(t)
                        }
                    }
                })
            } else {
                var r = {
                    id: this.pp.getId() + "_media_youtube",
                    name: this.pp.getId() + "_media_youtube",
                    src: "http://www.youtube.com/apiplayer",
                    width: this.pp.getIsMobileClient() ? this.pp.config._width : s,
                    height: this.pp.getIsMobileClient() ? this.pp.config._height : n,
                    bgcolor: "#000000",
                    allowScriptAccess: "always",
                    wmode: "transparent",
                    FlashVars: {
                        enablejsapi: 1,
                        autoplay: 0,
                        version: 3,
                        modestbranding: 1,
                        showinfo: 0
                    }
                };
                this.createFlash(r, e)
            }
        },
        flashReadyListener: function() {
            this._youtubeResizeFix(), this.addListeners(), this.mediaElement.cueVideoById(this.youtubeGetId(), this.media.position || 0, this._playbackQuality)
        },
        _youtubeResizeFix: function() {
            this.applyCommand("volume", this.pp.getConfig("volume"))
        },
        addListeners: function() {
            this.mediaElement.addEventListener("onStateChange", "projekktor('" + this.pp.getId() + "').playerModel.stateChange"), this.mediaElement.addEventListener("onError", "projekktor('" + this.pp.getId() + "').playerModel.errorListener"), this.mediaElement.addEventListener("onPlaybackQualityChange", "projekktor('" + this.pp.getId() + "').playerModel.qualityChangeListener")
        },
        setSeek: function(t) {
            try {
                this.mediaElement.seekTo(t, !0), this.getState("PLAYING") || this.timeListener({
                    position: this.mediaElement.getCurrentTime(),
                    duration: this.mediaElement.getDuration()
                })
            } catch (e) {}
        },
        setVolume: function(t) {
            try {
                this.mediaElement.setVolume(100 * t)
            } catch (e) {}
        },
        setPause: function(t) {
            try {
                this.mediaElement.pauseVideo()
            } catch (e) {}
        },
        setPlay: function(t) {
            try {
                this.mediaElement.playVideo()
            } catch (e) {}
        },
        setQuality: function(t) {
            try {
                this.mediaElement.setPlaybackQuality(t)
            } catch (e) {}
        },
        getVolume: function() {
            try {
                return this.mediaElement.getVolume()
            } catch (t) {}
            return 0
        },
        getPoster: function() {
            return this.media.config.poster || this.pp.config.poster || "http://img.youtube.com/vi/" + this.youtubeGetId() + "/0.jpg"
        },
        getPlaybackQuality: function() {
            try {
                return this.mediaElement.getPlaybackQuality()
            } catch (t) {
                return !1
            }
        },
        getSrc: function() {
            return this.youtubeGetId() || null
        },
        errorListener: function(t) {
            switch (void 0 == t.data ? t : t.data) {
                case 100:
                    this.setTestcard(500);
                    break;
                case 101:
                case 150:
                    this.setTestcard(501);
                    break;
                case 2:
                    this.setTestcard(502)
            }
        },
        stateChange: function(t) {
            if (clearTimeout(this._updateTimer), null !== this.mediaElement && !this.getState("COMPLETED")) switch (void 0 == t.data ? t : t.data) {
                case -1:
                    this.setPlay(), this.ffFix = !0;
                    break;
                case 0:
                    if (this.getState("AWAKENING")) break;
                    this._setBufferState("full"), this.endedListener({});
                    break;
                case 1:
                    this._setBufferState("full"), (this.media.position || 0) > 0 && this._isFF() && this.ffFix && (this.ffFix = !1, this.setSeek(this.media.position)), this.playingListener({}), this.canplayListener({}), this.updateInfo();
                    break;
                case 2:
                    this.pauseListener({});
                    break;
                case 3:
                    this.waitingListener({});
                    break;
                case 5:
                    this.useIframeAPI !== !0 && this.onReady()
            }
        },
        onReady: function() {
            if (this.setVolume(this.pp.getVolume()), t("#" + this.pp.getId() + "_media").attr("ALLOWTRANSPARENCY", !0).attr({
                    scrolling: "no",
                    frameborder: 0
                }).css({
                    overflow: "hidden",
                    display: "block",
                    border: "0"
                }), this.media.title || this.pp.config.title || this.pp.getIsMobileClient()) return this.displayReady(), void 0;
            var e = this;
            t.ajax({
                url: "http://gdata.youtube.com/feeds/api/videos/" + this.youtubeGetId() + "?v=2&alt=jsonc",
                async: !1,
                complete: function(i, s) {
                    try {
                        data = i.responseText, "string" == typeof data && (data = t.parseJSON(data)), data.data.title && e.sendUpdate("config", {
                            title: data.data.title + " (" + data.data.uploader + ")"
                        })
                    } catch (n) {}
                    e.displayReady()
                }
            })
        },
        youtubeGetId: function() {
            return encodeURIComponent(this.media.file[0].src.replace(/^[^v]+v.(.{11}).*/, "$1"))
        },
        updateInfo: function() {
            var t = this;
            clearTimeout(this._updateTimer),
                function() {
                    if (null == t.mediaElement) return clearTimeout(t._updateTimer), void 0;
                    try {
                        t.getState("PLAYING") && (t.timeListener({
                            position: t.mediaElement.getCurrentTime(),
                            duration: t.mediaElement.getDuration()
                        }), t.progressListener({
                            loaded: t.mediaElement.getVideoBytesLoaded(),
                            total: t.mediaElement.getVideoBytesTotal()
                        }), t._updateTimer = setTimeout(arguments.callee, 500))
                    } catch (e) {}
                }()
        }
    }), $p.newModel({
        modelId: "YTAUDIO",
        iLove: [{
            ext: "youtube.com",
            type: "audio/youtube",
            platform: "flash",
            fixed: "maybe"
        }]
    }, "YTVIDEO")
});
var projekktorDisplay = function() {};
jQuery(function(t) {
    projekktorDisplay.prototype = {
        version: "1.1.00",
        logo: null,
        logoIsFading: !1,
        display: null,
        displayClicks: 0,
        buffIcn: null,
        buffIcnSprite: null,
        bufferDelayTimer: null,
        _controlsDims: null,
        config: {
            displayClick: {
                callback: "setPlayPause",
                value: null
            },
            displayPlayingClick: {
                callback: "setPlayPause",
                value: null
            },
            displayDblClick: {
                callback: null,
                value: null
            },
            staticControls: !1,
            bufferIconDelay: 1e3,
            spriteUrl: "",
            spriteWidth: 50,
            spriteHeight: 50,
            spriteTiles: 25,
            spriteOffset: 1,
            spriteCountUp: !1
        },
        initialize: function() {
            this.display = this.applyToPlayer(t("<div/>")), this.startButton = this.applyToPlayer(t("<div/>").addClass("start"), "startbtn"), this.buffIcn = this.applyToPlayer(t("<div/>").addClass("buffering"), "buffericn"), this.imaContainer = this.applyToPlayer(t("<div/>").addClass("ima"), "ima"), this.setActive(), "" !== this.config.spriteUrl && (this.buffIcnSprite = t("<div/>").appendTo(this.buffIcn).css({
                width: this.config.spriteWidth,
                height: this.config.spriteHeight,
                marginLeft: (this.buffIcn.width() - this.config.spriteWidth) / 2 + "px",
                marginTop: (this.buffIcn.height() - this.config.spriteHeight) / 2 + "px",
                backgroundColor: "transparent",
                backgroundImage: "url(" + this.config.spriteUrl + ")",
                backgroundRepeat: "no-repeat",
                backgroundPosition: "0 0"
            }).addClass("inactive")), this.pp.getMediaContainer(), this.pluginReady = !0
        },
        displayReadyHandler: function() {
            var t = this;
            this.hideStartButton(), this.startButton.unbind().click(function() {
                t.pp.setPlay()
            })
        },
        syncingHandler: function() {
            this.showBufferIcon(), this.pp.getState("IDLE") && this.hideStartButton()
        },
        readyHandler: function() {
            this.hideBufferIcon(), this.pp.getState("IDLE") && this.showStartButton()
        },
        bufferHandler: function(t) {
            (this.pp.getState("PLAYING") || this.pp.getState("AWAKENING")) && ("EMPTY" == t ? this.showBufferIcon() : this.hideBufferIcon())
        },
        stateHandler: function(t) {
            switch (t) {
                case "IDLE":
                    clearTimeout(this._cursorTimer), this.display.css("cursor", "pointer");
                    break;
                case "PLAYING":
                    this.hideBufferIcon(), this.hideStartButton();
                    break;
                case "IDLE":
                    this.showStartButton();
                    break;
                case "STARTING":
                case "AWAKENING":
                    this.showBufferIcon(), this.hideStartButton();
                    break;
                case "COMPLETED":
                case "STOPPED":
                    this.hideBufferIcon();
                    break;
                default:
                    this.hideStartButton()
            }
        },
        errorHandler: function() {
            this.hideBufferIcon(), this.hideStartButton()
        },
        startHandler: function() {
            this.mousemoveHandler()
        },
        scheduleLoadingHandler: function() {
            this.hideStartButton(), this.showBufferIcon()
        },
        scheduledHandler: function() {
            this.getConfig("autoplay") || this.showStartButton(), this.hideBufferIcon()
        },
        plugineventHandler: function(t) {
            if ("controlbar" == t.PLUGIN && "show" == t.EVENT && this.getConfig("staticControls")) {
                var e = 100 * t.height / this.pp.getDC().height();
                this.display.height(100 - e + "%").data("sc", !0)
            }
        },
        qualityChangeHandler: function() {
            this.hideBufferIcon()
        },
        mousemoveHandler: function(t) {
            var e = this.display;
            return this.pp.getState("IDLE") ? (e.css("cursor", "pointer"), void 0) : (e.css("cursor", "auto"), clearTimeout(this._cursorTimer), -1 == "AWAKENING|ERROR|PAUSED".indexOf(this.pp.getState()) && (this._cursorTimer = setTimeout(function() {
                e.css("cursor", "none")
            }, 3e3)), void 0)
        },
        mousedownHandler: function(e) {
            var i = this;
            if (-1 != (t(e.target).attr("id") || "").indexOf("_media") && (clearTimeout(this._cursorTimer), this.display.css("cursor", "auto"), 1 == e.which)) {
                switch (this.pp.getState()) {
                    case "ERROR":
                        return this.pp.setConfig({
                            disallowSkip: !1
                        }), this.pp.setActiveItem("next"), void 0;
                    case "IDLE":
                        return this.pp.setPlay(), void 0
                }
                this.pp.getHasGUI() !== !0 && (this.displayClicks++, this.pp._promote("displayClick"), this.displayClicks > 0 && setTimeout(function() {
                    1 == i.displayClicks ? "PLAYING" == i.pp.getState() ? i.clickHandler("displayPlaying") : i.clickHandler("display") : 2 == i.displayClicks && i.clickHandler("displayDbl"), i.displayClicks = 0
                }, 250))
            }
        },
        showStartButton: function() {
            this.startButton.addClass("active").removeClass("inactive")
        },
        hideStartButton: function() {
            this.startButton.addClass("inactive").removeClass("active")
        },
        hideBufferIcon: function() {
            clearTimeout(this.bufferDelayTimer), this.buffIcn.addClass("inactive").removeClass("active")
        },
        showBufferIcon: function(t) {
            var e = this;
            if (clearTimeout(this.bufferDelayTimer), !this.pp.getHasGUI()) {
                if ("YTAUDIO" !== this.pp.getModel() && "YTVIDEO" !== this.pp.getModel() || this.pp.getState("IDLE") || (t = !0), t !== !0 && this.getConfig("bufferIconDelay") > 0) return this.bufferDelayTimer = setTimeout(function() {
                    e.showBufferIcon(!0)
                }, this.getConfig("bufferIconDelay")), void 0;
                if (!this.buffIcn.hasClass("active") && (this.buffIcn.addClass("active").removeClass("inactive"), null !== e.buffIcnSprite)) {
                    var i = e.config.spriteCountUp === !0 ? 0 : (e.config.spriteHeight + e.config.spriteOffset) * (e.config.spriteTiles - 1),
                        s = i;
                    e.buffIcnSprite.addClass("active").removeClass("inactive"),
                        function() {
                            e.buffIcn.is(":visible") && (e.buffIcnSprite.css("backgroundPosition", "0px -" + s + "px"), e.config.spriteCountUp === !0 ? s += e.config.spriteHeight + e.config.spriteOffset : s -= e.config.spriteHeight + e.config.spriteOffset, (s > (i + e.config.spriteHeight) * e.config.spriteTiles || e.config.spriteOffset > s) && (s = i), setTimeout(arguments.callee, 60))
                        }()
                }
            }
        }
    }
});
var projekktorControlbar = function() {};
jQuery(function(t) {
    projekktorControlbar.prototype = {
        version: "1.1.01",
        _cTimer: null,
        _isDVR: !1,
        _noHide: !1,
        _vSliderAct: !1,
        cb: null,
        controlElements: {},
        controlElementsConfig: {
            sec_dur: null,
            min_dur: null,
            sec_abs_dur: null,
            min_abs_dur: null,
            hr_dur: null,
            sec_elp: null,
            min_elp: null,
            sec_abs_elp: null,
            min_abs_elp: null,
            hr_elp: null,
            sec_rem: null,
            min_rem: null,
            sec_abs_rem: null,
            min_abs_rem: null,
            hr_rem: null,
            sec_tip: null,
            min_tip: null,
            sec_abs_tip: null,
            min_abs_tip: null,
            hr_tip: null,
            cb: null,
            playhead: {
                on: null,
                call: null
            },
            loaded: null,
            golive: [{
                on: ["touchstart", "click"],
                call: "goliveClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            scrubber: null,
            scrubbertip: null,
            scrubberknob: null,
            scrubberdrag: [{
                on: ["mouseenter"],
                call: "scrubberShowTooltip"
            }, {
                on: ["mouseout"],
                call: "scrubberHideTooltip"
            }, {
                on: ["mousemove"],
                call: "scrubberdragTooltip"
            }, {
                on: ["mousedown"],
                call: "scrubberdragStartDragListener"
            }],
            play: [{
                on: ["touchstart", "click"],
                call: "playClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            pause: [{
                on: ["touchstart", "click"],
                call: "pauseClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            stop: [{
                on: ["touchstart", "click"],
                call: "stopClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            prev: [{
                on: ["touchstart", "click"],
                call: "prevClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            next: [{
                on: ["touchstart", "click"],
                call: "nextClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            rewind: [{
                on: ["touchstart", "click"],
                call: "rewindClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            forward: [{
                on: ["touchstart", "click"],
                call: "forwardClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            fsexit: [{
                on: ["touchstart", "click"],
                call: "exitFullscreenClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            fsenter: [{
                on: ["touchstart", "click"],
                call: "enterFullscreenClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            loquality: [{
                on: ["touchstart", "click"],
                call: "setQualityClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            hiquality: [{
                on: ["touchstart", "click"],
                call: "setQualityClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            vslider: [{
                on: ["touchstart", "click"],
                call: "vsliderClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            vmarker: [{
                on: ["touchstart", "click"],
                call: "vsliderClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            vknob: {
                on: ["mousedown"],
                call: "vknobStartDragListener"
            },
            volumePanel: [{
                on: ["mousemove"],
                call: "volumeBtnHover"
            }, {
                on: ["mouseout"],
                call: "volumeBtnOut"
            }],
            volume: null,
            mute: [{
                on: ["touchstart", "click"],
                call: "muteClk"
            }, {
                on: ["mouseout"],
                call: "volumeBtnOut"
            }, {
                on: ["mousemove"],
                call: "volumeBtnHover"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            unmute: [{
                on: ["touchstart", "click"],
                call: "unmuteClk"
            }, {
                on: ["mouseout"],
                call: "volumeBtnOut"
            }, {
                on: ["mousemove"],
                call: "volumeBtnHover"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            vmax: [{
                on: ["touchstart", "click"],
                call: "vmaxClk"
            }, {
                on: ["mouseout"],
                call: "volumeBtnOut"
            }, {
                on: ["mousemove"],
                call: "volumeBtnHover"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            open: [{
                on: ["touchstart", "click"],
                call: "openCloseClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            close: [{
                on: ["touchstart", "click"],
                call: "openCloseClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            loop: [{
                on: ["touchstart", "click"],
                call: "loopClk"
            }, {
                on: ["touchend"],
                call: "touchEnd"
            }],
            draghandle: {
                on: ["mousedown"],
                call: "handleStartDragListener"
            },
            controls: null,
            title: null
        },
        config: {
            toggleMute: !1,
            showCuePoints: !1,
            fadeDelay: 2500,
            showOnStart: !1,
            showOnIdle: !1,
            controlsTemplate: '<ul class="left"><li><div %{play}></div><div %{pause}></div></li></ul><ul class="right"><li><div %{fsexit}></li><li><div %{loquality}></div><div %{hiquality}></div></li><li><div %{tracksbtn}></div></li><li><div %{vmax}></div></li><li><div %{vslider}><div %{vmarker}></div><div %{vknob}></div></div></li><li><div %{mute}></div></li><li><div %{timeleft}>%{hr_elp}:%{min_elp}:%{sec_elp} | %{hr_dur}:%{min_dur}:%{sec_dur}</div></li></ul><ul class="bottom"><li><div %{scrubber}><div %{loaded}></div><div %{playhead}></div><div %{scrubberknob}></div><div %{scrubberdrag}></div></div></li></ul><div %{scrubbertip}>%{hr_tip}:%{min_tip}:%{sec_tip}</div>'
        },
        initialize: function() {
            var e = this,
                i = this.playerDom.html(),
                s = !0,
                n = this.pp.getNS();
            for (var a in this.controlElementsConfig)
                if (i.match(RegExp(n + a, "gi"))) {
                    s = !1;
                    break
                }
            s ? (this.cb = this.applyToPlayer(t("<div/>").addClass("controls")), this.applyTemplate(this.cb, this.getConfig("controlsTemplate"))) : this.cb = this.playerDom.find("." + n + "controls");
            for (var a in this.controlElementsConfig) this.controlElements[a] = t(this.playerDom).find("." + n + a), $p.utils.blockSelection(t(this.controlElements[a]));
            this.addGuiListeners(), this.hidecb(!0), this.pluginReady = !0
        },
        applyTemplate: function(e, i) {
            var s = this,
                n = this.pp.getNS();
            if (i) {
                var a = i.match(/\%{[a-zA-Z_]*\}/gi);
                null != a && t.each(a, function(t, e) {
                    var s = e.replace(/\%{|}/gi, "");
                    i = e.match(/\_/gi) ? i.replace(e, '<span class="' + n + s + '"></span>') : i.replace(e, 'class="' + n + s + '"')
                }), e.html(i)
            }
        },
        updateDisplay: function() {
            var t = this,
                e = this.pp.getState();
            if (!this.pp.getHasGUI()) {
                if (0 == this.getConfig("controls")) return this.hidecb(!0), void 0;
                2 > this.pp.getItemCount() || this.getConfig("disallowSkip") ? (this._active("prev", !1), this._active("next", !1)) : (this._active("prev", !0), this._active("next", !0)), 1 > this.pp.getItemIdx() && this._active("prev", !1), this.pp.getItemIdx() >= this.pp.getItemCount() - 1 && this._active("next", !1), this.getConfig("disablePause") ? (this._active("play", !1), this._active("pause", !1)) : ("PLAYING" === e && this.drawPauseButton(), "PAUSED" === e && this.drawPlayButton(), "IDLE" === e && this.drawPlayButton()), this._active("stop", "IDLE" !== e), this._active("forward", "IDLE" !== e), this._active("rewind", "IDLE" !== e), this.pp.getInFullscreen() === !0 ? this.drawExitFullscreenButton() : this.drawEnterFullscreenButton(), this.getConfig("enableFullscreen") || (this._active("fsexit", !1), this._active("fsenter", !1)), this._active("loop", !0), this.controlElements.loop.addClass(this.pp.getConfig("loop") ? "on" : "off").removeClass(this.pp.getConfig("loop") ? "off" : "on"), this.displayQualityToggle(), this.displayTime(), this.displayVolume(this._getVolume())
            }
        },
        addGuiListeners: function() {
            var e = this;
            t.each(this.controlElementsConfig, function(i, s) {
                if (null == s) return !0;
                s instanceof Array || (s = [s]);
                for (var n = 0; s.length > n; n++) null != s[n].on && t.each(s[n].on, function(t, a) {
                    var r = "on" + a in window.document,
                        o = s[n].call;
                    if (!r) {
                        var l = document.createElement("div");
                        l.setAttribute("on" + a, "return;"), r = "function" == typeof l["on" + a]
                    }
                    r && e.controlElements[i].bind(a, function(t) {
                        e.clickCatcher(t, o, e.controlElements[i])
                    })
                });
                return !0
            }), this.cb.mousemove(function(t) {
                e.controlsFocus(t)
            }), this.cb.mouseout(function(t) {
                e.controlsBlur(t)
            })
        },
        clickCatcher: function(t, e, i) {
            var s = this;
            return t.stopPropagation(), t.preventDefault(), this[e](t, i), !1
        },
        touchEnd: function() {
            var t = this;
            this._cTimer = setTimeout(function() {
                t.hidecb()
            }, this.getConfig("fadeDelay")), this._noHide = !1
        },
        drawTitle: function() {
            this.controlElements.title.html(this.getConfig("title", ""))
        },
        hidecb: function(t) {
            return clearTimeout(this._cTimer), null != this.cb ? 0 == this.getConfig("controls") ? (this.cb.removeClass("active").addClass("inactive"), void 0) : (this.getConfig("showOnIdle") && this.pp.getState("IDLE") || (t && (this._noHide = !1), this._noHide || this.cb.hasClass("inactive") || (this.cb.removeClass("active").addClass("inactive"), this.sendEvent("hide", this.cb))), void 0) : void 0
        },
        showcb: function(t) {
            var e = this;
            if (clearTimeout(this._cTimer), this.pp.getHasGUI() || 0 == this.getConfig("controls")) return this.cb.removeClass("active").addClass("inactive"), void 0;
            if (null != this.cb && !("IDLE|AWAKENING|ERROR".indexOf(this.pp.getState()) > -1 && 1 != t)) {
                if (this.cb.hasClass("active") && t !== !1) return this._cTimer = setTimeout(function() {
                    e.hidecb()
                }, this.getConfig("fadeDelay")), void 0;
                this.cb.removeClass("inactive").addClass("active"), this.sendEvent("show", this.cb), this._cTimer = setTimeout(function() {
                    e.hidecb()
                }, this.getConfig("fadeDelay"))
            }
        },
        displayTime: function(e, i, s) {
            if (!this.pp.getHasGUI()) {
                var n = Math.round(10 * (e || this.pp.getLoadPlaybackProgress() || 0)) / 10,
                    a = i || this.pp.getDuration() || 0,
                    r = s || this.pp.getPosition() || 0,
                    o = t.extend({}, this._clockDigits(a, "dur"), this._clockDigits(r, "elp"), this._clockDigits(a - r, "rem"));
                if (this.controlElements.playhead.data("pct") != n) {
                    this.controlElements.playhead.data("pct", n).css({
                        width: n + "%"
                    }), this.controlElements.scrubberknob.css({
                        left: n + "%"
                    });
                    for (var l in this.controlElements) {
                        if ("cb" == l) break;
                        o[l] && t.each(this.controlElements[l], function() {
                            t(this).html(o[l])
                        })
                    }
                }
            }
        },
        displayProgress: function() {
            var t = Math.round(10 * this.pp.getLoadProgress()) / 10;
            this.controlElements.loaded.data("pct") != t && this.controlElements.loaded.data("pct", t).css("width", t + "%")
        },
        displayVolume: function(e) {
            var i = this;
            if (1 != this._vSliderAct && null != e) {
                var s = this.cb.hasClass("active"),
                    i = this,
                    n = this.getConfig("fixedVolume"),
                    a = this.controlElements.mute.hasClass("toggle") || this.controlElements.unmute.hasClass("toggle") || this.getConfig("toggleMute");
                if (this._active("mute", !n), this._active("unmute", !n), this._active("vmax", !n), this._active("vknob", !n), this._active("vmarker", !n), this._active("vslider", !n), !n) {
                    this.controlElements.vslider.width() > this.controlElements.vslider.height() ? (this.controlElements.vmarker.css("width", 100 * e + "%"), this.controlElements.vknob.css("left", 100 * e + "%")) : (this.controlElements.vmarker.css("height", 100 * e + "%"), this.controlElements.vknob.css("top", 100 - 100 * e + "%"));
                    var r = this.controlElements.volume.find("li"),
                        o = r.length - Math.ceil(100 * e / r.length);
                    for (var l = 0; r.length >= l; l++) l >= o ? t(r[l]).addClass("active") : t(r[l]).removeClass("active");
                    if (a) switch (parseFloat(e)) {
                        case 0:
                            this._active("mute", !1), this._active("unmute", !0), this._active("vmax", !0);
                            break;
                        default:
                            this._active("mute", !0), this._active("unmute", !1), this._active("vmax", !1)
                    }
                    s && this.cb.fadeTo(1, .99).fadeTo(1, 1, function() {
                        i.cb.removeAttr("style")
                    })
                }
            }
        },
        displayCuePoints: function(e) {
            var i = this,
                s = this.pp.getNS();
            this.getConfig("showCuePoints") && (i.controlElements.scrubber.remove("." + s + "cuepoint"), t.each(this.pp.getCuePoints() || [], function() {
                var n = Math.max(100 / e, Math.round(e / 100), 1),
                    a = 100 * this.on / e - 100 * (n / 2) / e,
                    r = this,
                    o = i.pp,
                    l = t(document.createElement("div")).addClass(s + "cuepoint").addClass("inactive").css("left", a + "%").css("width", n + "%").data("on", this.on);
                "" != this.title && l.attr("title", this.title), this.addListener("unlock", function() {
                    t(l).removeClass("inactive").addClass("active"), l.click(function() {
                        i.pp.setPlayhead(l.data("on"))
                    })
                }), i.controlElements.scrubber.append(l)
            }))
        },
        drawPauseButton: function(t) {
            this._active("pause", !0), this._active("play", !1)
        },
        drawPlayButton: function(t) {
            this._active("pause", !1), this._active("play", !0)
        },
        drawEnterFullscreenButton: function(t) {
            this._active("fsexit", !1), this._active("fsenter", !0)
        },
        drawExitFullscreenButton: function(t) {
            this._active("fsexit", !0), this._active("fsenter", !1)
        },
        displayQualityToggle: function(e) {
            var i = this.getConfig("playbackQualities"),
                s = this.pp.getPlaybackQualities(),
                n = this.pp.getNS();
            if (best = [], 2 > s.length || 2 > i.length) return this.controlElements.loquality.removeClass().addClass("inactive").addClass(n + "loquality").data("qual", ""), this.controlElements.hiquality.removeClass().addClass("inactive").addClass(n + "hiquality").data("qual", ""), void 0;
            i.sort(function(t, e) {
                return t.minHeight - e.minHeight
            });
            for (var a = i.length; a--; a > 0)
                if (t.inArray(i[a].key, s) > -1 && best.push(i[a].key), best.length > 1) break;
            this.cb.addClass("qualities"), best[0] == this.pp.getPlaybackQuality() ? (this._active("loquality", !0).addClass("qual" + best[1]).data("qual", best[1]), this._active("hiquality", !1).addClass("qual" + best[0]).data("qual", best[0])) : (this._active("loquality", !1).addClass("qual" + best[1]).data("qual", best[1]), this._active("hiquality", !0).addClass("qual" + best[0]).data("qual", best[0]))
        },
        itemHandler: function(e) {
            t(this.cb).find("." + this.pp.getNS() + "cuepoint").remove(), this.pp.setVolume(this._getVolume()), this.updateDisplay(), this.hidecb(!0), this.drawTitle(), this.displayQualityToggle(), this.pluginReady = !0
        },
        startHandler: function() {
            this.pp.setVolume(this._getVolume()), 1 == this.getConfig("showOnStart") ? this.showcb(!0) : this.hidecb(!0)
        },
        readyHandler: function(t) {
            clearTimeout(this._cTimer), this.getConfig("showOnIdle") && (this.showcb(!0), this.cb.removeClass("inactive").addClass("active").show()), this.pluginReady = !0
        },
        stateHandler: function(t) {
            return this.updateDisplay(), "STOPPED|AWAKENING|IDLE|DONE".indexOf(t) > -1 && (this.displayTime(0, 0, 0), this.displayProgress(0), this.pp.getIsMobileClient() && this.hidecb(!0)), "STOPPED|DONE|IDLE".indexOf(t) > -1 ? (this.hidecb(!0), void 0) : ("ERROR".indexOf(t) > -1 && (this._noHide = !1, this.hidecb(!0)), this.displayProgress(), void 0)
        },
        scheduleModifiedHandler: function() {
            "IDLE" !== this.pp.getState() && (this.updateDisplay(), this.displayTime(), this.displayProgress())
        },
        volumeHandler: function(t) {
            try {
                t > 0 && this.cookie("muted", !1), this.cookie("muted") || this.cookie("volume", t)
            } catch (e) {
                console.log(e)
            }
            this.displayVolume(this._getVolume())
        },
        progressHandler: function(t) {
            this.displayProgress()
        },
        timeHandler: function(t) {
            this.displayTime(), this.displayProgress()
        },
        qualityChangeHandler: function(t) {
            this.displayQualityToggle(t)
        },
        streamTypeChangeHandler: function(t) {
            "dvr" == t && (this._isDVR = !0, this.setActive(this.controlElements.golive, !0))
        },
        isLiveHandler: function(t) {
            t ? this.controlElements.golive.addClass("on").removeClass("off") : this.controlElements.golive.addClass("off").removeClass("on")
        },
        fullscreenHandler: function(t) {
            var e = this,
                i = this.pp.getNS();
            clearTimeout(this._cTimer), this._noHide = !1, this._vSliderAct = !1, this.getConfig("controls") && this.getConfig("enableFullscreen") && (t ? (this.cb.addClass("fullscreen"), this.drawExitFullscreenButton()) : (this.cb.removeClass("fullscreen"), this.drawEnterFullscreenButton()), "IDLE" != this.pp.getState() || this.getConfig("showOnIdle") || this.hidecb(!0))
        },
        durationChangeHandler: function(t) {
            this.displayCuePoints(t)
        },
        errorHandler: function(t) {
            this.hidecb(!0)
        },
        leftclickHandler: function() {
            this.mouseleaveHandler()
        },
        focusHandler: function(t) {
            this.showcb()
        },
        mouseenterHandler: function(t) {
            this.showcb()
        },
        mousemoveHandler: function(t) {
            this.pp.getState("STARTING") || this.showcb()
        },
        mouseleaveHandler: function() {},
        mousedownHandler: function(t) {
            this.showcb()
        },
        controlsFocus: function(t) {
            this._noHide = !0
        },
        controlsBlur: function(t) {
            this._noHide = !1
        },
        setQualityClk: function(e) {
            this.pp.setPlaybackQuality(t(e.currentTarget).data("qual"))
        },
        goliveClk: function(t) {
            this.pp.setSeek(-1)
        },
        playClk: function(t) {
            this.pp.setPlay()
        },
        pauseClk: function(t) {
            this.pp.setPause()
        },
        stopClk: function(t) {
            this.pp.setStop()
        },
        startClk: function(t) {
            this.pp.setPlay()
        },
        controlsClk: function(t) {},
        prevClk: function(t) {
            this.pp.setActiveItem("previous")
        },
        nextClk: function(t) {
            this.pp.setActiveItem("next")
        },
        forwardClk: function(t) {
            this.pp.setPlayhead("+10")
        },
        rewindClk: function(t) {
            this.pp.setPlayhead("-10")
        },
        muteClk: function(t) {
            this.cookie("muted", !0), this.pp.setVolume(0)
        },
        unmuteClk: function(t) {
            this.cookie("muted", !1), this.pp.setVolume(this._getVolume())
        },
        vmaxClk: function(t) {
            this.cookie("muted", !1), this.pp.setVolume(1)
        },
        enterFullscreenClk: function(t) {
            this.pp.setFullscreen(!0)
        },
        exitFullscreenClk: function(t) {
            this.pp.setFullscreen(!1)
        },
        loopClk: function(e) {
            this.pp.setLoop(t(e.currentTarget).hasClass("inactive") || !1), this.updateDisplay()
        },
        vmarkerClk: function(t) {
            vsliderClk(t)
        },
        openCloseClk: function(e) {
            var i = this;
            t(t(e.currentTarget).attr("class").split(/\s+/)).each(function(t, e) {
                -1 != e.indexOf("toggle") && (i.playerDom.find("." + e.substring(6)).slideToggle("slow", function() {
                    i.pp.setSize()
                }), i.controlElements.open.toggle(), i.controlElements.close.toggle())
            })
        },
        volumeBtnHover: function(t) {
            clearTimeout(this._outDelay), this.setActive(this.controlElements.volumePanel, !0)
        },
        volumeBtnOut: function(t, e) {
            var i = this;
            t.currentTarget == e.get(0) && t.relatedTarget != e.get(0) && (this._outDelay = setTimeout(function() {
                i.setActive(i.controlElements.volumePanel, !1)
            }, 100))
        },
        vsliderClk: function(e) {
            if (1 != this._vSliderAct) {
                var i = t(this.controlElements.vslider),
                    s = i.width() > i.height() ? "hor" : "vert",
                    n = "hor" == s ? i.width() : i.height(),
                    a = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX,
                    r = e.originalEvent.touches ? e.originalEvent.touches[0].pageY : e.pageY,
                    o = "hor" == s ? a - i.offset().left : r - i.offset().top,
                    l = 0;
                l = 0 > o || isNaN(o) || void 0 == o ? 0 : "hor" == s ? o / n : 1 - o / n, this.pp.setVolume(l)
            }
        },
        scrubberShowTooltip: function(t) {
            0 != this.pp.getDuration() && (clearTimeout(this._cTimer), this.setActive(this.controlElements.scrubbertip, !0))
        },
        scrubberHideTooltip: function(t) {
            this.setActive(this.controlElements.scrubbertip, !1)
        },
        scrubberdragTooltip: function(e) {
            if (0 != this.pp.getDuration()) {
                this.setActive(this.controlElements.scrubbertip, !0);
                var i = t(this.controlElements.scrubberdrag[0]),
                    s = t(this.controlElements.loaded[0]),
                    n = t(this.controlElements.scrubbertip),
                    a = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX,
                    r = e.originalEvent.touches ? e.originalEvent.touches[0].pageY : e.pageY,
                    o = a - i.offset().left - n.outerWidth() / 2,
                    l = this.pp.getDuration() / 100 * (100 * (a - i.offset().left) / i.width()),
                    h = this._clockDigits(l, "tip");
                if (this._isDVR) {
                    l = this.pp.getDuration() - l;
                    var u = new Date(1e3 * ((new Date).getTime() / 1e3 - l)),
                        u = u.getSeconds() + 60 * u.getMinutes() + 3600 * u.getHours();
                    h = this._clockDigits(u, "tip")
                }
                for (var c in this.controlElements) {
                    if ("cb" == c) break;
                    h[c] && t.each(this.controlElements[c], function() {
                        t(this).html(h[c])
                    })
                }
                o = 0 > o ? 0 : o, o = o > i.width() - n.outerWidth() ? i.width() - n.outerWidth() : o, n.css({
                    left: o + "px"
                })
            }
        },
        scrubberdragStartDragListener: function(e) {
            if (1 != this.getConfig("disallowSkip")) {
                this._sSliderAct = !0;
                var i = this,
                    s = t(this.controlElements.scrubberdrag[0]),
                    n = t(this.controlElements.loaded[0]),
                    a = 0,
                    r = Math.abs(parseInt(s.offset().left) - e.clientX),
                    o = function(t) {
                        var e = Math.abs(s.offset().left - t.clientX);
                        e = e > s.width() ? s.width() : e, e = e > n.width() ? n.width() : e, e = 0 > e ? 0 : e, e = Math.abs(e / s.width()) * i.pp.getDuration(), e > 0 && e != a && (a = e, i.pp.setPlayhead(a))
                    },
                    l = function(t) {
                        return t.stopPropagation(), t.preventDefault(), i.playerDom.unbind("mouseup.slider"), s.unbind("mousemove", h), s.unbind("mouseup", l), i._sSliderAct = !1, !1
                    },
                    h = function(t) {
                        return clearTimeout(i._cTimer), t.stopPropagation(), t.preventDefault(), o(t), !1
                    };
                this.playerDom.bind("mouseup.slider", l), s.mouseup(l), s.mousemove(h), o(e)
            }
        },
        vknobStartDragListener: function(e, i) {
            this._vSliderAct = !0;
            var s = this,
                n = this.pp.getInFullscreen() === !0 && this.controlElements.vslider.length > 1 ? 1 : 0,
                a = t(i[n]),
                r = t(this.controlElements.vslider[n]),
                o = Math.abs(parseFloat(a.position().left) - e.clientX),
                l = Math.abs(parseFloat(a.position().top) - e.clientY),
                h = 0,
                u = function(t) {
                    return s.playerDom.unbind("mouseup", u), r.unbind("mousemove", c), r.unbind("mouseup", u), a.unbind("mousemove", c), a.unbind("mouseup", u), s._vSliderAct = !1, !1
                },
                c = function(e) {
                    clearTimeout(s._cTimer);
                    var i = e.clientX - o,
                        i = i > r.width() - a.width() / 2 ? r.width() - a.width() / 2 : i,
                        i = 0 > i ? 0 : i,
                        u = e.clientY - l,
                        u = u > r.height() - a.height() / 2 ? r.height() - a.height() / 2 : u,
                        u = 0 > u ? 0 : u;
                    s.controlElements.vslider.width() > s.controlElements.vslider.height() ? (a.css("left", i + "px"), h = Math.abs(i / (r.width() - a.width() / 2)), t(s.controlElements.vmarker[n]).css("width", 100 * h + "%")) : (a.css("top", u + "px"), h = 1 - Math.abs(u / (r.height() - a.height() / 2)), t(s.controlElements.vmarker[n]).css("height", 100 * h + "%"))
                };
            this.playerDom.mouseup(u), r.mousemove(c), r.mouseup(u), a.mousemove(c), a.mouseup(u)
        },
        handleStartDragListener: function(t, e) {
            var i = this,
                s = Math.abs(parseInt(this.cb.position().left) - t.clientX),
                n = Math.abs(parseInt(this.cb.position().top) - t.clientY);
            var a = function(t) {
                return t.stopPropagation(), t.preventDefault(), i.playerDom.unbind("mouseup", a), i.playerDom.unbind("mouseout", a), i.playerDom.unbind("mousemove", r), !1
            };
            var r = function(t) {
                t.stopPropagation(), t.preventDefault(), clearTimeout(i._cTimer);
                var e = t.clientX - s;
                e = e > i.playerDom.width() - i.cb.width() ? i.playerDom.width() - i.cb.width() : e, e = 0 > e ? 0 : e, i.cb.css("left", e + "px");
                var a = t.clientY - n;
                return a = a > i.playerDom.height() - i.cb.height() ? i.playerDom.height() - i.cb.height() : a, a = 0 > a ? 0 : a, i.cb.css("top", a + "px"), !1
            };
            this.playerDom.mousemove(r), this.playerDom.mouseup(a)
        },
        _getVolume: function() {
            var t = parseFloat(this.cookie("volume") || this.getConfig("volume") || .5),
                e = this.cookie("muted") || !1;
            return this.getConfig("fixedVolume") || null == t ? this.getConfig("volume") : e ? 0 : t
        },
        _active: function(t, e) {
            var i = this.controlElements[t];
            return 1 == e ? i.addClass("active").removeClass("inactive") : i.addClass("inactive").removeClass("active"), i
        },
        _clockDigits: function(t, e) {
            (0 > t || isNaN(t) || void 0 == t) && (t = 0);
            var i = Math.floor(t / 3600),
                s = t % 3600,
                n = Math.floor(s / 60),
                a = 60 * i + n,
                r = s % 60,
                o = Math.floor(r),
                l = t,
                h = {};
            return h["min_" + e] = 10 > n ? "0" + n : n, h["min_abs_" + e] = 10 > a ? "0" + a : a, h["sec_" + e] = 10 > o ? "0" + o : o, h["sec_abs_" + e] = 10 > l ? "0" + l : l, h["hr_" + e] = 10 > i ? "0" + i : i, h
        }
    }
});
var projekktorContextmenu = function() {};
jQuery(function(t) {
    projekktorContextmenu.prototype = {
        version: "1.1.00",
        reqVer: "1.2.13",
        _dest: null,
        _items: {},
        initialize: function() {
            var e = this,
                i = this.pp.getIframeWindow() || t(window);
            this._dest = $p.utils.blockSelection(this.applyToPlayer(t("<ul/>"))), this._items.player = {
                getContextTitle: function() {
                    return e.getConfig("playerName") + " V" + e.pp.getPlayerVer()
                },
                open: function() {
                    null != e.getConfig("playerHome") && (i.get(0).location.href = e.getConfig("playerHome"), e.pp.setPause())
                }
            }, this.pp.getConfig("helpHome") && (this._items.help = {
                getContextTitle: function() {
                    return e.pp.getConfig("messages")[100]
                },
                open: function() {
                    e.popup(e.pp.getConfig("helpHome"), 400, 600)
                }
            }), this.pluginReady = !0
        },
        mousedownHandler: function(e) {
            switch (e.which) {
                case 3:
                    var i = this.pp.getDC().offset(),
                        s = e.pageY - i.top,
                        n = e.pageX - i.left;
                    n + this._dest.width() > this.pp.getDC().width() && (n = this.pp.getDC().width() - this._dest.width() - 2), s + this._dest.height() > this.pp.getDC().height() && (s = this.pp.getDC().height() - this._dest.height() - 2), this.setActive(), this._dest.css({
                        top: s + "px",
                        left: n + "px"
                    });
                    break;
                case 1:
                    try {
                        this._items[t(e.target).data("plugin")].open()
                    } catch (a) {}
                default:
                    this.setInactive()
            }
        },
        mouseleaveHandler: function() {
            this.setInactive()
        },
        eventHandler: function(t, e) {
            t.indexOf("Contextmenu") > -1 && null == this._items[e.name] && (this._items[e.name] = e)
        },
        displayReadyHandler: function() {
            var e = this,
                i = null;
            this.setInactive(), this._dest.html("");
            for (var s in this._items) {
                i = t("<span/>").data("plugin", s).html(this._items[s].getContextTitle() || s);
                try {
                    this._items[s].setContextEntry(i)
                } catch (n) {}
                t("<li/>").append(i).data("plugin", s).appendTo(this._dest)
            }
        },
        popup: function(t, e, i) {
            centeredY = window.screenY + (window.outerHeight / 2 - i / 2), centeredX = window.screenX + (window.outerWidth / 2 - e / 2), window.open(t, "projekktor", "height=" + i + ",width=" + e + ",toolbar=0,scrollbars=0,status=0,resizable=1,location=0,menuBar=0" + ",left=" + centeredX + ",top=" + centeredY).focus()
        }
    }
});
//@ sourceMap