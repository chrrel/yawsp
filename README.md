# YAWSP - Yet Another Wordpress Security Plugin

YAWSP is a minimal plugin for enhancing security and privacy of WordPress websites.

## Features

### Security Hardenings
* Disable the REST API endpoint `/wp-json/wp/v2/users` to prevent the leakage of usernames
* Disable author archvies completely so that e.g. `/?author=1` does not yield a username.
* Replace the author display name with the website tile (e.g. in RSS feeds).
* Create an anti-spam honeypot: Set the "website" field for comments as an invisible field that must not be filled.
* Log failed and successfull logins to the WordPress backend to log files.
* Prevent users from editing source code files using the built-in file editor.
* Add HTTP Security Headers
* Add output escaping to `the_title` and `the_content` to prevent XSS.

### Privacy Improvements
* Gravatar (used to show avatars for comments) is disabled. Instead, local images are shown.
* Remove cookie consent field in comments so that no cookies are to be saved.
* Replace a comment's IP address with "127.0.0.1" when the comment is approved or classified as spam.
* Replace embedded YouTube videos with youtube-nocookie.com embedds.

### Security through Obscurity
* Remove Wordpress version information from ...
	* ... generator tag in HTML head.
	* ... generator info in RSS feeds.
	* ... script and stylesheet links (e.g. `abc.js?ver=1.0.1`).
* Remove links to the REST Api from HTML head and HTTP headers.
* Remove shortlink from HTML head and HTTP headers.
* Remove links to `wlwmanifest.xml` and `xmlrpc.php?rsd` from HTML head.

## License
This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0).

