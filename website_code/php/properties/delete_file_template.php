<?php
/**
 * 
 * delete file template, allows the site to delete files from the media folder
 *
 * @author Patrick Lockley
 * @version 1.0
 * @copyright Copyright (c) 2008,2009 University of Nottingham
 * @package
 */
require_once("../../../config.php");

/** XXX/ TODO SECURITY HOLE - NEED TO CHECK $_POST['file'] IS VALID */
if(unlink($_POST['file'])){

    receive_message($_SESSION['toolkits_logon_username'], "FILE", "SUCCESS", "The file " . $_POST['file'] . "has been deleted", "User " . $_SESSION['toolkits_logon_username'] . " has deleted " . $_POST['file']);

}else{

    receive_message($_SESSION['toolkits_logon_username'], "FILE", "MAJOR", "The file " . $_POST['file'] . "hasn't been deleted", "User " . $_SESSION['toolkits_logon_username'] . " was not deleted " . $_POST['file']);

}

?>
