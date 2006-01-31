<?php
/**
* xoops_pyro_TreeWidget component class file
*
* @copyright	The Xoops project http://www.xoops.org/
* @license      http://www.fsf.org/copyleft/gpl.html GNU public license
* @package      xoops_pyro
* @subpackage   xoops_pyro_TreeWidget
* @author       Skalpa Keo <skalpa@xoops.org>
* @since        2.3.0
* @version		$Id$
*/

XOS::import( 'xoops_pyro_Widget' );

/**
* xoops_pyro_TreeWidget
*
* The tree widget renders hierarchical data.
* The default HTML output will show an expandable/collapsable tree made of UL/LI elements.
* Its dynamic behavior makes strong use of CSS, and only requires minimal scripting (mainly to
* assign appropriate CSS classes to elements during initialization).
* 
* The data is provided to the tree as an array:
* <code>
* $data = array(
* 	'item1' => array(
* 		'name' => 'Item 1',
* 	),
* 	'item2' => array(
* 		'name' => 'Item 2 with sub',
* 		'link' => 'item2.html',
* 		'children' => array(
* 			'i2sub1' => array(
* 				'name' => 'SubItem 1',
* 				'link' => 'subitem1.html',
* 			),
* 			'i2sub2' => array(
* 				'name' => 'SubItem 2',
* 				'link' => 'subitem2.html',
* 			),
* 		),
* 	),
* );
* $widget->treeData = $data;
* echo $widget->render();
* </code>
* 
* @author 		Skalpa Keo
* @package		xoops_pyro
* @subpackage	xoops_pyro_TreeWidget
* @since        2.3.0
*/
class xoops_pyro_TreeWidget extends xoops_pyro_Widget {
	
	var $stylesheet = 'www/default.css';
	var $javascript = 'www/treewidget.js';
	
	/**
	 * The tree nodes data
	 *
	 * The tree data is an array of nodes. Each node must be an hash containing a 'name' element, and optionally
	 * a 'link' element (if it links to another location) or a 'children' element containing this node
	 * subnodes.
	 * 
	 * @var array
	 */	
	var $treeData = array();
	
	function xoInit( $options = array() ) {
		array_unshift( $this->elementClasses, 'pyro_TreeWidget' );
		return true;	
	}
	
	function render() {
		$str = $this->renderOpeningTag( 'ul' );
		foreach ( $this->treeData as $node ) {
			$str .= $this->renderNode( $node );
		}
		$str .= '</ul>';
		$str .= "\n<script type='text/javascript'>\n";
		$str .= 'var ' . str_replace( '-', '', $this->elementId ) . ' = new xoops_pyro_TreeWidget("' . $this->elementId . '");';
		$str .= "\n</script>\n";
		return $str;
	}
	
	/**
	 * Internal method used to render a single node
	 * @param array $node
	 * @return string
	 */
	function renderNode( $node ) {
		if ( !isset( $node['link'] ) ) {
			$str = '<li>' . htmlspecialchars( $node['name'] );
		} else {
			$str = '<li><a href="' . htmlspecialchars( $node['link'] ) . '">' . htmlspecialchars( $node['name'] ) . '</a>';
		}
		if ( !@empty( $node['children'] ) ) {
			$str .= "\n<ul>\n";
			foreach ( $node['children'] as $child ) {
				$str .= $this->renderNode( $child );
			}
			$str .= "\n</ul>\n";
		}
		$str .= "</li>\n";
		return $str;
	}
	
}


?>