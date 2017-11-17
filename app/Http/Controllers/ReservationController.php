<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Objects\Reservations;
use App\Manager\ReservationManager;

class ReservationController extends Controller
{
    public function getAllReservations()
    {
        $this->initBegin();

        $manager = new ReservationManager();
        $results = $manager->getAllReservations();

        $this->end();

        if ($results != NULL)
          return json_encode($results);
        else {
          return response()->json(['status' => 'fail'], 404);
        }
    }

    public function getReservation($id)
    {
        $this->initBegin();

        $manager = new ReservationManager();
        $results = $manager->getReservation($id);

        $this->end();

        if ($results != NULL)
          return json_encode($results);
        else {
          return response()->json(['status' => 'fail'],404);
        }
    }

    public function createReservation(Request $request, $id)
    {
        $this->validate($request, [
          'requestid'         => 'required',
          'computerid'        => 'required',
          'imageid'           => 'required',
          'imagerevisionid'   => 'required',
          'managementnodeid'  => 'required',
          'remoteIP'          => 'required',
          'lastcheck'         => 'required',
          'pw'                => 'required',
          'connectIP'         => 'required',
          'connectport'       => 'required'
        ]);
/*
        $reservation = Reservations::find($id);
        $reservation->requestid         = $request->requestid;
        $reservation->computerid        = $request->computerid;
        $reservation->imageid           = $request->imageid;
        $reservation->imagerevisionid   = $request->imagerevisionid;
        $reservation->managementnodeid  = $request->managementnodeid;
        $reservation->remoteIP          = $request->remoteIP;
        $date = new \DateTime($request->lastcheck);
        $dd = $date->format('Y-m-d');
        $reservation->lastcheck         = $dd;
        $reservation->pw                = $request->pw;
        $reservation->connectIP         = $request->connectIP;
        $reservation->connectport       = $request->connectport;

        $reservation->save();
        return response()->json(['status' => 'success']);
        */
    }

/*
    public function updateReservation(Request $request)
    {
        $this->validate($request, [
          'requestid'         => 'required',
          'computerid'        => 'required',
          'imageid'           => 'required',
          'imagerevisionid'   => 'required',
          'managementnodeid'  => 'required',
          'remoteIP'          => 'required',
          'lastcheck'         => 'required',
          'pw'                => 'required',
          'connectIP'         => 'required',
          'connectport'       => 'required'
        ]);

        $reservation = new Reservations();
        $reservation->requestid         = $request->requestid;
        $reservation->computerid        = $request->computerid;
        $reservation->imageid           = $request->imageid;
        $reservation->imagerevisionid   = $request->imagerevisionid;
        $reservation->managementnodeid  = $request->managementnodeid;
        $reservation->remoteIP          = $request->remoteIP;
        $date = new \DateTime($request->lastcheck);
        $dd = $date->format('Y-m-d');
        $reservation->lastcheck         = $dd;
        $reservation->pw                = $request->pw;
        $reservation->connectIP         = $request->connectIP;
        $reservation->connectport       = $request->connectport;

        $reservation->save();
        return response()->json(['status' => 'success']);
    }
*/

    public function updateReservation(Request $request, $id)
    {
        $this->validate($request, [
          'requestid'         => 'required',
          'computerid'        => 'required',
          'imageid'           => 'required',
          'imagerevisionid'   => 'required',
          'managementnodeid'  => 'required',
          'remoteIP'          => 'required',
          'lastcheck'         => 'required',
          'pw'                => 'required',
          'connectIP'         => 'required',
          'connectport'       => 'required'
        ]);

exit;
        $reservation = Reservations::find($id);
        $reservation->requestid         = $request->requestid;
        $reservation->computerid        = $request->computerid;
        $reservation->imageid           = $request->imageid;
        $reservation->imagerevisionid   = $request->imagerevisionid;
        $reservation->managementnodeid  = $request->managementnodeid;
        $reservation->remoteIP          = $request->remoteIP;
        $date = new \DateTime($request->lastcheck);
        $dd = $date->format('Y-m-d');
        $reservation->lastcheck         = $dd;
        $reservation->pw                = $request->pw;
        $reservation->connectIP         = $request->connectIP;
        $reservation->connectport       = $request->connectport;

        $reservation->save();
        return response()->json(['status' => 'success']);
    }

    public function deleteReservation($id)
    {
        if(Reservations::destroy($id)){
            return response()->json(['status' => 'success']);
        }
    }
}


?>
