<?php

namespace App\Manager;

class ReservationManager
{

  function viewRequests() {

    // DATABASE CALLS
    $requests = $this->getUserRequests("all");
// check to see if requests  has something

    $images = $this->getImages();
    $computers = $this->getComputers();
    $resources = $this->getUserResources(array("imageAdmin"));


    $refresh = 0;
    $connect = 0;
    $failed = 0;

    $normal = '';
    $imaging = '';
    $long = '';
    $server = '';

var_dump("Made it?");
exit;

      // NO IDEA WHAT THIS does
      /*
    $pendingids = array(); # array of all currently pending ids
    $newreadys = array();# array of ids that were in pending and are now ready
    if(array_key_exists('pendingreqids', $_SESSION['usersessiondata']))
      $lastpendingids = $_SESSION['usersessiondata']['pendingreqids'];
    else
      $lastpendingids = array(); # array of ids that were pending last time (needs to get set from $pendingids at end of function)
    */

    $reqids = array();
    if(checkUserHasPerm('View Debug Information'))
      $nodes = getManagementNodes();

// Checking to see if request has something
    if($count = count($requests)) {
      $now = time();
      for($i = 0, $noedit = 0, $text = '', $showcreateimage = 0, $cluster = 0, $col3 = 0;
         $i < $count;
         $i++, $noedit = 0, $text = '', $cluster = 0, $col3 = 0) {
        if($requests[$i]['forcheckout'] == 0 &&
           $requests[$i]['forimaging'] == 0)
          continue;
        if(count($requests[$i]['reservations']))
          $cluster = 1;
        $cdata = array('requestid' => $requests[$i]['id']);
        $reqids[] = $requests[$i]['id'];
        $imageid = $requests[$i]["imageid"];

// COULD BE PROBLEM HERE
        if(requestIsReady($requests[$i]) && $requests[$i]['useraccountready']) {
          if(in_array($requests[$i]['id'], $lastpendingids)) {
            if(! is_null($requests[$i]['servername']))
              $newreadys[] = $requests[$i]['servername'];
            else
              $newreadys[] = $requests[$i]['prettyimage'];
          }
          $connect = 1;
          # request is ready, print Connect! and End buttons

          // NO IDEA WHAT THIS IS
          $cont = addContinuationsEntry('AJconnectRequest', $cdata, SECINDAY);


          if($requests[$i]['serveradmin']) {
            $cdata2 = $cdata;
            $cdata2['notbyowner'] = 0;
            if($user['id'] != $requests[$i]['userid'])
              $cdata2['notbyowner'] = 1;
            $cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata2, SECINDAY);
          }
        }
        elseif($requests[$i]["currstateid"] == 5) {
          # request has failed

          if($requests[$i]['serveradmin']) {
            $cont = addContinuationsEntry('AJconfirmRemoveRequest', $cdata, SECINDAY);
          }

          $noedit = 1;
          $failed = 1;
        }
        elseif(datetimeToUnix($requests[$i]["start"]) < $now) {
          # other cases where the reservation start time has been reached
          if(($requests[$i]["currstate"] == 'complete' &&
             $requests[$i]['laststate'] == 'timeout') ||
            $requests[$i]["currstate"] == 'timeout' ||
            ($requests[$i]["currstate"] == 'pending' &&
            $requests[$i]["laststate"] == 'timeout')) {
            # request has timed out
          //  $text .= getViewRequestHTMLitem('timeoutblock');
            $noedit = 1;
            if($requests[$i]['serveradmin']) {
              $cont = addContinuationsEntry('AJconfirmRemoveRequest', $cdata, SECINDAY);
            }
          }
          elseif($requests[$i]['currstate'] == 'maintenance' ||
                 ($requests[$i]['currstate'] == 'pending' &&
               $requests[$i]['laststate'] == 'maintenance')) {
            # request is in maintenance

            $noedit = 1;
            $col3 = 1;
          }
          elseif($requests[$i]['currstate'] == 'image' ||
                 $requests[$i]['currstate'] == 'checkpoint' ||
                 ($requests[$i]['currstate'] == 'pending' &&
               ($requests[$i]['laststate'] == 'image' ||
               $requests[$i]['laststate'] == 'checkpoint'))) {
            # request is in image

            $noedit = 1;
            $col3 = 1;
            $refresh = 1;
          }
          else {
            # computer is loading, print Pending... and Delete button
            # TODO figure out a different way to estimate for reboot and reinstall states
            # TODO if user account not ready, print accurate information in details
            $pendingids[] = $requests[$i]['id'];
            $remaining = 1;
            if(isComputerLoading($requests[$i], $computers)) {
              if(datetimeToUnix($requests[$i]["daterequested"]) >=
                 datetimeToUnix($requests[$i]["start"])) {
                $startload = datetimeToUnix($requests[$i]["daterequested"]);
              }
              else {
                $startload = datetimeToUnix($requests[$i]["start"]);
              }
              $imgLoadTime = getImageLoadEstimate($imageid);
              if($imgLoadTime == 0)
                $imgLoadTime = $images[$imageid]['reloadtime'] * 60;
              $tmp = ($imgLoadTime - ($now - $startload)) / 60;
              $remaining = sprintf("%d", $tmp) + 1;
              if($remaining < 1) {
                $remaining = 1;
              }
            }
            $data = array('text' => '');
            if($requests[$i]['currstateid'] != 26 &&
               $requests[$i]['currstateid'] != 27 &&
               $requests[$i]['currstateid'] != 28 &&
               $requests[$i]['currstateid'] != 24 &&
               ($requests[$i]["currstateid"] != 14 ||
               ($requests[$i]['laststateid'] != 26 &&
                $requests[$i]['laststateid'] != 27 &&
                $requests[$i]['laststateid'] != 28 &&
                $requests[$i]['laststateid'] != 24)))
              $data['text'] = i("<br>Est:&nbsp;") . $remaining . i("&nbsp;min remaining\n");
            $text .= getViewRequestHTMLitem('pendingblock', $requests[$i]['id'], $data);
            $refresh = 1;
            if($requests[$i]['serveradmin'] && $requests[$i]['laststateid'] != 24) {
              $cdata2 = $cdata;
              $cdata2['notbyowner'] = 0;
              if($user['id'] != $requests[$i]['userid'])
                $cdata2['notbyowner'] = 1;
              $cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata2, SECINDAY);
              $text .= getViewRequestHTMLitem('deletebtn', $cont);
            }
          }
        }
        else {
          # reservation is in the future

          if($requests[$i]['serveradmin']) {
            $cdata2 = $cdata;
            $cdata2['notbyowner'] = 0;
            if($user['id'] != $requests[$i]['userid'])
              $cdata2['notbyowner'] = 1;
            $cont = addContinuationsEntry('AJconfirmDeleteRequest', $cdata2, SECINDAY);
            $text .= getViewRequestHTMLitem('deletebtn', $cont);
          }
        }

        if(! $noedit) {
          # print edit button
          $editcont = addContinuationsEntry('AJeditRequest', $cdata, SECINDAY);
          $imgcont = addContinuationsEntry('AJstartImage', $cdata, SECINDAY);
          if($requests[$i]['serveradmin']) {
            $text .= getViewRequestHTMLitem('openmoreoptions');
            $text .= getViewRequestHTMLitem('editoption', $editcont);
            if(array_key_exists($imageid, $resources['image']) && ! $cluster &&            # imageAdmin access, not a cluster,
               ($requests[$i]['currstateid'] == 8 || $requests[$i]['laststateid'] == 8)) { # reservation has been in inuse state
              $text .= getViewRequestHTMLitem('endcreateoption', $imgcont);
            }
            /*else
              $text .= getViewRequestHTMLitem('endcreateoptiondisable');*/
            if(array_key_exists($imageid, $resources['image']) && ! $cluster &&
               $requests[$i]['server'] && ($requests[$i]['currstateid'] == 8 ||
              ($requests[$i]['currstateid'] == 14 && $requests[$i]['laststateid'] == 8))) {
              $chkcdata = $cdata;
              $chkcdata['checkpoint'] = 1;
              $imgcont = addContinuationsEntry('AJstartImage', $chkcdata, SECINDAY);
              $text .= getViewRequestHTMLitem('checkpointoption', $imgcont);
            }
            elseif($requests[$i]['server'] && $requests[$i]['currstateid'] == 24)
              $text .= getViewRequestHTMLitem('checkpointoptiondisable');
            if($requests[$i]['currstateid'] == 8 ||
               (! $cluster &&
               $requests[$i]['OSinstalltype'] != 'none' &&
               $requests[$i]['currstateid'] != 3 &&
               $requests[$i]['laststateid'] != 3 &&
               $requests[$i]['currstateid'] != 13 &&
               $requests[$i]['laststateid'] != 13 &&
               $requests[$i]['currstateid'] != 24 &&
               $requests[$i]['laststateid'] != 24 &&
               $requests[$i]['currstateid'] != 16 &&
               $requests[$i]['laststateid'] != 16 &&
               $requests[$i]['currstateid'] != 26 &&
               $requests[$i]['laststateid'] != 26 &&
               $requests[$i]['currstateid'] != 28 &&
               $requests[$i]['laststateid'] != 28 &&
               $requests[$i]['currstateid'] != 27 &&
               $requests[$i]['laststateid'] != 27)) {
              $cont = addContinuationsEntry('AJrebootRequest', $cdata, SECINDAY);
              $text .= getViewRequestHTMLitem('rebootoption', $cont);
              $cont = addContinuationsEntry('AJshowReinstallRequest', $cdata, SECINDAY);
              $text .= getViewRequestHTMLitem('reinstalloption', $cont);
            }
            else {
              $text .= getViewRequestHTMLitem('rebootoptiondisable');
              $text .= getViewRequestHTMLitem('reinstalloptiondisable');
            }
        }
        elseif($col3 == 0)
          //$text .= "    <TD></TD>\n";

        # print name of server request
        if($requests[$i]['server']) {
          if($requests[$i]['servername'] == '')
            $text .= getViewRequestHTMLitem('servername', $requests[$i]['prettyimage']);
          else
            $text .= getViewRequestHTMLitem('servername', $requests[$i]['servername']);
        }

        # print name of image, add (Testing) if it is the test version of an image
        if(!$requests[$i]['server']) {
          $data = array('addtest' => 0);
          if($requests[$i]["test"])
            $data['addtest'] = 1;
          $text .= getViewRequestHTMLitem('imagename', $requests[$i]['prettyimage'], $data);
        }

        # print start time
        if(! $requests[$i]['server']) {
          $data = array('start' => $requests[$i]['start'],
                        'requested' => $requests[$i]['daterequested']);
          $text .= getViewRequestHTMLitem('starttime', '', $data);
        }

        # print end time
        $data = array('end' => $requests[$i]['end']);
        $text .= getViewRequestHTMLitem('endtime', '', $data);

        # print date requested
        if(! $requests[$i]['server'])
          $text .= getViewRequestHTMLitem('requesttime', $requests[$i]['daterequested']);

        # print server request details
        if($requests[$i]['server']) {
          $data = array('owner' => getUserUnityID($requests[$i]['userid']),
                        'requesttime' => $requests[$i]['daterequested'],
                        'admingroup' => $requests[$i]['serveradmingroup'],
                        'logingroup' => $requests[$i]['serverlogingroup'],
                        'image' => $requests[$i]['prettyimage'],
                        'starttime' => $requests[$i]['start']);
          if($requests[$i]['currstateid'] == 14)
            $data['stateid'] = $requests[$i]['laststateid'];
          else
            $data['stateid'] = $requests[$i]['currstateid'];
          $text .= getViewRequestHTMLitem('serverdetails', $requests[$i]['id'], $data);
        }

        if(checkUserHasPerm('View Debug Information')) {
          if(! is_null($requests[$i]['vmhostid'])) {
            $query = "SELECT c.hostname "
                   . "FROM computer c, "
                   .      "vmhost v "
                   . "WHERE v.id = {$requests[$i]['vmhostid']} AND "
                   .       "v.computerid = c.id";
            $qh = doQuery($query, 101);
            $row = mysql_fetch_assoc($qh);
            $vmhost = $row['hostname'];
          }

        }

        if($requests[$i]['server'])
          $server .= $text;
        elseif($requests[$i]['forimaging'])
          $imaging .= $text;
        elseif($requests[$i]['longterm'])
          $long .= $text;
        else
          $normal .= $text;
      }
    }

