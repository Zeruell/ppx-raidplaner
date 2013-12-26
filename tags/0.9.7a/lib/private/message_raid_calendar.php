<?php

function prepareRaidListRequest( $Month, $Year )
{
    $StartDateTime = mktime(0, 0, 0, intval($Month), 1, intval($Year));
    $StartDate = getdate( $StartDateTime );

    if ( $StartDate["wday"] != 1 )
    {
        $StartDateTime = strtotime("previous monday", $StartDateTime);
        $StartDate = getdate( $StartDateTime );
    }

    $EndDateTime = strtotime("+6 weeks", $StartDateTime);
    $EndDate = getdate( $EndDateTime );

    $listRequest["StartDay"]     = $StartDate["mday"];
    $listRequest["StartMonth"]   = $StartDate["mon"];
    $listRequest["StartYear"]    = $StartDate["year"];

    $listRequest["EndDay"]       = $EndDate["mday"];
    $listRequest["EndMonth"]     = $EndDate["mon"];
    $listRequest["EndYear"]      = $EndDate["year"];

    $listRequest["DisplayMonth"] = $Month-1;
    $listRequest["DisplayYear"]  = $Year;

    return $listRequest;
}

// -----------------------------------------------------------------------------

function parseRaidQuery( $QueryResult, $Limit )
{
    global $s_Roles;
    echo "<raids>";

    $RaidData = Array();
    $RaidInfo = Array();

    while ($Data = $QueryResult->fetch( PDO::FETCH_ASSOC ))
    {
        array_push($RaidData, $Data);

        // Create used slot counts

        if ( !isset($RaidInfo[$Data["RaidId"]]) )
        {
            for ( $i=0; $i < sizeof($s_Roles); ++$i )
            {
                $RaidInfo[$Data["RaidId"]]["role".$i] = 0;
            }

            $RaidInfo[$Data["RaidId"]]["bench"] = 0;
        }

        // Count used slots

        if ( ($Data["Status"] == "ok") ||
             ($Data["Status"] == "available") )
        {
            ++$RaidInfo[$Data["RaidId"]]["role".$Data["Role"]];
        }
    }

    $LastRaidId = -1;
    $RaidDataCount = sizeof($RaidData);

    $NumRaids = 0;

    for ( $DataIdx=0; $DataIdx < $RaidDataCount; ++$DataIdx )
    {
        $Data = $RaidData[$DataIdx];

        if ( $LastRaidId != $Data["RaidId"] )
        {
            // If no user assigned for this raid
            // or row belongs to this user
            // or it's the last entry
            // or the next entry is a different raid

            $IsCorrectUser = $Data["UserId"] == UserProxy::GetInstance()->UserId;

            if ( ($IsCorrectUser) ||
                 ($Data["UserId"] == NULL) ||
                 ($DataIdx+1 == $RaidDataCount) ||
                 ($RaidData[$DataIdx+1]["RaidId"] != $Data["RaidId"]) )
            {
                $status = "notset";
                $attendanceIndex = 0;
                $role = "";
                $comment = "";

                if ( $IsCorrectUser )
                {
                    $status = $Data["Status"];
                    $attendanceIndex = ($status == "unavailable") ? -1 : intval($Data["CharacterId"]);
                    $role = $Data["Role"];
                    $comment = $Data["Comment"];
                }

                $StartDate = getdate($Data["StartUTC"]);
                $EndDate   = getdate($Data["EndUTC"]);

                echo "<raid>";

                echo "<id>".$Data["RaidId"]."</id>";
                echo "<location>".$Data["Name"]."</location>";
                echo "<stage>".$Data["Stage"]."</stage>";
                echo "<size>".$Data["Size"]."</size>";
                echo "<startDate>".$StartDate["year"]."-".LeadingZero10($StartDate["mon"])."-".LeadingZero10($StartDate["mday"])."</startDate>";
                echo "<start>".LeadingZero10($StartDate["hours"]).":".LeadingZero10($StartDate["minutes"])."</start>";
                echo "<end>".LeadingZero10($EndDate["hours"]).":".LeadingZero10($EndDate["minutes"])."</end>";
                echo "<image>".$Data["Image"]."</image>";
                echo "<description>".$Data["Description"]."</description>";
                echo "<status>".$status."</status>";
                echo "<attendanceIndex>".$attendanceIndex."</attendanceIndex>";
                echo "<comment>".$comment."</comment>";
                echo "<role>".$role."</role>";

                for ( $i=0; $i < sizeof($s_Roles); ++$i )
                {
                    echo "<role".$i."Slots>".$Data["SlotsRole".($i+1)]."</role".$i."Slots>";
                    echo "<role".$i.">".$RaidInfo[$Data["RaidId"]]["role".$i]."</role".$i.">";
                }

                echo "</raid>";

                $LastRaidId = $Data["RaidId"];
                ++$NumRaids;

                if ( ($Limit > 0) && ($NumRaids == $Limit) )
                    break;
            }
        }
    }

    echo "</raids>";
}

