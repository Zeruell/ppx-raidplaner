# Introduction #

As of Version 0.8.12 there is a setup script, located in the folder "setup". You can run this script to create your database and config files.<br>
If you've got problems with the script you can follow the instructions below to configure the raidplaner by yourself.<br>
<br>
You need the following services to install the raidplaner:<br>
<ul><li>MySQL 4.1 or better<br>
</li><li>PHP 5.2 or better with mcrypt<br>
</li><li>An Apache webserver (no other servers were tested)</li></ul>

<h1>Upgrade instructions</h1>

<ol><li><b>Backup your current files and database!</b>
</li><li>Copy the contents of the new package to your FTP. Make sure that your "lib/config" folder is not overwritten<br>
</li><li>Open http://<your url>/setup, click on "update" and follow the instructions</li></ol>

If you've used an intermediate version of Raidplaner, there might be some error messages during the update process. As the update script tries to take partially updated installations into account you can ignore these in almost all cases.<br>
However - if something went wrong it will help me to know which update steps failed, so it is advisable to keep the messages somewhere (e.g. make a screenshot).<br>
<br>
<h1>Manual setup instructions</h1>

<b>It is highly recommended to use the setup tool to install the raidplaner.</b>

<ol><li>Copy the unzipped data in a separate folder on your server, e.g. "raid".<br>
</li><li>Import the database default layout (from the download section) into your database. A good tool for this is the import function from phpMyAdmin. All tables will be preceded by "raid<code>_</code>" so you can import everything into a database already in use by other systems (e.g. PhpBB3)<br>
</li><li>Create (or edit) the config files located in "lib/config" to match your setup. The files are listed in the section below.<br>
</li><li>Login with the user "admin" and the password "root".</li></ol>

The following defines can be made in each of the files listed below, e.g.<br>
<pre><code>&lt;?php<br>
	define("SQL_HOST", "localhost");<br>
	define("RP_DATABASE", "raid");<br>
	define("RP_USER", "root");<br>
	define("RP_PASS", "root");<br>
	define("RP_TABLE_PREFIX", "raid_");<br>
	define("ALLOW_REGISTRATION", false);<br>
	define("USE_CLEARTEXT_PASSWORDS", false);<br>
?&gt;<br>
</code></pre>

<h1>Config.php</h1>

<b>SQL_HOST</b> refers to the server your database is running on, e.g. "mysqlhost.myhoster.com".<br>
<br>
<b>RP_DATABASE</b> refers to the database you imported the default layout into, e.g. "mytable".<br>
<br>
<b>RP_USER</b> refers to the mysql user that has access to the RP_TABLE, e.g. "root".<br>
<br>
<b>RP_PASS</b> refers to the password of the given RP_USER.<br>
<br>
<b>RP_TABLE_PREFIX</b> should not be changed unless you renamed all tables to use another prefix.<br>
The default prefix is "raid<i>".</i>

<b>ALLOW_REGISTRATION</b> should be set to "false" if you do not want users to register manually.<br>
This only makes sense if your raidplaner is connected to e.g. a PhpBB3 forum.<br>
<br>
<b>USE_CLEARTEXT_PASSWORDS</b> should be set to "false" to improve security for non-https connections. You may want to set this to "true" to fix slow logins or problems with certain eqdkp configurations.<br>
<br>
<h1>config.phpbb3.php</h1>

<b>PHPBB3_BINDING</b> should be set to "false" if the raidplaner is not bound to a PhpBB3 Forum.<br>
If you do that, ALLOW_REGISTRATION in the config.php should be set to "true".<br>
<br>
<b>PHPBB3_DATABASE</b> refers to the database holding your PhpBB3 forum, e.g. "mytable".<br>
<br>
<b>PHPBB3_USER</b> refers to the mysql user that has access to the PHPBB3_DATABASE, e.g. "root".<br>
<br>
<b>PHPBB3_PASS</b> refers to the password of the given PHPBB3_USER.<br>
<br>
<b>PHPBB3_TABLE_PREFIX</b> should be set to the value preceding all your PhpBB3 tables, e.g. "phpbb<i>".<br>
Use an empty string if your tables have no prefix.</i>

<b>PHPBB3_MEMBER_GROUPS</b> Should be a comma separated list of PhpBB3 group ids (e.g. "2, 3, 4").<br>
Users within these groups will be automatically assigned the "member" status if they login the first time.<br>
<br>
<b>PHPBB3_RAIDLEAD_GROUPS</b> Should be a comma separated list of  PhpBB3 group ids (e.g. "1").<br>
Users within these groups will be automatically assigned the "raidlead" status if they login the first time.<br>
<br>
You can find your PhpBB3 group ids in the table "phpbb_groups". The Id needed is from the column "group_id".<br>
<br>
<h1>config.eqdkp.php</h1>

Same as phpbb3 but with "EQDKP<i>" instead of "PHPBB3</i>".<br>
"<i>MEMBER_GROUPS" and "</i>RAIDLEAD_GROUPS" are not used for this binding.<br>
<br>
<h1>config.vb3.php</h1>

Same as phpbb3 but with "VB3<i>" instead of "PHPBB3</i>".<br>
<br>
You can find your vBulletin3 group ids in the table "vb_usergroup". The Id needed is from the column "usergroupid".<br>
<br>
<h1>config.mybb.php</h1>

Same as phpbb3 but with "MYBB<i>" instead of "PHPBB3</i>".<br>
<br>
You can find your mybb group ids in the table "mybb_usergroups". The Id needed is from the column "gid".<br>
<br>
<h1>config.smf.php</h1>

Same as phpbb3 but with "SMF<i>" instead of "PHPBB3</i>".<br>
<br>
You can find your mybb group ids in the table "smf_membergroups". The Id needed is from the column "id_group".