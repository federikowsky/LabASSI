<?php

namespace App\Services\OAuth;

use App\Models\User;
use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;

class GoogleOAuthService extends BaseOAuthService
{
    protected $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
        $this->setup();
    }

    private function setup()
    {
        $client = new Google_Client();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $client->addScope('email');
        $client->addScope('profile');
        $client->setAccessType('offline'); // Ask for a refresh token

        $this->set_client($client);
    }

    /**
     * Get the authentication URL
     *
     * @return string
     */
    public function get_auth_url(): string
    {
        return $this->client->createAuthUrl();
    }

    private function get_or_create_user($user_info, ?string $refresh_token): int
    {
        // check if the user already exists in the database
        $existing_user = $this->userModel->find_by_oauth_provider('google', $user_info->id);

        if ($existing_user)
        {
            // if the user exists, update the refresh token (if present) and return the database ID
            if ($refresh_token) {
                $this->userModel->update_refresh_token($existing_user['id'], $refresh_token);
            }
            $user_id = $existing_user['id'];
        } else {
            // if the user does not exist, register them and return the new user ID
            $user_id = $this->userModel->register_oauth_user([
                'username' => $user_info->name,
                'email' => $user_info->email,
                'oauth_provider' => 'google',
                'provider_id' => $user_info->id,
                'refresh_token' => $refresh_token,
            ]);
        }

        return $user_id;
    }
    
    /**
     * Authenticate the user using the OAuth code and save the refresh token.
     *
     * @param string $code
     * @return array
     * @throws \Exception
     */
    public function authenticate(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            throw new \Exception('Authentication error: ' . $token['error']);
        }

        // Set the access token for the client
        // This is required for the getUserInfo() method to work properly
        $this->client->setAccessToken($token);

        // Obtain the user details
        $oauth = new Google_Service_Oauth2($this->client);
        $user_info = $oauth->userinfo->get();

        $user_id = $this->get_or_create_user($user_info, $token['refresh_token'] ?? null);

        return [
            'id' => $user_id,
            'email' => $user_info->email,
            'name' => $user_info->name,
            'picture' => $user_info->picture,
        ];
    }

    /**
     * Store the refresh token in the database associated with the user.
     *
     * @param string $google_user_id
     * @param string $refresh_token
     */
    protected function save_refresh_token(string $google_user_id, string $refresh_token): void
    {
        // Find the existing user in the database using the OAuth provider ID
        $existing_user = $this->userModel->find_by_oauth_provider('google', $google_user_id);

        if ($existing_user) {
            // If the user exists, update the refresh token
            $this->userModel->update_refresh_token($existing_user['id'], $refresh_token);
        } else {
            throw new \Exception('User not found while saving refresh token.');
        }
    }

    /**
     * Update the access token using the refresh token.
     *
     * @param string $refresh_token
     * @return array
     * @throws \Exception
     */
    public function refresh_access_token(string $refresh_token): array
    {
        $this->client->refreshToken($refresh_token);
        $new_token = $this->client->getAccessToken();

        if (isset($new_token['error'])) {
            throw new \Exception('Error refreshing access token: ' . $new_token['error']);
        }

        return $new_token;
    }
}
