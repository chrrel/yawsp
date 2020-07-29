# YAWSP - Yet Another Wordpress Security Plugin

YAWSP is a minimal plugin for enhancing security and privacy of WordPress websites.

## Features

### Security Hardening
* Disable the REST API endpoint wp-json/wp/v2/users to prevent the leakage of usernames
* Disable author archvies completely so that e.g. /?author=1 does not yield a username.
* Create an anti-spam honeypot: Set the "website" field for comments as an invisible field that must not be filled.
* Log failed and successfull logins to the WordPress backend to log files.
* Prevent users from editing source code files using the built-in file editor.

### Privacy Improvements
* Gravatar (used to show avatars for comments) is disabled. Instead, local images are shown.
* Remove cookie consent field in comments so that no cookies are to be saved.
* Replace a comment's IP address with "127.0.0.1" when the comment is approved or classified as spam.
* Replace embedded YouTube videos with youtube-nocookie.com embedds.

## License
This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0).

