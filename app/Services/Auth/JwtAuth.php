<?php

namespace App\Services\Auth;

use App\Services\Auth\JwtBuilder;
use App\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;

class JwtAuth
{
    public function authenticateAndReturnJwtToken(string $email, string $password): ?string
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            return false;
        }

        try {
            /** @var User $user */
            return $this->createJwtToken(Auth::user());

        } catch (\Throwable $exception) {
            logger($exception->getMessage());
            return false;
        }
    }

    protected function createJwtToken(User $user, CarbonInterface $ttl = null): string
    {
        return (new JwtBuilder())
            ->issuedBy(config('app.url'))
            ->audience(config('app.name'))
            ->issuedAt(now())
            ->canOnlyBeUsedAfter(now()->addMinute())
            ->expiresAt($ttl ?? now()->addSeconds(config('jwt.ttl')))
            ->relatedTo($user->id)
            ->getToken();
    }
}