    if(! empty($normal)) {
      if(! empty($imaging) || ! empty($long))
        $text .= i("You currently have the following <strong>normal</strong> reservations:") . "<br>\n";
      else
        $text .= i("You currently have the following normal reservations:") . "<br>\n";
      if($lengthchanged) {
        $text .= "<font color=red>";
        $text .= i("NOTE: The maximum allowed reservation length for one of these reservations was less than the length you submitted, and the length of that reservation has been adjusted accordingly.");
        $text .= "</font>\n";
      }
      $text .= "<table id=reslisttable summary=\"lists reservations you currently have\" cellpadding=5>\n";
      $text .= "  <TR>\n";
      $text .= "    <TD colspan=3></TD>\n";
      $text .= "    <TH>" . i("Environment") . "</TH>\n";
      $text .= "    <TH>" . i("Starting") . "</TH>\n";
      $text .= "    <TH>" . i("Ending") . "</TH>\n";
      $text .= "    <TH>" . i("Initially requested") . "</TH>\n";
      if(checkUserHasPerm('View Debug Information'))
        $text .= "    <TH>" . i("Req ID") . "</TH>\n";
      $text .= "  </TR>\n";
      $text .= $normal;
      $text .= "</table>\n";
    }
    if(! empty($imaging)) {
      if(! empty($normal))
        $text .= "<hr>\n";
      $text .= i("You currently have the following <strong>imaging</strong> reservations:") . "<br>\n";
      $text .= "<table id=imgreslisttable summary=\"lists imaging reservations you currently have\" cellpadding=5>\n";
      $text .= "  <TR>\n";
      $text .= "    <TD colspan=3></TD>\n";
      $text .= "    <TH>" . i("Environment") . "</TH>\n";
      $text .= "    <TH>" . i("Starting") . "</TH>\n";
      $text .= "    <TH>" . i("Ending") . "</TH>\n";
      $text .= "    <TH>" . i("Initially requested") . "</TH>\n";
      $computers = getComputers();
      if(checkUserHasPerm('View Debug Information'))
        $text .= "    <TH>Req ID</TH>\n";
      $text .= "  </TR>\n";
      $text .= $imaging;
      $text .= "</table>\n";
    }
    if(! empty($long)) {
      if(! empty($normal) || ! empty($imaging))
        $text .= "<hr>\n";
      $text .= i("You currently have the following <strong>long term</strong> reservations:") . "<br>\n";
      $text .= "<table id=\"longreslisttable\" summary=\"lists long term reservations you currently have\" cellpadding=5>\n";
      $text .= "  <TR>\n";
      $text .= "    <TD colspan=3></TD>\n";
      $text .= "    <TH>" . i("Environment") . "</TH>\n";
      $text .= "    <TH>" . i("Starting") . "</TH>\n";
      $text .= "    <TH>" . i("Ending") . "</TH>\n";
      $text .= "    <TH>" . i("Initially requested") . "</TH>\n";
      $computers = getComputers();
      if(checkUserHasPerm('View Debug Information'))
        $text .= "    <TH>Req ID</TH>\n";
      $text .= "  </TR>\n";
      $text .= $long;
      $text .= "</table>\n";
    }
    if(! empty($server)) {
      if(! empty($normal) || ! empty($imaging) || ! empty($long))
        $text .= "<hr>\n";
      $text .= i("You currently have the following <strong>server</strong> reservations:") . "<br>\n";
      $text .= "<table id=\"longreslisttable\" summary=\"lists server reservations you currently have\" cellpadding=5>\n";
      $text .= "  <TR>\n";
      $text .= "    <TD colspan=3></TD>\n";
      $text .= "    <TH>" . i("Name") . "</TH>\n";
      $text .= "    <TH>" . i("Ending") . "</TH>\n";
      $computers = getComputers();
      $text .= "    <TH>" . i("Details") . "</TH>\n";
      if(checkUserHasPerm('View Debug Information'))
        $text .= "    <TH>" . i("Req ID") . "</TH>\n";
      $text .= "  </TR>\n";
      $text .= $server;
      $text .= "</table>\n";
    }

