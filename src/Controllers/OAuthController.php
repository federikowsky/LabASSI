<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Core\Flash;
use App\Facades\OAuth;

class OAuthController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * Redirects the user to the login page of the specified OAuth provider.
     *
     * @param string $provider
     */
    public function redirect_to_provider(string $provider)
    {
        $oauth_service = OAuth::get_provider($provider);
        $auth_url = $oauth_service->get_auth_url();
        header('Location: ' . $auth_url);
        exit();
    }

    /**
     * Handle the callback from the specified OAuth provider.
     *
     * @param string $provider
     */
    public function handle_provider_callback(string $provider)
    {
        if (!isset($_GET['code'])) {
            throw new \Exception('Auth code not provided');
        }

        try {
            // Authenticate the user and get the profile details via the specified provider
            $oauth_service = OAuth::get_provider($provider);
            $user_info = $oauth_service->authenticate($_GET['code']);

            // Regenerate the session to prevent CSRF and session fixation attacks and set the user data
            if (session()->regenerate())
            {
                session()->set('user_id', $user_info['id']);
                session()->set('username', $user_info['name']);
                session()->set('email', $user_info['email']);
                session()->set('picture', $user_info['picture']);
            } else {
                throw new \Exception('Failed to start session');
            }

            return redirect('/');
        } catch (\Exception $e) {
            // return redirect('/auth/login')->with_message('Authentication failed: ' . $e->getMessage(), Flash::FLASH_ERROR);
            return redirect('/auth/login')->with_message('Something went wrong, please try again', Flash::FLASH_ERROR);
        }
    }
}
