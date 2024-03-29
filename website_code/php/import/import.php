<?php
/**
 * 
 * Import page, imports a users template
 *
 * @author Patrick Lockley
 * @version 1.0
 * @copyright Copyright (c) 2008,2009 University of Nottingham
 * @package
 */

require_once("../../../config.php");
include "../user_library.php";
include "../template_library.php";
include "../file_library.php";
include "../template_status.php";

ini_set('memory_limit','64M');

$likelihood_array = array();
$delete_folder_array = array();
$delete_file_array = array();
$rlt_name = "";

/**
 * 
 * Function make new template
 * This function checks http security settings
 * @param string $type = type of template
 * @param string $zip_path = the path we are zipping
 * @version 1.0
 * @author Patrick Lockley
 */

function make_new_template($type,$zip_path){

    global $xerte_toolkits_site, $delete_folder_array, $folder_id;

    $database_connect_id = database_connect("new_template(import) database connect success","new_template(import) database connect fail");

    /*
     *get the root folder for this user
     */

    $root_folder_id = get_user_root_folder();

    /*
     * get the maximum id number from templates, as the id for this template
     */

    $maximum_template_id = get_maximum_template_number();

    $root_folder = get_user_root_folder();

    $query_for_template_type_id = "select template_type_id, template_framework from " . $xerte_toolkits_site->database_table_prefix . "originaltemplatesdetails where template_name = '" .  $type . "'";

    $query_for_template_type_id_response = mysql_query($query_for_template_type_id);	

    $row_template_type = mysql_fetch_array($query_for_template_type_id_response);	

    /*
     * create the new template record in the database
     */

    /*
     * See if we have been given a name, if not, use a fixed one.
     */

    if($_POST['templatename']!=""){

        $template_name = mysql_real_escape_string($_POST['templatename']);	

    }else{

        $template_name = "Imported template";

    }

    $query_for_new_template = "INSERT INTO " . $xerte_toolkits_site->database_table_prefix . "templatedetails (template_id, creator_id, template_type_id, date_created, date_modified, access_to_whom, template_name) VALUES (\"" . ($maximum_template_id+1) . "\",\"" . $_SESSION['toolkits_logon_id'] . "\", \"" . $row_template_type['template_type_id'] . "\",\"" . date('Y-m-d') . "\",\"" . date('Y-m-d') . "\",\"Private\",\"" . $template_name . "\")";

    if(mysql_query($query_for_new_template)){

        /*
         * Are we importing into a folder
         */

        if($folder_id==""){

            $folder_id = $root_folder_id;

        }

        $query_for_template_rights = "INSERT INTO " . $xerte_toolkits_site->database_table_prefix . "templaterights (template_id,user_id,role, folder) VALUES (\"" . ($maximum_template_id+1) . "\",\"" .  $_SESSION['toolkits_logon_id'] . "\", \"creator\" ,\"" . $folder_id . "\")";

        if(mysql_query($query_for_template_rights)){		

            /*
             * Make the folders and copy the files in
             */

            receive_message($_SESSION['toolkits_logon_username'], "ADMIN", "SUCCESS", "Created new template record for the database", $query_for_new_template . " " . $query_for_template_rights);

            mkdir($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . ($maximum_template_id+1) . "-" . $_SESSION['toolkits_logon_username'] . "-" . $type);

            chmod($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . ($maximum_template_id+1) . "-" . $_SESSION['toolkits_logon_username'] . "-" . $type,0777);

            mkdir($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . ($maximum_template_id+1) . "-" . $_SESSION['toolkits_logon_username'] . "-" . $type . "/media/");

            chmod($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . ($maximum_template_id+1) . "-" . $_SESSION['toolkits_logon_username'] . "-" . $type . "/media/",0777);

            copy_loop($zip_path, $xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . ($maximum_template_id+1) . "-" . $_SESSION['toolkits_logon_username'] . "-" . $type . "/");

            echo "New template imported.****";

            /*
             * Remove the files
             */

            array_splice($delete_folder_array,0);

            delete_loop($zip_path);

            while($delete_folder = array_pop($delete_folder_array)){

                rmdir($delete_folder);

            }

            rmdir($zip_path);



        }else{

            receive_message($_SESSION['toolkits_logon_username'], "ADMIN", "CRITICAL", "Failed to create new template record for the database", $query_for_template_rights);

        }

    }else{

        receive_message($_SESSION['toolkits_logon_username'], "ADMIN", "CRITICAL", "Failed to create new template record for the database", $query_for_new_template);

        echo("FAILED-" . $_SESSION['toolkits_most_recent_error']);

    }

    mysql_close($database_connect_id);	

}
/**
 * 
 * Function replace_existing_template
 * This function removes a template and replaces it by importing over the top
 * @param string $path_to_copy_from = path from imported file
 * @param string $template_id = id of template to replace
 * @version 1.0
 * @author Patrick Lockley
 */