    # connect div
    if($connect) {
      $text .= "<br><br>";
      $text .= i("Click the <b>Connect!</b> button to get further information about connecting to the reserved system. You must click the button from a web browser running on the same computer from which you will be connecting to the remote computer; otherwise, you may be denied access to the machine.") . "\n";
    }

    if($refresh) {
      $text .= "<br><br>";
      $text .= i("This page will automatically update every 20 seconds until the <font color=red><i>Pending...</i></font> reservation is ready.") . "\n";
    }

    if($failed) {
      $text .= "<br><br>";
      $text .= i("An error has occurred that has kept one of your reservations from being processed. We apologize for any inconvenience this may have caused.") . "\n";
    }

    $cont = addContinuationsEntry('AJviewRequests', array(), SECINDAY);
    $text .= "<INPUT type=hidden id=resRefreshCont value=\"$cont\">\n";

    $cont = addContinuationsEntry('AJpreviewClickThrough', array());
    $text .= "<INPUT type=hidden id=previewclickthroughcont value=\"$cont\">\n";

    $text .= "</div>\n";
    if($mode != 'AJviewRequests') {
      $text .= newReservationHTML();

      $text .= newReservationConfigHTML();

      $_SESSION['usersessiondata']['pendingreqids'] = $pendingids;
    }
    else {
      $text = str_replace("\n", ' ', $text);
      $text = str_replace("('", "(\'", $text);
      $text = str_replace("')", "\')", $text);
      print "document.body.style.cursor = 'default';";
      if(count($requests) == 0)
        print "dojo.removeClass('noresspan', 'hidden');";
      else
        print "dojo.addClass('noresspan', 'hidden');";
      if($refresh)
        print "refresh_timer = setTimeout(resRefresh, 20000);\n";
      if(count($newreadys))
        print "notifyResReady('" . implode("\n", $newreadys) . "');";
      $_SESSION['usersessiondata']['pendingreqids'] = $pendingids;
      print(setAttribute('subcontent', 'innerHTML', $text));
      print "AJdojoCreate('subcontent');";
      if($incPaneDetails) {
        $text = detailStatusHTML($refreqid);
        print(setAttribute('resStatusText', 'innerHTML', $text));
      }
      print "checkResGone(" . json_encode($reqids) . ");";
      if(count($pendingids))
        print "document.title = '" . count($pendingids) . " Pending :: VCL :: Virtual Computing Lab';";
      else
        print "document.title = 'VCL :: Virtual Computing Lab';";
      return;
    }
    }
  }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getUserRequests($type, $id)
    ///
    /// \param $type - "normal", "forimaging", or "all"
    /// \param $id - (optional) user's id from userlist table
    ///
    /// \return an array of user's requests; the array has the following elements
    /// for each entry where forcheckout == 1 for the image:\n
    /// \b id - id of the request\n
    /// \b userid - id of user owning request\n
    /// \b imageid - id of requested image\n
    /// \b imagerevisionid - revision id of requested image\n
    /// \b image - name of requested image\n
    /// \b prettyimage - pretty name of requested image\n
    /// \b OS - name of the requested os\n
    /// \b OSinstalltype - installtype for OS\n
    /// \b start - start time of request\n
    /// \b end - end time of request\n
    /// \b daterequested - date request was made\n
    /// \b currstateid - current stateid of request\n
    /// \b currstate - current state of request\n
    /// \b laststateid - last stateid of request\n
    /// \b laststate - last state of request\n
    /// \b forimaging - 0 if an normal request, 1 if imaging request\n
    /// \b forcheckout - 1 if image is available for reservations, 0 if not\n
    /// \b test - test flag - 0 or 1\n
    /// \b longterm - 1 if request length is > 24 hours\n
    /// \b server - 1 if corresponding entry in serverprofiles\n
    /// \b serverowner - 1 user owns the reservation, 0 if not\n
    /// \b resid - id of primary reservation\n
    /// \b compimageid - currentimageid for primary computer\n
    /// \b computerstateid - current stateid of primary computer\n
    /// \b computerid - id of primary computer\n
    /// \b IPaddress - IP address of primary computer\n
    /// \b comptype - type of primary computer\n
    /// \b vmhostid - if VM, id of host's entry in vmhost table, NULL otherwise\n
    /// the following additional items if a server request (values will be NULL
    /// if not a server request), some values can be NULL:\n
    /// \b servername - name of server request\n
    /// \b serverrequestid - from server request table\n
    /// \b fixedIP - if specified for request\n
    /// \b fixedMAC - if specified for request\n
    /// \b serveradmingroupid - id of admin user group\n
    /// \b serveradmingroup - name of admin user group\n
    /// \b serverlogingroupid - id of login user group\n
    /// \b serverlogingroup - name of login user group\n
    /// \b monitored - whether or not request is to be monitored (0 or 1)\n
    /// \b useraccountready - whether or not all accounts for this user have been
    /// created on the reserved machine(s)\n
    /// and an array of subimages named reservations with the following elements
    /// for each subimage:\n
    /// \b resid - id of reservation\n
    /// \b imageid - id of requested image\n
    /// \b imagerevisionid - revision id of requested image\n
    /// \b image - name of requested image\n
    /// \b prettyname - pretty name of requested image\n
    /// \b OS - name of the requested os\n
    /// \b compimageid - currentimageid for computer\n
    /// \b computerstateid - current stateid of computer\n
    /// \b computerid - id of reserved computer\n
    /// \b IPaddress - IP address of reserved computer\n
    /// \b type - type of computer\n
    /// \b resacctuserid - empty if user account has not been created on this machine
    /// yet, the user's numeric id if it has\n
    /// \b password - password for this user on the machine; if it is empty but
    /// resacctuserid is not empty, the user should use a federated password
    ///
    /// \brief builds an array of current requests made by the user
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getUserRequests($type, $id=0) {

      $ingroupids = "''";

    	$query = "SELECT i.name AS image, "
    	       .        "i.prettyname AS prettyimage, "
    	       .        "i.id AS imageid, "
    	       .        "rq.userid, "
    	       .        "rq.start, "
    	       .        "rq.end, "
    	       .        "rq.daterequested, "
    	       .        "rq.id, "
    	       .        "o.prettyname AS OS, "
    	       .        "o.type AS ostype, "
    	       .        "o.installtype AS OSinstalltype, "
    	       .        "rq.stateid AS currstateid, "
    	       .        "s.name AS currstate, "
    	       .        "rq.laststateid, "
    	       .        "ls.name AS laststate, "
    	       .        "rs.computerid, "
    	       .        "rs.id AS resid, "
    	       .        "c.currentimageid AS compimageid, "
    	       .        "c.stateid AS computerstateid, "
    	       .        "c.IPaddress, "
    	       .        "c.type AS comptype, "
    	       .        "c.vmhostid, "
    	       .        "rq.forimaging, "
    	       .        "i.forcheckout, "
    	       .        "rs.managementnodeid, "
    	       .        "rs.imagerevisionid, "
    	       .        "rq.test,"
    	       .        "sp.name AS servername, "
    	       .        "sp.requestid AS serverrequestid, "
    	       .        "sp.fixedIP, "
    	       .        "sp.fixedMAC, "
    	       .        "sp.admingroupid AS serveradmingroupid, "
    	       .        "uga.name AS serveradmingroup, "
    	       .        "sp.logingroupid AS serverlogingroupid, "
    	       .        "ugl.name AS serverlogingroup, "
    	       .        "sp.monitored, "
    	       .        "ra.password, "
    	       .        "ra.userid AS resacctuserid, "
    	       .        "rs.pw "
    	       . "FROM image i, "
    	       .      "OS o, "
    	       .      "computer c, "
    	       .      "state s, "
    	       .      "state ls, "
    	       .      "request rq "
    	       . "LEFT JOIN serverrequest sp ON (sp.requestid = rq.id) "
    	       . "LEFT JOIN usergroup uga ON (uga.id = sp.admingroupid) "
    	       . "LEFT JOIN usergroup ugl ON (ugl.id = sp.logingroupid) "
    	       . "LEFT JOIN reservation rs ON (rs.requestid = rq.id) "
    	       . "LEFT JOIN reservationaccounts ra ON (ra.reservationid = rs.id AND ra.userid = $id) "
    	       . "WHERE (rq.userid = $id OR "
    	       .       "sp.admingroupid IN ($ingroupids) OR "
    	       .       "sp.logingroupid IN ($ingroupids)) AND "
    	       .       "rs.imageid = i.id AND "
    	       .       "rq.end > NOW() AND "
    	       .       "i.OSid = o.id AND "
    	       .       "c.id = rs.computerid AND "
    	       .       "rq.stateid = s.id AND "
    	       .       "s.name NOT IN ('deleted', 'makeproduction') AND "
    	       .       "rq.laststateid = ls.id AND "
    	       .       "ls.name NOT IN ('deleted', 'makeproduction') ";
    	if($type == "normal")
    		$query .=   "AND rq.forimaging = 0 "
    		       .    "AND i.forcheckout = 1 "
    		       .    "AND sp.requestid IS NULL ";
    	if($type == "forimaging")
    		$query .=   "AND rq.forimaging = 1 "
    		       .    "AND sp.requestid IS NULL ";
    	if($type == "server")
    		$query .=   "AND sp.requestid IS NOT NULL ";
    	$query .= "ORDER BY rq.start, "
    	       .           "rs.id";
    	$qh = doQuery($query, 160);
    	$count = -1;
    	$data = array();
    	$foundids = array();
    	$lastreqid = 0;
    	while($row = mysql_fetch_assoc($qh)) {
    		if($row['id'] != $lastreqid) {
    			$lastreqid = $row['id'];
    			$count++;
    			$data[$count] = $row;
    			$data[$count]['useraccountready'] = 1;
    			$data[$count]['reservations'] = array();
    		}
    		if(array_key_exists($row['id'], $foundids)) {
    			$data[$count]['reservations'][] = array(
    				'resid' => $row['resid'],
    				'image' => $row['image'],
    				'prettyname' => $row['prettyimage'],
    				'imageid' => $row['imageid'],
    				'imagerevisionid' => $row['imagerevisionid'],
    				'OS' => $row['OS'],
    				'computerid' => $row['computerid'],
    				'compimageid' => $row['compimageid'],
    				'computerstateid' => $row['computerstateid'],
    				'IPaddress' => $row['IPaddress'],
    				'comptype' => $row['comptype'],
    				'password' => $row['password'],
    				'resacctuserid' => $row['resacctuserid']
    			);
    			if($row['userid'] != $id && empty($row['resacctuserid']))
    				$data[$count]['useraccountready'] = 0;
    			continue;
    		}
    		$foundids[$row['id']] = 1;
    		if(! is_null($row['serverrequestid'])) {
    			$data[$count]['server'] = 1;
    			$data[$count]['longterm'] = 0;
    			if($row['userid'] == $user['id']) {
    				$data[$count]['serverowner'] = 1;
    				$data[$count]['serveradmin'] = 1;
    			}
    			else {
    				$data[$count]['serverowner'] = 0;
    				if(! empty($row['serveradmingroupid']) &&
    				   array_key_exists($row['serveradmingroupid'], $user['groups']))
    					$data[$count]['serveradmin'] = 1;
    				else
    					$data[$count]['serveradmin'] = 0;
    			}
    		}
    		elseif((datetimeToUnix($row['end']) - datetimeToUnix($row['start'])) > SECINDAY) {
    			$data[$count]['server'] = 0;
    			$data[$count]['longterm'] = 1;
    			$data[$count]['serverowner'] = 1;
    			$data[$count]['serveradmin'] = 1;
    		}
    		else {
    			$data[$count]['server'] = 0;
    			$data[$count]['longterm'] = 0;
    			$data[$count]['serverowner'] = 1;
    			$data[$count]['serveradmin'] = 1;
    		}
    		if($row['userid'] != $id && empty($row['resacctuserid']))
    			$data[$count]['useraccountready'] = 0;
    	}
    	return $data;
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getImages($includedeleted=0, $imageid=0)
    ///
    /// \param $includedeleted = (optional) 1 to show deleted images, 0 not to
    /// \param $imageid = (optional) only get data for this image, defaults
    /// to getting data for all images
    ///
    /// \return $imagelist - array of images with the following elements:\n
    /// \b name - name of image\n
    /// \b prettyname - pretty name of image\n
    /// \b ownerid - userid of owner\n
    /// \b owner - unity id of owner\n
    /// \b platformid - platformid for the platform the image if for\n
    /// \b platform - platform the image is for\n
    /// \b osid - osid for the os on the image\n
    /// \b os - os the image contains\n
    /// \b installtype - method used to install image\n
    /// \b ostypeid - id of the OS type in the image\n
    /// \b ostype - name of the OS type in the image\n
    /// \b minram - minimum amount of RAM needed for image\n
    /// \b minprocnumber - minimum number of processors needed for image\n
    /// \b minprocspeed - minimum speed of processor(s) needed for image\n
    /// \b minnetwork - minimum speed of network needed for image\n
    /// \b maxconcurrent - maximum concurrent usage of this iamge\n
    /// \b reloadtime - time in minutes for image to be loaded\n
    /// \b deleted - 'yes' or 'no'; whether or not this image has been deleted\n
    /// \b test - 0 or 1; whether or not there is a test version of this image\n
    /// \b resourceid - image's resource id from the resource table\n
    /// \b lastupdate - datetime image was last updated\n
    /// \b forcheckout - 0 or 1; whether or not the image is allowed to be directly
    ///                  checked out\n
    /// \b maxinitialtime - maximum time (in minutes) to be shown when requesting
    ///                     a reservation that the image can reserved for\n
    /// \b imagemetaid - NULL or corresponding id from imagemeta table and the
    /// following additional information:\n
    /// \b checkuser - whether or not vcld should check for a logged in user\n
    /// \b sysprep - whether or not to use sysprep on creation of the image\n
    /// \b connectmethods - array of enabled connect methods\n
    /// \b subimages - an array of subimages to be loaded along with selected
    /// image\n
    /// \b imagerevision - an array of revision info about the image, it has these
    /// keys: id, revision, userid, user, datecreated, prettydate, production,
    /// imagename
    ///
    /// \brief generates an array of images
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getImages($includedeleted=0, $imageid=0) {
    	# key in $imagelist is for $includedeleted
    	static $imagelist = array(0 => array(), 1 => array());
    	if(! empty($imagelist[$includedeleted])) {
    		if($imageid == 0)
    			return $imagelist[$includedeleted];
    		else
    			return array($imageid => $imagelist[$includedeleted][$imageid]);
    	}
    	# get all image meta data
    	$allmetadata = array();
    	$query = "SELECT checkuser, "
    	       .        "rootaccess, "
    	       .        "subimages, "
    	       .        "sysprep, "
    	       .        "sethostname, "
    	       .        "id "
    	       . "FROM imagemeta";
    	$qh = doQuery($query);
    	while($row = mysql_fetch_assoc($qh))
    		$allmetadata[$row['id']] = $row;

    	# get all image revision data
    	$allrevisiondata = array();
    	$query = "SELECT i.id, "
    	       .        "i.imageid, "
    	       .        "i.revision, "
    	       .        "i.userid, "
    	       .        "CONCAT(u.unityid, '@', a.name) AS user, "
    	       .        "i.datecreated, "
    	       .        "DATE_FORMAT(i.datecreated, '%c/%d/%y %l:%i %p') AS prettydate, "
    	       .        "i.deleted, "
    	       .        "i.datedeleted, "
    	       .        "i.production, "
    	       .        "i.imagename "
    	       . "FROM imagerevision i, "
    	       .      "affiliation a, "
    	       .      "user u "
    	       . "WHERE i.userid = u.id AND ";
    	if(! $includedeleted)
    		$query .=   "i.deleted = 0 AND ";
    	$query .=      "u.affiliationid = a.id";
    	$qh = doQuery($query, 101);
    	while($row = mysql_fetch_assoc($qh)) {
    		$id = $row['imageid'];
    		unset($row['imageid']);
    		$allrevisiondata[$id][$row['id']] = $row;
    	}
    	$query = "SELECT i.id AS id,"
    	       .        "i.name AS name, "
    	       .        "i.prettyname AS prettyname, "
    	       .        "i.ownerid AS ownerid, "
    	       .        "CONCAT(u.unityid, '@', a.name) AS owner, "
    	       .        "i.platformid AS platformid, "
    	       .        "p.name AS platform, "
    	       .        "i.OSid AS osid, "
    	       .        "o.name AS os, "
    	       .        "o.installtype, "
    	       .        "ot.id AS ostypeid, "
    	       .        "ot.name AS ostype, "
    	       .        "i.minram AS minram, "
    	       .        "i.minprocnumber AS minprocnumber, "
    	       .        "i.minprocspeed AS minprocspeed, "
    	       .        "i.minnetwork AS minnetwork, "
    	       .        "i.maxconcurrent AS maxconcurrent, "
    	       .        "i.reloadtime AS reloadtime, "
    	       .        "i.deleted AS deleted, "
    	       .        "i.test AS test, "
    	       .        "r.id AS resourceid, "
    	       .        "i.lastupdate, "
    	       .        "i.forcheckout, "
    	       .        "i.maxinitialtime, "
    	       .        "i.imagemetaid, "
    	       .        "ad.id AS addomainid, "
    	       .        "ad.name AS addomain, "
    	       .        "iadd.baseOU "
    	       . "FROM platform p, "
    	       .      "OS o, "
    	       .      "OStype ot, "
    	       .      "resource r, "
    	       .      "resourcetype t, "
    	       .      "user u, "
    	       .      "affiliation a, "
    	       .      "image i "
    	       . "LEFT JOIN imageaddomain iadd ON (i.id = iadd.imageid) "
    	       . "LEFT JOIN addomain ad ON (iadd.addomainid = ad.id) "
    	       . "WHERE i.platformid = p.id AND "
    	       .       "r.resourcetypeid = t.id AND "
    	       .       "t.name = 'image' AND "
    	       .       "r.subid = i.id AND "
    	       .       "i.OSid = o.id AND "
    	       .       "o.type = ot.name AND "
    	       .       "i.ownerid = u.id AND "
    	       .       "u.affiliationid = a.id ";
    	if(! $includedeleted)
    		$query .= "AND i.deleted = 0 ";
       $query .= "ORDER BY i.prettyname";
    	$qh = doQuery($query, 120);
    	while($row = mysql_fetch_assoc($qh)) {
    		if(is_null($row['maxconcurrent']))
    			$row['maxconcurrent'] = 0;
    		$imagelist[$includedeleted][$row["id"]] = $row;
    		$imagelist[$includedeleted][$row["id"]]['checkuser'] = 1;
    		$imagelist[$includedeleted][$row["id"]]['rootaccess'] = 1;
    		if($row['ostype'] == 'windows' || $row['ostype'] == 'osx')
    			$imagelist[$includedeleted][$row['id']]['sethostname'] = 0;
    		else
    			$imagelist[$includedeleted][$row['id']]['sethostname'] = 1;
    		$imagelist[$includedeleted][$row['id']]['adauthenabled'] = 0;
    		if($row['addomainid'] != NULL)
    			$imagelist[$includedeleted][$row['id']]['adauthenabled'] = 1;
    		if($row["imagemetaid"] != NULL) {
    			if(isset($allmetadata[$row['imagemetaid']])) {
    				$metaid = $row['imagemetaid'];
    				$imagelist[$includedeleted][$row['id']]['checkuser'] = $allmetadata[$metaid]['checkuser'];
    				$imagelist[$includedeleted][$row['id']]['rootaccess'] = $allmetadata[$metaid]['rootaccess'];
    				$imagelist[$includedeleted][$row['id']]['sysprep'] = $allmetadata[$metaid]['sysprep'];
    				if($allmetadata[$metaid]['sethostname'] != NULL)
    					$imagelist[$includedeleted][$row['id']]['sethostname'] = $allmetadata[$metaid]['sethostname'];
    				$imagelist[$includedeleted][$row["id"]]["subimages"] = array();
    				if($allmetadata[$metaid]["subimages"]) {
    					$query2 = "SELECT imageid "
    				        . "FROM subimages "
    				        . "WHERE imagemetaid = $metaid";
    					$qh2 = doQuery($query2, 101);
    					while($row2 = mysql_fetch_assoc($qh2))
    						$imagelist[$includedeleted][$row["id"]]["subimages"][] =  $row2["imageid"];
    				}
    			}
    			else
    				$imagelist[$includedeleted][$row["id"]]["imagemetaid"] = NULL;
    		}
    		if(isset($allrevisiondata[$row['id']]))
    			$imagelist[$includedeleted][$row['id']]['imagerevision'] = $allrevisiondata[$row['id']];
    		$imagelist[$includedeleted][$row['id']]['connectmethods'] = getImageConnectMethods($row['id']);
    	}
    	if($imageid != 0)
    		return array($imageid => $imagelist[$includedeleted][$imageid]);
    	return $imagelist[$includedeleted];
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getUserResources($userprivs, $resourceprivs, $onlygroups,
    ///                               $includedeleted, $userid, $groupid)
    ///
    /// \param $userprivs - array of privileges to look for (such as
    /// imageAdmin, imageCheckOut, etc) - this is an OR list; don't include 'block'
    /// or 'cascade'
    /// \param $resourceprivs - array of privileges to look for (such as
    /// available, administer, manageGroup) - this is an OR list; don't include
    /// 'block' or 'cascade'
    /// \param $onlygroups - (optional) if 1, return the resource groups instead
    /// of the resources
    /// \param $includedeleted - (optional) included deleted resources if 1,
    /// don't if 0
    /// \param $userid - (optional) id from the user table, if not given, use the
    /// id of the currently logged in user
    /// \param $groupid - (optional) id from the usergroup table, if not given, look
    /// up by $userid; $userid must be 0 to look up by $groupid
    ///
    /// \return an array of 2 arrays where the first indexes are resource types
    /// and each one's arrays are a list of resources available to the user where
    /// the index of each item is the id and the value is the name of the
    /// resource\n
    /// if $onlygroups == 1:\n
    /// {[computer] => {[groupid] => "groupname",\n
    ///                 [groupid] => "groupname"},\n
    ///  [image] => {[groupid] => "groupname",\n
    ///              [groupid] => "groupname"},\n
    ///   ...}\n
    /// if $onlygroups == 0:\n
    /// {[computer] => {[compid] => "hostname",\n
    ///                 [compid] => "hosename"},\n
    ///  [image] => {[imageid] => "prettyname",\n
    ///              [imageid] => "prettyname"},\n
    ///   ...}
    ///
    /// \brief builds a list of resources a user has access to and returns it
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getUserResources($userprivs, $resourceprivs=array("available"),
                              $onlygroups=0, $includedeleted=0, $userid=0,
                              $groupid=0) {
    	global $user;
    	if(isset($userprivs['managementnodeAdmin']))
    		$userprivs[] = 'mgmtNodeAdmin';
    	$key = getKey(array($userprivs, $resourceprivs, $onlygroups, $includedeleted, $userid, $groupid));
    	if(isset($_SESSION['userresources'][$key]))
    		return $_SESSION['userresources'][$key];
    	#FIXME this whole function could be much more efficient
    	$bygroup = 0;
    	if($userid == 0 && $groupid != 0)
    		$bygroup = 1;
    	if(! $userid)
    		$userid = $user["id"];
    	$return = array();

    	$nodeprivs = array();
    	$startnodes = array();
    	# build a list of nodes where user is granted $userprivs
    	$inlist = "'" . implode("','", $userprivs) . "'";
    	$query = "SELECT u.privnodeid "
    	       . "FROM userpriv u, "
    	       .      "userprivtype t "
    	       . "WHERE u.userprivtypeid = t.id AND "
    	       .       "t.name IN ($inlist) AND ";
    	if(! $bygroup) {
    		$query .=   "(u.userid = $userid OR "
    		       .    "u.usergroupid IN (SELECT usergroupid "
    		       .                      "FROM usergroupmembers "
    		       .                      "WHERE userid = $userid))";
    	}
    	else
    		$query .=   "u.usergroupid = $groupid";
    	$qh = doQuery($query, 101);
    	while($row = mysql_fetch_assoc($qh)) {
    		array_push($startnodes, $row["privnodeid"]);
    	}
    	# build data array from userprivtype and userpriv tables to reduce queries
    	# in addNodeUserResourcePrivs
    	$privdataset = array('user' => array(), 'usergroup' => array());
    	$query = "SELECT t.name, "
    	       .        "u.privnodeid "
    	       . "FROM userprivtype t, "
    	       .      "userpriv u "
    	       . "WHERE u.userprivtypeid = t.id AND "
    	       .       "u.userid IS NOT NULL AND "
    	       .       "u.userid = $userid AND "
    	       .       "t.name IN ('block','cascade',$inlist)";
    	$qh = doQuery($query);
    	while($row = mysql_fetch_assoc($qh))
    		$privdataset['user'][$row['privnodeid']][] = $row['name'];
    	$query = "SELECT t.name, "
    	       .        "u.usergroupid, "
    	       .        "u.privnodeid "
    	       . "FROM userprivtype t, "
    	       .      "userpriv u "
    	       . "WHERE u.userprivtypeid = t.id AND "
    			 .       "u.usergroupid IS NOT NULL AND ";
    	if($bygroup)
    		$query .=   "u.usergroupid = $groupid AND ";
    	else
    		$query .=   "u.usergroupid IN (SELECT usergroupid "
    		       .                      "FROM usergroupmembers "
    				 .                      "WHERE userid = $userid) AND ";
    	$query .=      "t.name IN ('block','cascade',$inlist) "
    	       . "ORDER BY u.privnodeid, "
    	       .          "u.usergroupid";
    	$qh = doQuery($query, 101);
    	while($row = mysql_fetch_assoc($qh))
    		$privdataset['usergroup'][$row['privnodeid']][] = array('name' => $row['name'], 'groupid' => $row['usergroupid']);

    	# travel up tree looking at privileges granted at parent nodes
    	foreach($startnodes as $nodeid) {
    		getUserResourcesUp($nodeprivs, $nodeid, $userid, $userprivs, $privdataset);
    	}
    	# travel down tree looking at privileges granted at child nodes if cascade privs at this node
    	foreach($startnodes as $nodeid) {
    		getUserResourcesDown($nodeprivs, $nodeid, $userid, $userprivs, $privdataset);
    	}
    	$nodeprivs = simplifyNodePrivs($nodeprivs, $userprivs); // call this before calling addUserResources
    	addUserResources($nodeprivs, $userid);

    	# build a list of resource groups user has access to
    	$resourcegroups = array();
    	$types = getTypes("resources");
    	foreach($types["resources"] as $type) {
    		$resourcegroups[$type] = array();
    	}
    	foreach(array_keys($nodeprivs) as $nodeid) {
    		// if user doesn't have privs at this node, no need to look
    		// at any resource groups here
    		$haspriv = 0;
    		foreach($userprivs as $priv) {
    			if($nodeprivs[$nodeid][$priv])
    				$haspriv = 1;
    		}
    		if(! $haspriv)
    			continue;
    		# check to see if resource groups has any of $resourceprivs at this node
    		foreach(array_keys($nodeprivs[$nodeid]["resources"]) as $resourceid) {
    			foreach($resourceprivs as $priv) {
    				if(isset($nodeprivs[$nodeid]["resources"][$resourceid][$priv])) {
    					list($type, $name, $id) = explode('/', $resourceid);
    					$resourcegroups[$type][$id] = $name;
    				}
    			}
    		}
    		# check to see if resource groups has any of $resourceprivs cascaded to this node
    		foreach(array_keys($nodeprivs[$nodeid]["cascaderesources"]) as $resourceid) {
    			foreach($resourceprivs as $priv) {
    				if(isset($nodeprivs[$nodeid]["cascaderesources"][$resourceid][$priv]) &&
    					! (isset($nodeprivs[$nodeid]["resources"][$resourceid]["block"]))) {
    					list($type, $name, $id) = explode('/', $resourceid);
    					$resourcegroups[$type][$id] = $name;
    				}
    			}
    		}
    	}

    	if(! $bygroup)
    		addOwnedResourceGroups($resourcegroups, $userid);
    	if($onlygroups) {
    		foreach(array_keys($resourcegroups) as $type)
    			uasort($resourcegroups[$type], "sortKeepIndex");
    		$_SESSION['userresources'][$key] = $resourcegroups;
    		return $resourcegroups;
    	}

    	$resources = array();
    	foreach(array_keys($resourcegroups) as $type) {
    		$resources[$type] =
    		   getResourcesFromGroups($resourcegroups[$type], $type, $includedeleted);
    	}
    	if(! $bygroup)
    		addOwnedResources($resources, $includedeleted, $userid);
    	$noimageid = getImageId('noimage');
    	if(isset($resources['image'][$noimageid]))
    		unset($resources['image'][$noimageid]);
    	$_SESSION['userresources'][$key] = $resources;
    	return $resources;
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getComputers($sort, $includedeleted, $compid)
    ///
    /// \param $sort - (optional) 1 to sort; 0 not to
    /// \param $includedeleted = (optional) 1 to show deleted images, 0 not to
    /// \param $compid - (optional) only get info for this computer id
    ///
    /// \return an array with info about the computers in the comptuer table; each
    /// element's index is the id from the table; each element has the following
    /// items\n
    /// \b state - current state of the computer\n
    /// \b stateid - id of current state\n
    /// \b owner - unity id of owner\n
    /// \b ownerid - user id of owner\n
    /// \b platform - computer's platform\n
    /// \b platformid - id of computer's platform\n
    /// \b schedule - computer's schedule\n
    /// \b scheduleid - id of computer's schedule\n
    /// \b currentimg - computer's current image\n
    /// \b currentimgid - id of computer's current image\n
    /// \b imagerevisionid - revision id of computer's current image\n
    /// \b nextimg - computer's next image\n
    /// \b nextimgid - id of computer's next image\n
    /// \b nextimg - computer's next image\n
    /// \b nextimgid - id of computer's next image\n
    /// \b ram - amount of RAM in computer in MB\n
    /// \b procnumber - number of processors in computer\n
    /// \b procspeed - speed of processor(s) in MHz\n
    /// \b network - speed of computer's NIC\n
    /// \b hostname - computer's hostname\n
    /// \b IPaddress - computer's IP address\n
    /// \b privateIPaddress - computer's private IP address\n
    /// \b eth0macaddress - computer's eth0 mac address\n
    /// \b eth1macaddress - computer's eth1 mac address\n
    /// \b type - either 'blade' or 'lab' - used to determine what backend utilities\n
    /// \b deleted - 0 or 1; whether or not this computer has been deleted\n
    /// \b resourceid - computer's resource id from the resource table\n
    /// \b location - computer's location\n
    /// \b provisioningid - id of provisioning engine\n
    /// \b provisioning - pretty name of provisioning engine\n
    /// \b vmprofileid - if vmhost, id of vmprofile
    /// need to be used to manage computer\n
    /// \b natenabled - 0 or 1; if NAT is enabled for this computer\n
    /// \b nathostid - id from nathost table if NAT is enabled or empty string if
    /// not
    ///
    /// \brief builds an array of computers
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getComputers($sort=0, $includedeleted=0, $compid="") {
    	$nathosts = getNAThosts();
    	$return = array();
    	$query = "SELECT c.id AS id, "
    	       .        "st.name AS state, "
    	       .        "c.stateid AS stateid, "
    	       .        "CONCAT(u.unityid, '@', a.name) AS owner, "
    	       .        "u.id AS ownerid, "
    	       .        "p.name AS platform, "
    	       .        "c.platformid AS platformid, "
    	       .        "sc.name AS schedule, "
    	       .        "c.scheduleid AS scheduleid, "
    	       .        "cur.prettyname AS currentimg, "
    	       .        "c.currentimageid AS currentimgid, "
    	       .        "c.imagerevisionid, "
    	       .        "ir.revision AS imagerevision, "
    	       .        "next.prettyname AS nextimg, "
    	       .        "c.nextimageid AS nextimgid, "
    	       .        "c.RAM AS ram, "
    	       .        "c.procnumber AS procnumber, "
    	       .        "c.procspeed AS procspeed, "
    	       .        "c.network AS network, "
    	       .        "c.hostname AS hostname, "
    	       .        "c.IPaddress AS IPaddress, "
    	       .        "c.privateIPaddress, "
    	       .        "c.eth0macaddress, "
    	       .        "c.eth1macaddress, "
    	       .        "c.type AS type, "
    	       .        "c.deleted AS deleted, "
    	       .        "r.id AS resourceid, "
    	       .        "c.notes, "
    	       .        "c.vmhostid, "
    	       .        "c2.hostname AS vmhost, "
    	       .        "c2.id AS vmhostcomputerid, "
    	       .        "c.location, "
    	       .        "c.provisioningid, "
    	       .        "pr.prettyname AS provisioning, "
    	       .        "vh2.vmprofileid, "
    	       .        "c.predictivemoduleid, "
    	       .        "m.prettyname AS predictivemodule, "
    	       .        "nh.id AS nathostid, "
    	       .        "nh2.id AS nathostenabledid, "
    	       .        "COALESCE(nh2.publicIPaddress, '') AS natpublicIPaddress, "
    	       .        "COALESCE(nh2.internalIPaddress, '') AS natinternalIPaddress "
    	       . "FROM state st, "
    	       .      "platform p, "
    	       .      "schedule sc, "
    	       .      "image cur, "
    	       .      "user u, "
    	       .      "affiliation a, "
    	       .      "module m, "
    	       .      "computer c "
    	       . "LEFT JOIN resourcetype t ON (t.name = 'computer') "
    	       . "LEFT JOIN resource r ON (r.resourcetypeid = t.id AND r.subid = c.id) "
    	       . "LEFT JOIN vmhost vh ON (c.vmhostid = vh.id) "
    	       . "LEFT JOIN vmhost vh2 ON (c.id = vh2.computerid) "
    	       . "LEFT JOIN computer c2 ON (c2.id = vh.computerid) "
    	       . "LEFT JOIN image next ON (c.nextimageid = next.id) "
    	       . "LEFT JOIN provisioning pr ON (c.provisioningid = pr.id) "
    	       . "LEFT JOIN nathostcomputermap nm ON (nm.computerid = c.id) "
    	       . "LEFT JOIN nathost nh ON (nm.nathostid = nh.id) "
    	       . "LEFT JOIN nathost nh2 ON (r.id = nh2.resourceid) "
    	       . "LEFT JOIN imagerevision ir ON (c.imagerevisionid = ir.id) "
    	       . "WHERE c.stateid = st.id AND "
    	       .       "c.platformid = p.id AND "
    	       .       "c.scheduleid = sc.id AND "
    	       .       "c.currentimageid = cur.id AND "
    	       .       "c.ownerid = u.id AND "
    	       .       "u.affiliationid = a.id AND "
    	       .       "c.predictivemoduleid = m.id ";
    	if(! $includedeleted)
    		$query .= "AND c.deleted = 0 ";
    	if(! empty($compid))
    		$query .= "AND c.id = $compid ";
    	$query .= "ORDER BY c.hostname";
    	$qh = doQuery($query, 180);
    	while($row = mysql_fetch_assoc($qh)) {
    		if(is_null($row['nathostid'])) {
    			$row['natenabled'] = 0;
    			$row['nathost'] = '';
    		}
    		else {
    			$row['natenabled'] = 1;
    			$row['nathost'] = $nathosts[$row['nathostid']]['hostname'];
    		}
    		if(is_null($row['nathostenabledid']))
    			$row['nathostenabled'] = 0;
    		else
    			$row['nathostenabled'] = 1;
    		$return[$row['id']] = $row;
    	}
    	if($sort) {
    		uasort($return, "sortComputers");
    	}
    	return $return;
    }
}
