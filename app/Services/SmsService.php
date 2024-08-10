<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Traits\Makable;
use Illuminate\Support\Facades\Log;
use SmsGateway24\Exceptions\SDKException;
use SmsGateway24\SmsGateway24;

class SmsService
{
    use Makable;

    public function getToken()
    {
        $url = "https://smsgateway24.com/getdata/gettoken";
        $paramsArr = [];

        $paramsArr['email'] = config('sms.email');
        $paramsArr['pass'] = config('sms.password');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsArr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        /* Example of good answer: */
        /* {"error":0,"token":"1f0e86f9fe7ee6b52724fd5fd3511225","message":"OK"} */
        return json_decode($server_output, true)['token'] ?? null;
    }

    public function sendMessage(string $to, string $message, int $customerId, bool $urgent = false): void
    {
        try {
            $gateway = new SmsGateway24(config('sms.api_key'));
            $to = preg_replace_callback('/07(.*?)/s', function ($matches) {
                return "+9647{$matches[1]}";
            }, $to);
            Log::info("################ Sending To : $to ################");
            $smsId = $gateway->addSms(
                preg_replace_callback('/07(.*?)/s', function ($matches) {
                    return "+9647{$matches[1]}";
                }, $to),
                $message,
                config('sms.device_id'),
                now()->format('Y-m-d H:i:s'),
                null,
                $customerId,
                $urgent
            );

//            $smsStatus = $gateway->getSmsStatus($smsId);
//            Log::info("######### Sending Sms Status #########");
////            Log::info("{$smsStatus->status_description}");
//            Log::info("Code : {$smsStatus->status}");
//            Log::info("##################");

        } catch (\Exception|\Error $e) {
            Log::info("######### Sending Sms Error #########");
            Log::info("{$e->getMessage()} \n {$e->getFile()} \n Line: {$e->getLine()}");
            Log::info("######### To : $to #########");
            Log::info("######### Customer Id : $customerId #########");
            Log::info("######### ----------------- #########");
        }
    }

    public function sendVerificationCode($code, string $to, int $customerId): void
    {
        SendSmsJob::dispatch($to, "Hello Pom user \n Your phone verification code is $code", $customerId, true);
    }
}
