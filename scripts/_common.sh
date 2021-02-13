#!/bin/bash

#=================================================
# COMMON VARIABLES
#=================================================

YNH_PHP_VERSION="7.3"

#https://resources.phplist.com/system/start?s[]=requirements
# PHP 7.0 or later with the following PHP extensions:
#  pcre imap Core date hash SPL filter openssl mbstring session curl xml iconv json gettext
#  SimpleXML mysqli mysql 
# GD (required by CKEditor Plugin only)

# dependencies used by the app 
#(copied from https://releases.turnkeylinux.org/turnkey-phplist/16.0-buster-amd64/turnkey-phplist-16.0-buster-amd64.manifest)
pkg_dependencies="php7.3-common php7.3-curl php7.3-gd php7.3-imap php7.3-json php7.3-mysql php7.3-opcache php7.3-readline php7.3-xml php7.3-zip"

#=================================================
# PERSONAL HELPERS
#=================================================

#=================================================
# EXPERIMENTAL HELPERS
#=================================================

#=================================================
# FUTURE OFFICIAL HELPERS
#=================================================
