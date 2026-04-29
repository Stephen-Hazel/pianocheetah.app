<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<meta name="csrf"
      content="<?= csrf_hash() ?>"
      class="hidden">

<div class="feed-wrap">

   <!-- Composer -->
   <div class="card composer">
      <div class="composer-top">
         <img src="<?= esc(avatar_url($me ['avatar'] ?? null)) ?>"
              alt="me" class="avatar-sm">
         <button class="composer-trigger"
                 onclick="toggleComposer()">
            What's on your mind, <?= esc(
               explode(' ', $me ['name']) [0]
            ) ?>?
         </button>
      </div>

      <form id="composerForm" class="composer-form hidden"
            action="<?= base_url('feed/create') ?>"
            method="post" enctype="multipart/form-data">
         <?= csrf_field() ?>
         <textarea name="body" rows="4" maxlength="2000"
                   placeholder="What's on your mind?"
                   required></textarea>

         <div class="composer-footer">
            <label class="btn btn-outline btn-sm">
               &#128247; Add photo
               <input type="file" name="image"
                      accept="image/*" class="hidden"
                      onchange="previewImg(this)">
            </label>
            <img id="imgPreview" class="hidden img-preview" src="" alt="">
            <button type="submit" class="btn btn-primary">Post</button>
         </div>
      </form>
   </div>

   <!-- Posts -->
   <?php if (empty($posts)): ?>
      <div class="card empty-state">
         <p>No posts yet. Add some friends or write the first post!</p>
      </div>
   <?php endif; ?>

   <?php foreach ($posts as $post): ?>
   <div class="card post">
      <div class="post-header">
         <a href="<?= base_url('profile/' . $post ['user_id']) ?>">
            <img src="<?= esc(avatar_url($post ['userAvatar'] ?? null)) ?>"
                 alt="" class="avatar-sm">
         </a>
         <div class="post-meta">
            <a href="<?= base_url('profile/' . $post ['user_id']) ?>"
               class="post-author">
               <?= esc($post ['userName']) ?>
            </a>
            <span class="post-time">
               <?= date('M j, Y g:ia', strtotime($post ['created_at'])) ?>
            </span>
         </div>
         <?php if ((int)$post ['user_id'] === $me ['id']): ?>
         <form method="post"
               action="<?= base_url('feed/delete/' . $post ['id']) ?>"
               class="post-delete"
               onsubmit="return confirm('Delete this post?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn-icon" title="Delete">
               &#128465;
            </button>
         </form>
         <?php endif; ?>
      </div>

      <div class="post-body"><?= nl2br(esc($post ['body'])) ?></div>

      <?php if ($post ['image']): ?>
         <div class="post-image">
            <img src="<?= base_url(esc($post ['image'])) ?>"
                 alt="post image" loading="lazy">
         </div>
      <?php endif; ?>

      <div class="post-actions">
         <div class="like-wrap">
            <button class="btn-like <?= $post ['iLiked'] ? 'liked' : '' ?>"
                    data-post-id="<?= $post ['id'] ?>"
                    onclick="likePost(<?= $post ['id'] ?>, this)">
               &#10084;
               <span class="like-count"><?= (int)$post ['likeCount'] ?></span>
               <?= $post ['iLiked'] ? 'Liked' : 'Like' ?>
            </button>
            <div class="like-tooltip hidden"></div>
         </div>
      </div>

      <!-- Comments -->
      <div class="comments">
         <?php foreach ($comments [$post ['id']] ?? [] as $c): ?>
         <div class="comment">
            <a href="<?= base_url('profile/' . $c ['user_id']) ?>">
               <img src="<?= esc(avatar_url($c ['userAvatar'] ?? null)) ?>"
                    alt="" class="avatar-xs">
            </a>
            <div class="comment-content">
               <a href="<?= base_url('profile/' . $c ['user_id']) ?>"
                  class="comment-author">
                  <?= esc($c ['userName']) ?>
               </a>
               <?php if ($c ['body']): ?>
               <p class="comment-body"><?= nl2br(esc($c ['body'])) ?></p>
               <?php endif; ?>
               <?php if ($c ['image']): ?>
               <img src="<?= base_url(esc($c ['image'])) ?>"
                    class="comment-image" alt="">
               <?php endif; ?>
               <span class="comment-time">
                  <?= date('M j g:ia', strtotime($c ['created_at'])) ?>
               </span>
            </div>
            <?php if ((int)$c ['user_id'] === $me ['id']): ?>
            <form method="post"
                  action="<?= base_url('comment/delete/' . $c ['id']) ?>">
               <?= csrf_field() ?>
               <button class="btn-icon" title="Delete">&#128465;</button>
            </form>
            <?php endif; ?>
         </div>
         <?php endforeach; ?>

         <div class="comment-compose">
            <img src="<?= esc(avatar_url($me ['avatar'] ?? null)) ?>"
                 alt="" class="avatar-xs">
            <form method="post"
                  action="<?= base_url('comment/create/' . $post ['id']) ?>"
                  enctype="multipart/form-data"
                  class="comment-form">
               <?= csrf_field() ?>
               <div class="comment-input-wrap">
                  <textarea name="body" rows="1"
                            placeholder="Write a comment…"
                            maxlength="1000"></textarea>
                  <div class="comment-form-actions">
                     <label class="btn-icon" title="Add image">
                        &#128247;
                        <input type="file" name="image"
                               accept="image/*" class="hidden"
                               onchange="previewCommentImg(this)">
                     </label>
                     <button type="submit"
                             class="btn btn-primary btn-sm">Post</button>
                  </div>
               </div>
               <img class="comment-img-preview hidden" src="" alt="">
            </form>
         </div>
      </div>
   </div>
   <?php endforeach; ?>

</div>

<script>
function toggleComposer () {
   const form = document.getElementById('composerForm');
   form.classList.toggle('hidden');
   if (!form.classList.contains('hidden')) {
      form.querySelector('textarea').focus();
   }
}

function previewImg (input) {
   const preview = document.getElementById('imgPreview');
   if (input.files && input.files [0]) {
      const reader = new FileReader();
      reader.onload = e => {
         preview.src = e.target.result;
         preview.classList.remove('hidden');
      };
      reader.readAsDataURL(input.files [0]);
   }
}
</script>

<?= $this->endSection() ?>
