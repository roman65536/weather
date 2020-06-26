<?php
##########################################################################
#                                                                        #
# (c) Sun Microsystems, Inc.                                             #
# Proactive Services Switzerland                                         #
#                                                                        #
# Ver:         1.0                                                       #
#                                                                        #
# Author:      Curzio Della Santa (Curzio.DellaSanta@sun.com)            #
#                                                                        #
##########################################################################
#                                                                        #
# Description: This file contains all the function that handle the       #
#              error messages and access to the MySQL Database           #
#                                                                        #
#              ALL ERROR MESSAGES  PASS THROUGH THOSE FUNCTIONS          #
#              ALL THE DB REQUESTS PASS THROUGH THOSE FUNCTIONS          #
#                                                                        #
# Functions:   error($text)                                              #
#              message($text)                                            #
#              crit_error($text)                                         #
#              modify_table($query) return affected rows                 #
#              get_rows($query,&$result) return number of rows           #
#              open_database()                                           #
#              close_database()                                          #
#                                                                        #
##########################################################################


$config_data = array( 'db_hostname'  =>"localhost",
		      'db_user'      =>"root",
		      'db_name'      =>"sensors",
);


/*error handling*/
function error($text) {
  echo "<h2><font color=\"#ff0000\">ERROR: ".$text."</font></h2><br>";
}

function message($text) {
  echo "<h2><font color=\"#ff0000\">".$text."</font></h2><br>";
}

function crit_error($text) {
  die("<h2><font color=\"#ff0000\">CRITICAL ERROR: ".$text."</font></h2><br>");
}
  

# function that executes the sql query (normally insert/update/alter ... but not selects)
# and returns the numer of affected rows
function modify_table($query) {
/* The connetion to the DB should already be established */

  $t = mysql_query($query) or $error = mysql_error();

  if (isset($error)) {
     error("Wrong SQL query</h2><br>Error message:". $error."<br>Executing: ".$query);
     return -1;#exit;
  }

  return mysql_affected_rows();
}

# function that executes the sql query (normally a select)
# and returns the numer of selected lines
function get_rows($query,&$result) {
  /* The connetion to the DB should already be established */
  $t = mysql_query($query) or $error = mysql_error();

  if (isset($error)) {
     error("Wrong SQL query</h2><br>Error message:". $error."<br>Executing: ".$query);
     return -1;#exit;
  }

  $result = array(); # clears $result
  while ($r = mysql_fetch_array($t, MYSQL_ASSOC))  array_push($result,$r);
  $lines = mysql_num_rows($t);
  mysql_free_result($t);

  return $lines;
}

#function to open the database link
function open_database() {
# initialize database
  global $database_link,$config_data;

  $database_link = mysql_connect($config_data['db_hostname'],$config_data['db_user'],'65536')  or crit_error("Could not connect to host \"".$config_data['db_hostname']."\" as \"".$config_data['db_user']."\"<br> Error: ". mysql_error());
  mysql_select_db($config_data['db_name']) or crit_error("Could not select database: ". mysql_error() );
}

#function to close the database link
function close_database() {
  global $database_link;

  mysql_close($database_link);
}




?>
