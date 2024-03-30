<?php

namespace App\Http\Controllers\api;

use App\Models\Annoucements;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;





class AnnoucementsController extends Controller
{
    public function index()
    {
       $annoucements = Annoucements::all();
       if($annoucements->count() > 0){

        return response()->json([
            'status' =>200,
            'annoucements' =>$annoucements
        ], 200);
       }else{

        return response()->json([
            'status' => 404,
            'message_status' =>'NO Records Found'
        ],404);
       }
    }
    public function store(Request $request){
        
        $user = Auth::guard('api')->user();
       
        if (!$user || $user->role !== 'organizer') {
        return response()->json([
            'status' => false,
            'message' => 'Only organizers can create announcements'
        ], 403); 
    }
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'required_skills' => 'required|array'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);

        } else {

            $requiredSkillsString = implode(',', $request->required_skills);
            $announcement = new Annoucements();
            $announcement->title = $request->title;
            $announcement->type = $request->type;
            $announcement->date = $request->date;
            $announcement->description = $request->description;
            $announcement->location = $request->location;
            $announcement->required_skills = $requiredSkillsString;
            $announcement->organizer_id = $user->id;
            $announcement->save();

            if($announcement){
                return response()->json([
                    'status' => 200,
                    'message' => 'announcement created successfully',
                    'announcement' => $announcement

                ],200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'something went wrong'
                ],500);
            }
      }
 }
 public function show($id){
    $announcement = Annoucements::find($id);
    $user = Auth::guard('api')->user();
     if ($announcement){
         return response()->json([
             'status' => 200,
             'announcement' => $announcement
         ],200);
     } else {
         return response()->json([
             'status' => 404,
             'message' => 'no announcement found'
         ],404);
     }
 }
    public function edit($id){
        $user = Auth::guard('api')->user();
        $announcement = Annoucements::find($id);
        if ($announcement){
            return response()->json([
                'status' => 200,
                'message' => $announcement
            ],200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no annoucement found'
            ],404);
        }
    }
    
  

    public function update(Request $request, int $id){

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'required_skills' => 'required|string'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $announcement = Annoucements::find($id);
            
            if($announcement){
                $announcement->update([
                    'title' => $request->title,
                    'type' => $request->type,
                    'date' => $request->date,
                    'description' => $request->description,
                    'location' => $request->location,
                    'required_skills' => $request->required_skills,
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Announcement updated successfully'
                ],200);
            }
            else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No such announcement found'
                ],404);
            }
        }
    }    
  
       
    public function destroy($id){
        $announcement = Annoucements::find($id);
        if ($announcement){
            $announcement->delete();
            return response()->json([
                'status' => 200,
                'message' => 'announcement deleted successfully'
            ],404);
        }
        else {
            return response()->json([
                'status' => 404,
                'message' => 'no such announcement found'
            ],404);
        }

    }


    public function apply(Request $request, $id){
       
        {
            $validator = Validator::make($request->all(),[
                'message' => 'required|string|max:255',
                'required_skills' => 'required|array'
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages()
                ], 422);
            }
            // if announcement exists
            $annoucement = Annoucements::find($id);
            if (!$annoucement) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Announcement not found'
                ], 404);
            }
            $reservation = new reservation();
            $reservation->annoucement_id = $id;
            $reservation->benevole_id = auth()->id();
            $reservation->message = $request->message;
            $reservation->required_skills = $annoucement->required_skills;
            $reservation->save();
            return response()->json([
                'status' => 200,
                'message' => 'Reservation submitted successfully'
            ], 200);
        }
    
    }

    public function acceptApplication($applicationId){
        $user = auth()->user();
            if (!$user || $user->role !== 'organizer') {
            return response()->json([
                'status' => false,
                'message' => 'Only organizers can accept applications'
        ], 403); 
        }
        $application = Application::findOrFail($applicationId);
        $application->update(['status' => 'accepted']);

        return response()->json([
             'status' => 200,
             'message' => 'Application accepted successfully'
        ], 200);
    }
 
    public function rejectApplication($applicationId) {
        $user = auth()->user();
        if (!$user || $user->role !== 'organizer') {
            return response()->json([
                'status' => false,
                'message' => 'Only organizers can reject applications'
            ], 403); 
        }
        $application = Application::findOrFail($applicationId);


        $application->update(['status' => 'rejected']);

        return response()->json([
            'status' => 200,
            'message' => 'Application rejected successfully'
        ], 200);
    }

    public function userApplications()
{
    $user = auth()->user();
    
    if ($user && $user->role === 'volunteer') {
        $applications = Application::where('volunteer_id', $user->id)->get();
        
        if ($applications->count() > 0) {
            return response()->json([
                'status' => 200,
                'applications' => $applications
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No applications found for the authenticated user'
            ], 404);
        }
    } else {
        return response()->json([
            'status' => 403,
            'message' => 'Only volunteers can access their applications'
        ], 403);
    }
}

}

 