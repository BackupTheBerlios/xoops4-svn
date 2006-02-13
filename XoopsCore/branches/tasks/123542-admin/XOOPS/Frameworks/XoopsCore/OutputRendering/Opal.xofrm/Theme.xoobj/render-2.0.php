<?php

	$this->canvasTemplate	= './theme.html';
	$this->pageTemplate		= '';
	
	
	// Assign metas as 'xoops_meta_xxxx'
	foreach ( $this->metas['meta'] as $name => $content ) {
		$this->template->assign( 'xoops_meta_' . $name, $content );
	}
	// Assign xoops_js
	$this->template->assign(
		'xoops_js',
		'//--></script><script type="text/javascript" src="' .
		$this->metas['script']['include/xoops.js']['src'] . '"></script><script type="text/javascript"><!--'
    );

	// Assign blocks vars (old-style)
	$blocks =& $this->template->get_template_vars( 'xoBlocks' );

	if ( !@empty( $blocks['canvas_left'] ) ) {
		$this->template->assign_by_ref( 'xoops_lblocks', $blocks['canvas_left'] );
		$this->template->assign( 'xoops_showlblock', 1 );
	}
	if ( !@empty( $blocks['canvas_right'] ) ) {
		$this->template->assign_by_ref( 'xoops_rblocks', $blocks['canvas_right'] );
		$this->template->assign( 'xoops_showrblock', 1 );
	}
	if ( !@empty( $blocks['page_top'] ) ) {
		$this->template->assign_by_ref( 'xoops_ccblocks', $blocks['page_top'] );
		$this->template->assign( 'xoops_showcblock', 1 );
	}
	if ( !@empty( $blocks['page_topleft'] ) ) {
		$this->template->assign_by_ref( 'xoops_clblocks', $blocks['page_topleft'] );
		$this->template->assign( 'xoops_showcblock', 1 );
	}
	if ( !@empty( $blocks['page_topright'] ) ) {
		$this->template->assign_by_ref( 'xoops_crblocks', $blocks['page_topright'] );
		$this->template->assign( 'xoops_showcblock', 1 );
	}

	// XOOPS 2.0 themes need the content to be rendered and assigned to the var 'xoops_contents'
	ob_start();
	$this->renderZone( 'content' );
	$this->content .= ob_get_contents();
	ob_end_clean();
	
	$this->template->assign( 'xoops_contents', $this->content );



?>