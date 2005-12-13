
XOOPS 2.3.0-dev
Dummy readme file to compensate the lack of documentation.

Note: this thing is still in alpha stage. It can be used without problems assuming you know how to handle the few drawbacks it has. Now, if you're afraid to edit files by yourself, you may want to wait a little more.

Installation:
- Copy the content of the htdocs/ folder where it can be accessed by your server
- Copy the XOOPS and XOOPS-data folders where you want to (it's recommended to let them out of the webroot)
- Make all the folders inside 'XOOPS-data' server writable
- Burn the 2 other folders
- The actual installer hasn't been changed to handle the new vars. So you'll have to help it a little: start the installation procedure normally, until the mainfile.php generation (the page with the "writing constants"... dont worry about the one that fails). Once it's done, manually edit mainfile.php and enter the values of XOOPS_PATH and XOOPS_VAR_PATH with the physical paths of your XOOPS and XOOPS-data folders. Then click "next" in the installer to go on with the installation procedure.

!NB! By default the kernel is configured to use "development mode". You should only use this locally, as it's very slow and only here for modules devs/themes designers to work.
This mode shows the PHP error message within the page and should NEVER be used on a public server. To switch to production mode, you have to edit the XOOPS/Boot/hosts.php file and replace the last line to set the xoRunMode variable to 0 (zero).

Quick notes: 
All the new stuff will be documented, but here are a few notes for people who would like to start playing before the articles are written.
1) The old tplsets system has disappeared to be replaced by the new theme engine. Online edition of templates is not possible anymore, but will come back with a new WYSIWYG editor module (this objective is actually why the smarty delimiters have been changed).
2) The default templates delimiters are ([ ]) . The new engine tries to differentiate templates by looking at their filename: .html tpls are considered "old-school" tpls and processed before compilation so the <{ }> they contain are replaced.
NB: please tell us if you've done a module using templates with another extension than .html, so we can change the test.
3) The theme engine uses 3 templates to build a page (canvas,page,content) see the theme class comments for an explanation.
4) xoAppUrl and xoImgUrl are smarty COMPILER plugins, and can't use variables. The 1st ones generates URIs to modules pages, the 2nd one uses the theme inheritance system.
If you need to use vars, you can replace xoAppUrl by ([$xoops->url($stuff)]).
You can also replace xoImgUrl by ([$xoops->url( $xoTheme->resourcePath($stuff) )]) but its not recommended (this is resource hungry, it's actually why xoImgUrl is implemented as a compiler plugin ).
5) The new xotpl: resource handler uses full paths to templates.
v1:
([include file='xotpl:my_component#rel/path/to/file.xotpl']) get the 'path/to/file.xotpl' template of the 'my_component' object (it's a components-based system, so objects can own templates now)
v2: (not recommended, but the only way to include modules tpls right now)
([include file='xotpl:modules/stuff/tplname.xotpl'])
6) Only the code part has been taken care of. All the CSS rules for old markup have not been put back in the default theme (which will make most of the actual modules look strange). Now if you want to clean up the old messy CSS file to do that and contribute your work, you're welcome.
7) The default theme is not MSIE 5.5/6 compat yet.
