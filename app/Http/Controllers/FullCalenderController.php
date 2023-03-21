<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\Event;
  
class FullCalenderController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
  
  
        return view('fullcalender');
    }

    public function getevents(Request $request)
    {
  
        
       
             $data = Event::whereDate('start', '>=', $request->start)
                       ->whereDate('end',   '<=', $request->end)
                       ->get(['id', 'title', 'start', 'end']);
  
             return response()->json($data);
        
  
    }

    public function editevent(Request $request)
    {
        $where = array('id' => $request->id);
        $event  = Event::where($where)->first();

        return Response()->json($event);
    }
 
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function ajax(Request $request)
    {
        $event = Event::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
            'title' => $request->title,
            'description' => $request->description,
            'start' => $request->start_time,
            'end' => $request->end_time,
            ]
        );

        return response()->json($event);


        /*
        switch ($request->type) {

            case 'add':
              $event = Event::create([
                  'title' => $request->title,
                  'description' => $request->description,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
 
              return response()->json($event);
             break;
  
           case 'update':
              $event = Event::find($request->id)->update([
                  'title' => $request->title,
                  'description' => $request->description,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
 
              return response()->json($event);
             break;
  
           case 'delete':
              $event = Event::find($request->id)->delete();
  
              return response()->json($event);
             break;
             
           default:
             # code...
             break;
        }
*/
    }

    public function deleteevent(Request $request)
    {
        $event = Event::where('id', $request->id)->delete();

        return Response()->json($event);
    }

}