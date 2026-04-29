<?php

namespace App\Models;

use CodeIgniter\Model;

class Friend extends Model
{
   protected $table      = 'friends';
   protected $primaryKey = 'id';

   protected $allowedFields = [
      'requester_id', 'addressee_id', 'status',
   ];

   protected $useTimestamps = true;
   protected $createdField  = 'created_at';
   protected $updatedField  = '';
   protected $returnType    = 'array';

   public function getRow (int $a, int $b) : ?array
   {
      return $this->db->query(
         'SELECT * FROM friends
          WHERE (requester_id=? AND addressee_id=?)
             OR (requester_id=? AND addressee_id=?)
          LIMIT 1',
         [$a, $b, $b, $a]
      )->getRowArray();
   }

   public function getFriendIds (int $userId) : array
   {
      $rows = $this->db->query(
         'SELECT requester_id, addressee_id FROM friends
          WHERE status=\'accepted\'
            AND (requester_id=? OR addressee_id=?)',
         [$userId, $userId]
      )->getResultArray();

      $ids = [];
      foreach ($rows as $row) {
         $ids [] = ($row ['requester_id'] == $userId)
            ? (int)$row ['addressee_id']
            : (int)$row ['requester_id'];
      }
      return $ids;
   }

   public function getFriends (int $userId) : array
   {
      return $this->db->query(
         'SELECT u.*, f.id AS friendRowId FROM users u
          JOIN friends f
            ON (f.requester_id=u.id OR f.addressee_id=u.id)
          WHERE f.status=\'accepted\'
            AND (f.requester_id=? OR f.addressee_id=?)
            AND u.id != ?
          ORDER BY u.name',
         [$userId, $userId, $userId]
      )->getResultArray();
   }

   public function getPending (int $userId) : array
   {
      return $this->db->query(
         'SELECT u.*, f.id AS friendRowId FROM users u
          JOIN friends f ON f.requester_id=u.id
          WHERE f.addressee_id=? AND f.status=\'pending\'
          ORDER BY f.created_at DESC',
         [$userId]
      )->getResultArray();
   }

   public function getSuggestions (int $userId) : array
   {
      return $this->db->query(
         'SELECT u.* FROM users u
          WHERE u.id != ?
            AND u.id NOT IN (
               SELECT CASE
                  WHEN requester_id=? THEN addressee_id
                  ELSE requester_id
               END FROM friends
               WHERE requester_id=? OR addressee_id=?
            )
          ORDER BY u.name
          LIMIT 20',
         [$userId, $userId, $userId, $userId]
      )->getResultArray();
   }

   public function sendRequest (int $from, int $to) : void
   {
      $existing = $this->getRow($from, $to);
      if (!$existing) {
         $this->insert([
            'requester_id' => $from,
            'addressee_id' => $to,
            'status'       => 'pending',
         ]);
      }
   }

   public function accept (int $rowId, int $addresseeId) : void
   {
      $this->db->query(
         'UPDATE friends SET status=\'accepted\'
          WHERE id=? AND addressee_id=?',
         [$rowId, $addresseeId]
      );
   }

   public function decline (int $rowId, int $addresseeId) : void
   {
      $this->db->query(
         'DELETE FROM friends WHERE id=? AND addressee_id=?',
         [$rowId, $addresseeId]
      );
   }

   public function unfriend (int $userId, int $otherId) : void
   {
      $this->db->query(
         'DELETE FROM friends
          WHERE (requester_id=? AND addressee_id=?)
             OR (requester_id=? AND addressee_id=?)',
         [$userId, $otherId, $otherId, $userId]
      );
   }
}
