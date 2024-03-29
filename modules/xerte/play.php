<?php
require_once("module_functions.php");

//Function show_template
//
// Version 1.0 University of Nottingham
// (pl)
// Set up the preview window for a xerte piece

function show_template($row_play){

    global $xerte_toolkits_site;

    $string_for_flash_xml = $xerte_toolkits_site->users_file_area_short . $row_play['template_id'] . "-" . $row_play['username'] . "-" . $row_play['template_name'] . "/data.xml?time=" . time();

    $string_for_flash = $xerte_toolkits_site->users_file_area_short . $row_play['template_id'] . "-" . $row_play['username'] . "-" . $row_play['template_name'] . "/";

    list($x, $y) = explode("~",get_template_screen_size($row_play['template_name'],$row_play['template_framework']));
    echo file_get_contents("modules/" . $row_play['template_framework'] . "/preview_" . $row_play['template_framework'] . "_top");

    // slightly modified xerte preview code to allow for flash vars
    echo "myRLO = new rloObject('" . $x . "','" . $y . "','modules/" . $row_play['template_framework'] . "/parent_templates/" . $row_play['template_name'] . "/" . $row_play['template_name'] . ".rlt','$string_for_flash', '$string_for_flash_xml', '$xerte_toolkits_site->site_url')";

    echo "</script></div><div id=\"popup_parent\"></body></html>";

}
