<?php

namespace App\Controllers;

use App\Models\User;
use Config\Google as GoogleConfig;
use League\OAuth2\Client\Provider\Google;

class Auth extends BaseController
{
   private function provider () : Google
   {
      $cfg = new GoogleConfig();
      return new Google([
         'clientId'     => $cfg->clientId,
         'clientSecret' => $cfg->clientSecret,
         'redirectUri'  => $cfg->redirectUri,
      ]);
   }

   public function login ()
   {
      if ($this->myId()) {
         return redirect()->to(base_url('feed'));
      }
      return view('login');
   }

   public function googleRedirect ()
   {
      $provider = $this->provider();
      $authUrl  = $provider->getAuthorizationUrl([
         'scope' => ['openid', 'profile', 'email'],
      ]);
      session()->set('oauth2state', $provider->getState());
      return redirect()->to($authUrl);
   }

   public function callback ()
   {
      $state = $this->request->getGet('state');
      $code  = $this->request->getGet('code');

      if (!$state || $state !== session()->get('oauth2state')) {
         session()->remove('oauth2state');
         return redirect()->to(base_url('auth/login'))
            ->with('error', 'Invalid OAuth state. Please try again.');
      }
      session()->remove('oauth2state');

      if (!$code) {
         return redirect()->to(base_url('auth/login'))
            ->with('error', 'No authorization code received.');
      }

      try {
         $provider = $this->provider();
         $token    = $provider->getAccessToken(
            'authorization_code', ['code' => $code]
         );
         $gUser    = $provider->getResourceOwner($token);

         $avatar = (string) ($gUser->getAvatar() ?? '');
         if ($avatar) {
            $avatar = preg_replace('/=s\d+-c$/', '', $avatar) . '=s200-c';
         }

         $user = new User();
         $row  = $user->upsertGoogle(
            (string) $gUser->getId(),
            (string) $gUser->getEmail(),
            (string) $gUser->getName(),
            $avatar
         );

         session()->set('userId', $row ['id']);
         session()->set('user',   $row);

         return redirect()->to(base_url('feed'));
      }
      catch (\Exception $e) {
         log_message('error', 'OAuth callback: ' . $e->getMessage());
         return redirect()->to(base_url('auth/login'))
            ->with('error', 'Sign-in failed. Please try again.');
      }
   }

   public function logout ()
   {
      session()->destroy();
      return redirect()->to(base_url('auth/login'));
   }
}
