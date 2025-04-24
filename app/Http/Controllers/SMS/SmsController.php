<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {

        // return $request->all();

        $basic  = new \Vonage\Client\Credentials\Basic("944c2b9a", "5NNIl5hDqCD2cbXT");
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($request->number, $request->subject, $request->message)
        );

        $message = $response->current();

        // return $message->getStatus();

        if ($message->getStatus() == 0) {
            return response()->json([
                'status' => true,
                'message' => 'The message was sent successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'The message failed with status: ' . $message->getStatus()
            ]);
        }
    }
}
