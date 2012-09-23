<?php
    function msgRaidDetail( $Request )
    {
        if (ValidUser())
        {
            echo "<show>".$Request["showPanel"]."</show>";
           
            $Connector = Connector::GetInstance();
            
            $ListRaidSt = $Connector->prepare("Select ".RP_TABLE_PREFIX."Raid.*, ".RP_TABLE_PREFIX."Location.Name AS LocationName, ".RP_TABLE_PREFIX."Location.Image AS LocationImage, ".
                                              RP_TABLE_PREFIX."Attendance.UserId, ".RP_TABLE_PREFIX."Attendance.CharacterId, ".RP_TABLE_PREFIX."Attendance.Status, ".RP_TABLE_PREFIX."Attendance.Role, ".RP_TABLE_PREFIX."Attendance.Comment, ".
                                              RP_TABLE_PREFIX."Character.Name, ".RP_TABLE_PREFIX."Character.Class, ".RP_TABLE_PREFIX."Character.Mainchar, ".RP_TABLE_PREFIX."Character.Role1, ".RP_TABLE_PREFIX."Character.Role2, ".
                                              "UNIX_TIMESTAMP(".RP_TABLE_PREFIX."Raid.Start) AS StartUTC, ".
                                              "UNIX_TIMESTAMP(".RP_TABLE_PREFIX."Raid.End) AS EndUTC ".
                                              "FROM `".RP_TABLE_PREFIX."Raid` ".
                                              "LEFT JOIN `".RP_TABLE_PREFIX."Location` USING(LocationId) ".
                                              "LEFT JOIN `".RP_TABLE_PREFIX."Attendance` USING(RaidId) ".
                                              "LEFT JOIN `".RP_TABLE_PREFIX."Character` USING(CharacterId) ".
                                              "WHERE RaidId = :RaidId");
            
            $ListRaidSt->bindValue( ":RaidId", $Request["id"], PDO::PARAM_INT );
            
            if (!$ListRaidSt->execute())
            {
                postErrorMessage( $ListRaidSt );
            }
            else
            {
                echo "<raid>";
                
                $Data = $ListRaidSt->fetch( PDO::FETCH_ASSOC );
                
                $Participants = Array();
                
                $StartDate = getdate($Data["StartUTC"]);
                $EndDate   = getdate($Data["EndUTC"]);
                
                echo "<raidId>".$Data["RaidId"]."</raidId>";
                echo "<locationId>".$Data["LocationId"]."</locationId>";
                echo "<location>".$Data["LocationName"]."</location>";
                echo "<stage>".$Data["Stage"]."</stage>";
                echo "<mode>".$Data["Mode"]."</mode>";
                echo "<image>".$Data["LocationImage"]."</image>";
                echo "<size>".$Data["Size"]."</size>";
                echo "<startDate>".$StartDate["year"]."-".LeadingZero10($StartDate["mon"])."-".LeadingZero10($StartDate["mday"])."</startDate>";
                echo "<start>".LeadingZero10($StartDate["hours"]).":".LeadingZero10($StartDate["minutes"])."</start>";
                echo "<end>".LeadingZero10($EndDate["hours"]).":".LeadingZero10($EndDate["minutes"])."</end>";
                echo "<description>".$Data["Description"]."</description>";
                echo "<slots>";
                echo "<required>".$Data["SlotsRole1"]."</required>";
                echo "<required>".$Data["SlotsRole2"]."</required>";
                echo "<required>".$Data["SlotsRole3"]."</required>";
                echo "<required>".$Data["SlotsRole4"]."</required>";
                echo "<required>".$Data["SlotsRole5"]."</required>";
                echo "</slots>";
            
                if ( $Data["UserId"] != NULL )
                {
                    do
                    {
                        if ( $Data["UserId"] != 0 )
                        {
                            array_push( $Participants, intval($Data["UserId"]) );
                        }
                            
                        if ( $Data["CharacterId"] == 0 )
                        {
                            // CharacterId is 0 on random players or players that are absent
                                                
                            if ( $Data["UserId"] != 0 )
                            {
                                // Fetch the mainchar of the registered player and display this
                                // character as "absent"
                                
                                $CharSt = $Connector->prepare(  "SELECT ".RP_TABLE_PREFIX."Character.*, ".RP_TABLE_PREFIX."User.Login AS UserName ".
                                                                "FROM `".RP_TABLE_PREFIX."User` LEFT JOIN `".RP_TABLE_PREFIX."Character` USING(UserId) ".
                                                                "WHERE UserId = :UserId ORDER BY Mainchar, CharacterId ASC LIMIT 1" );
                            
                                $CharSt->bindValue( ":UserId", $Data["UserId"], PDO::PARAM_INT );
                                
                                if (!$CharSt->execute())
                                {
                                    postErrorMessage( $ErrorInfo );
                                }
                                else
                                {
                                    $CharData = $CharSt->fetch( PDO::FETCH_ASSOC );
                                    
                                    if ( $CharData["CharacterId"] != NULL )
                                    {
                                        echo "<attendee>";
                                        
                                        echo "<id>".$CharData["CharacterId"]."</id>";
                                        echo "<name>".$CharData["Name"]."</name>";
                                        echo "<mainchar>".$CharData["Mainchar"]."</mainchar>";
                                        echo "<class>".$CharData["Class"]."</class>";
                                        echo "<role>".$CharData["Role1"]."</role>";
                                        echo "<role1>".$CharData["Role1"]."</role1>";
                                        echo "<role2>".$CharData["Role2"]."</role2>";
                                        echo "<status>".$Data["Status"]."</status>";
                                        echo "<comment>".$Data["Comment"]."</comment>";
                                        
                                        echo "</attendee>";                      
                                    }
                                    // else {
                                    // Character has been deleted or player has no character.
                                    // This character does not need to be displayed. }                                                     
                                }
                            
                                $CharSt->closeCursor();
                            }
                            else
                            {
                                // CharacterId and UserId set to 0 means "random player"
                                
                                echo "<attendee>";
                                
                                echo "<id>0</id>";
                                echo "<name>".$Data["Comment"]."</name>";
                                echo "<class>random</class>";
                                echo "<mainchar>true</mainchar>";
                                echo "<role>".$Data["Role"]."</role>";
                                echo "<role1>".$Data["Role"]."</role1>";
                                echo "<role2>".$Data["Role"]."</role2>";
                                echo "<status>".$Data["Status"]."</status>";
                                echo "<comment></comment>";
                                
                                echo "</attendee>";
                            } 
                        }
                        else
                        {
                            // CharacterId is set
                            
                            echo "<attendee>";
                            
                            echo "<id>".$Data["CharacterId"]."</id>";
                            echo "<name>".$Data["Name"]."</name>";
                            echo "<class>".$Data["Class"]."</class>";
                            echo "<mainchar>".$Data["Mainchar"]."</mainchar>";
                            echo "<role>".$Data["Role"]."</role>";
                            echo "<role1>".$Data["Role1"]."</role1>";
                            echo "<role2>".$Data["Role2"]."</role2>";
                            echo "<status>".$Data["Status"]."</status>";
                            echo "<comment>".$Data["Comment"]."</comment>";
                            
                            echo "</attendee>";
                        }                        
                    }
                    while ( $Data = $ListRaidSt->fetch( PDO::FETCH_ASSOC ) );
                }
                
                $AllUsersSt = $Connector->prepare(  "SELECT ".RP_TABLE_PREFIX."Character.*, ".RP_TABLE_PREFIX."User.UserId ".
                                                    "FROM `".RP_TABLE_PREFIX."User` LEFT JOIN `".RP_TABLE_PREFIX."Character` USING(UserId) ".
                                                    "WHERE Mainchar = \"true\" AND `Group` != \"none\"" );
                
                $AllUsersSt->execute();
                
                while ( $User = $AllUsersSt->fetch( PDO::FETCH_ASSOC ) )
                {
                    if ( !in_array( intval($User["UserId"]), $Participants ) )
                    {
                        echo "<attendee>";
                        
                        echo "<id>".$User["CharacterId"]."</id>";
                        echo "<name>".$User["Name"]."</name>";
                        echo "<class>".$User["Class"]."</class>";
                        echo "<mainchar>true</mainchar>";
                        echo "<role>".$User["Role1"]."</role>";
                        echo "<role1>".$User["Role1"]."</role1>";
                        echo "<role2>".$User["Role2"]."</role2>";
                        echo "<status>undecided</status>";
                        echo "<comment></comment>";
                        
                        echo "</attendee>";
                    }
                }
                
                $AllUsersSt->closeCursor();                
                echo "</raid>";                            
            }
            
            $ListRaidSt->closeCursor();
            
            echo "<locations>";
            
            msgQueryLocations( $Request );
            
            echo "</locations>";
        }
        else
        {
            echo "<error>".L("AccessDenied")."</error>";
        }
    }
?>