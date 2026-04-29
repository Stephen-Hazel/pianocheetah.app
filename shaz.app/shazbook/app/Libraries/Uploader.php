<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class Uploader
{
   private array $allowedTypes = [
      'image/jpeg', 'image/png', 'image/gif', 'image/webp',
   ];

   public function avatar (UploadedFile $file, int $userId) : string
   {
      $this->validate($file, 2 * 1024 * 1024);
      $dest = FCPATH . 'uploads/avatars/';
      $name = 'avatar_' . $userId . '_' . time()
         . '.' . $file->getExtension();
      $file->move($dest, $name);
      return 'uploads/avatars/' . $name;
   }

   public function post (UploadedFile $file, int $userId) : string
   {
      $this->validate($file, 4 * 1024 * 1024);
      $dest = FCPATH . 'uploads/posts/';
      $name = 'post_' . $userId . '_' . time()
         . '_' . random_int(1000, 9999)
         . '.' . $file->getExtension();
      $file->move($dest, $name);
      return 'uploads/posts/' . $name;
   }

   public function comment (UploadedFile $file, int $userId) : string
   {
      $this->validate($file, 4 * 1024 * 1024);
      $dest = FCPATH . 'uploads/posts/';
      $name = 'comment_' . $userId . '_' . time()
         . '_' . random_int(1000, 9999)
         . '.' . $file->getExtension();
      $file->move($dest, $name);
      return 'uploads/posts/' . $name;
   }

   private function validate (UploadedFile $file, int $maxBytes) : void
   {
      if (!$file->isValid()) {
         throw new \RuntimeException(
            'Upload failed: ' . $file->getErrorString()
         );
      }
      if (!in_array($file->getMimeType(), $this->allowedTypes)) {
         throw new \RuntimeException(
            'Only JPEG, PNG, GIF, and WebP images are allowed.'
         );
      }
      if ($file->getSize() > $maxBytes) {
         $mb = $maxBytes / 1024 / 1024;
         throw new \RuntimeException(
            "Image must be under {$mb}MB."
         );
      }
   }
}
