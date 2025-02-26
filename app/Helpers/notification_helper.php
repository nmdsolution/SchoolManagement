<?php

use App\Models\User;
use App\Models\Settings;

function send_notification($user, $title, $body, $type)
{
    $FcmToken = User::where('fcm_id', '!=', '')->whereIn('id', $user)->get()->pluck('fcm_id');

    $url = 'https://fcm.googleapis.com/fcm/send';
    $serverKey = Settings::select('message')->where('type', 'fcm_server_key')->pluck('message')->first();

    $notification_data1 = [
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        "title" => $title,
        "body" => $body,
        "type" => $type,

    ];
    $notification_data2 = [
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        "type" => $type,

    ];

    $data = [
        "registration_ids" => $FcmToken,
        "notification" => $notification_data1,
        "data" => $notification_data2,
        "priority" => "high"
    ];
    $encodedData = json_encode($data);

    $headers = [
        'Authorization:key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

    // Execute post
    $result = curl_exec($ch);
    if ($result == FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    // dd($result);

    // Close connection
    curl_close($ch);
}


function send_sms($phone, $body){
    $url = 'https://mboadeals.net/api/v1/sms/sendsms';
    $user_id = env('SMS_UID');
    $password = env('SMS_PASSWORD');
    $sender_name = env('SMS_SENDERNAME');

    $data = array(
        'user_id' => $user_id,
        'message' => $body,
        'password' => $password,
        'phone_str' => $phone,
        'sender_name' => $sender_name
    );

    $encodedData = json_encode($data);

    $headers = [
        'Content-Type: application/json',
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);

    $response = curl_exec($curl);
    curl_close($curl);

    if($response){
        $response = json_decode($response, true);
        
        if($response['success']) return true;
    }

    return false;
}