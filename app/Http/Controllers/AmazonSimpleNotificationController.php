<?php

namespace App\Http\Controllers;

use App\Services\AmazonSimpleNotificationService;
use App\Http\Requests\DeviceTokenPostRequest;
use Aws\Exception\AwsException;

class AmazonSimpleNotificationController extends Controller
{
    /**
     * @var amazonSimpleNotificationService
     */
    protected $amazonSimpleNotificationService;

    /**
     * Instantiate a new controller instance.
     *
     * @param AmazonSimpleNotificationService $amazonSimpleNotificationService Amazon simple notification service
     *
     * @return void
     */
    public function __construct(AmazonSimpleNotificationService $amazonSimpleNotificationService)
    {
        $this->amazonSimpleNotificationService = $amazonSimpleNotificationService;
    }

    /**
     * Index.
     *
     * @return void
     */
    public function index()
    {
        return view('notification');
    }

    /**
     * Publish.
     *
     * @return void
     */
    public function publish()
    {
        $title       = '亨利的世界標題';
        $body        = '亨利的世界內容';
        $icon        = secure_asset('images/messaging-icon.png');
        $clickAction = 'https://henrywar.blogspot.com/';
        try {
            $endpointArnAry = $this->amazonSimpleNotificationService->listEndpointsByPlatformApplication('arn:aws:sns:ap-southeast-1:852538786610:app/GCM/HenryLocal');
			foreach ($endpointArnAry as $endpointArn) {
				$this->amazonSimpleNotificationService->publish($endpointArn['EndpointArn'], $title, $body, $icon, $clickAction);
			}
        } catch (AwsException $e) {
        }
        echo '發送完成' . PHP_EOL;
    }

    /**
     * Register endpoint.
     *
     * @return void
     */
    public function registerEndpoint(DeviceTokenPostRequest $request)
    {
        //if ($request->ajax()) {
            try {
                $endpointArn = $this->amazonSimpleNotificationService->createPlatformEndpoint('arn:aws:sns:ap-southeast-1:852538786610:app/GCM/HenryLocal', $request['token']);
				return response()->json([
                    'success' => true
                ]);
            } catch (AwsException $e) {
			    dd($e->getMessage());
                // output error message if fails
                error_log($e->getMessage());
            }
        //}       
    }
}
