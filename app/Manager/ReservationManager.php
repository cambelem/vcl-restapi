<?php

namespace App\Manager;

use App\Models\Imagemeta;
use App\Models\ImageRevision;
use App\Models\Vcluser;
use Illuminate\Database\Schema\Blueprint;

require_once __DIR__ . ("/../.ht-inc/utils.php");
require_once __DIR__ . ("/../.ht-inc/requests.php");


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

  function createReservation($postData) {
    if (empty($postData))
      return;
    global $user, $remoteIP;

    $data = processRequestInput($postData);

    if($data['start'] == 0) {
      $nowfuture = 'now';
      $startts = unixFloor15();
      if($data['ending'] == 'duration') {
        $endts = $startts + ($data['duration'] * 60);
        $nowArr = getdate();
        if(($nowArr['minutes'] % 15) != 0)
          $endts += 900;
      }
    }
    else {
      $nowfuture = 'future';
      $startts = $data['start'];
      if($data['ending'] == 'duration')
        $endts = $startts + ($data['duration'] * 60);
    }
    if($data['ending'] == 'indefinite')
      $endts = datetimeToUnix('2038-01-01 00:00:00');
    elseif($data['ending'] == 'endat')
      $endts = $postData['end'];

    $images = getImages();

    # check for exceeding max overlaps
    $max = getMaxOverlap($user['id']);
    if(checkOverlap($startts, $endts, $max)) {
      print "dojo.byId('deployerr').innerHTML = '";
      print i("The selected time overlaps with another reservation you have.");
      print "<br>";
      if($max == 0)
        print i("You cannot have any overlapping reservations.");
      else
        printf(i("You can have up to %d overlapping reservations."), $max);
      print "'; dojo.removeClass('deployerr', 'hidden');";
      return;
    }

    $imaging = 0;
    if($data['type'] == 'imaging')
      $imaging = 1;

    $availablerc = isAvailable($images, $data['imageid'], $data['revisionids'],
                               $startts, $endts, 1, 0, 0, 0, $imaging, $data['ipaddr'],
                               $data['macaddr']);

    if($availablerc == -4) {
      $msg = i("The IP address you specified is assigned to another VCL node and cannot be used at this time. Submitting a time in the future may allow you to make the reservation, but if the IP remains assigned to the other node, the reservation will fail at deploy time.");
      $data = array('err' => 1,
                    'errmsg' => $msg);
      sendJSON($data);
      return;
    }
    elseif($availablerc == -3) {
      $msg = i("The IP or MAC address you specified overlaps with another reservation using the same IP or MAC address you specified. Please use a different IP or MAC or select a different time to deploy the server.");
      $data = array('err' => 1,
                    'errmsg' => $msg);
      sendJSON($data);
      return;
    }
    elseif($availablerc == -2) {
      $msg = i("The time you requested overlaps with a maintenance window.");
      $data = array('err' => 1,
                    'errmsg' => $msg);
      sendJSON($data);
      return;
    }
    elseif($availablerc == -1) {
      cleanSemaphore();
      $msg = i("You have requested an environment that is limited in the number of concurrent reservations that can be made. No further reservations for the environment can be made for the time you have selected.");
      $data = array('err' => 1,
                    'errmsg' => $msg);
      sendJSON($data);
      return;
    }
    elseif($availablerc == 0) {
      cleanSemaphore();
      $data = array('err' => 2);
      sendJSON($data);
      return;
    }
    $requestid = addRequest($imaging, $data['revisionids'], (1 - $data['nousercheck']));
    if($data['type'] == 'server') {
      if($data['ipaddr'] != '') {
        # save additional network info in variable table
        $allnets = getVariable('fixedIPavailnetworks', array());
        $key = long2ip($data['network']) . "/{$data['netmask']}";
        $allnets[$key] = array('router' => $data['router'],
                        'dns' => $data['dnsArr']);
        setVariable('fixedIPavailnetworks', $allnets, 'yaml');
      }
      $query = "UPDATE reservation "
             . "SET remoteIP = '$remoteIP' "
             . "WHERE requestid = $requestid";
      doQuery($query);

      $fields = array('requestid'/*, 'serverprofileid'*/);
      $values = array($requestid/*, $data['profileid']*/);
      if($data['name'] == '') {
        $fields[] = 'name';
        $name = $images[$data['imageid']]['prettyname'];
        $values[] = "'$name'";
      }
      else {
        $fields[] = 'name';
        $name = mysql_real_escape_string($data['name']);
        $values[] = "'$name'";
      }
      if($data['ipaddr'] != '') {
        $fields[] = 'fixedIP';
        $values[] = "'{$data['ipaddr']}'";
      }
      if($data['macaddr'] != '') {
        $fields[] = 'fixedMAC';
        $values[] = "'{$data['macaddr']}'";
      }
      if($data['admingroupid'] != 0) {
        $fields[] = 'admingroupid';
        $values[] = $data['admingroupid'];
      }
      if($data['logingroupid'] != 0) {
        $fields[] = 'logingroupid';
        $values[] = $data['logingroupid'];
      }
      if($data['monitored'] != 0) {
        $fields[] = 'monitored';
        $values[] = 1;
      }
      $allfields = implode(',', $fields);
      $allvalues = implode(',', $values);
      $query = "INSERT INTO serverrequest ($allfields) VALUES ($allvalues)";
      doQuery($query, 101);
      if($data['ipaddr'] != '') {
        $srqid = dbLastInsertID();
        $var = array('netmask' => $data['netmask'],
                      'router' => $data['router'],
                      'dns' => $data['dnsArr']);
        setVariable("fixedIPsr$srqid", $var, 'yaml');
      }
      # TODO configs
      //saveRequestConfigs($requestid, $data['imageid'], $data['configs'], $data['configvars']);

      // Successfully added?
      return 1;
    }
  }

  function deleteReservation($id)
  {
    // TODO: $id is trusting that id is a number. Come back to this later
    $requestid = $id;

    // getRequestInfo grabbing alot of info not needed below (except serverrequest)
    $request = getRequestInfo($requestid, 1);
    if(is_null($requestid)) {
      viewRequests();
      return;
    }

    if($request['serverrequest']) {
      $query = "SELECT id FROM serverrequest WHERE requestid = $requestid";
      $qh = doQuery($query);
      if($row = mysql_fetch_assoc($qh)) {
        $query = "DELETE FROM serverrequest WHERE requestid = $requestid";
        doQuery($query, 152);
        deleteVariable("fixedIPsr{$row['id']}");
      }
    }

    $query = "DELETE FROM request WHERE id = $requestid";
    doQuery($query, 153);

    $query = "DELETE FROM reservation WHERE requestid = $requestid";
    doQuery($query, 154);

    // assumes everything deleted successfull
    // TODO: Make sure it's deletd.
    return 1;
  }
}
