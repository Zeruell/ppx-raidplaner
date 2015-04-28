# Raidplaner customization #

## Creating custom themes ##

Themes are described via XML and have to be placed in the "images/themes" folders.<br>
Lets have a look at an example file:<br>
<br>
<pre><code>&lt;?xml version="1.0" encoding="UTF-8" ?&gt;<br>
&lt;theme&gt;<br>
    &lt;banner&gt;cataclysm.jpg&lt;/banner&gt;<br>
    &lt;bgimage&gt;cataclysm.jpg&lt;/bgimage&gt;<br>
    &lt;bgrepeat&gt;repeat-x&lt;/bgrepeat&gt;<br>
    &lt;bgcolor&gt;#8a8a8a&lt;/bgcolor&gt;<br>
    &lt;portalmode&gt;false&lt;/portalmode&gt;<br>
&lt;/theme&gt;<br>
</code></pre>

The first line is the standard XML header. Leave this as-is (unless you know what you're doing).<br>
The root element is called "theme". There are no additional attributes, so this can be left unchanged, too.<br>
Next are the elements describing the different parts of the theme.<br>
All nodes (as in <code>&lt;node&gt;&lt;/node&gt;</code>) are mandatory should be written all lowercase.<br>
<br>
<b>banner</b><br>
The image named here has to exist in the folder "images/banner" and will be displayed at the top of the page. The image has to be of the dimensions 1024x141.<br>
If you set the banner to "<i>none</i>", a black area will be displayed. If you set the banner to "<i>disable</i>" the banner will not be shown.<br>
<br>
<b>bgimage</b><br>
This image will be displayed in the background of the raidplaner and has to exist in the folder "images/background". It can be of any dimension but should work well with whatever repeat pattern is defined with the "bgrepeat" node.<br>
If set bgimage to "<i>none</i>" no background will be displayed.<br>
<br>
<b>bgrepeat</b><br>
This node defines the repeat mode of the background patterns. <br>
The values entered have to be compatible with the css attribute "background-repeat".<br>
Possible values are "<i>no-repeat</i>", "<i>repeat-x</i>", "<i>repeat-y</i>", "<i>repeat</i>".<br>
<br>
<b>bgcolor</b><br>
The color of the page behind the background image.<br>
This can be useful if you don't want to use a background image, the image should blend into a single color or you want to use a transparent image.<br>
If set bgcolor to "<i>none</i>" the background color will be transparent.<br>
<br>
<b>portalmode</b><br>
When set to "<i>true</i>" this setting will move the raidplaner window to the top left of the document. This might be useful when integrating the raidplaner into a portal/cms.<br>
<br>
<h2>Changes beyond theme files</h2>

<h3>Modifying script files</h3>

As of version 1.0.0 the raidplaner loads a unified, compiled javascript by default.<br>
This script is packed, so modifications are close to impossible.<br>
To use the separate script files included in every raidplaner installation, open the file "index.php" and change the SCRIPT_DEBUG define to true like this:<br>
<br>
<pre><code>define("SCRIPT_DEBUG", true);<br>
<br>
</code></pre>

This will load each file separately. To load all files as one merged (but unoptimized) file, you need to change the loading code in the file index.php by switching comments. The result should look like this:<br>
<br>
<pre><code>if (defined("SCRIPT_DEBUG") &amp;&amp; SCRIPT_DEBUG)<br>
{<br>
     //include_once("lib/script/_scripts.js.php");<br>
     echo "&lt;script type=\"text/javascript\" src=\"lib/script/_scripts.js.php?version=".$gSiteVersion."&amp;r=".((registeredUser()) ? 1 : 0)."\"&gt;&lt;/script&gt;";<br>
}<br>
</code></pre>

<h3>CSS and image changes</h3>

It is not recommended to do any changes that go beyond the standard theme mechanism.<br>
If you however <b>do</b> want to modify other elements have a look at the folder "lib/layout". Here you will find stylesheets for each section of the raidplaner. All css files will be merged<br>
into a single file called "<code>_</code>layout.css.php". If you need to create any additional css file you should include it here.<br>
<br>
The raidplaner uses JQuery UI. If you want to change the optics of the used interface elements you can download a custom style from <a href='http://jqueryui.com'>http://jqueryui.com</a> and replace the<br>
"jquery-ui-1.8.23.custom.css" and the "ui-<code>*</code>" files in the folder "lib/layout/images".<br>
<br>
<table><thead><th> calendar.css </th><th> Styles for the calendar view </th></thead><tbody>
<tr><td> combobox.css </td><td> Styles for dropdown boxes </td></tr>
<tr><td> default.css </td><td> Common styles used throughout the raidplaner </td></tr>
<tr><td> profile.css </td><td> Styles for the profile view </td></tr>
<tr><td> raid.css </td><td> Styles for the raid detail view </td></tr>
<tr><td> raidlist.css </td><td> Styles for the raid list view </td></tr>
<tr><td> settings.css </td><td> Styles for the settings view </td></tr>
<tr><td> shadow.css </td><td> Styles used for elements with shadows </td></tr>
<tr><td> shadowIE.css </td><td> Internet Explorer specific style-overwrites for shadows </td></tr>
<tr><td> sheet.css </td><td> Styles used for sheets (e.g. new raid sheet) </td></tr>
<tr><td> sheetIE.css </td><td> Internet Explorer specific style-overwrites for sheets </td></tr>
<tr><td> tooltip.css </td><td> Styles used in tooltips </td></tr>
<tr><td> tooltipIE.css </td><td> Internet Explorer specific style-overwrites for tooltip </td></tr></tbody></table>

You will find most game related images in the top level "images" folder. The images found in "lib/layout/images" are ment to be game-agnostic interface elements.<br>
The top level "images" folder contains 5 folders for game specific images:<br>
<br>
<table><thead><th> classesbig </th><th> Class icons of size 64x64 </th></thead><tbody>
<tr><td> classessmall </td><td> Class icons of size 32x32 </td></tr>
<tr><td> raidbig </td><td> Raid location icons of size 64x64 </td></tr>
<tr><td> raidsmall </td><td> Raid location icons of size 32x32 </td></tr>
<tr><td> roles </td><td> Role icons and slot images </td></tr></tbody></table>

All images placed in "raidbig" and "raidsmall" have to exist in both folders.<br>
So if you want to add a new "mylocation.png" you should place a 32x32 version called "mylocation.png" in "raidsmall" and a 64x64 version called "mylocation.png" in "raidbig".<br>
The images will automatically become available in the location image chooser.<br>
Images placed in "classesbig" and "classessmall" follow the same rule but have to be referenced from the "gameconfig.php" (see below).<br>
<br>
The roles folder relies heavily on the settings you made in the "gameconfig.php". The images here must be named like the roles the refer to. So if you define a role "magic", there has to be a "magic.png" file in this folder.<br>
This folder also contains slot graphics. You can use the existing graphics to create new ones for your role or simple use the ones available. As these images are referenced directly from the "gameconfig.php" you don't need to follow any name conventions here.<br>
<br>
<h2>Configuring the raidplaner for other games</h2>

The raidplaner comes with common settings for World of Warcraft. To use the raidplaner for other games you might want to change the available roles and classes.<br>
To do this you will have to edit the "lib/private/gameconfig.php". The file contains several comments explaining which settings will have what effect. You will need basic knowledge of PHP when editing this file.<br>
<br>
When configuring the raidplaner for a new game you should always work with an empty database, i.e. there should be no characters defined for any user. If this is not the case, you should backup your database and clear the tables "<i><code>&lt;</code>prefix<code>&gt;</code></i>Character" and "<i><code>&lt;</code>prefix<code>&gt;</code></i>Attendance".<br>
If you do not work with an empty database, you will end up with incorrect profile pages and other weird stuff.<br>
<br>
<h3>Defining new roles</h3>

Roles are defined in the $s_Roles array. There can be up to 5 roles and there has to be at least 1 role.<br>
The roles have to be defined as a key/value pair like this:<br>
<br>
<pre><code>$s_Roles = Array(<br>
    "identifier" =&gt; "roleLoca"<br>
);<br>
</code></pre>

The key ("identifier") will be used in the database as well as for identifying the correct image for the role (see above). The localization name has to be added to all localization files (or at least the one you are using).<br>
Localization files can be found in "lib/private/locale".<br>
<br>
<pre><code>if ( defined("LOCALE_MAIN") )<br>
{<br>
    $g_Locale[ "roleLoca" ] = "Meine neue Rolle";<br>
    // ...<br>
</code></pre>

After defining your new roles you have to set up the raid slot images. These images are shown in the raid detail view and should make it easy to see which slot is available for which role. The slotimages are assigned to the corresponding role at the same index as used in $s_Roles. So the first entry in $s_RoleImages will be the slot image for the first role defined in $s_Roles.<br>
<br>
Finally you might want to define role sets to by used by class definitions. For example: A deathknight can be played as a damage dealer as wall as a tank. So you might want to define a role set for this combination.<br>
<br>
<pre><code>class Roles<br>
{<br>
    public static $all = Array("dmg","heal","tank","identifier");<br>
    // ...<br>
</code></pre>

<h3>Defining new classes</h3>

Defining new classes is very similar to defining new roles. You have to edit or extend the $s_Classes array, which is again associative. As with the roles array, the key will be the class identifier used in the database and when finding the corresponding class image.<br>
The value of each class entry has to be set to an array containing the localization key and a role set (array of role identifiers). If you defined role sets you can just use the fitting role set here.<br>
Note the the class "empty" is mandatory.<br>
<br>
<pre><code>$s_Classes = Array(<br>
    "empty" =&gt; Array( "", Roles::$damage ),<br>
    "newClass" =&gt; Array( "classLoca", Roles::$all ),<br>
    // ...<br>
</code></pre>

<h3>Defining new group sizes</h3>

The raidplaner supports custom group sizes. If your game does not support the common World of Warcraft group sizes (5,10,25) or you want to disallow/add a size you need to change this array.<br>
The $s_GroupSizes array needs to define the size of the group as key and the default slot size for each role as value. So if you have 5 roles and want to define a new group with 20 players, it will look like this:<br>
<br>
<pre><code>$s_GroupSizes = Array(<br>
    20  =&gt; Array(4,4,4,4,4),<br>
    // ...<br>
</code></pre>