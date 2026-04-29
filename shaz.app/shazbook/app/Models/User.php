<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
   protected $table      = 'users';
   protected $primaryKey = 'id';

   protected $allowedFields = [
      'google_id', 'email', 'name', 'avatar', 'bio',
   ];

   protected $useTimestamps  = true;
   protected $createdField   = 'created_at';
   protected $updatedField   = '';
   protected $returnType     = 'array';

   public function findByGoogleId (string $googleId) : ?array
   {
      return $this->where('google_id', $googleId)->first();
   }

   public function upsertGoogle (
      string $googleId,
      string $email,
      string $name,
      string $avatar
   ) : array {
      $user = $this->findByGoogleId($googleId);
      if ($user) {
         return $user;
      }
      $id = $this->insert([
         'google_id' => $googleId,
         'email'     => $email,
         'name'      => $name,
         'avatar'    => $avatar,
      ]);
      return $this->find($id);
   }

   public function search (string $query, int $excludeId) : array
   {
      return $this
         ->where('id !=', $excludeId)
         ->groupStart()
            ->like('name',  $query)
            ->orLike('email', $query)
         ->groupEnd()
         ->findAll(20);
   }
}
