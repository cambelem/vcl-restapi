# Notes Considering Modification of Webpage Themes

The themes for the website are configured in the vcl-theme/themes directory.  'default' was the theme that came with the initial installation of the VCL software.  'newdropdownmenus' is the theme that has been added and modified, displaying the website as you now see it.

The VCL website is rendered by the browser when running index.php, found in the vcl-theme directory.  Each page (excluding the first page, see next section) follows the template designed by page.php in the *theme* directory being used.  The content for the page is decided by the $actionFunction variable, which is set in index.php like so:
```
$actionFunction = $actions['mode'][$mode];
```
Another variable, $arg, is set in index.php like so:
```
$arg = $actions['args'][$mode];
```
Then, when `$actionFunction($arg)` is called, it references the proper function (contained in either utils.php or authentication.php, both within vcl-theme/.ht-inc directory).  Each page is therefore rendered based on the string value of `$actionFunction`, which is dependent on the `$actions` array.  The `$actions` array is populated in vcl-theme/.ht-inc/states.php.



## Theme for the First Page:

The first time index.php is run, the `$mode` variable is set to 'selectauth' and the `$args` variable is null.  The URL of the first page should read *https&#58;localhost/vcl/index.php?mode=selectauth*.

When `$mode = 'selectauth'` and `$args = null`, index.php reads `$actionFunction($arg)` as a function call for `selectAuth()`.

`selectAuth()` is found inside vcl-theme/.ht-inc/authentication.php and this is the function that renders the first page of VCL!  This function was modified so that the lines
```
$HTMLheader = getHeader(0);
print $HTMLheader;
$printedHTMLheader = 1;
```
and
```
print getFooter();
```
are no longer called (they've been commented out), but have been replaced by
```
readfile("themes/newdropdownmenus/selectAuthHeader.html");
```
and
```
readfile("themes/newdropdownmenus/selectAuthFooter.html");
```
respectively.  This is why the first page (mode=selectauth page) behaves differently than the others.  

These html files are called to wrap the content of the first page (acting as a template), and they are both found inside vcl-theme/themes/newdropdownmenus.  The header file (selectAuthHeader.html) contains the references to the javascript files that run the chatbot, and allow bootstrap styling for the page.

The main stylesheet for the page is vcl-theme/themes/newdropdownmenus/css/firstPage.css


## Theme for the Interior of the Website:

Details to come...
