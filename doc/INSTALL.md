Installing lisoperators extension
==============================

1. Copy the package into the `extension' directory in the root of your
   eZ Publish installation.

2. Enable the extension in eZ Publish. To do this edit
   site.ini.append(.php) in the folder root_of_ezpublish/settings/override. If this
   file does not exist; create it. Locate (or add) the block
   [ExtensionSettings] and add the line:

   ActiveExtensions[]=listripiccio

   If you run several sites using only one distribution and only some of the
   sites should use the extension, make the changes in the override file of
   that siteaccess.
   E.g root_of_ezpublish/settings/siteaccess/news/site.ini.append(.php)
   But instead of using ActiveExtensions you must add these lines instead:

   [ExtensionSettings]
   ActiveAccessExtensions[]=listripiccio

   Alternatively you can also enable the extension over the backend.
   Go to setup -> extensions -> tick the checkbox -> then press refresh button

3. Run the php script from commandline:

   $ php bin/php/ezpgenerateautoloads.php --extension

   Which is need to build an array of classes that are used by the autoload system
   in PHP to load classes. You will need eZ Components availlable to run this script.

   Alternatively you can also regenerate autoloads over the backend.
   Go to setup -> extensions -> then press regenerate autoloads button