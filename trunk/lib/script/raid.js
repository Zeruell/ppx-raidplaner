// -----------------------------------------------------------------------------
//  CRaidMemberList
// -----------------------------------------------------------------------------

function CRaidMemberList()
{
    this.players = Array();
    this.nextRandomId = -1;
    
    // -------------------------------------------------------------------------
    
    this.AddPlayer = function( a_PlayerXML ) 
    {
        var newPlayer = {
            id         : parseInt(a_PlayerXML.children("id:first").text()),
            name       : a_PlayerXML.children("name:first").text(),
            class      : a_PlayerXML.children("class:first").text(),
            mainchar   : a_PlayerXML.children("mainchar:first").text(),
            activeRole : parseInt( a_PlayerXML.children("role:first").text() ),
            firstRole  : parseInt( a_PlayerXML.children("role1:first").text() ),
            secondRole : parseInt( a_PlayerXML.children("role2:first").text() ),
            status     : a_PlayerXML.children("status:first").text(),
            comment    : a_PlayerXML.children("comment:first").text()
        };
        
        this.players.push(newPlayer);
        
        this.nextRandomId = Math.min(newPlayer.id, this.nextRandomId);      
    }
    
    // -----------------------------------------------------------------------------
    
    this.AddRandomPlayer = function( a_RoleIdx )
    {
        var newPlayer = {
            id         : this.nextRandomId,
            name       : "Random",
            class      : "random",
            mainchar   : true,
            activeRole : a_RoleIdx,
            firstRole  : a_RoleIdx,
            secondRole : a_RoleIdx,
            status     : "ok",
            comment    : ""
        };
        
        --this.nextRandomId;
        this.players.push(newPlayer);
        this.UpdateRoleList( a_RoleIdx );
    }
    
    // -------------------------------------------------------------------------
    
    this.ForEachPlayer = function( a_Callback )
    {
        for ( var pIdx=0; pIdx<this.players.length; ++pIdx )
        {
            a_Callback( this.players[pIdx] );
        }
    }
    
    // -------------------------------------------------------------------------
    
    this.ForEachPlayerWithRole = function( a_Role, a_Callback )
    {
        for ( var pIdx=0; pIdx<this.players.length; ++pIdx )
        {
            if ( this.players[pIdx].activeRole == a_Role )
            {
                a_Callback( this.players[pIdx] );
            }
        }
    }
    
    // -------------------------------------------------------------------------
    
    this.GetPlayerIndex = function( a_PlayerId )
    {
        for ( var pIdx=0; pIdx<this.players.length; ++pIdx )
        {
            if ( this.players[pIdx].id == a_PlayerId )
            {
                return pIdx;
            }
        }
    
        return -1;
    }
    
    // -------------------------------------------------------------------------
    
    this.ChangePlayerName = function( a_PlayerId, a_Name )
    {
        var pIdx = this.GetPlayerIndex( a_PlayerId );
        this.players[pIdx].name = a_Name;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayPlayerSlot = function( a_Player, a_ClipStatus )
    {
        var HTMLString  = "";        
        var layoutClass = ((a_ClipStatus.clipItemCount % a_ClipStatus.colsPerClip) == 0) ? "break" : "nobreak";
        
        HTMLString += "<div class=\"activeSlot "+layoutClass+"\" id=\"sp"+a_Player.id+"\">";
        HTMLString += "<div class=\"playerIcon\" style=\"background-image: url(images/classessmall/"+a_Player.class+".png)\"></div>";
        
        if ( a_Player.class == "random" )
            HTMLString += "<input class=\"editableName\" type=\"test\" value=\"" + a_Player.name + "\"/>";
        else
            HTMLString += "<div class=\"playerName\">" + a_Player.name + "</div>";
        
        HTMLString += "</div>";
        
        HTMLString += this.UpdateClipStatus( a_ClipStatus );        
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayWaitSlot = function( a_Player, a_ClipStatus )
    {
        var HTMLString = "";
        var layoutClass = ((a_ClipStatus.clipItemCount % a_ClipStatus.colsPerClip) == 0) ? "break" : "nobreak";
        
        HTMLString += "<div class=\"waitSlot "+layoutClass+"\" id=\"sp"+a_Player.id+"\">";
        HTMLString += "<div class=\"playerIcon\" style=\"background-image: url(images/classessmall/"+a_Player.class+".png)\"></div>";
        HTMLString += "<div class=\"playerName\">" + a_Player.name + "</div>";
        HTMLString += "</div>";
        
        HTMLString += this.UpdateClipStatus( a_ClipStatus );        
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayBenchSlot = function( a_Player, a_ClipStatus )
    {
        var HTMLString = "";
        var layoutClass = ((a_ClipStatus.clipItemCount % a_ClipStatus.colsPerClip) == 0) ? "break" : "nobreak";
        
        HTMLString += "<div class=\"benchSlot "+layoutClass+"\" id=\"sp"+a_Player.id+"\">";
        HTMLString += "<div class=\"playerIcon\" style=\"background-image: url(images/classessmall/"+a_Player.class+".png)\"></div>";
        HTMLString += "<div class=\"playerName\">" + a_Player.name + "</div>";
        HTMLString += "</div>";
        
        HTMLString += this.UpdateClipStatus( a_ClipStatus );        
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayEmptySlot = function( a_ClipStatus )
    {
        var HTMLString = "";
        var layoutClass = ((a_ClipStatus.clipItemCount % a_ClipStatus.colsPerClip) == 0) ? "break" : "nobreak";
        
        HTMLString += "<div class=\"emptySlot "+layoutClass+"\">";
        HTMLString += "<div class=\"playerIcon\" style=\"background-image: url(images/classessmall/empty.png)\"></div>";
        HTMLString += "</div>";
               
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayAddRandomSlot = function( a_ClipStatus )
    {
        var HTMLString = "";
        var layoutClass = ((a_ClipStatus.clipItemCount % a_ClipStatus.colsPerClip) == 0) ? "break" : "nobreak";
        
        HTMLString += "<div class=\"randomSlot "+layoutClass+"\">";
        HTMLString += "</div>";
               
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.UpdateClipStatus = function( a_ClipStatus, a_Additional )
    {
        var HTMLString = "";
        var maxNumRows = 9;
        
        ++a_ClipStatus.itemCount;
        ++a_ClipStatus.clipItemCount;
        
        if ( a_ClipStatus.clipItemCount == ((a_ClipStatus.colsPerClip*maxNumRows)-1) )
        {
            var layoutClass = (a_ClipStatus.colsPerClip == 1) ? "break" : "nobreak";
            
            HTMLString += "<div class=\"clipchange "+layoutClass+"\" onclick=\"raidShowClip('role"+a_ClipStatus.roleId+"clip"+(a_ClipStatus.currentId+1)+"')\"></div>";
            HTMLString += "</div>";
            HTMLString += "<div class=\"clip\" id=\"role"+a_ClipStatus.roleId+"clip"+(a_ClipStatus.currentId+1)+"\">";
            HTMLString += "<div class=\"clipchange break\" onclick=\"raidShowClip('role"+a_ClipStatus.roleId+"clip"+a_ClipStatus.currentId+"')\"></div>";
            
            ++a_ClipStatus.currentId;
            a_ClipStatus.clipItemCount = 1;
        }
                
        return HTMLString;
    }
    
    // -------------------------------------------------------------------------
    
    this.DisplayRole = function( a_RoleId, a_Columns, a_RequiredSlots )
    {
        var HTMLString = "<div class=\"roleList\">";
        
        HTMLString += "<h2>"+g_RoleNames[g_RoleIdents[a_RoleId]]+"</h2>";
        HTMLString += "<div class=\"clip\" id=\"role"+a_RoleId+"clip0\">";
        
        var clipStatus = {
            roleId        : a_RoleId,
            colsPerClip   : a_Columns,
            currentId     : 0,
            itemCount     : 0,
            clipItemCount : 0,
        };
        
        var self = this;
        
        // Display raiding players
        
        this.ForEachPlayerWithRole( a_RoleId, function(a_Player) {
            if ( a_Player.status == "ok" )
            {
                HTMLString += self.DisplayPlayerSlot( a_Player, clipStatus );
            }
        });
        
        // Display waiting players
        
        this.ForEachPlayerWithRole( a_RoleId, function(a_Player) {
            if ( a_Player.status == "available" )
            {
                HTMLString += self.DisplayWaitSlot( a_Player, clipStatus );
            }
        });
        
        // Display required slots
        
        var displayOnlyAddRandom = clipStatus.itemCount >= a_RequiredSlots;
        
        while ( clipStatus.itemCount < a_RequiredSlots-1 )
        {
            HTMLString += this.DisplayEmptySlot( clipStatus, false );
            HTMLString += this.UpdateClipStatus( clipStatus ); 
        }
        
        // Always add one empty slot to add randoms
        // Do not update clip status here as this can be placed where the "…"
        // normaly will be placed.
        
        if ( displayOnlyAddRandom )
            HTMLString += this.DisplayAddRandomSlot( clipStatus );
        else
            HTMLString += this.DisplayEmptySlot( clipStatus );
        
        HTMLString += "</div>";
        HTMLString += "</div>";
        
        return HTMLString;
    }

    // -----------------------------------------------------------------------------
    
    this.ShowDropTargets = function( a_PlayerId, a_Source )
    {
        var playerList = $("#raiddetail").data("players");
        var pIdx       = this.GetPlayerIndex( a_PlayerId );
        var player     = this.players[pIdx];
        var roleLists  = $("#setup").children(".roleList");
        var roleIdx    = 0;
        
        roleLists.each( function() { 
            
            if ( ((roleIdx == player.firstRole) || 
                  (roleIdx == player.secondRole)) &&
                  (roleIdx != player.activeRole) )
            {
                var currentRoleIdx = roleIdx;
                $(this).droppable({
                    drop: function() { 
                        a_Source.draggable("option", "revert", false);
                        a_Source.draggable("destroy").detach();
                        playerList.MovePlayer(a_PlayerId, currentRoleIdx);
                    }
                });
            }
            else
            {
                $(this).fadeTo(100, 0.15);
            }
            
            ++roleIdx;
        });
    }
    
    // -----------------------------------------------------------------------------
    
    this.HideDropTargets = function()
    {
        $("#setup").children(".roleList")
            .fadeTo(100, 1.0)
            .droppable("destroy");
    }
    
    // -----------------------------------------------------------------------------
    
    this.BindRaidSetup = function( a_Parent )
    {
        var clips = a_Parent.children(".clip");
        var config = a_Parent.data("config");
        var playerList = $("#raiddetail").data("players");
                
        clips.children(".waitSlot").each( function() {
            var pid = parseInt( $(this).attr("id").substr(2) );
            
            $(this).click( function() { playerList.UpgradePlayer(pid); });
            $(this).draggable({
                delay          : 100,
                revert         : true,
                revertDuration : 200,
                helper         : "clone",
                start          : function() { playerList.ShowDropTargets(pid, $(this)); },
                stop           : function() { playerList.HideDropTargets(); }
            });
        });
        
        clips.children(".activeSlot").each( function() {
            var pid = parseInt( $(this).attr("id").substr(2) );
            
            $(this).click( function() { playerList.DowngradePlayer(pid); });
            $(this).draggable({
                delay          : 100,
                revert         : true,
                revertDuration : 200,
                helper         : "clone",
                start          : function() { playerList.ShowDropTargets(pid, $(this)); },
                stop           : function() { playerList.HideDropTargets(); }
            });
        
            $(this).children(".editableName").each( function() {
                $(this)
                    .click( function(event) { event.stopPropagation(); })
                    .blur( function() { playerList.ChangePlayerName(pid, $(this).val()); });
            });
        });
        
        clips.children(".randomSlot").each( function() {
            $(this).click( function() { playerList.AddRandomPlayer(config.id); });
        });
        
        clips.children(".emptySlot").each( function() {
            $(this).click( function() { playerList.AddRandomPlayer(config.id); });
        });
    }
    
    // -------------------------------------------------------------------------
    
    this.UpdateRoleList = function( a_RoleIdx )
    {
        var roleList = $( $("#setup").children(".roleList")[a_RoleIdx] );
        var roleConf = roleList.data("config");
                
        roleList.replaceWith( this.DisplayRole(roleConf.id, roleConf.columns, roleConf.reqSlots) );
        roleList = $( $("#setup").children(".roleList")[a_RoleIdx] );
        roleList.data("config", roleConf);
        
        $("#"+roleConf.clip).show();
        
        this.BindRaidSetup( roleList );
    }
    
    // -------------------------------------------------------------------------
    
    this.UpgradePlayer = function( a_PlayerId )
    {
        var pIdx = this.GetPlayerIndex( a_PlayerId );
        this.players[pIdx].status = "ok";
        this.UpdateRoleList( this.players[pIdx].activeRole );
    }
    
    // -------------------------------------------------------------------------
    
    this.DowngradePlayer = function( a_PlayerId )
    {
        var pIdx = this.GetPlayerIndex( a_PlayerId );
        var roleIdx = this.players[pIdx].activeRole;
        
        if ( this.players[pIdx].class == "random" )
            this.players.splice(pIdx,1);
        else
            this.players[pIdx].status = "available";
        
        this.UpdateRoleList( roleIdx );
    }
    
    // -----------------------------------------------------------------------------
    
    this.MovePlayer = function( a_PlayerId, a_RoleIdx )
    {
        var pIdx     = this.GetPlayerIndex( a_PlayerId );
        var prevRole = this.players[pIdx].activeRole;
        
        this.players[pIdx].activeRole = a_RoleIdx;
        
        this.UpdateRoleList( prevRole );
        this.UpdateRoleList( a_RoleIdx );
    }
}

// -----------------------------------------------------------------------------
//  player list functions
// -----------------------------------------------------------------------------

function raidShowClip( a_ClipId )
{
    var clipToShow = $("#"+a_ClipId);
    var roleList = clipToShow.parent();
    var roleConf = roleList.data("config");
    roleConf.clip = a_ClipId;
    
    roleList.children(".clip").hide();
    clipToShow.show();
}

// -----------------------------------------------------------------------------
//  display functions
// -----------------------------------------------------------------------------

function displayRaidInfo( a_RaidXML, a_AppendTo )
{
    var MonthArray = Array(L("January"), L("February"), L("March"), L("April"), L("May"), L("June"), L("July"), L("August"), L("September"), L("October"), L("November"), L("December"));
    
    var raidImage = a_RaidXML.children("image:first").text();
    var raidName  = a_RaidXML.children("location:first").text();
    var raidSize  = 0;
    var numRoles  = Math.min( g_RoleNames.length, 5 );
    
    a_RaidXML.children("slots").children("required").each( function() {
       raidSize += parseInt($(this).text());
    });
        
    var startDate = a_RaidXML.children("startDate:first").text().split("-");
    var endDate   = a_RaidXML.children("endDate:first").text().split("-");
    
    var startTime = a_RaidXML.children("start:first").text();
    var endTime   = a_RaidXML.children("end:first").text();
    
    var HTMLString = "<div class=\"raidinfo\">";
    HTMLString += "<img src=\"images/raidbig/" + raidImage + "\" class=\"raidicon\">";
    HTMLString += "<div class=\"raidname\">" + raidName + "</div>";
    HTMLString += "<div class=\"raidsize\">" + raidSize + " " + L("Players") + "</div>";
    HTMLString += "<div class=\"datetime\">" + parseInt(startDate[2]) + ". " + MonthArray[startDate[1]-1] + ", ";
    HTMLString += startTime + " - " + endTime + "</div>";
    HTMLString += "</div>";
    
    $("#"+a_AppendTo).prepend(HTMLString);
}

// -----------------------------------------------------------------------------

function displayRaidSetup( a_RaidXML )
{
    var playerList = $("#raiddetail").data("players");
    var numRoles   = Math.min( g_RoleNames.length, 5 );
    var roleCounts = a_RaidXML.children("slots:first");
    
    var HTMLString = "<div id=\"setup\"></div>";    
    $("#raiddetail").append(HTMLString);
    
    for ( var i=0; i<numRoles; ++i )
    {
        var numCols  = (i<numRoles-1) ? 1 : 6-i;
        var required = parseInt(roleCounts.children("required").eq(i).text());
        
        HTMLString = playerList.DisplayRole(i, numCols, required);
        $("#setup").append(HTMLString);
        $("#setup").children(":last").data("config", {
            id       : i,
            columns  : numCols,
            reqSlots : required,
            clip     : "role"+i+"clip0",
        });
    }
    
    $("#setup").children(".roleList").each( function() { 
        $(this).children(".clip:first").show();
    });

    playerList.BindRaidSetup( $("#setup").children(".roleList") );    
    displayRaidInfo( a_RaidXML, "setup" );
}

// -----------------------------------------------------------------------------

function displayRaidSlackers( a_RaidXML )
{
    var HTMLString = "<div id=\"slackers\">";
    HTMLString += "</div>";
    
    $("#raiddetail").append(HTMLString);
    
    displayRaidInfo( a_RaidXML, "slackers" );
}

// -----------------------------------------------------------------------------

function displayRaidSettings( a_MessageXML )
{
    var HTMLString = "<div id=\"setup\">";
    HTMLString += "</div>";
    
    $("#raiddetail").append(HTMLString);
}

// -----------------------------------------------------------------------------

function displayRaid( a_XMLData )
{
    hideTooltip();
    closeSheet();
    
    var PlayerList = new CRaidMemberList();
    var HTMLString = "<div id=\"raiddetail\">";
    
    HTMLString += "<div id=\"tablist\" class=\"tabs setup\">";
    HTMLString += "<div id=\"setuptoggle\" class=\"tab_icon icon_setup\"></div>";
    HTMLString += "<div id=\"slackerstoggle\" class=\"tab_icon icon_slackers_off\"></div>";
    HTMLString += "<div id=\"settingstoggle\" class=\"tab_icon icon_settings_off\"></div>";    
    HTMLString += "</div>";
    HTMLString += "<button id=\"applyButton\">" + L("Apply") + "</button>";
    HTMLString += "</div>";
    
    $("#body").empty().append(HTMLString);
            
    var Message = $(a_XMLData).children("messagehub:first");
    var Raid    = Message.children("raid:first");
    var RaidId  = parseInt(Raid.children("raidId").text());
    
    $("#raiddetail").data("players",PlayerList);    
    Raid.children("attendee").each(function() {
        PlayerList.AddPlayer($(this));
    });    
    
    displayRaidSetup( Raid );
    displayRaidSlackers( Raid );
    displayRaidSettings( Message );
    
    // Setup toplevel UI
    
    $("#applyButton").button({ icons: { secondary: "ui-icon-disk" }})
        .click( function() { triggerRaidUpdate(); } )
        .css( "font-size", 11 )
        .css( "position", "absolute" )
        .css( "left", 819 );
    
    $("#setuptoggle").click( function() {
        changeContext( "raid,setup,"+RaidId );
    });
    
    $("#slackerstoggle").click( function() {
        changeContext( "raid,slackers,"+RaidId );
    });
    
    $("#settingstoggle").click( function() {
        changeContext( "raid,settings,"+RaidId );
    });
    
    loadRaidPanel( Message.children("show").text(), RaidId );
}

// -----------------------------------------------------------------------------

function showRaidPanel( a_Panel, a_Section )
{
    $("#setup").hide();
    $("#slackers").hide();
    $("#settings").hide();
    
    $("#tablist").removeClass("setup");
    $("#tablist").removeClass("slackers");
    $("#tablist").removeClass("settings");

    $("#setuptoggle").removeClass("icon_setup");
    $("#slackerstoggle").removeClass("icon_slackers");
    $("#settingstoggle").removeClass("icon_settings");
    
    $("#setuptoggle").addClass("icon_setup_off");
    $("#slackerstoggle").addClass("icon_slackers_off");
    $("#settingstoggle").addClass("icon_settings_off");

    $(a_Panel).show();
    $("#tablist").addClass(a_Section);
    $("#"+a_Section+"toggle").removeClass("icon_"+a_Section+"_off");
    $("#"+a_Section+"toggle").addClass("icon_"+a_Section);
}

// -----------------------------------------------------------------------------
//  callbacks
// -----------------------------------------------------------------------------

function loadRaid( a_RaidId, a_PanelName )
{
    reloadUser();
    
    if ( g_User == null ) 
        return;
        
    $("#body").empty();
        
    var Parameters = {
        id : a_RaidId,
        showPanel : a_PanelName
    };
    
    AsyncQuery( "raid_detail", Parameters, displayRaid );

}

// -----------------------------------------------------------------------------

function loadRaidPanel( a_Name, a_RaidId )
{
    if ( g_User == null ) 
        return;
        
    if ( $("#raiddetail").length == 0 )
    {
        loadRaid( a_RaidId, a_Name );
    }
    else
    {
        switch( a_Name )
        {    
        default:
        case "setup":
            showRaidPanel("#setup", "setup");
            break;
        
        case "slackers":
            showRaidPanel("#slackers", "slackers");
            break;
            
        case "settings":
            showRaidPanel("#settings", "settings");
            break;
        }
    }
}