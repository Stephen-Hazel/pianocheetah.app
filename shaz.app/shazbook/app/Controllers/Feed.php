<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Friend;
use App\Models\Like;
use App\Models\Post;
use App\Libraries\Uploader;

class Feed extends BaseController
{
   public function index ()
   {
      $friend = new Friend();
      $post   = new Post();

      $friendIds = $friend->getFriendIds($this->myId());
      $posts     = $post->getFeed($this->myId(), $friendIds);

      $comment  = new Comment();
      $comments = $comment->getForPosts(array_column($posts, 'id'));

      return view('feed', [
         'me'       => $this->me(),
         'posts'    => $posts,
         'comments' => $comments,
      ]);
   }

   public function create ()
   {
      $body = trim((string) $this->request->getPost('body'));
      if ($body === '') {
         return redirect()->to(base_url('feed'))
            ->with('error', 'Post cannot be empty.');
      }
      if (mb_strlen($body) > 2000) {
         return redirect()->to(base_url('feed'))
            ->with('error', 'Post is too long (max 2000 chars).');
      }

      $image   = null;
      $imgFile = $this->request->getFile('image');
      if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
         try {
            $up    = new Uploader();
            $image = $up->post($imgFile, $this->myId());
         }
         catch (\RuntimeException $e) {
            return redirect()->to(base_url('feed'))
               ->with('error', $e->getMessage());
         }
      }

      $post = new Post();
      $post->insert([
         'user_id' => $this->myId(),
         'body'    => $body,
         'image'   => $image,
      ]);

      return redirect()->to(base_url('feed'));
   }

   public function like (int $postId)
   {
      $like   = new Like();
      $result = $like->toggle($postId, $this->myId());
      return $this->response
         ->setContentType('application/json')
         ->setBody(json_encode($result));
   }

   public function likers (int $postId)
   {
      $like  = new Like();
      $rows  = $like->getLikers($postId);
      $names = array_column($rows, 'name');
      return $this->response
         ->setContentType('application/json')
         ->setBody(json_encode($names));
   }

   public function delete (int $postId)
   {
      $post = new Post();
      $row  = $post->find($postId);

      if ($row && (int)$row ['user_id'] === $this->myId()) {
         if ($row ['image'] && file_exists(FCPATH . $row ['image'])) {
            unlink(FCPATH . $row ['image']);
         }
         $post->delete($postId);
      }

      return redirect()->to(base_url('feed'));
   }
}
