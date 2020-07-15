<?php

namespace App\Services;

use Aws\Laravel\AwsFacade as Aws;

/**
 * Amazon simple notification service
 *
 * @filesource
 */
class AmazonSimpleNotificationService
{
    /**
     * @var amazonSimpleNotificationClient
     */
    protected $amazonSimpleNotificationClient;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
        $this->amazonSimpleNotificationClient = AWS::createClient('sns');
	}

    /**
     * Creates a platform application object for one of the supported push notification services, such as APNS and GCM, to which devices and mobile apps may register.
     *
     * @param string $platformApplicationArn Platform application arn.
     * @param string $token                  Token.
     *
     * @return string Endpoint arn
     */
    public function createPlatformEndpoint(string $platformApplicationArn, string $token)
    {
        $response = $this->amazonSimpleNotificationClient->createPlatformEndpoint([
            'PlatformApplicationArn' => $platformApplicationArn,
            'Token'                  => $token,
            'Attributes'             => ['Enabled' => 'true']
        ]);
        return $response['EndpointArn'];
    }

    /**
     * Retrieves the endpoint attributes for a device on one of the supported push notification services, such as FCM and APNS.
     *
     * @param string $endpointArn Endpoint arn.
     *
     * @return string Attributes, e.g. { ["Enabled"]=> string "true" ["Token"]=> string "xyz" }
     */
    public function getEndpointAttributes(string $endpointArn)
    {
        $response = $this->amazonSimpleNotificationClient->getEndpointAttributes([
            'EndpointArn' => $endpointArn,
        ]);
        return $response['Attributes'];
    }

    /**
     * Deletes the endpoint for a device and mobile app from Amazon SNS.
     *
     * @param string $endpointArn Endpoint arn.
     *
     * @return string Endpoint arn
     */
    public function deleteEndpoint(string $endpointArn)
    {
        $response = $this->amazonSimpleNotificationClient->deleteEndpoint([
            'EndpointArn' => $endpointArn,
        ]);
        return ($response['statusCode'] == 200) ? true : false;
    }

    /**
     * Lists the endpoints and endpoint attributes for devices in a supported push notification service, such as GCM and APNS.
     *
     * @param string $platformApplicationArn Platform application arn.
     *
     * @return string Endpoints
     */
    public function listEndpointsByPlatformApplication(string $platformApplicationArn)
    {
        $response = $this->amazonSimpleNotificationClient->listEndpointsByPlatformApplication([
            'PlatformApplicationArn' => $platformApplicationArn,
        ]);
        return $response['Endpoints'];
    }

    /**
     * Sends a message to all of a topic's subscribed endpoints.
     *
     * @param mixed  $targetArn   Target arn.
     * @param string $title       Title.
     * @param string $body        Body.
     * @param mixed  $icon        Icon.
     * @param mixed  $clickAction Click action.
     *
     * @return string Message Id
     */
    public function publish($targetArn, string $title, string $body, $icon = null, $clickAction = null)
    {
        $published = $this->amazonSimpleNotificationClient->publish([
            'MessageStructure' => 'json',
            'Message'          => json_encode(['default' => 'default', 'GCM' => json_encode(['notification' => ['title' => $title, 'body' => $body, 'icon' => $icon, 'click_action' => $clickAction]])]),
            'TargetArn'        => $targetArn
        ]);
        return $published['MessageId'];
    }
}
