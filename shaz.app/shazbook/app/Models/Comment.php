<?php

namespace App\Models;

use CodeIgniter\Model;

class Comment extends Model
{
   protected $table      = 'comments';
   protected $primaryKey = 'id';

   protected $allowedFields = ['post_id', 'user_id', 'body', 'image'];

   protected $useTimestamps = true;
   protected $createdField  = 'created_at';
   protected $updatedField  = '';
   protected $returnType    = 'array';

   public function getForPosts (array $postIds) : array
   {
      if (empty($postIds)) return [];
      $placeholders = implode(
         ',', array_fill(0, count($postIds), '?')
      );
      $rows = $this->db->query(
         "SELECT c.*, u.name AS userName, u.avatar AS userAvatar
          FROM comments c
          JOIN users u ON u.id = c.user_id
          WHERE c.post_id IN ($placeholders)
          ORDER BY c.created_at ASC",
         $postIds
      )->getResultArray();

      $grouped = [];
      foreach ($rows as $row) {
         $grouped [$row ['post_id']] [] = $row;
      }
      return $grouped;
   }
}
