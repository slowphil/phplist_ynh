# Phplist for YunoHost

> *This package allow you to install [Phplist](https://www.phplist.org/) on a YunoHost server.  
If you don't have YunoHost, please see [here](https://yunohost.org/#/install) to know how to install and enjoy it.*

## Overview

Phplist manages sending emails to lists. The lists can be uploaded but user can also subscribe/unsubscibe.

**THIS IS WORK IN PROGRESS - NOT FOR PRODUCTION**

**Shipped version:** 3.6.0
[source repo](https://github.com/phpList/phplist3)

## Screenshots

## Demo

## Configuration
Once installed, go to the chosen URL and log in as admin with the password you enter upon configuring.

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

Are LDAP and HTTP auth supported? HTTP auth mostly working, needs tweaking
Can the app be used by multiple users? The app is meant to be publicly visible (visitors can access it) so that people can subscribe to your lists 
All users that are given the phplist admin permission (same as the admin user declared at install) are considered superusers in phplist 

#### Supported architectures

## Limitations
Fail2ban not configured yet
Contact me if you know how to do that.
SSO mostly working, maybe a bit rough presently. Authorized users should be automatically forwarded to the admin page.   

## Additional information

## Links

 * Report a bug: https://github.com/slowphil/phplist_ynh/issues
 * YunoHost website: https://yunohost.org/

---

