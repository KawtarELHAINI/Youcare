<?php

namespace App\Http\Controllers;

use App\Models\reservation;
use App\Models\Annoucements;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;


class ReservationController extends Controller
{

 
    public function store(Request $request, $id)
    {

        $user = Auth::guard('api')->user();
       
        if (!$user || $user->role !== 'benevole') {
        return response()->json([
            'status' => false,
            'message' => 'Only benevole can create reservations'
        ], 403); 
    }
        $validator = Validator::make($request->all(),[
            'message' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        }



       
        // $reservation->benevole_id = user()->id();

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
        $reservation->benevole_id = $user->id;
        $reservation->message = $request->message;
        $reservation->save();
        return response()->json([
            'status' => 200,
            'message' => 'Reservation submitted successfully'
        ], 200);
    }


    public function updateStatut($id, Request $request)
 {
     $request->validate([
         'Newstatuts' => 'required',
     ]);
     Reservation::where('id', $id)->update([
         'statuts' => $request->Newstatut,
     ]);
     return response()->json([
         'statut' => 'success',
         'message' => 'Reservation status updated successfully',
     ], 200);
 }



 public function index()
 {
    $user = Auth::guard('api')->user()->id;

     $reservations = Reservation::where('status', 'waiting')
         ->whereHas('annoucement', function ($query) use ($user) {
             $query->where('organizer_id', $user);
         })
         ->get();

     return response()->json([
         'statuts' => 'success',
         'reservations' => $reservations,

     ], 200);
 }


}

 

