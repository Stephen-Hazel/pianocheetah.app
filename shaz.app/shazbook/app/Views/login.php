<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shazbook — sign in</title>
   <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body class="login-page">

<div class="login-wrap">
   <div class="login-brand">
      <img src="<?= base_url('images/logo.png') ?>"
           alt="shazbook" class="login-logo">
      <h1>shazbook</h1>
      <p>Connect with friends and share moments.</p>
   </div>

   <div class="login-card">
      <h2>Sign in</h2>

      <?php if (session()->getFlashdata('error')): ?>
         <div class="alert alert-error">
            <?= esc(session()->getFlashdata('error')) ?>
         </div>
      <?php endif; ?>

      <a href="<?= base_url('auth/redirect') ?>"
         class="btn btn-google">
         <svg viewBox="0 0 24 24" width="20" height="20">
            <path fill="#4285F4"
               d="M23.745 12.27c0-.79-.07-1.54-.19-2.27h-11.3v4.51h6.47c-.29 1.48-1.14
               2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
            <path fill="#34A853"
               d="M12.255 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45
               1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96h-3.98v3.09C3.515 21.3
               7.615 24 12.255 24z"/>
            <path fill="#FBBC05"
               d="M5.525 14.29c-.25-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29V6.62h-3.98a
               11.86 11.86 0 0 0 0 10.76l3.98-3.09z"/>
            <path fill="#EA4335"
               d="M12.255 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C18.205 1.19
               15.495 0 12.255 0c-4.64 0-8.74 2.7-10.71 6.62l3.98 3.09c.95-2.85
               3.6-4.96 6.73-4.96z"/>
         </svg>
         Continue with Google
      </a>

      <p class="login-note">
         By signing in you agree to keep it friendly.
      </p>
   </div>
</div>

</body>
</html>
