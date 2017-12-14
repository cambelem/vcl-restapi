<?php //app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use League\Fractal\Manager;
require_once __DIR__ . ('/../../.ht-inc/utils.php');

class Controller extends BaseController
{
    use ResponseTrait;

    /**
     * Constructor
     *
     * @param Manager|null $fractal
     */
    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal === null ? new Manager() : $fractal;
        $this->setFractal($fractal);
    }

    /**
     * Validate HTTP request against the rules
     *
     * @param Request $request
     * @param array $rules
     * @return bool|array
     */
    protected function validateRequest(Request $request, array $rules)
    {
        // Perform Validation
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            // crete error message by using key and value
            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return NULL;
    }

    public function initBegin()
    {
        initGlobals();
//var_dump($GLOBALS['vclhost1']);
        dbConnect();

        global $user;

        $user = array();
        $user['unityid']="admin";
        $user['affiliationid']="1";
        $user['affiliation']="Local";
        $user['firstname']="vcl";
        $user['lastname']="admin";
        $user['preferredname']="";
        $user['email']="root@localhost";
        $user['emailnotices']="0";
        $user['IMtype']=null;
        $user['IMid']=null;
        $user['id']="1";
        $user['width']="1024";
        $user['height']="768";
        $user['bpp']="16";
        $user['audiomode']="local";
        $user['mapdrives']="1";
        $user['mapprinters']="1";
        $user['mapserial']="1";
        $user['rdpport']="3389";
        $user['showallgroups']="1";
        $user['lastupdated']="2007-05-17 09:58:39";
        $user['usepublickeys']="0";
        $user['sshpublickeys']="";
        $user['shibonly']="0";

        $user['groups'][3]="adminUsers";
        $user['groups'][6]="Allow No User Check";
        $user['groups'][7]="Default for Editable by";
        $user['groups'][1]="global";
        $user['groups'][4]="manageNewImages";
        $user['groups'][5]="Specify End Time";

        $user['groupperms'][1]="Manage Additional User Group Permissions";
        $user['groupperms'][2]="Manage Block Allocations (global)";
        $user['groupperms'][14]="Manage Federated User Groups (global)";
        $user['groupperms'][5]="Manage VM Profiles";
        $user['groupperms'][7]="Schedule Site Maintenance";
        $user['groupperms'][6]="Search Tools";
        $user['groupperms'][3]="Set Overlapping Reservation Count";
        $user['groupperms'][16]="Site Configuration (global)";
        $user['groupperms'][11]="User Lookup (affiliation only)";
        $user['groupperms'][10]="User Lookup (global)";
        $user['groupperms'][9]="View Dashboard (affiliation only)";
        $user['groupperms'][8]="View Dashboard (global)";
        $user['groupperms'][4]="View Debug Information";
        $user['groupperms'][12]="View Statistics by Affiliation";

        $user['privileges'][0]="addomainAdmin";
        $user['privileges'][1]="cascade";
        $user['privileges'][2]="computerAdmin";
        $user['privileges'][3]="groupAdmin";
        $user['privileges'][4]="imageAdmin";
        $user['privileges'][5]="imageCheckOut";
        $user['privileges'][6]="mgmtNodeAdmin";
        $user['privileges'][7]="nodeAdmin";
        $user['privileges'][8]="resourceGrant";
        $user['privileges'][9]="scheduleAdmin";
        $user['privileges'][10]="serverCheckOut";
        $user['privileges'][11]="userGrant";
        $user['privileges'][12]="managementnodeAdmin";
        $user['login']="admin";
        $user['memberCurrentBlock']=0;
      //  setVCLLocale();

    }

    public function end()
    {
        cleanSemaphore();
        dbDisconnect();
    }
}
