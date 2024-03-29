<?php 
require_once("config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Publishing your project</title>

<!-- 

University of Nottingham Xerte Online Toolkits

Properties HTML page 
Version 1.0

-->

<link href="website_code/styles/properties_tab.css" media="screen" type="text/css" rel="stylesheet" />
<link href="website_code/styles/frontpage.css" media="screen" type="text/css" rel="stylesheet" />

<script type="text/javascript" language="javascript" src="website_code/scripts/ajax_management.js"></script>

<script type="text/javascript" language="javascript">

var site_url = "<?php echo $xerte_toolkits_site->site_url;  ?>";
var properties_ajax_php_path = "website_code/php/properties/";
var management_ajax_php_path = "website_code/php/management/";
var ajax_php_path = "website_code/php/";

</script>

<script type="text/javascript" language="javascript" src="website_code/scripts/validation.js"></script>
<script type="text/javascript" language="javascript" src="website_code/scripts/import.js"></script>
<script type="text/javascript" language="javascript" src="website_code/scripts/template_management.js"></script>
<script type="text/javascript" language="javascript" src="website_code/scripts/properties_tab.js"></script>
<script type="text/javascript" language="javascript" src="website_code/scripts/screen_display.js"></script>
<script type="text/javascript" language="javascript" src="website_code/scripts/file_system.js"></script>

</head>

<!--

Start the page and once loaded set the default option

-->

<body onload="javascript:tab_highlight('1');publish_template()">

<!--

Hidden Iframe to allow for ajax file uploads

-->

<iframe id="upload_iframe" name="upload_iframe" src="#" style="width:0px;height:0px; display:none"></iframe>

<div class="properties_main">
	<div class="corner" style="background-image:url(website_code/images/MessBoxTL.gif); background-position:top left;">
	</div>
	<div class="central" style="background-image:url(website_code/images/MessBoxTop.gif);">
	</div>
	<div class="corner" style="background-image:url(website_code/images/MessBoxTR.gif); background-position:top right;">
	</div>
	<div class="main_area_holder_1">
		<div class="main_area_holder_2">
			<div class="main_area">
				<div>
					<span id="title">
						<img src="website_code/images/Icon_Page.gif" style="vertical-align:middle; padding-left:10px;" /> 
						Publishing your project
					</span>
				</div>
				<div id="data_area">
				
						<!--
						
							Dynamic area is the DIV used by the AJAX queries (The right hand side area of the properties panel.
						
						-->
				
						<div id="dynamic_area">
						</div>
						
						<!--

							Set up the three parts for each tab
							
							Structure
							
							tab1-1 is the small part to the right of the main tab, this is used to deal with the border round the main section
							tab1 is the actual tab with the text in it

						-->
						
						<div id="menu_tabs">
							<div class="tab_spacer" style="height:35px;">							
							</div>
							<div id="tab1-1" class="tab_right_pad" style="height:38px;">
							</div>
							<div id="tab1" class="tab" style="width:146px; height:38px;">
								<p onclick="javascript:tab_highlight('1');publish_template()">
									Project
								</p>									
							</div>
							<div class="tab_spacer">							
							</div>
							<div id="tab2-1" class="tab_right_pad" style="height:38px;">
							</div>
							<div id="tab2" class="tab"  style="width:146px; height:38px;">
								<p onclick="javascript:tab_highlight('2'); access_template()">
									Access
								</p>									
							</div>
							<div class="tab_spacer">							
							</div>
							<div id="tab3-1" class="tab_right_pad" style="height:38px;">					
							</div>
							<div id="tab3" class="tab"  style="width:146px;  height:38px;">
								<p onclick="javascript:tab_highlight('3'); rss_template()">
									RSS
								</p>									
							</div>
							<div class="tab_spacer">							
							</div>
							<div id="tab4-1" class="tab_right_pad" style="height:38px;">																	
							</div>
							<div id="tab4" class="tab"  style="width:146px;  height:38px;">
								<p onclick="javascript:tab_highlight('4'); syndication_template()">
									Open Content
								</p>									
							</div>
							<div class="tab_spacer">							
							</div>					
							<!-- 
							
								Last spacer given sufficient heigbt to fill the rest of the border for the right hand panel	
														
							-->
							<div class="tab_spacer" style="height:315px;">							
							</div>
						</div>						
				</div>									
			</div>		
		</div>
	</div>	
	<div class="corner" style="background-image:url(website_code/images/MessBoxBL.gif); background-position:top left;">
	</div>
	<div class="central" style="background-image:url(website_code/images/MessBoxBottom.gif);">
	</div>
	<div class="corner" style="background-image:url(website_code/images/MessBoxBR.gif); background-position:top right;">
	</div>
</div>

</body>
</html>
