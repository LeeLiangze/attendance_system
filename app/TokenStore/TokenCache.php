<?php

namespace App\TokenStore;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TokenCache {
    private static $awsApiUrl = "https://adsprapiman-aue.azure-api.net/cds/odata/Staff";
    private static $key = '54b97d3c14a942ea886ffc8047c186de';
    private static $request_parameter = array(
        "\$select" => "KnownAs,LastName,StaffId,AccountingCentreCode,GroupName,RegionName,LocationName,StartDate,Email",
        "\$filter" => "StaffStatus eq 'Employee' and LocationName eq 'Singapore Office'",
        "\$skip" => "400",
        "\$orderby" => "StartDate asc",
        "\$count" => "true"
    );

    private static $enableFiddler = false;

    public static function storeTokens($access_token, $refresh_token, $expires) {
        $token_expires = Carbon::createFromTimestamp($expires)->toDateTimeString();
        DB::table('oauth_token')->insert(['access_token'=>$access_token,'refresh_token'=>$refresh_token,'token_expires'=>$token_expires]);
    }

    public static function clearTokens() {
        unset($_SESSION['access_token']);
        unset($_SESSION['refresh_token']);
        unset($_SESSION['token_expires']);
    }

    public static function getAccessToken() {
        // Check if tokens exist
        $token = DB::table('oauth_token')->orderBy('id','desc')->first();
        $expires = $token->token_expires;
        $refreshToken = $token->refresh_token;
        $accessToken = $token->access_token;
        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = Carbon::now();
        if ($expires <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => env('OAUTH_APP_ID'),
                'clientSecret'            => env('OAUTH_APP_PASSWORD'),
                'redirectUri'             => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => env('RESOURCES'),
                'scopes'                  => env('OAUTH_SCOPES')
            ]);
            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $refreshToken
                ]);
                // Store the new values
                self::storeTokens($newToken->getToken(), $newToken->getRefreshToken(),
                    $newToken->getExpires());

                return $newToken->getToken();
            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        }
        else {
            // Token is still valid, just return it
            return $accessToken;
        }
    }

    // Parses an ID token returned from Azure to get the user's
    // display name.
    public static function getUserName($id_token)
    {
        $token_parts = explode(".", $id_token);

        // First part is header, which we ignore
        // Second part is JWT, which we want to parse
        error_log("getUserName found id token: " . $token_parts[1]);

        // First, in case it is url-encoded, fix the characters to be
        // valid base64
        $encoded_token = str_replace('-', '+', $token_parts[1]);
        $encoded_token = str_replace('_', '/', $encoded_token);
        error_log("After char replace: " . $encoded_token);

        // Next, add padding if it is needed.
        switch (strlen($encoded_token) % 4) {
            case 0:
                // No pad characters needed.
                error_log("No padding needed.");
                break;
            case 2:
                $encoded_token = $encoded_token . "==";
                error_log("Added 2: " . $encoded_token);
                break;
            case 3:
                $encoded_token = $encoded_token . "=";
                error_log("Added 1: " . $encoded_token);
                break;
            default:
                // Invalid base64 string!
                error_log("Invalid base64 string");
                return null;
        }

        $json_string = base64_decode($encoded_token);
        error_log("Decoded token: " . $json_string);
        $jwt = json_decode($json_string, true);
        error_log("Found user name: " . $jwt['name']);
        return $jwt['name'];
    }

    // get all staff info
    public static function getAllStaff($skip = "0"){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $access_token = self::getAccessToken();
        $request_param = array(
            "\$select" => "KnownAs,LastName,StaffId,AccountingCentreCode,GroupName,RegionName,LocationName,StartDate,Email",
            "\$filter" => "StaffStatus eq 'Employee' and LocationName eq 'Singapore Office'",
            "\$skip" => $skip,
            "\$orderby" => "StartDate asc",
            "\$count" => "true"
        );
        $url = self::$awsApiUrl . "?" . http_build_query($request_param);
        $request = self::makeApiCall($access_token, "GET", $url);
        return $request;
    }

    // Uses the Calendar API's CalendarView to get all events
    // on a specific day. CalendarView handles expansion of recurring items.
    public static function getEventsForDate($access_token, $date)
    {
        error_log("getEventsForDate called:");
        error_log("  access token: " . $access_token);
        error_log("  date: " . date_format($date, "M j, Y g:i a (e)"));

        // Set the start of our view window to midnight of the specified day.
        $windowStart = $date->setTime(0, 0, 0);
        $windowStartUrl = self::encodeDateTime($windowStart);
        error_log("  Window start (UTC): " . $windowStartUrl);

        // Add one day to the window start time to get the window end.
        $windowEnd = $windowStart->add(new DateInterval("P1D"));
        $windowEndUrl = self::encodeDateTime($windowEnd);
        error_log("  Window end (UTC): " . $windowEndUrl);

        // Build the API request URL
        $calendarViewUrl = self::$outlookApiUrl . "/Me/CalendarView?"
            . "startDateTime=" . $windowStartUrl
            . "&endDateTime=" . $windowEndUrl
            . "&\$select=Subject,Start,End" // Use $select to limit the data returned
            . "&\$orderby=Start";           // Sort the results by the start time.

        return self::makeApiCall($access_token, "GET", $calendarViewUrl);
    }

    // Use the Calendar API to add an event to the default calendar.
    public static function addEventToCalendar($subject, $location, $startTime, $endTime, $attendeeString, $userName)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $access_token = self::getAccessToken();
        // Create a static body.
        $htmlBody = "Added by ". $userName;
        // Generate the JSON payload
        $event = array(
            "subject" => $subject,
            "location" => array("displayName" => $location),
            "start" => ["dateTime"=>$startTime->toDateTimeString(),"timeZone"=>"Singapore Standard Time"],
            "end" => ["dateTime"=>$endTime->toDateTimeString(),"timeZone"=>"Singapore Standard Time"],
            "body" => array("contentType" => "HTML", "content" => $htmlBody),
            "attendees" => array(array(
                "emailAddress" => ["address" => $attendeeString,"name"=>$userName],
                "type" => "required"
            ))
        );
        $eventPayload = json_encode($event);
        error_log("EVENT PAYLOAD: " . $eventPayload);
        $createEventUrl = self::$outlookApiUrl . "/me/events";
        $response = self::makeApiCall($access_token, "POST", $createEventUrl, $eventPayload);
        // If the call succeeded, the response should be a JSON representation of the
        // new event. Try getting the Id property and return it.
        if ($response['id']) {
            return $response['id'];
        } else {
            error_log("ERROR: " . $response);
            return $response;
        }
    }

    // Use the Calendar API to add an event to the default calendar.
    public static function updateEventToCalendar($subject, $event_id = '')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $access_token = self::getAccessToken();
        if (!empty($event_id)) {
            // Generate the JSON payload
            $event = array(
                "subject" => $subject);
            $eventPayload = json_encode($event);
            error_log("EVENT PAYLOAD: " . $eventPayload);

            $createEventUrl = self::$outlookApiUrl . "/me/events/" . $event_id;

            $response = self::makeApiCall($access_token, "PATCH", $createEventUrl, $eventPayload);

            // If the call succeeded, the response should be a JSON representation of the
            // new event. Try getting the Id property and return it.
            if ($response['id']) {
                return $response['id'];
            } else {
                error_log("ERROR: " . $response);
                return $response;
            }
        }
        return 'TRUE';
    }

    // Use the Calendar API to add an event to the default calendar.
    public static function deleteEventToCalendar($event_id = '')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $access_token = self::getAccessToken();
        if (!empty($event_id)) {

            // Generate the JSON payload
            $createEventUrl = self::$outlookApiUrl . "/me/events/" . $event_id;

            self::makeApiCall($access_token,"DELETE", $createEventUrl);

        }
        return 'TRUE';
    }

    // Use the Calendar API to add an attachment to an event.
    public static function addAttachmentToEvent($access_token, $eventId, $attachmentData)
    {
        // Generate the JSON payload
        $attachment = array(
            "@odata.type" => "#Microsoft.OutlookServices.FileAttachment",
            "Name" => "voucher.txt",
            "ContentBytes" => base64_encode($attachmentData)
        );

        $attachmentPayload = json_encode($attachment);
        error_log("ATTACHMENT PAYLOAD: " . $attachmentPayload);

        $createAttachmentUrl = self::$outlookApiUrl . "/Me/Events/" . $eventId . "/Attachments";

        return self::makeApiCall($access_token, "POST", $createAttachmentUrl, $attachmentPayload);
    }

    // Make an API call.
    public static function makeApiCall($access_token, $method, $url, $payload = NULL)
    {
//        var_dump($access_token, $method, $url, $payload);
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'Ocp-Apim-Subscription-Key' => self::$key,
        );
        $client = new Client();
        switch (strtoupper($method)) {
            case "GET":
                $request = $client->get($url,['headers' => $headers]);
                $response = $request->getBody();
                break;
            case "POST":
                $request = $client->post($url,['body' => $payload,'headers' => $headers]);
                $response = $request->getBody()->getContents();
                break;
            case "PATCH":
                $request = $client->patch($url,  ['body'=>$payload, 'headers'=>$headers]);
                $response = $request->getBody()->getContents();
                break;
            case "DELETE":
                $request = $client->delete($url, ['headers'=>$headers]);
                $response = print_r($request, 1);
                break;
            default:
                error_log("INVALID METHOD: " . $method);
                exit;
        }

        return json_decode($response, true);
    }

    // This function convert a dateTime from local TZ to UTC, then
    // encodes it in the format expected by the Outlook APIs.
    public static function encodeDateTime($dateTime)
    {
        $utcDateTime = $dateTime->setTimeZone(new \DateTimeZone("Asia/Singapore"));
        $dateFormat = "Y-m-d\TH:i:s\Z";
        return date_format($utcDateTime, $dateFormat);
    }

    // This function generates a random GUID.
    public static function makeGuid()
    {
        if (function_exists('com_create_guid')) {
            error_log("Using 'com_create_guid'.");
            return strtolower(trim(com_create_guid(), '{}'));
        } else {
            error_log("Using custom GUID code.");
            $charid = strtolower(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);

            return $uuid;
        }
    }

    public static function isFailure($httpStatus)
    {
        // Simplistic check for failure HTTP status
        return ($httpStatus >= 400);
    }
}
