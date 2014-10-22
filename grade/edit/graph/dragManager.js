// dragObject contains data for currently dragged element
var dragObject = {
    selectedObject : null,
    offsetX : 0,
    offsetY : 0,
    zone: {left:120, top:120, right:120, bottom:400},
    

// invoked onmousedown
engageDrag : function(evt) {
    evt = (evt) ? evt : window.event;
    dragObject.selectedObject = (evt.target) ? evt.target : evt.srcElement;
    var target = (evt.target) ? evt.target : evt.srcElement;
    var dragContainer = target;
    // in case event target is nested in draggable container
    while (target.className != "draggable" && target.parentNode) {
        target = dragContainer = target.parentNode;
    }
    if (dragContainer) {
        dragObject.selectedObject = dragContainer;
        DHTMLAPI.setZIndex(dragContainer, 100);
        dragObject.setOffsets(evt, dragContainer);
        dragObject.setDragEvents( );
        evt.cancelBubble = true;
        evt.returnValue = false;
        if (evt.stopPropagation) {
            evt.stopPropagation( );
            evt.preventDefault( );
        }
    }
    return false;
},

// calculate offset of mousedown within draggable element
setOffsets : function (evt, dragContainer) {
    if (evt.pageX) {
        dragObject.offsetX = evt.pageX - ((dragContainer.offsetLeft) ? dragContainer.offsetLeft : dragContainer.left);
        dragObject.offsetY = evt.pageY - ((dragContainer.offsetTop) ? dragContainer.offsetTop : dragContainer.top);
    } else if (evt.offsetX || evt.offsetY) {
        dragObject.offsetX = evt.offsetX - ((evt.offsetX < -2) ? 0 : document.body.scrollLeft);
        dragObject.offsetY = evt.offsetY - ((evt.offsetY < -2) ? 0 : document.body.scrollTop);
    }
},
// invoked onmousemove
dragIt : function (evt) {
       evt = (evt) ? evt : window.event;
       var obj = dragObject;
       var posX = (obj.selectedObject.offsetLeft) ? obj.selectedObject.offsetLeft : obj.selectedObject.left;
       var posY = (evt.pageX) ? (evt.pageY - obj.offsetY) : (evt.clientY - obj.offsetY);
//       if(insidePlot(posY)) {
//           DHTMLAPI.moveTo(obj.selectedObject, posX,posY);
//           updateMarkers(obj.selectedObject, posY);
//       }
if(insidePlot(posY)) {
       	   
       	   var limitPos = getMarkerLimit(obj.selectedObject); // Added By Junaid Malik
       	   console.log('Current is ' + posY); // Added By Junaid Malik
       	   console.log('Limiting is '+limitPos); // Added By Junaid Malik
           if (posY < limitPos[0] && posY > limitPos[1]) { // Added By Junaid Malik
           	DHTMLAPI.moveTo(obj.selectedObject, posX,posY); 
           	updateMarkers(obj.selectedObject, posY); 
           }
           
           	
           
           
       }
       evt.cancelBubble = true;
       evt.returnValue = false;


},

/*dragIt : function (evt) {
       evt = (evt) ? evt : window.event;
       var obj = dragObject;
       if (evt.pageX) {
           DHTMLAPI.moveTo(obj.selectedObject, (evt.pageX - obj.offsetX),(evt.pageY - obj.offsetY));
       } else if (evt.clientX || evt.clientY) {
           DHTMLAPI.moveTo(obj.selectedObject, (evt.clientX - obj.offsetX),(evt.clientY - obj.offsetY));
       }
       evt.cancelBubble = true;
       evt.returnValue = false;
},*/

/*dragIt : function (evt) {
    evt = (evt) ? evt : window.event;
    var x, y, width, height;
    var obj = dragObject;
    if (evt.pageX) {
        x = evt.pageX - obj.offsetX;
        y = evt.pageY - obj.offsetY;
    } else if (evt.clientX || evt.clientY) {
        x = evt.clientX - obj.offsetX;
        y = evt.clientY - obj.offsetY;
    }
    width = DHTMLAPI.getElementWidth(obj.selectedObj);
    height = DHTMLAPI.getElementHeight(obj.selectedObj);
    x = (x < obj.zone.left) ? obj.zone.left :((x + width > obj.zone.right) ? obj.zone.right - width : x);
    y = (y < obj.zone.top) ? obj.zone.top :((y + height > obj.zone.bottom) ? obj.zone.bottom - height : y);
    DHTMLAPI.moveTo(obj.selectedObject, x, y);
    evt.cancelBubble = true;
    evt.returnValue = false;
},*/

// invoked onmouseup
releaseDrag : function (evt) {
    DHTMLAPI.setZIndex(dragObject.selectedObject, 0);
    dragObject.clearDragEvents( );
    dragObject.selectedObject = null;
},

// set temporary events
setDragEvents : function ( ) {
    addEvent(document, "mousemove", dragObject.dragIt, false);
    addEvent(document, "mouseup", dragObject.releaseDrag, false);
},

// remove temporary events
clearDragEvents : function ( ) {
    removeEvent(document, "mousemove", dragObject.dragIt, false);
    removeEvent(document, "mouseup", dragObject.releaseDrag, false);
},

// initialize, assigning mousedown events to all
// elements with class="draggable" attributes
init : function ( ) {
    var elems = [];
    if (document.all) {
        // IE 5 & 5.5 don't know wildcard for getElementsByTagName
        // so use document.body.all, which lets IE 4 work OK
        elems = document.body.all;
    } else if (document.body && document.body.getElementsByTagName) {
        elems = document.body.getElementsByTagName("*");
    }
    for (var i = 0; i < elems.length; i++) {
        if (elems[i].className.match(/draggable/)) {
            addEvent(elems[i], "mousedown", dragObject.engageDrag, false);
        }
    }
}

};
// set onload event via eventsManager.js
addOnLoadEvent(dragObject.init);