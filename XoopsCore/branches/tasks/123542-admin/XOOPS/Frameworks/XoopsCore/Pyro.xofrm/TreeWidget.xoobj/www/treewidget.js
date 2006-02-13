
function xoops_pyro_TreeWidget( elementId, openClass, closedClass ) {
	var me=this;
	
	this.elementId = elementId;
	this.targetElement = document.getElementById( elementId );
	this.openClass = openClass ? openClass : "openNode";
	this.closedClass = closedClass ? closedClass : "closedNode";
	this.enabled = ( this.targetElement && document.createElement ) ? true : false;
	/**
	* Expand all the nodes of this tree
	*/
	this.expandAll = function () {
		if ( !this.enabled )	return;
		return this.switchNode( this.targetElement, true );
	}
	/**
	* Collapse all the nodes of this tree
	*/
	this.collapseAll = function () {
		if ( !this.enabled )	return;
		return this.switchNode( this.targetElement, false );
	}
	/**
	* Expand nodes ensuring the specified item is visible
	*/
	this.revealItem = function ( targetId ) {
		if ( !this.enabled )	return;
		var target;
		if ( this.switchNode( this.targetElement, true, targetId ) )
			if ( target = document.getElementById( targetId ) )
				target.scrollIntoView( false );
	}
	
	this.switchNode = function ( elt, state, targetId ) {
		if ( !elt.childNodes || !elt.childNodes.length )	return false;
		var nItem, nSub, hasSub = false;
		for ( nItem = 0; nItem < elt.childNodes.length; nItem++ ) {
			var item = elt.childNodes[nItem];
			if ( targetId && item.id == targetId )	return true;
			if ( item.nodeName && item.nodeName.toLowerCase() == 'li' ) {
				for ( nSub = 0; nSub < item.childNodes.length; nSub++ ) {
					var subItem = item.childNodes[nSub];
					if ( subItem.nodeName && subItem.nodeName.toLowerCase() == 'ul' ) {
						hasSub = true;
						if ( targetId && this.switchNode( elt, state, targetId ) ) {
							item.className = this[ state ? 'openClass' : 'closeClass' ];
							return true;
						}
					}
				}
				if ( hasSub && targetId ) {
					item.className = this[ state ? 'openClass' : 'closeClass' ];
				}
			}
		}
	}
	
	this.handleClick = function ( evt ) {
		if ( !evt ) evt=window.event;
		var tgt = evt.target ? evt.target : evt.srcElement;
		if ( tgt.nodeType == 3 ) tgt.nodeType = tgt.parentNode;
		
		if ( tgt != this ) {
			return true;
		} else {
			if ( this.className.indexOf( me.openClass ) != -1 ) {
				xoops.replaceElementClass( this, me.openClass, me.closedClass );
			} else {
				xoops.replaceElementClass( this, me.closedClass, me.openClass );
			}
			evt.cancelBubble = true;
			if ( evt.stopPropagation )	evt.stopPropagation();
			return false;
		}
	}
	
	this.initializeTree = function ( elt ) {
		if ( !this.enabled )	return;
		if ( !elt.childNodes || !elt.childNodes.length )	return false;
		var nItem, nSub, hasSub = false;
		for ( nItem = 0; nItem < elt.childNodes.length; nItem++ ) {
			var item = elt.childNodes[nItem];
			if ( item.nodeName && item.nodeName.toLowerCase() == 'li' ) {
				hasSub = false;
				for ( nSub = 0; nSub < item.childNodes.length; nSub++ ) {
					var subItem = item.childNodes[nSub];
					if ( subItem.nodeName && subItem.nodeName.toLowerCase() == 'ul' ) {
						hasSub = true;
						this.initializeTree( subItem );
					}
				}
				if ( !hasSub ) {
					item.className = 'treeItem';
					item.onclick = function () { return true; }
				} else {
					if ( !item.className ) {
						item.className = this.openClass;
					}
					item.className += ' treeNode';
					item.onclick = this.handleClick;
				}
			}
		}
	}
	this.targetElement.className = 'pyro_TreeWidget';
	this.initializeTree( this.targetElement );
}
