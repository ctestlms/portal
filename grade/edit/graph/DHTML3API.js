// DHTML3API.js custom API for cross-platform
// object positioning by Danny Goodman (http://www.dannyg.com).
// Release 3.0. Supports NN4, IE, and W3C DOMs.
var DHTMLAPI = {
    browserClass : new Object( ),
    
    init : function ( ) {
        this.browserClass.isCSS = ((document.body && document.body.style) ? true : false);
        this.browserClass.isW3C = ((this.browserClass.isCSS && document.getElementById) ? true : false),
        this.browserClass.isIE4 = ((this.browserClass.isCSS && document.all) ? true : false),
        this.browserClass.isNN4 = ((document.layers) ? true : false),
        this.browserClass.isIECSSCompat = ((document.compatMode && document.compatMode.indexOf("CSS1") >= 0) ? true : false)
    },
    
    // Seek nested NN4 layer from string name
    seekLayer : function (doc, name) {
        var elem;
        for (var i = 0; i < doc.layers.length; i++) {
            if (doc.layers[i].name == name) {
                elem = doc.layers[i];
                break;
            }
            // dive into nested layers if necessary
            if (doc.layers[i].document.layers.length > 0) {
                elem = this.seekLayer(doc.layers[i].document, name);
                if (elem) {
                    break;
                }
            }
        }
        return elem;
    },
    
    // Convert element name string or object reference
    // into a valid element object reference
    getRawObject : function (elemRef) {
        var elem;
        if (typeof elemRef == "string") {
            if (this.browserClass.isW3C) {
                elem = document.getElementById(elemRef);
            } else if (this.browserClass.isIE4) {
                elem = document.all(elemRef);
            } else if (this.browserClass.isNN4) {
                elem = this.seekLayer(document, elemRef);
            }
        } else {
            // pass through object reference
            elem = elemRef;
        }
        return elem;
    },
    
    // Convert element name string or object reference
    // into a valid style (or NN4 layer) object reference
    getStyleObject : function (elemRef) {
        var elem = this.getRawObject(elemRef);
        if (elem && this.browserClass.isCSS) {
            elem = elem.style;
        }
        return elem;
    },
    
    // Position an element at a specific pixel coordinate
    moveTo : function (elemRef, x, y) {
        var elem = this.getStyleObject(elemRef);
        if (elem) {
            if (this.browserClass.isCSS) {
                // equalize incorrect numeric value type
                var units = (typeof elem.left == "string") ? "px" : 0;
                elem.left = x + units;
                elem.top = y + units;
            } else if (this.browserClass.isNN4) {
                elem.moveTo(x,y);
            }
        }
    },

    // Move an element by x and/or y pixels
    moveBy : function (elemRef, deltaX, deltaY) {
        var elem = this.getStyleObject(elemRef);
        if (elem) {
            if (this.browserClass.isCSS) {
                // equalize incorrect numeric value type
                var units = (typeof elem.left == "string") ? "px" : 0;
                if (!isNaN(this.getElementLeft(elemRef))) {
                    elem.left = this.getElementLeft(elemRef) + deltaX + units;
                    elem.top = this.getElementTop(elemRef) + deltaY + units;
                }
            } else if (this.browserClass.isNN4) {
                elem.moveBy(deltaX, deltaY);
            }
        }
    },

    // Set the z-order of an object
    setZIndex : function (obj, zOrder) {
        var elem = this.getStyleObject(obj);
        if (elem) {
            elem.zIndex = zOrder;
        }
    },
    
    // Set the background color of an object
    setBGColor : function (obj, color) {
        var elem = this.getStyleObject(obj);
        if (elem) {
            if (this.browserClass.isCSS) {
                elem.backgroundColor = color;
            } else if (this.browserClass.isNN4) {
                elem.bgColor = color;
            }
        }
    },
    
    // Set the visibility of an object to visible
    show : function (obj) {
        var elem = this.getStyleObject(obj);
        if (elem) {
            elem.visibility = "visible";
        }
    },
    
    // Set the visibility of an object to hidden
    hide : function (obj) {
        var elem = this.getStyleObject(obj);
        if (elem) {
            elem.visibility = "hidden";
        }
    },
    
    // return computed value for an element's style property
    getComputedStyle : function (elemRef, CSSStyleProp) {
        var elem = this.getRawObject(elemRef);
        var styleValue, camel;
        if (elem) {
            if (document.defaultView && document.defaultView.getComputedStyle) {
                // W3C DOM version
                var compStyle = document.defaultView.getComputedStyle(elem, "");
                styleValue = compStyle.getPropertyValue(CSSStyleProp);
            } else if (elem.currentStyle) {
                // make IE style property camelCase name from CSS version
                var IEStyleProp = CSSStyleProp;
                var re = /-\D/;
                while (re.test(IEStyleProp)) {
                    camel = IEStyleProp.match(re)[0].charAt(1).toUpperCase( );
                    IEStyleProp = IEStyleProp.replace(re, camel);
                }
                styleValue = elem.currentStyle[IEStyleProp];
            }
        }
        return (styleValue) ? styleValue : null;
    },
    
    // Retrieve the x coordinate of a positionable object
    getElementLeft : function (elemRef) {
        var elem = this.getRawObject(elemRef);
        var result = null;
        if (this.browserClass.isCSS || this.browserClass.isW3C) {
            result = parseInt(this.getComputedStyle(elem, "left"));
        } else if (this.browserClass.isNN4) {
            result = elem.left;
        }
        return result;
    },
    
    // Retrieve the y coordinate of a positionable object
    getElementTop : function (elemRef) {
        var elem = this.getRawObject(elemRef);
        var result = null;
        if (this.browserClass.isCSS || this.browserClass.isW3C) {
            result = parseInt(this.getComputedStyle(elem, "top"));
        } else if (this.browserClass.isNN4) {
            result = elem.top;
        }
        return result;
    },
    
    // Retrieve the rendered width of an element
    getElementWidth : function (elemRef) {
        var result = null;
        var elem = this.getRawObject(elemRef);
        if (elem) {
            if (elem.offsetWidth) {
                if (elem.scrollWidth && (elem.offsetWidth != elem.scrollWidth)) {
                    result = elem.scrollWidth;
                } else {
                    result = elem.offsetWidth;
                }
            } else if (elem.clip && elem.clip.width) {
                // Netscape 4 positioned elements
                result = elem.clip.width;
            }
        }
        return result;
    },
    
    // Retrieve the rendered height of an element
    getElementHeight : function (elemRef) {
        var result = null;
        var elem = this.getRawObject(elemRef);
        if (elem) {
            if (elem.offsetHeight) {
                result = elem.offsetHeight;
            } else if (elem.clip && elem.clip.height) {
                result = elem.clip.height;
            }
        }
        return result;
    },
    
    // Return the available content width space in browser window
    getInsideWindowWidth : function ( ) {
        if (window.innerWidth) {
            return window.innerWidth;
        } else if (this.browserClass.isIECSSCompat) {
            // measure the html element's clientWidth
            return document.body.parentElement.clientWidth;
        } else if (document.body && document.body.clientWidth) {
            return document.body.clientWidth;
        }
        return null;
    },
    
    // Return the available content height space in browser window
    getInsideWindowHeight : function ( ) {
        if (window.innerHeight) {
            return window.innerHeight;
        } else {
            if (document.body.clientHeight != document.body.parentNode.clientHeight) {
                // measure the html element's clientHeight
                return document.body.parentNode.clientHeight;
            } else {
                return document.body.clientHeight;
            }
        }
        return null;
    }
}

addOnLoadEvent(function() {DHTMLAPI.init()});
