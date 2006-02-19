
function XoopsUtility() {
	this.url = '';
	
	
	this.addElementClass = function (  elt, cls ) {
		var current = " " + elt.className + " ";
		if ( current.indexOf( " " + cls + " " ) == -1 ) {
			elt.className += elt.className ? ( " " + cls ) : cls;
		}
	}
	this.removeElementClass = function ( elt, cls ) {
		var current = " " + elt.className + " ";
		var nClass = current.replace( new RegExp( " " + cls + " " ), " " );
		elt.className = nClass.substr( 1, nClass.length - 2 );
	}
	this.replaceElementClass = function( elt, cls1, cls2 ) {
		var current = " " + elt.className + " ";
		var nClass = current.replace( new RegExp( " " + cls1 + " " ), " " + cls2 + " " );
		elt.className = nClass.substr( 1, nClass.length - 2 );
	}

}

var xoops = new XoopsUtility();


	function xoHideRedirectMessage() {
		var msg = document.getElementById( "xo-redirect-message" );
		var dec = .05;
		if (!msg) return;
		if ( msg.style.opacity === '' ) {
			if (document.defaultView) {
				msg.style.opacity = document.defaultView.getComputedStyle(msg, "").getPropertyValue("opacity") - dec;
			} else msg.style.opacity = 1-dec;
		} else msg.style.opacity -= dec;
		if ( msg.style.opacity ) {
			window.setTimeout( "xoHideRedirectMessage()", 75 );
		}
	}
