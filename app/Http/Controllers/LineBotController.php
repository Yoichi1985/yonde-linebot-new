<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineBotController extends Controller
{
    public function index()
    {
        return view('linebot.index');
    }

    public function parrot(Request $request)
    {
        Log::debug($request->header());
        Log::debug($request->input());

        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        $signature = $request->header('x-line-signature');

        if (!$lineBot->validateSignature($request->getContent(), $signature)) {
            abort(400, 'Invalid signature');
        }

        $events = $lineBot->parseEventRequest($request->getContent(), $signature);

        Log::debug($events);

        foreach ($events as $event) {
            if (!($event instanceof TextMessage)) {
                Log::debug('Non text message has come');
                continue;
            }

            $replyToken = $event->getReplyToken();
            $replyText = $event->getText();
            $num = "19851109";

            if ($num === ($replyText)) {
                $lineBot->replyText($replyToken, 'おめでとうございます！1,000ポイントGET(^^♪ポイントはLINE Payにチャージ、又はラインスタンプに交換ができます。');
            } else {
                $lineBot->replyText($replyToken, 'パスワードが間違っています。再度確認の上送信してください。');
            }
            // $lineBot->replyText($replyToken, $replyText);
            // $lineBot->replyText($replyToken, '成功');
        }
    }
}
