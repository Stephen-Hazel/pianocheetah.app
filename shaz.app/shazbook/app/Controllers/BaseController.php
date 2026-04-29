<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
   protected $helpers = ['url', 'form', 'html', 'shazbook'];

   public function initController (
      RequestInterface $request,
      ResponseInterface $response,
      LoggerInterface $logger
   ) {
      parent::initController($request, $response, $logger);
   }

   protected function me () : ?array
   {
      return session()->get('user');
   }

   protected function myId () : ?int
   {
      return session()->get('userId');
   }
}
