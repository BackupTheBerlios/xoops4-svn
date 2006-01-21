<?php
/**
* xoops_http response codes definition file
*
* See the enclosed file LICENSE for licensing information.
* If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
*
* @copyright	The XOOPS project http://www.xoops.org/
* @license		http://www.fsf.org/copyleft/gpl.html GNU public license
* @author		Skalpa Keo <skalpa@xoops.org>
* @since		2.3.0
* @package		xoops_http
* @version		$Id$
*/

define( 'HTTP_STATUS_OK',			200 );		// The client's request was successful, and the server's response contains the requested data.
define( 'HTTP_STATUS_CREATED',		201 );		// This status code is used whenever a new URL is created. With this result code, the Location header is given by the server to specify where the new data was placed.
define( 'HTTP_STATUS_ACCEPTED',		202 );		// The request was accepted but not immediately acted upon. More information about the transaction may be given in the entity-body of the server's response. There is no guarantee that the server will actually honor the request, even though it may seem like a legitimate request at the time of acceptance.
define( 'HTTP_STATUS_NONAUTH',		203 );		// The information in the entity header is from a local or third-party copy, not from the original server.
define( 'HTTP_STATUS_NOCONTENT',	204 );		// A status code and header are given in the response, but there is no entity-body in the reply. Browsers should not update their document view upon receiving this response. This is a useful code for CGI programs to use when they accept data from a form but want the browser view to stay at the form.
define( 'HTTP_STATUS_RESETCONTENT',	205 );		// The browser should clear the form used for this transaction for additional input. For data-entry CGI applications.
define( 'HTTP_STATUS_PARTCONTENT',	206 );		// The server is returning partial data of the size requested. Used in response to a request specifying a Range header. The server must specify the range included in the response with the Content-Range header.

define( 'HTTP_STATUS_MOVED',		301 );		// The requested URL is no longer used by the server, and the operation specified in the request was not performed. The new location for the requested document is specified in the Location header. All future requests for the document should use the new URL.
define( 'HTTP_STATUS_SEEOTHER',		303 );		// The requested URL can be found at a different URL (specified in the Location header) and should be retrieved by a GET on that resource.
define( 'HTTP_STATUS_NOTMODIFIED',	304 );		// This is the response code to an If-Modified-Since or If-None-Match header, where the URL has not been modified since the specified date. The entity-body is not sent, and the client should use its own local copy.
define( 'HTTP_STATUS_USEPROXY',		305 );		// The requested URL must be accessed through the proxy in the Location header.
define( 'HTTP_STATUS_MOVEDTEMP',	307 );		// The requested URL has moved, but only temporarily. The Location header specifies the new location, but no information is given about the validity of the redirect in the future. The client should revisit the original URL in the future.

define( 'HTTP_STATUS_BADREQUEST',	400 );		//This response code indicates that the server detected a syntax error in the client's request.
define( 'HTTP_STATUS_UNAUTHORIZED',	401 );		// Given along with the WWW-Authenticate header to indicate that the request lacked proper authorization, and the client should supply proper authorization when requesting this URL again. See the description of the Authorization header for more information on how authorization works in HTTP.
define( 'HTTP_STATUS_NEEDPAYMENT',	402 );		// This code is not yet implemented in HTTP.
define( 'HTTP_STATUS_FORBIDDEN',	403 );		// The request was denied for a reason the server does not want to (or has no means to) indicate to the client.
define( 'HTTP_STATUS_NOTFOUND',		404 );		// The document at the specified URL does not exist.
define( 'HTTP_STATUS_BADMETHOD',	405 );		// This code is given with the Allow header and indicates that the method used by the client is not supported for this URL.
define( 'HTTP_STATUS_NOTACCEPTABLE',406 );		// The URL specified by the client exists, but not in a format preferred by the client. Along with this code, the server provides the Content-Language, Content-Encoding, and Content-type headers.
define( 'HTTP_STATUS_TIMEOUT',		408 );		// This response code means the client did not produce a full request within some predetermined time (usually specified in the server's configuration), and the server is disconnecting the network connection.
define( 'HTTP_STATUS_CONFLICT',		409 );		// This code indicates that the request conflicts with another request or with the server's configuration. Information about the conflict should be returned in the data portion of the reply. For example, this response code could be given when a client's request would cause integrity problems in a database.
define( 'HTTP_STATUS_GONE',			410 );		// This code indicates that the requested URL no longer exists and has been permanently removed from the server.

define( 'HTTP_STATUS_SERVERERROR',		500 );		// A part of the server (for example, a CGI program) has crashed or encountered a configuration error.
define( 'HTTP_STATUS_NOTIMPLEMENTED',	501 );		// The client requested an action that cannot be performed by the server.
define( 'HTTP_STATUS_SERVICEUNAVAIL',	503 );		// The service is temporarily unavailable, but should be restored in the future. If the server knows when it will be available again, a Retry-After header may also be supplied.




















?>