# Phplist for YunoHost

[![Integration level](https://dash.yunohost.org/integration/phplist.svg)](https://dash.yunohost.org/appci/app/phplist)

> *This package allow you to install [Phplist](https://www.phplist.org/) on a YunoHost server.  
If you don't have YunoHost, please see [here](https://yunohost.org/#/install) to know how to install and enjoy it.*

## Overview

Phplist manages sending emails to lists. The lists can be uploaded but user can also subscribe/unsubscibe.

**Note :** This App is mature, but its Yunohost package is still fresh. The package passes all automated tests so that basic install/removal/backup/restore should work fine. However, there may still be some usability issues related to packaging -- Please report!

**Shipped version:** 3.6.1
[source repo](https://github.com/phpList/phplist3)

## Demo
[demo page](https://www.phplist.org/demo/)

## Configuration
At install, you define a user that has admin right for phplist. When logged in Yunohost, this user automatically accesses the admin page of phplist when clicking on the tile (or adding /admin/ to the base url of the app). Other users can be granted that permission too. The built-in default "admin" account can be safely deleted.

After install go to the admin panel > config tab. You will probably want to customize many things regarding receiving subscriptions and sending email for your organization. See the [manual](https://www.phplist.org/manual/books/phplist-manual/). You can also import existing lists of adresses.

### Toggling Test Mode
Immediatly after setup, Phplist runs in test mode, not sending emails. This not a bug : before real use, a lot of stuff need to be configured right and need testing (otherwise you would soon become flagged as a spammer). 

Once configured and dry-run tested, the demo mode can be turned off from the (experimental) config-panel of our app.
To reach this confi-panel, go to your Yunohost admin panel, navigate to your Phplist install and append /config-panel to the path, eg:
https://mydomain.tld/yunohost/admin/#/apps/phplist/config-panel 

## Documentation
This installer was derived from yunohost's [template app](https://github.com/YunoHost/example_ynh)
with small bits (config-panel and actions) taken from [my_webapp_ynh](https://github.com/YunoHost-Apps/my_webapp_ynh) ([documentation](https://github.com/YunoHost/doc/blob/master/app_my_webapp.md)).

[Phplist documentation](https://www.phplist.org/)

## YunoHost specific features

#### Multi-user support

Are LDAP and HTTP auth supported? Yes, through a Yunohost SSO plugin.

Can the app be used by multiple users? Yes. The app is meant to be publicly visible (visitors can access it) so that people can subscribe to your lists.
All users that are given the phplist admin permission (same as the admin user declared at install) are considered superusers in phplist.

#### Supported architectures
* x86-64 - [![Build Status](https://ci-apps.yunohost.org/ci/logs/phplist.svg)](https://ci-apps.yunohost.org/ci/apps/phplist/)
* ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/phplist.svg)](https://ci-apps-arm.yunohost.org/ci/apps/phplist/)

## Limitations
Fail2ban not configured yet but the Admin page is still protected by Yunohost SSO. Contact me if you know how to setup F2B.
No logging is implemented but it could be done.

## Additional information
In this package, phpList comes with the set of default plugins (not all activated), plus a custom plugin for the Yunohost SSO. However, for security reasons, adding further phpList plugins can only be done with the help of a Yunohost admin (plugins directory is read-only).

The Captcha plugins is activated out-of-the-box. However, the built-in subscription page does not use it; you need to define a new subscirption page to use it. 

The API is disabled (this is the default for phplist). If you need it, clone this repo and edit the install script to enable it (Untested).

PhpList's built-in updater was removed. Updates will be handled by this package.

## Links

 * Report a bug: https://github.com/slowphil/phplist_ynh/issues
 * YunoHost website: https://yunohost.org/

---
