<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shazbook</title>
   <link rel="stylesheet"
         href="<?= base_url('css/style.css') ?>">
</head>
<body>

<?php if (isset($me) && $me): ?>
<nav class="navbar">
   <a href="<?= base_url('feed') ?>" class="nav-logo">
      <img src="<?= base_url('images/logo.png') ?>" alt="shazbook">
      shazbook
   </a>

   <div class="nav-links">
      <a href="<?= base_url('feed') ?>"
         class="nav-link <?= uri_string() === 'feed' ? 'active' : '' ?>">
         &#127968; Feed
      </a>
      <a href="<?= base_url('friends') ?>"
         class="nav-link <?= uri_string() === 'friends' ? 'active' : '' ?>">
         &#128101; Friends
      </a>
   </div>

   <div class="nav-user">
      <a href="<?= base_url('profile') ?>" class="nav-avatar-link">
         <img src="<?= esc(avatar_url($me ['avatar'] ?? null)) ?>"
              alt="me" class="nav-avatar">
         <span><?= esc(explode(' ', $me ['name']) [0]) ?></span>
      </a>
      <a href="<?= base_url('auth/logout') ?>"
         class="nav-link nav-logout">Logout</a>
   </div>
</nav>
<?php endif; ?>

<main class="main">

<?php if (session()->getFlashdata('error')): ?>
   <div class="alert alert-error">
      <?= esc(session()->getFlashdata('error')) ?>
   </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
   <div class="alert alert-success">
      <?= esc(session()->getFlashdata('success')) ?>
   </div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

</main>

<script>
function likePost (postId, btn) {
   fetch('<?= base_url('feed/like/') ?>' + postId, {
      method: 'POST',
      headers: {
         'X-CSRF-TOKEN':
            document.querySelector('meta[name="csrf"]')?.content || '',
         'Content-Type': 'application/json',
      },
   })
   .then(r => r.json())
   .then(data => {
      btn.classList.toggle('liked', data.liked);
      btn.querySelector('.like-count').textContent = data.count;
      delete btn.dataset.likerCache;
   });
}

document.addEventListener('mouseover', function (e) {
   const btn = e.target.closest('.btn-like');
   if (!btn) return;
   const tooltip = btn.parentElement.querySelector('.like-tooltip');
   if (!tooltip) return;

   if (btn.dataset.likerCache !== undefined) {
      tooltip.textContent = btn.dataset.likerCache;
      tooltip.classList.remove('hidden');
      return;
   }

   const postId = btn.dataset.postId;
   fetch('<?= base_url('feed/likers/') ?>' + postId)
      .then(r => r.json())
      .then(names => {
         let text;
         if (names.length === 0) {
            text = 'No likes yet';
         }
         else {
            text = names.join('\n');
         }
         btn.dataset.likerCache = text;
         tooltip.textContent = text;
         tooltip.classList.remove('hidden');
      });
});

function previewCommentImg (input) {
   const preview = input.closest('.comment-form')
      .querySelector('.comment-img-preview');
   if (input.files && input.files [0]) {
      const reader = new FileReader();
      reader.onload = e => {
         preview.src = e.target.result;
         preview.classList.remove('hidden');
      };
      reader.readAsDataURL(input.files [0]);
   }
}

document.addEventListener('mouseout', function (e) {
   const btn = e.target.closest('.btn-like');
   if (!btn) return;
   const tooltip = btn.parentElement.querySelector('.like-tooltip');
   if (tooltip) tooltip.classList.add('hidden');
});
</script>

</body>
</html>
