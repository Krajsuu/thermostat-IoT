<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EntriaAuthController extends Controller
{
    public function redirect()
    {
        return redirect(env('ENTRIA_URL'));
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');

        $redirectUri = $this->getRedirectUriFromEntriaUrl();
        $baseUrl = $this->getBaseUrlFromEntriaUrl();

        $tokenResponse = Http::asForm()->post(
            $baseUrl . '/oauth2/token',
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => env('ENTRIA_CLIENT_ID'),
                'client_secret' => env('ENTRIA_CLIENT_SECRET'),
                'redirect_uri' => $redirectUri,
            ]
        );

        if (!$tokenResponse->successful()) {
            return redirect('/login')
                ->with('error', 'Nie udało się pobrać tokena.');
        }

        $tokenData = $tokenResponse->json();

        if (!isset($tokenData['access_token'])) {
            return redirect('/login')
                ->with('error', 'Brak access tokena.');
        }

        $accessToken = $tokenData['access_token'];

        $userResponse = Http::withToken($accessToken)
            ->get($baseUrl . '/userinfo');

        if (!$userResponse->successful()) {
            return redirect('/login')
                ->with('error', 'Nie udało się pobrać danych użytkownika.');
        }

        $entriaUser = $userResponse->json();

        $user = User::firstOrCreate(
            [
                'email' => $entriaUser['email'],
            ],
            [
                'name' => trim(
                    ($entriaUser['given_name'] ?? '') . ' ' .
                    ($entriaUser['family_name'] ?? '')
                ),
                'password' => bcrypt(Str::random(32)),
            ]
        );
        Auth::login($user);
        return redirect('/dashboard');
    }

    private function getRedirectUriFromEntriaUrl(): string
    {
        parse_str(parse_url(env('ENTRIA_URL'), PHP_URL_QUERY), $query);

        return $query['redirect_uri'];
    }

    private function getBaseUrlFromEntriaUrl(): string
    {
        $url = env('ENTRIA_URL');

        $parsedUrl = parse_url($url);

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] .
            (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
            '/oauth';
    }
}