<?php
/**
 * Yunohost SSO Plugin for phplist
 *
 * This file is a part of phplist_ynh.
 * It is based on https://github.com/bradallenfisher/phplist-plugin-cosign
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * @category    phplist
 * @package     YunoSSOPlugin
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

 /**
 * Registers the plugin with phplist
 */
class YunoSSOPlugin extends phplistPlugin
{
    /*
    * Inherited Variables
    */
    public $name = 'Yunohost SSO Plugin';
    public $description = 'Use Yunohost\'s SSO to authenticate administrators';
    public $enabled = 1;
    public $version = '1.0';

    // create field(s) on lists/admin/?page=configure under the Yunohost section
    // if needed
    public $settings = array(
        'yunosso_logout' => array(
            'description' => 'the url where to go at logout',
            'type' => 'text',
            'value' => '',
            'allowempty' => true,
            'category'=> 'Yunohost',
        )
    );

    public function __construct()
    {
        parent::__construct();
    }
    
  /**
   * Checks if the user has the admin permission for phpList
   * in yunohost's LDAP
   * Returns array(0, "ERROR MESSAGE DESCRIBING WHAT HAPPENED") on
   * failure, or array(1, 'User has permission'); on success
   * This function checks for LDAP authentication by binding
   * anonymously to LDAP and performing a seach.
   * 
   * Code from https://github.com/phpList/phplist-plugin-ldap/blob/4d3383eda1cc44f0fab724f4645acfe2770f3fe7/plugins/ldapAuth.php#L293
   * simplified and adapted 
   */
  function checkLdapAuth($user, $app) {
    $aLdapUrl = "localhost";  // the url used to connect to the LDAP server
    $aBaseDn = "dc=yunohost,dc=org";  // the base of where to search for the actual target user
    $aFilter = "(&(uid= " . $user . 
      ")(objectClass=posixAccount)(permission=cn=" . $app . 
      ".admin,ou=permission,dc=yunohost,dc=org))";  // the search filter to find the target user's DN
    // var_dump($aFilter);
    // cover all cases
    $myResult = array(0, "Unknown error");

    // connect to the LDAP server
    $myLdapConn = ldap_connect($aLdapUrl);
    // var_dump($myLdapConn);
    // specify LDAP version protocol
    ldap_set_option($myLdapConn,LDAP_OPT_PROTOCOL_VERSION,3);
    
    // Enable LDAP recursive search using root DN.
    ldap_set_option($myLdapConn, LDAP_OPT_REFERRALS, 0);
    

    // if the connection succeeded
    if ($myLdapConn) {
      // do an anonymous LDAP bind
      $myBindResult = ldap_bind($myLdapConn);
      // check to see if bind failed
      if (!$myBindResult) {
        $myResult = array(0, 'Bind to LDAP server failed');
      }
      // bind was fine, keep going
      else {
        // search for the user in question
        $myLdapSearchResult = ldap_search($myLdapConn, $aBaseDn, $aFilter);
        // var_dump($myLdapSearchResult);
        if (!$myLdapSearchResult) {
          $myResult = array(0, 'User not found');
        }
        // if user was found, try to bind again as that user
        else {
          $myResult = array(1, 'User has permission');
        }
      }
      // cleanup the connection
      ldap_close($myLdapConn);
    }
    // connection failure
    else {
      $myResult = array(0, 'Connect failed');
    }
    echo "result before returning";
    echo "myResult = " . $myResult[0] . ", " . $myResult[1];

    return $myResult;

  }

    public function activate()
    {
        parent::activate();
        
        global $tables;

        if (!empty($_SESSION['adminloggedin'])) {
            return;
        }

        //on first entry 
        $Yunohost_app_name = $_SERVER['USER'];
        // ["SERVER_NAME"]==["HTTP_HOST"]=> "mydomain.tld"
        // ["DOCUMENT_URI"]==["SCRIPT_NAME"]==["PHP_SELF"]=> "/$approotpath/index.php"
        // ["REQUEST_URI"]=> "/$approotpath/" 
        // ["HTTP_REFERER"] => page of SSO "https://mydomain.tld/yunohost/sso"
        
        $authuser = $_SERVER['REMOTE_USER'];
        // also  ["HTTP_REMOTE_USER"] , ["HTTP_AUTH_USER"] , ["PHP_AUTH_USER"]
        $authpw = $_SERVER['PHP_AUTH_PW']; //not needed actually
        $authmail = $_SERVER['HTTP_EMAIL']; //save it in db; Useful?

        //~ var_dump($authuser);
        //~ var_dump($authpw);
        //~ var_dump($authmail);

        if (empty($authuser)) return;
        
        $user_has_permission = $this->checkLdapAuth($authuser, $Yunohost_app_name);

        // var_dump($user_has_permission);
        
        if ( $user_has_permission[0] == 0 ) {
          return;
        }
        else { // permissions are granted in Yunohost, identified user is considered super
          $superuser=1;


            $row = Sql_Fetch_Row_Query(
                  sprintf(
                    "SELECT id, privileges
                    FROM {$tables['admin']}
                    WHERE loginname = '%s'",
                    sql_escape($authuser)
                )
            );
            //~ var_dump($row);

            if ($row) {
                list($id, $privileges) = $row;

                $update = Sql_Query(
                  sprintf(
                    "UPDATE {$tables['admin']} SET
                    email = '%s',
                    superuser = %s,
                    disabled = 0
                    WHERE id=%s",
                    sql_escape($authmail),
                    $superuser,
                    $id
                  )
                );
            
                if (!$update) {
                  die(Fatal_Error(s("Fail to update user informations in database : %s",Sql_Error())));
                }
              
            }
            else {
                  $insert = Sql_Query(
                    sprintf(
                      "INSERT INTO {$tables['admin']}
                      (loginname,email,superuser,disabled)
                      VALUES
                      ('%s','%s',%s,0)",
                      sql_escape($authuser),
                      sql_escape($authmail),
                      $superuser
                    )
                  );
                  if (!$insert) {
                     die(Fatal_Error(s("Fail to create user in database : %s",Sql_Error())));
                  }
              
                  $id = Sql_Insert_Id();
            }

            $_SESSION['adminloggedin'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['logindetails'] = array(
                'adminname' => $authuser,
                'id' => $id,
                'superuser' => $superuser,
                //~ 'passhash' => $password,
            );

            if ($privileges) {
                $_SESSION['privileges'] = unserialize($privileges);
            }
            
            //since we authenticate jump to admin page
            $uritail = substr($_SERVER["HTTP_REFERER"], -6, 5);
            if ($uritail !== "admin") {
            header( "Location: admin/" );
            exit;
            }
          
        }
    }

    //When user logs out redirect them to the webaccess logout page and then back to here.
    public function logout()
    {
       // this is set in the settings page of phplist: lists/admin/?page=configure under the cosign section
        $cosignLogout = getConfig('yunosso_logout');

        //remove server vars on logout as well.
        $_SERVER['REMOTE_USER'] = "";
        $_SESSION['adminloggedin'] = "";
        $_SESSION['logindetails'] = "";

        //destroy the session
        session_destroy();

        //reroute the app to the proper cosign logout url
        //this is set from above and using the getConfig(); function to retrieve it
        //from lists/admin/?page=configure
        $url = "http://" . $_SERVER['HTTP_HOST'];
        //header( "Location: $cosignLogout" );
        header( "Location: " . $url . "/yunohost/" );

        //if you don't exit you will not... exit :)
        exit();
    }

}


