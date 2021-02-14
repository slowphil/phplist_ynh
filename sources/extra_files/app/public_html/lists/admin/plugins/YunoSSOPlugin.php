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

    // these 2 settings create fields on lists/admin/?page=configure under the cosign section
    public $settings = array(
        //~ 'cosign_realm' => array(
            //~ 'description' => 'Cosign required realm (leave empty to not validate)',
            //~ 'type' => 'text',
            //~ 'value' => '',
            //~ 'allowempty' => true,
            //~ 'category'=> 'Cosign',
        //~ ),
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

    public function activate()
    {
        parent::activate();
        
        global $tables;

        //~ file_put_contents('/tmp/mySSOdebug.log', 'Starting activate', FILE_APPEND);
        //~ var_dump($_SESSION['adminloggedin']);
        //~ file_put_contents('/tmp/mySSOdebug.log', print_r($_SESSION['adminloggedin']), FILE_APPEND);
        //~ var_dump($_SERVER['REMOTE_USER']);
        //~ file_put_contents('/tmp/mySSOdebug.log', print_r($_SERVER['REMOTE_USER']), FILE_APPEND);
        
        if (!empty($_SESSION['adminloggedin'])) {
            return;
        }

        //set in lists/admin/?page=configure under the cosign section
        //~ $requiredRealm = getConfig('cosign_realm');

        //~ if ($requiredRealm) {
            //~ if (!(isset($_SERVER['REMOTE_REALM']) && $requiredRealm == $_SERVER['REMOTE_REALM'])) {
                //~ return;
            //~ }
        //~ }
        
        //on first entry 
        // ["SERVER_NAME"]==["HTTP_HOST"]=> "mydomain.tld"
        // ["DOCUMENT_URI"]=> "/$approotpath/index.php"
        // ["REQUEST_URI"]=> "/$approotpath/" 
        // ["SCRIPT_NAME"]=> "/$approotpath/index.php" 
        // ["PHP_SELF"]=> "/$approotpath/index.php"
        // ["HTTP_REFERER"] => page of SSO "https://mydomain.tld/yunohost/sso"

        
        $authuser = $_SERVER['REMOTE_USER'];
        // also  ["HTTP_REMOTE_USER"]
        //  ["HTTP_AUTH_USER"]
        //  ["PHP_AUTH_USER"]
        $authpw = $_SERVER['PHP_AUTH_PW'];
        $authmail = $_SERVER['HTTP_EMAIL'];
        //~ file_put_contents('/tmp/mySSOdebug.log', '\nautheticated user :', FILE_APPEND);
        //~ file_put_contents('/tmp/mySSOdebug.log', var_dump($authuser), FILE_APPEND);
        //~ file_put_contents('/tmp/mySSOdebug.log', '\npw :', FILE_APPEND);
        //~ file_put_contents('/tmp/mySSOdebug.log', var_dump($authpw), FILE_APPEND);
        //~ file_put_contents('/tmp/mySSOdebug.log', '\nmail :', FILE_APPEND);
        //~ file_put_contents('/tmp/mySSOdebug.log', var_dump($authmail), FILE_APPEND);
        
        
        // permissions are set in Yunohost, all identified users are considered super
        $superuser=1;

        if (!empty($authuser)) {
            $row = Sql_Fetch_Row_Query(
                //~ sprintf(
                    //~ "SELECT id, password, superuser, privileges
                    //~ FROM {$tables['admin']}
                    //~ WHERE loginname = '%s'
                    //~ AND disabled = 0",
                  sprintf(
                    "SELECT id, privileges
                    FROM {$tables['admin']}
                    WHERE loginname = '%s'",
                    sql_escape($authuser)
                )
            );
        //~ file_put_contents('/tmp/mySSOdebug.log', var_dump($row), FILE_APPEND);

            if ($row) {
                //~ list($id, $password, $superuser, $privileges) = $row;
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
        //~ file_put_contents('/tmp/mySSOdebug.log', '\nfailded updating admin db :', FILE_APPEND);
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
        //~ file_put_contents('/tmp/mySSOdebug.log', '\nfailded inserting new admin in db :', FILE_APPEND);
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
        //~ if (empty($_SERVER['REMOTE_USER'])) {
            //~ return;
        //~ }
        // this is set in the settings page of phplist: lists/admin/?page=configure under the cosign section
        $cosignLogout = getConfig('yunosso_logout');

        //If you don't clear the local session cookie and only redirect the browser to the CoSign logout
        //URL, the CoSign session will still be logged out, but the local session will still be valid for
        //about a minute because the CoSign filter caches the credentials.
        //~ if (isset($_SERVER['COSIGN_SERVICE'])) {
            //~ $service_name = $_SERVER['COSIGN_SERVICE'];
            //~ setcookie( $service_name , "null", time()-1, '/', "", 1 );
        //~ }

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