function replace_existing_template($path_to_copy_from, $template_id){

    global $xerte_toolkits_site, $delete_file_array, $delete_folder_array;

    $query_for_play_content_strip = str_replace("\" . \$xerte_toolkits_site->database_table_prefix . \"", $xerte_toolkits_site->database_table_prefix, $xerte_toolkits_site->play_edit_preview_query);

    $query_for_play_content = str_replace("TEMPLATE_ID_TO_REPLACE", $template_id, $query_for_play_content_strip);

    $query_for_play_content_response = mysql_query($query_for_play_content);

    $row_play = mysql_fetch_array($query_for_play_content_response);

    delete_loop($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . $template_id . "-" . $row_play['username'] . "-" . $row_play['template_name'] . "/");

    while($delete_file = array_pop($delete_file_array)){

        unlink($delete_file);

    }

    copy_loop($path_to_copy_from, $xerte_toolkits_site->root_file_path . $xerte_toolkits_site->users_file_area_short . $template_id . "-" . $row_play['username'] . "-" . $row_play['template_name'] . "/");

    array_splice($delete_folder_array,0);

    delete_loop($path_to_copy_from);

    while($delete_folder = array_pop($delete_folder_array)){

        rmdir($delete_folder);

    }

    rmdir($path_to_copy_from);

    echo "Template successfully replaced.****";

}

/**
 * 
 * Function copy loop
 * This function checks http security settings
 * @param string $zip_path = path to the zipped files
 * @param string $final_path = path to where the files are going
 * @version 1.0
 * @author Patrick Lockley
 */

function copy_loop($zip_path, $final_path){

    global $xerte_toolkits_site;

    $d = opendir($zip_path);

    while($f = readdir($d)){

        if(is_dir($zip_path . $f)){

            if(($f!=".")&&($f!="..")){

                copy_loop($zip_path . $f . "/", $final_path . $f . "/");

            }			

        }else{

            rename($zip_path . $f, $final_path . $f);

        }

    }	

    closedir($d);

}

/**
 * 
 * Function delete loop
 * This function checks http security settings
 * @param string $path = path to the files we are deleting
 * @version 1.0
 * @author Patrick Lockley
 */

function delete_loop($path){

    global $likelihood_array, $delete_folder_array, $delete_file_array;

    $d = opendir($path);

    while($f = readdir($d)){

        if(is_dir($path . $f)){

            if(($f!=".")&&($f!="..")){

                delete_loop($path . $f . "/");

                array_push($delete_folder_array, $path . $f . "/");			

            }			

        }else{

            array_push($delete_file_array, $path . $f);

        }

    }	

    closedir($d);

}

/**
 * 
 * Function folder loop
 * This function checks http security settings
 * @param string $path = path to loop through
 * @version 1.0
 * @author Patrick Lockley
 */

function folder_loop($path){

    global $likelihood_array;

    $d = opendir($path);

    while($f = readdir($d)){

        if(is_dir($path . $f)){

            if(($f!=".")&&($f!="..")){

                folder_loop($path . $f . "/");

            }			

        }else{

            if(strpos($f,".rlt")!=0){

                $template_check = file_get_contents($path . $f);

                $folder = explode('"',substr($template_check,strpos($template_check,"targetFolder"),strpos($template_check,"version")-strpos($template_check,"targetFolder")));

                $start_point = strpos($template_check,"version");

                $version = explode('"',substr($template_check,$start_point,strpos($template_check," ",$start_point)-$start_point));

                $temp_array = array($folder[1],$version[1]);

                array_push($likelihood_array,$temp_array);

            }else{


            }

        }

    }

    closedir($d);

}

/*
 * Check who made the template
 */

