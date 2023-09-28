<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Notifications;
use App\Notifications\SendPushNotification; 

class FCMController extends Controller
{
    public function saveToken(Request $request)
    {
        try {
            $user = Auth::user();

            $existingToken = Token::where('user_id', $user->id)->get();

            if($existingToken){
                $token = $existingToken->device_token;
                return response()->json(["token" => $token]);
            }

            $token = $request->input('token');
            $fcm_token = new Token();
            $fcm_token->user_id = $user->id;
            $fcm_token->device_token = $token;
            $fcm_token->save();
            return response()->json(['token' => $token], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notification(Request $request){
    $request->validate([
        'title'=>'required',
        'message'=>'required',
        'fcmToken' => 'required'
    ]);

    try{
        $fcmTokens = $request->token;

        //Notifications::send(null,new SendPushNotification($request->title,$request->message,$fcmTokens));

        /* or */

        //auth()->user()->notify(new SendPushNotification($title,$message,$fcmTokens));

        /* or */

        Larafirebase::withTitle($request->title)
            ->withBody($request->message)
            ->sendMessage($fcmTokens);

        return response()->json(["sent!" => $request->message]);

    } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function sendNotification(Request $request)
    {
        $firebaseToken = $request->token;
            
        $SERVER_API_KEY = env('FCM_SERVER_KEY');
    
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $dataString = json_encode($data);
      
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
      
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                 
        $response = curl_exec($ch);
    
        return response()->json(['success', 'Notification send successfully.']);
    }


}
