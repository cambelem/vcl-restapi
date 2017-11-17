<?php

namespace App\Manager;

use App\Models\Imagemeta;
use App\Models\ImageRevision;
use App\Models\Vcluser;
use Illuminate\Database\Schema\Blueprint;

require_once __DIR__ . ("/../.ht-inc/utils.php");

class ReservationManager
{

  function getAllReservations() {

    $query = "SELECT rn.id as id,
                     i.prettyname as prettyimage,
                     r.start as start,
                     r.end as end,
                     rn.requestid as request,
                     rn.computerid as computer,
                     rn.imageid as image,
                     rn.imagerevisionid as imageRevision,
                     rn.managementnodeid as managementNode,
                     rn.remoteIP as remoteIP,
                     rn.lastcheck as lastcheck,
                     rn.pw as pw,
                     rn.connectip as connectIP,
                     rn.connectport as connectPort,
                     r.daterequested as dateRequested
              FROM   reservation as rn, request as r, image as i
              WHERE  rn.requestid = r.id and  rn.imageid = i.id";
    $qh = doQuery($query, 160);

  	$result = mysqli_fetch_assoc($qh);

    return $result;
  }

  function getReservation($id) {
    if ($id == NULL)
      return NULL;

    $query = "SELECT rn.id as id,
                     i.prettyname as prettyimage,
                     r.start as start,
                     r.end as end,
                     rn.requestid as request,
                     rn.computerid as computer,
                     rn.imageid as image,
                     rn.imagerevisionid as imageRevision,
                     rn.managementnodeid as managementNode,
                     rn.remoteIP as remoteIP,
                     rn.lastcheck as lastcheck,
                     rn.pw as pw,
                     rn.connectip as connectIP,
                     rn.connectport as connectPort,
                     r.daterequested as dateRequested
              FROM   reservation as rn, request as r, image as i
              WHERE  rn.requestid = r.id and  rn.imageid = i.id and rn.id = $id";
    $qh = doQuery($query, 160);

  	$result = mysqli_fetch_assoc($qh);

    return $result;
  }

  function requests() {

  //  $requests = getUserRequests("all");
    global $user;
    $user = getUserInfo(1, 0, 1);

    $requests = getUserRequests("all");
    $images = getImages(); //IT WORKS!
    $computers = getComputers(); //IT WORKS!
    $resources = getUserResources(array("imageAdmin")); //IT WORKS!


    $newbtn = '';
    if(in_array("imageCheckOut", $user["privileges"]) ||
  	   in_array("imageAdmin", $user["privileges"])) {

      $newbtn = "test";
    }

    if($newbtn == '' && count($requests) == 0)
  		return; // For people who don't have the privilege of accessing the data

    $refresh = 0;
    $connect = 0;
    $failed = 0;

    $normal = '';
    $imaging = '';
    $long = '';
    $server = '';

    $pendingids = array(); # array of all currently pending ids
    $newreadys = array();# array of ids that were in pending and are now ready
    if(array_key_exists('pendingreqids', $_SESSION['usersessiondata']))
      $lastpendingids = $_SESSION['usersessiondata']['pendingreqids'];
    else
      $lastpendingids = array(); # array of ids that were pending last time (needs to get set from $pendingids at end of function)


    $reqids = array();
    if(checkUserHasPerm('View Debug Information'))
      $nodes = getManagementNodes();
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
        if(requestIsReady($requests[$i]) && $requests[$i]['useraccountready']) {
          if(in_array($requests[$i]['id'], $lastpendingids)) {
            if(! is_null($requests[$i]['servername']))
              $newreadys[] = $requests[$i]['servername'];
            else
              $newreadys[] = $requests[$i]['prettyimage'];
          }
          $connect = 1;
          if($requests[$i]['serveradmin']) {
            $cdata2 = $cdata;
            $cdata2['notbyowner'] = 0;
            if($user['id'] != $requests[$i]['userid'])
              $cdata2['notbyowner'] = 1;
          }
        }
        elseif($requests[$i]["currstateid"] == 5) {
          # request has failed
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
            $noedit = 1;
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
            if($requests[$i]['currstateid'] != 26 &&
               $requests[$i]['currstateid'] != 27 &&
               $requests[$i]['currstateid'] != 28 &&
               $requests[$i]['currstateid'] != 24 &&
               ($requests[$i]["currstateid"] != 14 ||
               ($requests[$i]['laststateid'] != 26 &&
                $requests[$i]['laststateid'] != 27 &&
                $requests[$i]['laststateid'] != 28 &&
                $requests[$i]['laststateid'] != 24)))
            $refresh = 1;
            if($requests[$i]['serveradmin'] && $requests[$i]['laststateid'] != 24) {
              $cdata2 = $cdata;
              $cdata2['notbyowner'] = 0;
              if($user['id'] != $requests[$i]['userid'])
                $cdata2['notbyowner'] = 1;
            }
          }
        }
        else {
          if($requests[$i]['serveradmin']) {
            $cdata2 = $cdata;
            $cdata2['notbyowner'] = 0;
            if($user['id'] != $requests[$i]['userid'])
              $cdata2['notbyowner'] = 1;
          }
        }
        if(! $noedit) {
          if($requests[$i]['serveradmin']) {
            if(array_key_exists($imageid, $resources['image']) && ! $cluster &&            # imageAdmin access, not a cluster,
               ($requests[$i]['currstateid'] == 8 || $requests[$i]['laststateid'] == 8)) { # reservation has been in inuse state
            }
            if(array_key_exists($imageid, $resources['image']) && ! $cluster &&
               $requests[$i]['server'] && ($requests[$i]['currstateid'] == 8 ||
              ($requests[$i]['currstateid'] == 14 && $requests[$i]['laststateid'] == 8))) {
              $chkcdata = $cdata;
              $chkcdata['checkpoint'] = 1;
            }
            elseif($requests[$i]['server'] && $requests[$i]['currstateid'] == 24)

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
            }
          }
        }

        if(checkUserHasPerm('View Debug Information')) {
          if(! is_null($requests[$i]['vmhostid'])) {
            $query = "SELECT c.hostname "
                   . "FROM computer c, "
                   .      "vmhost v "
                   . "WHERE v.id = {$requests[$i]['vmhostid']} AND "
                   .       "v.computerid = c.id";
            $qh = $this->doQuery($query, 101);
            $row = mysqli_fetch_assoc($qh);
            $vmhost = $row['hostname'];
          }
        }
      }
    } else {
      // No Reservations
      return NULL;
    }


    if(! empty($imaging)) {
      $computers = getComputers();
    }

    if(! empty($long)) {
      $computers = getComputers();
    }

    if(! empty($server)) {
      $computers = getComputers();
    }

