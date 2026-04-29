<?php

namespace App\Models;

use CodeIgniter\Model;

class Like extends Model
{
   protected $table      = 'likes';
   protected $primaryKey = 'id';

   protected $allowedFields = ['post_id', 'user_id'];

   protected $useTimestamps = true;
   protected $createdField  = 'created_at';
   protected $updatedField  = '';
   protected $returnType    = 'array';

   public function toggle (int $postId, int $userId) : array
   {
      $existing = $this
         ->where('post_id', $postId)
         ->where('user_id', $userId)
         ->first();

      if ($existing) {
         $this->delete($existing ['id']);
         $liked = false;
      }
      else {
         $this->insert(['post_id' => $postId, 'user_id' => $userId]);
         $liked = true;
      }

      $count = $this->where('post_id', $postId)->countAllResults();
      return ['liked' => $liked, 'count' => $count];
   }

   public function getLikers (int $postId) : array
   {
      return $this->db->table('likes l')
         ->select('u.name')
         ->join('users u', 'u.id = l.user_id')
         ->where('l.post_id', $postId)
         ->orderBy('l.created_at', 'DESC')
         ->get()
         ->getResultArray();
   }
}
