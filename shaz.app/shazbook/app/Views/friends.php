<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="feed-wrap">

   <!-- Pending requests -->
   <?php if (!empty($pending)): ?>
   <div class="card">
      <h3 class="section-title">Friend Requests</h3>
      <div class="people-list">
         <?php foreach ($pending as $p): ?>
         <div class="person-card">
            <a href="<?= base_url('profile/' . $p ['id']) ?>">
               <img src="<?= esc(avatar_url($p ['avatar'] ?? null)) ?>"
                    alt="" class="avatar-md">
            </a>
            <div class="person-info">
               <a href="<?= base_url('profile/' . $p ['id']) ?>"
                  class="person-name">
                  <?= esc($p ['name']) ?>
               </a>
            </div>
            <div class="person-actions">
               <form method="post"
                     action="<?= base_url('friends/accept/'
                        . $p ['friendRowId']) ?>">
                  <?= csrf_field() ?>
                  <button class="btn btn-primary btn-sm">Confirm</button>
               </form>
               <form method="post"
                     action="<?= base_url('friends/decline/'
                        . $p ['friendRowId']) ?>">
                  <?= csrf_field() ?>
                  <button class="btn btn-outline btn-sm">Delete</button>
               </form>
            </div>
         </div>
         <?php endforeach; ?>
      </div>
   </div>
   <?php endif; ?>

   <!-- Friends -->
   <div class="card">
      <h3 class="section-title">
         Friends (<?= count($friends) ?>)
      </h3>
      <?php if (empty($friends)): ?>
         <p class="empty-msg">You haven't added any friends yet.</p>
      <?php else: ?>
      <div class="people-list">
         <?php foreach ($friends as $f): ?>
         <div class="person-card">
            <a href="<?= base_url('profile/' . $f ['id']) ?>">
               <img src="<?= esc(avatar_url($f ['avatar'] ?? null)) ?>"
                    alt="" class="avatar-md">
            </a>
            <div class="person-info">
               <a href="<?= base_url('profile/' . $f ['id']) ?>"
                  class="person-name">
                  <?= esc($f ['name']) ?>
               </a>
            </div>
            <div class="person-actions">
               <form method="post"
                     action="<?= base_url('friends/unfriend/' . $f ['id']) ?>"
                     onsubmit="return confirm('Remove friend?')">
                  <?= csrf_field() ?>
                  <button class="btn btn-outline btn-sm">Unfriend</button>
               </form>
            </div>
         </div>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>
   </div>

   <!-- Suggestions -->
   <?php if (!empty($suggestions)): ?>
   <div class="card">
      <h3 class="section-title">People You May Know</h3>
      <div class="people-list">
         <?php foreach ($suggestions as $s): ?>
         <div class="person-card">
            <a href="<?= base_url('profile/' . $s ['id']) ?>">
               <img src="<?= esc(avatar_url($s ['avatar'] ?? null)) ?>"
                    alt="" class="avatar-md">
            </a>
            <div class="person-info">
               <a href="<?= base_url('profile/' . $s ['id']) ?>"
                  class="person-name">
                  <?= esc($s ['name']) ?>
               </a>
            </div>
            <div class="person-actions">
               <form method="post"
                     action="<?= base_url('friends/request/' . $s ['id']) ?>">
                  <?= csrf_field() ?>
                  <button class="btn btn-primary btn-sm">Add Friend</button>
               </form>
            </div>
         </div>
         <?php endforeach; ?>
      </div>
   </div>
   <?php endif; ?>

</div>

<?= $this->endSection() ?>
