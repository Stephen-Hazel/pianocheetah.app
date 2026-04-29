<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<meta name="csrf"
      content="<?= csrf_hash() ?>"
      class="hidden">

<div class="profile-wrap">

   <!-- Header card -->
   <div class="card profile-header">
      <div class="profile-cover"></div>
      <div class="profile-info">
         <img src="<?= esc(avatar_url($profile ['avatar'] ?? null)) ?>"
              alt="avatar" class="profile-avatar">

         <div class="profile-details">
            <h2><?= esc($profile ['name']) ?></h2>
            <?php if ($profile ['bio']): ?>
               <p class="profile-bio"><?= nl2br(esc($profile ['bio'])) ?></p>
            <?php endif; ?>
            <span class="profile-stat">
               <?= count($friends) ?> friend<?= count($friends) != 1 ? 's' : '' ?>
            </span>
         </div>

         <div class="profile-actions">
            <?php if ($isOwnProfile): ?>
               <a href="<?= base_url('profile/edit') ?>"
                  class="btn btn-outline">Edit Profile</a>

            <?php elseif (!$friendRow): ?>
               <form method="post"
                     action="<?= base_url('friends/request/' . $profile ['id']) ?>">
                  <?= csrf_field() ?>
                  <button class="btn btn-primary">Add Friend</button>
               </form>

            <?php elseif ($friendRow ['status'] === 'pending'
                     && (int)$friendRow ['requester_id'] === $me ['id']): ?>
               <span class="btn btn-outline disabled">Request Sent</span>

            <?php elseif ($friendRow ['status'] === 'pending'): ?>
               <form method="post"
                     action="<?= base_url('friends/accept/' . $friendRow ['id']) ?>"
                     style="display:inline">
                  <?= csrf_field() ?>
                  <button class="btn btn-primary">Accept</button>
               </form>
               <form method="post"
                     action="<?= base_url('friends/decline/' . $friendRow ['id']) ?>"
                     style="display:inline">
                  <?= csrf_field() ?>
                  <button class="btn btn-outline">Decline</button>
               </form>

            <?php else: ?>
               <form method="post"
                     action="<?= base_url('friends/unfriend/' . $profile ['id']) ?>"
                     onsubmit="return confirm('Remove friend?')">
                  <?= csrf_field() ?>
                  <button class="btn btn-outline">Unfriend</button>
               </form>
            <?php endif; ?>
         </div>
      </div>
   </div>

   <div class="profile-columns">

      <!-- Friends sidebar -->
      <?php if (!empty($friends)): ?>
      <div class="card profile-friends-card">
         <h3>Friends</h3>
         <div class="friend-grid">
            <?php foreach (array_slice($friends, 0, 9) as $f): ?>
            <a href="<?= base_url('profile/' . $f ['id']) ?>"
               class="friend-thumb" title="<?= esc($f ['name']) ?>">
               <img src="<?= esc(avatar_url($f ['avatar'] ?? null)) ?>"
                  alt="<?= esc($f ['name']) ?>">
               <span><?= esc(explode(' ', $f ['name']) [0]) ?></span>
            </a>
            <?php endforeach; ?>
         </div>
      </div>
      <?php endif; ?>

      <!-- Posts column -->
      <div class="profile-posts">
         <?php if (empty($posts)): ?>
            <div class="card empty-state">
               <p>No posts yet.</p>
            </div>
         <?php endif; ?>

         <?php foreach ($posts as $post): ?>
         <div class="card post">
            <div class="post-header">
               <img src="<?= esc(avatar_url($profile ['avatar'] ?? null)) ?>"
                    alt="" class="avatar-sm">
               <div class="post-meta">
                  <span class="post-author"><?= esc($profile ['name']) ?></span>
                  <span class="post-time">
                     <?= date('M j, Y g:ia',
                        strtotime($post ['created_at'])) ?>
                  </span>
               </div>
               <?php if ($isOwnProfile): ?>
               <form method="post"
                     action="<?= base_url('feed/delete/' . $post ['id']) ?>"
                     class="post-delete"
                     onsubmit="return confirm('Delete this post?')">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn-icon">&#128465;</button>
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
                     <span class="like-count">
                        <?= (int)$post ['likeCount'] ?>
                     </span>
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
                     <p class="comment-body">
                        <?= nl2br(esc($c ['body'])) ?>
                     </p>
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

   </div><!-- .profile-columns -->
</div>

<?= $this->endSection() ?>
