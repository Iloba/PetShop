<?php

namespace App\Services\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\{Guard, UserProvider};
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var Request
     */
    private $request;

    private $lastAttempted;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = $this->authenticateByToken();
    }

    public function validate(array $credentials = [])
    {
        if (! $credentials) {
            return false;
        }

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    protected function authenticateByToken()
    {
        if (! empty($this->user)) {
            return $this->user;
        }

        $token = $this->getBearerToken();

        if (empty($token)) {
            return null;
        }

        try {
            $decoded = $this->authenticatedAccessToken($token);

            if (! $decoded) {
                $user = null;
            } else {
                $user = $this->provider->retrieveById($decoded->getRelatedTo());
            }

        } catch (\Exception $exception) {
            logger($exception);
            $user = null;
        }

        return $user;
    }

    protected function getBearerToken()
    {
        return $this->request->bearerToken();
    }

    public function attempt(array $credentials = [], $login = true)
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            $this->user = $user;
            return true;
        }

        return false;
    }

    protected function hasValidCredentials($user, $credentials)
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    public function authenticatedAccessToken($token)
    {
        return JwtParser::loadFromToken($token);
    }
}
