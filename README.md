# Joomla! Swiss Army Knife plugin
This plugin is intended to be a one stop shop for tweaks and functionality inside Joomla CMS.

* **Replace anywhere:** When enabled you can create a list of key/value pairs that callwill be replaced throughout the website dynamically on page load. There are also a few predefined ones, including {swiss.username}, {swiss.name}, {swiss.email}, {swiss.lastvisitdate}, {swiss.today} that would be replaced with user related data when displayed.

* **Cookie notice:** When enabled this will display info relating to the EU Cookie Directive, namely alerting users of the site that you use cookies. Once they accept the message will be gone from the screen. There are some confirguration options available.

* **Enable auto-login:** Allows users to automatically log in after registering.

* **Insert code:** When enabled this will allow you to add CSS, javascript or HTML to the final page. Code is added to the HEAD section and you can also add code after the opening BODY tag or before the closing BODY tag. You can enter your CSS/Javascript in the plugin or add links to an external code source/s

* **System message after login:** Display a system message to users after a successful login

* **Login redirect:** Choose a page to redirect users to after a successful login. Can be assigned to specific user groups.

* **Logout redirect:** Choose a page to redirect users to after a successful logout. Can be assigned to specific user groups.

* **Custom home page for registered users:** Select a page from the menu structures to set as a default home page, even over-riding the Joomla! default home. Different user groups can be assigned different pages.

* **Generator metatag fixer:** I dislike that Joomla! adds its own generator metatag. With this you can removeit or set your own choice of names in the tag.

* **Take site offline:** Using this you can take your site offline to the public by either entering a redirect URL or entering HTML that you would like to sisplay instead of your website. You can whitelist IP address/es to allow access for anybody that you want to have access to the website while it's offline

* **One click logout:** By default when a user click a logout button Joomla! will redirect to a logout page where the user clicks another logout button. When enabled option this will detect those first logout clicks and immediately log the user out without requiring a second click, and if desired you can create your own links to create a logout option for a user with `index.php?option=com_users&task=user.logout`.

* **WIP - Debugging output** When enabled you can (will) be able to see queries, memory usage and page profiling.

## Changelog
**2014-10-07**  
Initial draft version
**2014-12-08**  
Code tidying
Adds custom home page for registered users
Adds Generator metatag fixer
**2014-12-09**  
Version update: 1.2
Adds offline mode function
**2014-12-26**  
Version update: 1.4
Adds logout redirection
Adds usergroup based login/out redirection
Adds external links option for code insertion
Adds one click logout
Updates user home page options by allowing different user groups to have different home pages set.
Adds *Experimental* section containing works in progress (WIP)
Adds debugging options in WIP with possibilities to show queries, page profiling, memory usage etc.