<?php

namespace App\Controllers;

use App\Models\Comment as CommentModel;
use App\Libraries\Uploader;

class Comment extends BaseController
{
   public function create (int $postId)
   {
      $body    = trim((string) $this->request->getPost('body'));
      $image   = null;
      $imgFile = $this->request->getFile('image');

      if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
         try {
            $up    = new Uploader();
            $image = $up->comment($imgFile, $this->myId());
         }
         catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
         }
      }

      if ($body === '' && $image === null) {
         return redirect()->back()
            ->with('error', 'Comment cannot be empty.');
      }

      $comment = new CommentModel();
      $comment->insert([
         'post_id' => $postId,
         'user_id' => $this->myId(),
         'body'    => $body !== '' ? $body : null,
         'image'   => $image,
      ]);

      return redirect()->back();
   }

   public function delete (int $commentId)
   {
      $comment = new CommentModel();
      $row     = $comment->find($commentId);

      if ($row && (int)$row ['user_id'] === $this->myId()) {
         if ($row ['image'] && file_exists(FCPATH . $row ['image'])) {
            unlink(FCPATH . $row ['image']);
         }
         $comment->delete($commentId);
      }

      return redirect()->back();
   }
}
