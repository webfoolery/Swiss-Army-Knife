# Joomla! Swiss Army Knife plugin
This plugin is intended to be a one stop shop for tweaks and functionality inside Joomla CMS.

* **Replace anywhere:** When enabled you can create a list of key/value pairs that callwill be replaced throughout the website dynamically on page load. There are also a few predefined ones, including {swiss.username}, {swiss.name}, {swiss.email}, {swiss.lastvisitdate}, {swiss.today} that would be replaced with user related data when displayed.

* **Cookie notice:** When enabled this will display info relating to the EU Cookie Directive, namely alerting users of the site that you use cookies. Once they accept the message will be gone from the screen. There are some confirguration options available.

* **Enable auto-login:** Allows users to automatically log in after registering.

* **Insert code:** When enabled this will allow you to add CSS, javascript, metatags or HTML to the final page. Code can be added to the `<head>` or `<body>` section and you can also either position the code after the opening tag of before the closing tag. You can enter your CSS/Javascript in the plugin or add links to an external code source/s

* **System message after login:** Display a system message to users after a successful login

* **Login redirect:** Choose a page to redirect users to after a successful login. Can be assigned to specific user groups.

* **Logout redirect:** Choose a page to redirect users to after a successful logout. Can be assigned to specific user groups.

* **Custom home page for registered users:** Select a page from the menu structures to set as a default home page, even over-riding the Joomla! default home. Different user groups can be assigned different pages.

* **Generator metatag fixer:** I dislike that Joomla! adds its own generator metatag. With this you can removeit or set your own choice of names in the tag.

* **Take site offline:** Using this you can take your site offline to the public by either entering a redirect URL or entering HTML that you would like to sisplay instead of your website. You can whitelist IP address/es to allow access for anybody that you want to have access to the website while it's offline

* **One click logout:** By default when a user click a logout button Joomla! will redirect to a logout page where the user clicks another logout button. When enabled option this will detect those first logout clicks and immediately log the user out without requiring a second click, and if desired you can create your own links to create a logout option for a user with `index.php?option=com_users&task=user.logout`.

* **WIP - Debugging output** When enabled you can (will) be able to see queries, memory usage and page profiling.

* **WIP - Keep-alive/session end alert** When enabled the default session time of Joomla! will be negated as a small XHR reuest will be sent to the server to renew the user session automatically. This could be handy for users working on large content (eg. articles) that don't save frequently enough to avoid being automatically logged out of the system. The session end alert is intended to be linke the banking websites, ie. a message will pop up to say `'You'll be logged out in 10 seconds'` but this is still WIP.

* **Force enable admin link** In newer Joomla! versions (3+ I think) the icon link to the `/administrator` page in the top left of the administrator area becomes disabled when you are inside an extension or setting. This will allow that to always be active, so if you need another admin page It's simple to middle click it to open another one.

## Changelog  

**2024-02-25**
Version update 4.1.0
Fixes missing language strings on backend. Identified cause of long standing issue where all language strings in the ini file had to be prepended with COM_SOMETHING and suffixed with an extra _FIELD_LABEL. This was driving me mad! Turns out I was using a language string in the name of the fieldsets in the XML file but you can actually add a label to the fieldset with the text (language string) that you want and that parses exactly as you type it. Hallelula!
Restricts language files to remain inside the extension's directory structure rather than adding them to the administrator/languages directory
Removes the en-GB prefix from the language files in line with Joomla!4 guidelines

**2023-12-21**
Version 4.0.1
Removes deprecated `isSite()` method calls from swiss.php

**2023-02-19**
Version update 4.0.0
Code modifications to make the plugin suitable to Joomla!4.x
Improvements to the OpenGraph & Twitter metadata. They are now only injected if they don'e exist, so other components can set them more accurately if needed

**2022-04-02**  
Version update 1.9  
Fixes a mistake in last version where hardcoded links to a specific site caused OpenGraph data for that site to be embedded into the plugin code. This meant that any sites with the plugin installed were displaying OG data for that site instead of its own OG data  
Adds a dedicated OpenGraph metadata editor for adding content to the `Generator` section

**2022-01-12**  
Version update: 1.8  
Fixes issue when adding custom code immediately after opening `<head>` tag (code was placed before the tag)

**2020-05-22**  
Version update: 1.7  
Adds facility to add custom code to the `<head>` section, either after opening tag or before closing tag

**2019-09-12**  
Version update: 1.6  
Tested in J!3.9.11, seems OK!  
Adds proper Joomla! language folder structure  
Adds Force enable admin link  
Adds database query dump option in Debug 
Slight changes to update server  

**2014-12-29**  
Version update: 1.5  
Adds keepalive/session end alerts  
Small code improvements  
Adds an update server   

**2014-12-26**  
Version update: 1.4  
Adds logout redirection  
Adds usergroup based login/out redirection  
Adds external links option for code insertion  
Adds one click logout  
Updates user home page options by allowing different user groups to have different home pages set.  
Adds *Experimental* section containing works in progress (WIP)  
Adds debugging options in WIP with possibilities to show queries, page profiling, memory usage etc. 

**2014-12-09**  
Version update: 1.2
Adds offline mode function 

**2014-12-08**  
Code tidying  
Adds custom home page for registered users  
Adds Generator metatag fixer  

**2014-10-07**  
Initial draft version