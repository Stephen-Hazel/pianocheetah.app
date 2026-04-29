<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Google extends BaseConfig
{
   public string $clientId     = '';
   public string $clientSecret = '';
   public string $redirectUri  = '';

   public function __construct ()
   {
      parent::__construct();
      $this->clientId     = (string) env('GOOGLE_CLIENT_ID', '');
      $this->clientSecret = (string) env('GOOGLE_CLIENT_SECRET', '');
      $this->redirectUri  = (string) env('GOOGLE_REDIRECT_URI', '');
   }
}
