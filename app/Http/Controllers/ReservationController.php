<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Objects\Reservations;
use App\Manager\ReservationManager;

class ReservationController extends Controller
{
    public function index()
    {
        $manager = new ReservationManager();
        $manager->viewRequests();
        //$reservations = Reservations::all();
        //return response()->json($reservations);
    }

    public function update(Request $request, $id)
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

    public function store(Request $request)
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

    public function showById($id)
    {
        $reservation = Reservations::where('id', $id)->get();
        if(!empty($reservation['items'])){
          return response()->json($reservation);
        }
        else {
           return response()->json(['status' => 'fail']);
        }
    }

    public function updateById(Request $request, $id)
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

    public function destroyById($id)
    {
        if(Reservations::destroy($id)){
            return response()->json(['status' => 'success']);
        }
    }
}


?>
