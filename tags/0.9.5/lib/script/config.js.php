<?php
	if (!defined("UNIFIED_SCRIPT")) header("Content-type: text/javascript");
	
	require_once("../private/connector.class.php");
	require_once("../private/gameconfig.php");

	$Connector = Connector::GetInstance();
	$Settings = $Connector->prepare("Select `Name`, `TextValue`, `IntValue` FROM `".RP_TABLE_PREFIX."Setting`");

    if ( $Settings->execute() )
    {
    	$Site      = "";
    	$Banner     = "cataclysm.jpg";
    	$Background = "flower.png";
    	$BGColor    = "#898989";
    	$BGRepeat   = "repeat-xy";
    	$TimeFormat = 24;
    	
        while ( $Data = $Settings->fetch( PDO::FETCH_ASSOC ) )
        {
        	switch( $Data["Name"] )
        	{
        	case "Site":
        		$Site = $Data["TextValue"];
        		break;
        	
        	case "Theme":
        		$ThemeFile = "../../images/themes/".$Data["TextValue"].".xml";
        		
        		if ( file_exists($ThemeFile) )
        		{
	        		$Theme = new SimpleXMLElement( file_get_contents($ThemeFile) );
	        		$Banner = $Theme->banner;
	        		$Background = $Theme->bgimage;
	        		$BGColor = $Theme->bgcolor;
	        		$BGRepeat = $Theme->bgrepeat;
	        	}
        		break;
        	
        	case "TimeFormat":
        		$TimeFormat = $Data["IntValue"];
        		break;
        	
        	default:
        		break;
        	};
        }
    }
    	
    $Settings->closeCursor();
?>

var g_SiteVersion = <?php echo intval($_REQUEST["version"]) ?>;
var g_BannerLink = "<?php echo $Site; ?>";
var g_TimeFormat = <?php echo $TimeFormat; ?>;

var g_Theme = {
	background : "<?php echo $Background; ?>",
	banner     : "<?php echo $Banner; ?>",
	bgrepeat   : "<?php echo $BGRepeat; ?>",
	bgcolor    : "<?php echo $BGColor; ?>"
};

var g_RoleNames = Array(<?php echo sizeof($s_Roles); ?>);
var g_Classes  = Array(<?php echo sizeof($s_Classes); ?>);
var g_ClassIdx = Array(<?php echo sizeof($s_Classes); ?>);
var g_GroupSizes = Array(<?php
	for ($i=0; list($Count,$RoleSizes) = each($s_GroupSizes); ++$i)
	{
		if ($i>0) echo ",";
		echo $Count;
	}
	reset($s_GroupSizes);
?>);
var g_GroupRoleSizes = Array(<?php echo sizeof($s_GroupSizes); ?>);

<?php
	for ( $i=0; list($RoleIdent,$RoleName) = each($s_Roles); ++$i )
	{
		echo "g_RoleNames[\"".$RoleIdent."\"] = L(\"".$RoleName."\");\n";
	}
	reset($s_Roles);
?>

<?php 

	for ( $i=0; list($ClassIdent,$ClassConfig) = each($s_Classes); ++$i )
	{
		echo "g_ClassIdx[\"".$ClassIdent."\"] = ".$i."; ";
		echo "g_Classes[".$i."] = {";
		echo "ident : \"".$ClassIdent."\", ";
		echo "text : L(\"".$ClassConfig[0]."\"), ";
		echo "roles : Array(";
		
		for ( $r=0; $r < sizeof($ClassConfig[1]); ++$r )
		{
			if ($r > 0) echo ",";
			echo "\"".$ClassConfig[1][$r]."\""; 
		}
		
		echo ")};\n";
	}
	reset($s_Classes);
?>

<?php
	while ( list($Count,$RoleSizes) = each($s_GroupSizes) )
	{
		echo "g_GroupRoleSizes[".$Count."] = Array(".$RoleSizes[0].",".$RoleSizes[1].",".$RoleSizes[2].");\n";
	}
	reset($s_GroupSizes);
?>

// -----------------------------------------------------------------------------

function onChangeConfig()
{
	// Update logo
	
	$("#logo").detach();
	
	if ( g_BannerLink != "" )
		$("#menu").before("<a id=\"logo\" href=\"" + g_BannerLink + "\"></a>");
	else
		$("#menu").before("<div id=\"logo\"></div>");
	
	
	// Update theme
	
	$("#logo").css("background-image", "url(images/banner/" + g_Theme.banner + ")");
	$("body").css("background-color", g_Theme.bgcolor );
	$("body").css("background-repeat", g_Theme.bgrepeat );
	
	if ( g_Theme.background == "none" )
		$("body").css("background-image", "none");
	else
		$("body").css("background-image", "url(images/background/" + g_Theme.background + ")");
	
	// Update raid time fields
	
	if ( (g_User != null) && 
		 (g_User.isAdmin || g_User.isRaidlead) )
	{	
		var HTMLString = "";
		
		for ( i=4; i>=0; --i )
			HTMLString += "<option value=\"" + i + "\">" + formatHourPrefixed(i) + "</option>";
			
		for ( i=23; i>4; --i )
			HTMLString += "<option value=\"" + i + "\">" + formatHourPrefixed(i) + "</option>";
		
		var HourFieldWidth        = (g_TimeFormat == 24) ? 48 : 64;
		var LocationFieldWidth    = (g_TimeFormat == 24) ? 192 : 224;
		var DescriptionFieldWidth = (g_TimeFormat == 24) ? 310 : 342;
		
		$("#starthour")
			.css("width", HourFieldWidth)
			.empty().append(HTMLString);
			
		$("#endhour")
			.css("width", HourFieldWidth)
			.empty().append(HTMLString);
		
		$("#selectlocation")
			.css("width", LocationFieldWidth);
		
		$("#descriptiondummy")
			.css("width", DescriptionFieldWidth)
			.css("max-width", DescriptionFieldWidth);
		
		$("#description")
			.css("width", DescriptionFieldWidth)
			.css("max-width", DescriptionFieldWidth);
	}
}

// -----------------------------------------------------------------------------

function formatTime(a_Hour, a_Minute)
{
	if ( g_TimeFormat == 12 )
	{
		var numericHour = parseInt(a_Hour);
		var postFix = " pm";
		
		if ( numericHour < 12 )
			postFix = " am";
		else
			numericHour -= 12;
		
		
		if ( numericHour == 0 )
			return "12:" + a_Minute + postFix;
		
		return numericHour + ":" + a_Minute + postFix;
	}
	
	return a_Hour + ":" + a_Minute;
}

// -----------------------------------------------------------------------------

function formatTimeString( a_String )
{
 	var separatorIndex = a_String.indexOf(":");
 	
	var hour   = a_String.substr( 0, separatorIndex );
	var minute = a_String.substr( separatorIndex+1 );
	
	return formatTime( hour, minute );
}

// -----------------------------------------------------------------------------

function formatHourPrefixed( a_Hour )
{
	if ( g_TimeFormat == 12 )
	{
		var numericHour = parseInt(a_Hour);
		var preFix = "pm ";
		
		if ( numericHour < 12 )
			preFix = "am ";
		else
			numericHour -= 12;
		
		
		if ( numericHour == 0 )
			return preFix + "12";
		
		return preFix + numericHour;
	}
	
	return a_Hour;
}