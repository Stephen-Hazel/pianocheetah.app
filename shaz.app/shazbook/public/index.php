<?php

use CodeIgniter\Boot;
use Config\Paths;

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
   chdir(FCPATH);
}

/*
 * Resolve Paths.php location:
 *  - Local dev: app/ is a sibling of public/
 *  - Production: app/ lives at SHAZBOOK_APP_ROOT
 *    or at /home/z8wo4irg6pxb/shazbook/
 */
if (is_file(FCPATH . '../app/Config/Paths.php')) {
   require FCPATH . '../app/Config/Paths.php';
}
elseif (getenv('SHAZBOOK_APP_ROOT')) {
   require rtrim(getenv('SHAZBOOK_APP_ROOT'), '/\\')
      . '/app/Config/Paths.php';
}
else {
   require '/home/z8wo4irg6pxb/shazbook/app/Config/Paths.php';
}

$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
