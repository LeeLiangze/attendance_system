<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class AdsController extends Controller
{
    private $view_path;
    private $ctrl_url;

    public function __construct()
    {
        $this->middleware('auth');
        $this->ctrl_url = '/';
        $this->view_path = 'ManageOrganiser.Dashboard';
        View::share(['ctrl_url' => $this->ctrl_url, 'view_path' => $this->view_path, 'module_name' => 'OutLook', 'title' => 'Calendar']);
    }

    public function index()
    {
        return view($this->view_path . '.index');
    }


    public function login()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => env('OAUTH_APP_ID'),
            'clientSecret' => env('OAUTH_APP_PASSWORD'),
            'redirectUri' => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
            'urlResourceOwnerDetails' => '',
            'scopes' => env('OAUTH_SCOPES')
        ]);
        if (!isset($_GET['code'])) {
            // Generate the auth URL
            $authorizationUrl = $oauthClient->getAuthorizationUrl(['state'=>env('OAUTH_APP_ID'),
                'resource'=>env('RESOURCES')]);
            // Save client state so we can validate in response
            $_SESSION['oauth2state'] = $oauthClient->getState();
            // Redirect to authorization endpoint
            header('Location: ' . $authorizationUrl);
            exit();
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            exit('Invalid state');
        } else {
            try {
                // Try to get an access token using the authorization code grant.
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $tokenCache = new \App\TokenStore\TokenCache;
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(),
                    $accessToken->getExpires());
                // Redirect back to mail page
                return redirect(route('/'));

            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            }
        }
    }
}
