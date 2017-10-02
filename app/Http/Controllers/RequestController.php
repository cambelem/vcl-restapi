Request$request$request<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Objects\Request;

class RequestController extends Controller
{
    public function index()
    {
        $request = Request::all();
        return response()->json($request);
    }

    public function getRequestInfo($id)
    {
        $request = Request::where('id', $id)->get();
        if(!empty($request['items'])){
          return response()->json($request);
        }
        else {
           return response()->json(['status' => 'fail']);
        }
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

        $request = new Request();
        $request->requestid         = $request->requestid;
        $request->computerid        = $request->computerid;
        $request->imageid           = $request->imageid;
        $request->imagerevisionid   = $request->imagerevisionid;
        $request->managementnodeid  = $request->managementnodeid;
        $request->remoteIP          = $request->remoteIP;
        $date = new \DateTime($request->lastcheck);
        $dd = $date->format('Y-m-d');
        $request->lastcheck         = $dd;
        $request->pw                = $request->pw;
        $request->connectIP         = $request->connectIP;
        $request->connectport       = $request->connectport;

        $request->save();
        return response()->json(['status' => 'success']);
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

        $request = Request::find($id);
        $request->requestid         = $request->requestid;
        $request->computerid        = $request->computerid;
        $request->imageid           = $request->imageid;
        $request->imagerevisionid   = $request->imagerevisionid;
        $request->managementnodeid  = $request->managementnodeid;
        $request->remoteIP          = $request->remoteIP;
        $date = new \DateTime($request->lastcheck);
        $dd = $date->format('Y-m-d');
        $request->lastcheck         = $dd;
        $request->pw                = $request->pw;
        $request->connectIP         = $request->connectIP;
        $request->connectport       = $request->connectport;

        $request->save();
        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        if(Reservations::destroy($id)){
            return response()->json(['status' => 'success']);
        }
    }
}


?>
