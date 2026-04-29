<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="feed-wrap">
   <div class="card">
      <h2 style="margin-bottom:1.2rem">Edit Profile</h2>

      <form method="post" action="<?= base_url('profile/update') ?>"
            enctype="multipart/form-data">
         <?= csrf_field() ?>

         <!-- Avatar -->
         <div class="form-group avatar-upload-group">
            <label>Profile Photo</label>
            <img src="<?= esc(avatar_url($me ['avatar'] ?? null)) ?>"
                 id="avatarPreview" alt="avatar"
                 class="profile-avatar-preview">
            <label class="btn btn-outline btn-sm" style="cursor:pointer">
               Choose photo
               <input type="file" name="avatar" accept="image/*"
                      class="hidden"
                      onchange="prevAvatar(this)">
            </label>
            <small>JPEG/PNG/GIF/WebP, max 2 MB</small>
         </div>

         <!-- Name -->
         <div class="form-group">
            <label for="name">Display Name</label>
            <input type="text" id="name" name="name"
                   value="<?= esc($me ['name']) ?>"
                   maxlength="100" class="form-input">
         </div>

         <!-- Bio -->
         <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"
                      maxlength="500"
                      class="form-input"><?= esc($me ['bio'] ?? '') ?></textarea>
         </div>

         <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= base_url('profile') ?>"
               class="btn btn-outline">Cancel</a>
         </div>
      </form>
   </div>
</div>

<script>
function prevAvatar (input) {
   if (input.files && input.files [0]) {
      const reader = new FileReader();
      reader.onload = e => {
         document.getElementById('avatarPreview').src = e.target.result;
      };
      reader.readAsDataURL(input.files [0]);
   }
}
</script>

<?= $this->endSection() ?>
