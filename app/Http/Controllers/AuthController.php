<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\WhiteList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    /**
     * Login User
     * @description Mengautentikasi pengguna melalui email/password atau token akses Google. Mendukung login untuk akun Google UPN dan validasi NPM.
     */
    public function login(Request $request)
    {
        $request->validate([
            'accessToken' => 'required_without:email',
            'email' => 'sometimes|email',
            'password' => 'required_with:email',
        ]);

        if ($request->email) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return Auth::user();
            } else {
                return response()->json(['message' => 'Email atau password tidak sesuai'], 401);
            }
        }

        $providerUser = Socialite::driver('google')->userFromToken($request->accessToken);

        [$username, $domain] = explode('@', $providerUser->email);

        $whitelistPrefixes = ['21', '22', '23', '24', '25'];
        // $isWhitelisted = WhiteList::where('event_id', 1)->where('npm', $username)->exists();

        if (strpos($domain, 'student.upnjatim.ac.id') === false)
            return response()->json(['message' => 'Maaf, kamu harus menggunakan akun google UPN!'], 400);
        else if (substr($username, 2, 2) !== '08' || !in_array(substr($username, 0, 2), $whitelistPrefixes))
            return response()->json(['message' => 'Maaf, akun tersebut tidak memenuhi syarat untuk mencoblos!'], 400);
        // else if (!$isWhitelisted)
        //     return response()->json(['message' => 'Maaf, akun ini tidak terdaftar untuk mencoblos!'], 400);

        $user = User::query()->find(strtok($providerUser->email, '@'));

        // dd($providerUser->name);
        if (preg_match('/^\d{11}\s/', $providerUser->name)) {
            $providerUser->name = substr($providerUser->name, 12);
        } // check if user name starts with 8 digits number
        if (!$user) {
            $user = User::query()->create([
                'name' => $providerUser->name,
                'email' => $providerUser->email,
                'npm' => strtok($providerUser->email, '@'),
                'role' => 0,
                'picture' => $providerUser->avatar
            ]);
        } else {
            $user->update([
                'name' => $providerUser->name,
                'picture' => $providerUser->avatar
            ]);
        }

        if ($request->token == 1) {
            $login = $user->createToken('login')->plainTextToken;
            return [
                'user' => $providerUser,
                'login_token' => $login,
            ];
        }
        Auth::login($user);

        return $user;
    }

    /**
     * Google OAuth Callback (Debugging)
     * @description Mengambil data pengguna dari Google OAuth setelah otentikasi. (Biasanya digunakan untuk proses debug atau pengembangan.)
     */
    public function callback()
    {
        // $user = Socialite::driver('google')->user();

        // dd($user);
        return Socialite::driver('google')->user();
    }

    /**
     * Get Authenticated User
     * @description Mengambil data pengguna yang sedang login melalui token autentikasi.
     *
     * @status 200
     * @response User[]
     */
    public function user()
    {
        return Auth::user();
    }
}