/*
    if($mode != 'AJviewRequests') {
      $_SESSION['usersessiondata']['pendingreqids'] = $pendingids;
    }
    else {
      $_SESSION['usersessiondata']['pendingreqids'] = $pendingids;
      return;
    }
*/
  foreach($requests as $request)
  {
    $reservation = array(
        'id'             => $request['id'], //reservation id but not provided by request var
        'name'           => $request['prettyimage'],
        'start'          => $request['start'],
        'end'            => $request['end'],
        'request'        => $request['id'],
        'computer'       => $request['computerid'],
        'image'          => $request['imageid'],
        'imageRevision'  => $request['imagerevisionid'],
        'managementNode' => $request['managementnodeid'],
        'remoteIP'       => $request['id'],  //Cant provide
        'lastCheck'      => $request['id'], //Cant provide
        'pw'             => $request['id'], //Cant provide
        'connectIP'      => $request['id'], //Cant provide
        'connectPort'    => $request['id'], //Cant provide
        'created'        => $request['daterequested']  //daterequested?
    );
  }

  return $reservation;

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
      global $user;
      if($id == 0)
        $id = $user["id"];
      $includegroups = $this->getUsersGroups($user["id"]);
      if(empty($includegroups))
        $ingroupids = "''";
      else
        $ingroupids = implode(',', array_keys($includegroups));
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
      $qh = $this->doQuery($query, 160);
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
    /// \fn getUsersGroups($userid, $includeowned, $includeaffil)
    ///
    /// \param $userid - an id from the user table
    /// \param $includeowned - (optional, default=0) include groups the user owns
    ///                        but is not in
    /// \param $includeaffil - (optional, default=0) include @affiliation in name
    ///                        of group
    ///
    /// \return an array of the user's groups where the index is the id of the
    /// group
    ///
    /// \brief builds a array of the groups the user is member of
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getUsersGroups($userid, $includeowned=0, $includeaffil=0) {
    	if($includeaffil) {
    		$query = "SELECT m.usergroupid, "
    		       .        "CONCAT(g.name, '@', a.name) AS name "
    		       . "FROM usergroupmembers m, "
    		       .      "usergroup g, "
    		       .      "affiliation a "
    		       . "WHERE m.userid = $userid AND "
    		       .       "m.usergroupid = g.id AND "
    		       .       "g.affiliationid = a.id";
    	}
    	else {
    		$query = "SELECT m.usergroupid, "
    		       .        "g.name "
    		       . "FROM usergroupmembers m, "
    		       .      "usergroup g "
    		       . "WHERE m.userid = $userid AND "
    		       .       "m.usergroupid = g.id";
    	}

    	$qh = $this->doQuery($query, "101");

      exit;
    	$groups = array();
    	while($row = mysql_fetch_assoc($qh)) {
    		$groups[$row["usergroupid"]] = $row["name"];
    	}
    	if($includeowned) {
    		if($includeaffil) {
    			$query = "SELECT g.id AS usergroupid, "
    			       .        "CONCAT(g.name, '@', a.name) AS name "
    			       . "FROM usergroup g, "
    			       .      "affiliation a "
    			       . "WHERE g.ownerid = $userid AND "
    			       .       "g.affiliationid = a.id";
    		}
    		else {
    			$query = "SELECT id AS usergroupid, "
    			       .        "name "
    			       . "FROM usergroup "
    			       . "WHERE ownerid = $userid";
    		}
    		$qh = $this->doQuery($query, "101");
    		while($row = mysql_fetch_assoc($qh)) {
    			$groups[$row["usergroupid"]] = $row["name"];
    		}
    	}
    	uasort($groups, "sortKeepIndex");
    	return $groups;
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
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getImageConnectMethods($imageid, $revisionid, $nostatic=0)
    ///
    /// \param $imageid - id of an image
    /// \param $revisionid - (optional, default=0) revision id of image
    /// \param $nostatic - (optional, default=0) pass 1 to keep from using the
    /// static variable defined in the function
    ///
    /// \return an array of connect methods enabled for specified image where the
    /// key is the id of the connect method and the value is the description
    ///
    /// \brief builds an array of connect methods enabled for the image
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getImageConnectMethods($imageid, $revisionid=0, $nostatic=0) {
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getKey($data)
    ///
    /// \param $data - an array
    ///
    /// \return an md5 string that is unique for $data
    ///
    /// \brief generates an md5sum for $data
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getKey($data) {
    	return md5(serialize($data));
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn getProductionRevisionid($imageid, $nostatic=0)
    ///
    /// \param $imageid
    /// \param $nostatic - (optional, default=0) pass 1 to keep from using the
    /// static variable defined in the function
    ///
    /// \return the production revision id for $imageid
    ///
    /// \brief gets the production revision id for $imageid from the imagerevision
    /// table
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function getProductionRevisionid($imageid, $nostatic=0) {
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
    }

    ////////////////////////////////////////////////////////////////////////////////
    ///
    /// \fn $this->doQuery($query, $errcode, $db, $nolog)
    ///
    /// \param $query - SQL statement
    /// \param $errcode - error code
    /// \param $db - (optional, defaul=vcl), database to query against
    /// \param $nolog - (optional, defaul=0), don't log to queryLog table
    ///
    /// \return $qh - query handle
    ///
    /// \brief performs the query and returns $qh or aborts on error
    ///
    ////////////////////////////////////////////////////////////////////////////////
    function doQuery($query, $errcode=101, $db="vcl", $nolog=0) {
      global $mysql_link_vcl, $mysql_link_acct, $user, $mode, $ENABLE_ITECSAUTH;

      define("QUERYLOGGING", 1);

    	if($db == "vcl") {
    		if(QUERYLOGGING != 0 && (! $nolog) &&
    		   preg_match('/^(UPDATE|INSERT|DELETE)/', $query) &&
    		   strpos($query, 'UPDATE continuations SET expiretime = ') === FALSE) {
    			$logquery = str_replace("'", "\'", $query);
    			$logquery = str_replace('"', '\"', $logquery);
    			if(isset($user['id']))
    				$id = $user['id'];
    			else
    				$id = 0;
    			$q = "INSERT INTO querylog "
    			   .        "(userid, "
    			   .        "timestamp, "
    			   .        "mode, "
    			   .        "query) "
    			   . "VALUES "
    			   .        "($id, "
    			   .        "NOW(), "
    			   .        "'$mode', "
    			   .        "'$logquery')";
    			mysql_query($q, $mysql_link_vcl);
    		}
        var_dump(mysql_query($query, $mysql_link_vcl));
        exit;
    		for($i = 0; ! ($qh = mysql_query($query, $mysql_link_vcl)) && $i < 3; $i++) {
    			if(mysql_errno() == '1213') # DEADLOCK, sleep and retry
    				usleep(50);
    			else
    				abort($errcode, $query);
    		}
    	}
    	elseif($db == "accounts") {
    		if($ENABLE_ITECSAUTH)
    			$qh = mysql_query($query, $mysql_link_acct) or abort($errcode, $query);
    		else
    			$qh = NULL;
    	}
    	return $qh;
    }
}
