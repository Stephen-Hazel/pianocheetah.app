<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Friend;
use App\Models\Post;
use App\Models\User;
use App\Libraries\Uploader;

class Profile extends BaseController
{
   public function index (int $userId = 0)
   {
      if (!$userId) {
         $userId = $this->myId();
      }

      $user   = new User();
      $post   = new Post();
      $friend = new Friend();

      $profile = $user->find($userId);
      if (!$profile) {
         throw \CodeIgniter\Exceptions\PageNotFoundException
            ::forPageNotFound();
      }

      $posts     = $post->getUserPosts($userId, $this->myId());
      $friends   = $friend->getFriends($userId);
      $friendRow = ($userId !== $this->myId())
         ? $friend->getRow($this->myId(), $userId)
         : null;

      $comment  = new Comment();
      $comments = $comment->getForPosts(array_column($posts, 'id'));

      return view('profile', [
         'me'           => $this->me(),
         'profile'      => $profile,
         'posts'        => $posts,
         'comments'     => $comments,
         'friends'      => $friends,
         'friendRow'    => $friendRow,
         'isOwnProfile' => $userId === $this->myId(),
      ]);
   }

   public function edit ()
   {
      return view('profile_edit', ['me' => $this->me()]);
   }

   public function update ()
   {
      $bio  = trim((string) $this->request->getPost('bio'));
      $name = trim((string) $this->request->getPost('name'));
      $data = [];

      if ($name !== '') {
         $data ['name'] = mb_substr($name, 0, 100);
      }
      $data ['bio'] = mb_substr($bio, 0, 500);

      $imgFile = $this->request->getFile('avatar');
      if ($imgFile && !$imgFile->isValid()
         && $imgFile->getError() !== UPLOAD_ERR_NO_FILE) {
         return redirect()->to(base_url('profile/edit'))
            ->with('error', 'Photo upload failed: '
               . $imgFile->getErrorString()
               . ' (try a smaller image under 4 MB)');
      }
      if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
         try {
            $up = new Uploader();
            $data ['avatar'] = $up->avatar($imgFile, $this->myId());
         }
         catch (\RuntimeException $e) {
            return redirect()->to(base_url('profile/edit'))
               ->with('error', $e->getMessage());
         }
      }

      if (!empty($data)) {
         $user = new User();
         $user->update($this->myId(), $data);
         session()->set('user', $user->find($this->myId()));
      }

      return redirect()->to(base_url('profile'));
   }
}