if($_POST['replace']!=""){

    if(!is_user_creator(mysql_real_escape_string($_POST['replace']))){

        echo "Only the owner of this template can import over the top.****";
        die();

    }

}

$folder_id = "";

/*
 * Check the file is the write type
 */

if(($_FILES['filenameuploaded']['type']=="application/x-zip-compressed")||($_FILES['filenameuploaded']['type']=="application/zip")){

    $this_dir = rand() . "/";

    mkdir($xerte_toolkits_site->import_path . $this_dir);

    chmod($xerte_toolkits_site->import_path . $this_dir,0777);

    $new_file_name = $xerte_toolkits_site->import_path . $this_dir . time() . $_FILES['filenameuploaded']['name'];

    if(@move_uploaded_file($_FILES['filenameuploaded']['tmp_name'], $new_file_name)){

        require_once dirname(__FILE__)."/dUnzip2.inc.php";

        $zip = new dUnzip2($new_file_name);

        $zip->debug = false;

        $zip->getList();

        $file_data = array();

        $template_data_equivalent = null;

        /*
         * Look for the folders in the zip and move files accordingly
         */

        foreach($zip->compressedList as $x){

            foreach($x as $y){

                if(!(strpos($y,"media/")===false)){

                    $string = $zip->unzip($y, false, 0777);

                    $temp_array = array($y,$string,"media");

                    array_push($file_data,$temp_array);

                }

                if(!(strpos($y,".rlt")===false)){

                    $string = $zip->unzip($y, false, 0777);

                    $rlt_name = $y;

                    $temp_array = array($y,$string,"rlt");

                    array_push($file_data,$temp_array);		

                    if(!(strpos($string,"templateData=")===false)){

                        $temp = substr($string,strpos($string,"templateData=\"FileLocation + '")+strlen("templateData=\"FileLocation + '"));

                        $temp = substr($temp,0,strpos($temp,"'"));

                        $template_data_equivalent = $temp;

                    }

                }

            }

        }

        /*
         * Look for an xml file linked to the RLO
         */

        if($template_data_equivalent!=null){

            foreach($zip->compressedList as $x){

                foreach($x as $y){

                    if($y===$template_data_equivalent){

                        $data_xml = $zip->unzip($y, false, 0777);

                        $temp_array = array("data.xml",$data_xml,null);

                        array_push($file_data,$temp_array);

                    }

                }

            }

        }else{

            echo "File transfer has failed.****";

        }

        /*
         * Make some new folders
         */

        mkdir($xerte_toolkits_site->import_path . $this_dir . "media");

        chmod($xerte_toolkits_site->import_path . $this_dir, 0777);

        /*
         * Put the files into the right folders
         */

        while($file_to_create = array_pop($file_data)){

            if($file_to_create[2]=="media"){

                $fp = fopen($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],"w");

                fwrite($fp,$file_to_create[1]);

                fclose($fp);

                chmod($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],0777);

            }else if($file_to_create[2]=="rlt"){

                $fp = fopen($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],"w");

                fwrite($fp,$file_to_create[1]);

                fclose($fp);

                $template_check = $file_to_create[1];

                chmod($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],0777);

            }else{

                $fp = fopen($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],"w");

                fwrite($fp,$file_to_create[1]);

                fclose($fp);

                chmod($xerte_toolkits_site->import_path . $this_dir . $file_to_create[0],0777);

            }

        }

        $zip->close();

        unlink($new_file_name);

        /*
         * use the template attributes to make the folders required and name them accordingly
         */

        $folder = explode('"',substr($template_check,strpos($template_check,"targetFolder"),strpos($template_check,"version")-strpos($template_check,"targetFolder")));

        $start_point = strpos($template_check,"version");

        $version = explode('"',substr($template_check,$start_point,strpos($template_check," ",$start_point)-$start_point));

        if($_POST['replace']){

            $query = "select template_framework from " . $xerte_toolkits_site->database_table_prefix . "templatedetails, " . $xerte_toolkits_site->database_table_prefix . "originaltemplatesdetails where " . $xerte_toolkits_site->database_table_prefix . "templatedetails.template_type_id = " . $xerte_toolkits_site->database_table_prefix . "originaltemplatesdetails.template_type_id AND " . $xerte_toolkits_site->database_table_prefix . "templatedetails.template_id =\"" . mysql_real_escape_string($_POST['replace']) . "\"";

            $query_response = mysql_query($query);

            if($query_reponse===false){

                receive_message($_SESSION['toolkits_logon_username'], "USER", "CRITICAL", "Failed to get template type", "Failed to get template type");

                echo "Sorry, we cannot get a template type for this template.****";

            }else{

                $row = mysql_fetch_array($query_response);

                if($row['template_framework']=="xerte"){

                    folder_loop($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->module_path . "xerte/parent_templates/");

                    $template_found = false;

                    while($list_of_rlts = array_pop($likelihood_array)){

                        if(($folder[1]!="")&&($version[1]!="")&&($folder[1]==$list_of_rlts[0])&&($version[1]==$list_of_rlts[1])){

                            $template_found=true;
                            break;

                        }				

                    }	

                    if($template_found){

                        unlink($xerte_toolkits_site->import_path . $this_dir . $rlt_name);

                        $preview_xml = file_get_contents($xerte_toolkits_site->import_path . $this_dir . "data.xml");

                        $fh = fopen($xerte_toolkits_site->import_path . $this_dir . "preview.xml", "w");

                        fwrite($fh, $preview_xml);

                        fclose($fh);

                        /*
                         * Copy over the top
                         */

                        replace_existing_template($xerte_toolkits_site->import_path . $this_dir, mysql_real_escape_string($_POST['replace']));


                    }else{

                        echo "No valid template equivalent found on this site. Please contact your system administrators.****";
                        delete_loop($xerte_toolkits_site->import_path . $this_dir);

                        while($delete_file = array_pop($delete_file_array)){

                            unlink($delete_file);

                        }

                        while($delete_folder = array_pop($delete_folder_array)){

                            rmdir($delete_folder);

                        }

                        rmdir($xerte_toolkits_site->import_path . $this_dir);

                    }

                }

            }

        }else{

            if($_POST['folder']!=""){

                $folder_id = $_POST['folder'];	

            }

            if($template_check!=null){

                folder_loop($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->module_path . "xerte/parent_templates/");

                $template_found = false;

                while($list_of_rlts = array_pop($likelihood_array)){

                    if(($folder[1]!="")&&($version[1]!="")&&($folder[1]==$list_of_rlts[0])&&($version[1]==$list_of_rlts[1])){

                        $template_found=true;
                        break;

                    }				

                }	

                if($template_found){

                    /*
                     * Make a new template
                     */

                    unlink($xerte_toolkits_site->import_path . $this_dir . $rlt_name);

                    $preview_xml = file_get_contents($xerte_toolkits_site->import_path . $this_dir . "data.xml");

                    $fh = fopen($xerte_toolkits_site->import_path . $this_dir . "preview.xml", "w");

                    fwrite($fh, $preview_xml);

                    fclose($fh);

                    make_new_template($folder[1], $xerte_toolkits_site->import_path . $this_dir);

                }else{

                    echo "No valid template equivalent found on this site. Please contact your system administrators.****";
                    delete_loop($xerte_toolkits_site->import_path . $this_dir);

                    while($delete_file = array_pop($delete_file_array)){

                        unlink($delete_file);

                    }

                    while($delete_folder = array_pop($delete_folder_array)){

                        rmdir($delete_folder);

                    }	

                    rmdir($xerte_toolkits_site->import_path . $this_dir);

                }

            }else{

                echo "Only Xerte templates are currently imported.****";

                delete_loop($xerte_toolkits_site->import_path . $this_dir);

                while($delete_file = array_pop($delete_file_array)){

                    unlink($delete_file);

                }

                while($delete_folder = array_pop($delete_folder_array)){

                    rmdir($delete_folder);

                }

                rmdir($xerte_toolkits_site->import_path . $this_dir);


            }

        }

    }else{

        echo "File transfer has failed.****";
        delete_loop($xerte_toolkits_site->import_path . $this_dir);
        while($delete_file = array_pop($delete_file_array)){

            unlink($delete_file);

        }
        while($delete_folder = array_pop($delete_folder_array)){

            rmdir($delete_folder);

        }
        rmdir($xerte_toolkits_site->import_path . $this_dir);

    }

}else{

    echo $_FILES['filenameuploaded']['type'] . "<Br>";

    echo "You can only import Zip Files.****";

}

?>