// -----------------------------------------------------------------------------

function msgRaidCalendar( $Request )
{
    if (ValidUser())
    {
        $Connector = Connector::GetInstance();

        $ListRaidSt = $Connector->prepare(  "Select ".RP_TABLE_PREFIX."Raid.*, ".RP_TABLE_PREFIX."Location.*, ".
                                            RP_TABLE_PREFIX."Attendance.CharacterId, ".RP_TABLE_PREFIX."Attendance.UserId, ".
                                             RP_TABLE_PREFIX."Attendance.Status, ".RP_TABLE_PREFIX."Attendance.Role, ".RP_TABLE_PREFIX."Attendance.Comment, ".
                                            "UNIX_TIMESTAMP(".RP_TABLE_PREFIX."Raid.Start) AS StartUTC, ".
                                            "UNIX_TIMESTAMP(".RP_TABLE_PREFIX."Raid.End) AS EndUTC ".
                                            "FROM `".RP_TABLE_PREFIX."Raid` ".
                                            "LEFT JOIN `".RP_TABLE_PREFIX."Location` USING(LocationId) ".
                                            "LEFT JOIN `".RP_TABLE_PREFIX."Attendance` USING (RaidId) ".
                                            "LEFT JOIN `".RP_TABLE_PREFIX."Character` USING (CharacterId) ".
                                            "WHERE ".RP_TABLE_PREFIX."Raid.Start >= FROM_UNIXTIME(:Start) AND ".RP_TABLE_PREFIX."Raid.Start <= FROM_UNIXTIME(:End) ".
                                            "ORDER BY ".RP_TABLE_PREFIX."Raid.Start, ".RP_TABLE_PREFIX."Raid.RaidId" );

        $StartDateTime = mktime(0, 0, 0, intval($Request["StartMonth"]), intval($Request["StartDay"]), intval($Request["StartYear"]));
        $EndDateTime   = mktime(0, 0, 0, intval($Request["EndMonth"]), intval($Request["EndDay"]), intval($Request["EndYear"]));

        $ListRaidSt->bindValue(":Start",  $StartDateTime, PDO::PARAM_INT);
        $ListRaidSt->bindValue(":End",    $EndDateTime,   PDO::PARAM_INT);

        if (!$ListRaidSt->execute())
        {
            postErrorMessage( $ListRaidSt );
        }
        else
        {
            $_SESSION["Calendar"]["month"] = intval( $Request["DisplayMonth"] );
            $_SESSION["Calendar"]["year"]  = intval( $Request["DisplayYear"] );

            echo "<startDay>".$Request["StartDay"]."</startDay>";
            echo "<startMonth>".$Request["StartMonth"]."</startMonth>";
            echo "<startYear>".$Request["StartYear"]."</startYear>";
            echo "<displayMonth>".$Request["DisplayMonth"]."</displayMonth>";
            echo "<displayYear>".$Request["DisplayYear"]."</displayYear>";

            parseRaidQuery( $ListRaidSt, 0 );
        }

        $ListRaidSt->closeCursor();
    }
    else
    {
        echo "<error>".L("AccessDenied")."</error>";
    }
}
?>