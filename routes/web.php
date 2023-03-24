<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Issuing Tokens
Route::get('/', function () {
    $key = \Lcobucci\JWT\Signer\Key\InMemory::base64Encoded(
        'hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB'
    );

    $token = (new \Lcobucci\JWT\JwtFacade())->issue(
        new Lcobucci\JWT\Signer\Hmac\Sha256(),
        $key,
        static fn (
            \Lcobucci\JWT\Builder $builder,
            DateTimeImmutable $issuedAt
        ): \Lcobucci\JWT\Builder => $builder
            ->issuedBy('https://api.my-awesome-app.io')
            ->permittedFor('https://client-app.io')
            ->expiresAt($issuedAt->modify('+10 minutes'))
    );

    dd($token->toString());

    return view('welcome', [
        'token' => $token,
        'emeka' => 12090,
    ]);
});



