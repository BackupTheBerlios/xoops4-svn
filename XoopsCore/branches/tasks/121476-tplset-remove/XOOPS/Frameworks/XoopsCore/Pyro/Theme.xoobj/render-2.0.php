<?php

	$this->canvasTemplate	= './theme.html';
	$this->pageTemplate		= '';
	
	// XOOPS 2.0 themes need the content to be rendered and assigned to the var 'xoops_contents'
	ob_start();
	$this->renderZone( 'content' );
	$this->content .= ob_get_contents();
	ob_end_clean();
	
	$this->template->assign( 'xoops_contents', $this->content );



?>