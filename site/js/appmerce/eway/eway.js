var Eway = Class.create({
    initialize : function(options) {
        this.options = {
            path : "JSONP/v3/process",
            timeout : 9e4,
        };
        Object.extend(this.options, options || { });

        this.processing = false;
        this.head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
        this.script = null;
        this.key = null;
    },

    cleanup : function() {
        clearTimeout(this.timeout);
        window[this.key] = Prototype.emptyFunction;
        this.head.removeChild(this.script);
    },

    doPay : function(u, q, c) {
        if (this.processing)
            return;
        this.processing = true;

        this.key = this.getKey();
        var u = this.parseUrl(u, this.key, q);
        window[this.key] = function(json) {
            this.cleanup();
            c(json);
        }.bind(this);

        this.script = new Element('script', {
            type : 'text/javascript',
            src : u
        });
        this.head.appendChild(this.script);

        this.timeout = setTimeout( function() {
            this.cleanup();
            c({});
        }.bind(this), this.options.timeout);
    },

    getKey : function() {
        var f = (new Date).getTime();
        return "eWAY" + (++f).toString(36);
    },

    parseUrl : function(a, k, q) {
        var b = ["source", "protocol", "authority", "domain", "port", "path", "directoryPath", "fileName", "query", "anchor"];
        var c = (new RegExp("^(?:([^:/?#.]+):)?(?://)?(([^:/?#]*)(?::(\\d*))?)((/(?:[^?#](?![^?#/]*\\.[^?#/.]+(?:[\\?#]|$)))*/?)?([^?#/]*))?(?:\\?([^#]*))?(?:#(.*))?")).exec(a), d = {};
        for (var e = 0; e < 10; e++)
            d[b[e]] = c[e] ? c[e] : "";
        if (d.directoryPath.length > 0)
            d.directoryPath = d.directoryPath.replace(/\/?$/, "/");
        return d.protocol + "://" + d.authority + "/" + this.options.path + "?ewayjsonp=" + k + "&" + q;
    }
}); 