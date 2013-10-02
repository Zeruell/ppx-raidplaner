<?php

function msgQuerySettings( $aRequest )
{
    $Out = Out::getInstance();
        
    if ( validAdmin() )
    {
        $Connector = Connector::getInstance();

        // Pass through parameter

        $Out->pushValue("show", $aRequest["showPanel"]);

        // Load users

        $UserSt = $Connector->prepare("Select * FROM `".RP_TABLE_PREFIX."User` ORDER BY Login, `Group`");

        if ( !$UserSt->execute() )
        {
            postErrorMessage( $UserSt );
        }
        else
        {
            $Users = Array();
            
            while ( $Data = $UserSt->fetch( PDO::FETCH_ASSOC ) )
            {
                $UserData = Array(
                    "id"            => $Data["UserId"],
                    "login"         => xmlentities( $Data["Login"], ENT_COMPAT, "UTF-8" ),
                    "bindingActive" => $Data["BindingActive"],
                    "binding"       => $Data["ExternalBinding"],
                    "group"         => $Data["Group"]
                );
                
                array_push($Users, $UserData);
            }
            
            $Out->pushValue("user", $Users);
        }

        $UserSt->closeCursor();

        // Load settings

        $SettingSt = $Connector->prepare("Select * FROM `".RP_TABLE_PREFIX."Setting` ORDER BY Name");

        if ( !$SettingSt->execute() )
        {
            postErrorMessage( $SettingSt );
        }
        else
        {
            $Settings = Array();
            
            while ( $Data = $SettingSt->fetch( PDO::FETCH_ASSOC ) )
            {
                $SettingData = Array(
                    "name"      => $Data["Name"],
                    "intValue"  => $Data["IntValue"],
                    "textValue" => $Data["TextValue"]
                );
                
                array_push($Settings, $SettingData);
            }
            
            $Out->pushValue("setting", $Settings);
        }

        $SettingSt->closeCursor();

        // Load themes

        $ThemeFiles = scandir( "../images/themes" );
        $Themes = Array();

        foreach ( $ThemeFiles as $ThemeFileName )
        {
            if (strpos($ThemeFileName,".") > 0)
            {
                $Theme = new SimpleXMLElement( file_get_contents("../images/themes/".$ThemeFileName) );
                $SimpleThemeFileName = substr($ThemeFileName, 0, strrpos($ThemeFileName, "."));
                
                if ($Theme->name != "")
                    $ThemeName = $Theme->name;
                else
                    $ThemeName = str_replace("_", " ", $SimpleThemeFileName);
    
                array_push($Themes, Array(
                    "name" => $ThemeName,
                    "file" => $SimpleThemeFileName
                ));
            }
        }
        
        $Out->pushValue("theme", $Themes);

        // Query number of raids

        $NumRaids = 0;
        $Raids = $Connector->prepare( "SELECT COUNT(*) AS `NumberOfRaids` FROM `".RP_TABLE_PREFIX."Raid` WHERE Start < FROM_UNIXTIME(:Now)" );
        $Raids->bindValue( ":Now", time(), PDO::PARAM_INT );

        if ( !$Raids->execute() )
        {
            postErrorMessage( $Raids );
        }
        else
        {
            $Data = $Raids->fetch( PDO::FETCH_ASSOC );
            $NumRaids = $Data["NumberOfRaids"];
        }

        $Raids->closeCursor();
        $Out->pushValue("numRaids", $NumRaids);

        // Query attendance

        $Attendance = $Connector->prepare("SELECT `".RP_TABLE_PREFIX."Character`.Name, `".RP_TABLE_PREFIX."Attendance`.Status, `".RP_TABLE_PREFIX."User`.UserId, COUNT(*) AS Count ".
                                           "FROM `".RP_TABLE_PREFIX."User` LEFT JOIN `".RP_TABLE_PREFIX."Attendance` USING(UserId) ".
                                           "LEFT JOIN `".RP_TABLE_PREFIX."Raid` USING(RaidId) LEFT JOIN `".RP_TABLE_PREFIX."Character` USING(UserId) ".
                                           "WHERE `".RP_TABLE_PREFIX."Character`.Mainchar = 'true' AND `".RP_TABLE_PREFIX."Raid`.Start > `".RP_TABLE_PREFIX."User`.Created AND `".RP_TABLE_PREFIX."Raid`.Start < FROM_UNIXTIME(:Now) ".
                                           "GROUP BY UserId, Status ORDER BY Name" );

        $Attendance->bindValue( ":Now", time(), PDO::PARAM_INT );

        if ( !$Attendance->execute() )
        {
            postErrorMessage( $Attendance );
        }
        else
        {
            $UserId = 0;
            $MainCharName = "";
            $StateCounts = array( "available" => 0, "unavailable" => 0, "ok" => 0 );
            
            $Attendances = Array();

            while ( $Data = $Attendance->fetch( PDO::FETCH_ASSOC ) )
            {
                if ( $UserId == 0 )
                {
                    $UserId = $Data["UserId"];
                    $MainCharName = $Data["Name"];
                }
                else if ( $UserId != $Data["UserId"] )
                {
                    $AttendanceData = Array(
                        "id"          => $UserId,
                        "name"        => $MainCharName,
                        "ok"          => $StateCounts["ok"],
                        "available"   => $StateCounts["available"],
                        "unavailable" => $StateCounts["unavailable"],
                    );
                    
                    array_push($Attendances, $AttendanceData);
                    
                    $StateCounts["ok"] = 0;
                    $StateCounts["available"] = 0;
                    $StateCounts["unavailable"] = 0;
                    $UserId = $Data["UserId"];
                    $MainCharName = $Data["Name"];
                }

                if ( $Data["Status"] != "undecided" )
                    $StateCounts[$Data["Status"]] += $Data["Count"];
            }

            if ($UserId != 0)
            {
                $AttendanceData = Array(
                    "id"          => $UserId,
                    "name"        => $MainCharName,
                    "ok"          => $StateCounts["ok"],
                    "available"   => $StateCounts["available"],
                    "unavailable" => $StateCounts["unavailable"]
                );
                
                array_push($Attendances, $AttendanceData);
            }
            
            $Out->pushValue("attendance", $Attendances);
        }

        $Attendance->closeCursor();

        // Locations

        msgQueryLocations( $aRequest );
    }
    else
    {
        $Out->pushError(L("AccessDenied"));
    }
}

?>