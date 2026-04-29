<?php

namespace App\Models;

use CodeIgniter\Model;

class Post extends Model
{
   protected $table      = 'posts';
   protected $primaryKey = 'id';

   protected $allowedFields = ['user_id', 'body', 'image'];

   protected $useTimestamps = true;
   protected $createdField  = 'created_at';
   protected $updatedField  = '';
   protected $returnType    = 'array';

   public function getFeed (int $userId, array $friendIds) : array
   {
      $authorIds = array_merge([$userId], $friendIds);
      return $this->db->query(
         'SELECT p.*, u.name AS userName, u.avatar AS userAvatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id)
               AS likeCount,
            (SELECT COUNT(*) FROM likes
               WHERE post_id = p.id AND user_id = ?)
               AS iLiked
          FROM posts p
          JOIN users u ON u.id = p.user_id
          WHERE p.user_id IN (' .
            implode(',', array_fill(0, count($authorIds), '?')) .
         ')
          ORDER BY p.created_at DESC
          LIMIT 50',
         array_merge([$userId], $authorIds)
      )->getResultArray();
   }

   public function getUserPosts (int $userId, int $viewerId) : array
   {
      return $this->db->query(
         'SELECT p.*, u.name AS userName, u.avatar AS userAvatar,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id)
               AS likeCount,
            (SELECT COUNT(*) FROM likes
               WHERE post_id = p.id AND user_id = ?)
               AS iLiked
          FROM posts p
          JOIN users u ON u.id = p.user_id
          WHERE p.user_id = ?
          ORDER BY p.created_at DESC
          LIMIT 30',
         [$viewerId, $userId]
      )->getResultArray();
   }
}
