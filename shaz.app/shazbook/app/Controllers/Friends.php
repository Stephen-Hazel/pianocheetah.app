<?php

namespace App\Controllers;

use App\Models\Friend;

class Friends extends BaseController
{
   public function index ()
   {
      $friend = new Friend();

      return view('friends', [
         'me'          => $this->me(),
         'friends'     => $friend->getFriends($this->myId()),
         'pending'     => $friend->getPending($this->myId()),
         'suggestions' => $friend->getSuggestions($this->myId()),
      ]);
   }

   public function request (int $toId)
   {
      if ($toId !== $this->myId()) {
         (new Friend())->sendRequest($this->myId(), $toId);
      }
      return redirect()->back();
   }

   public function accept (int $rowId)
   {
      (new Friend())->accept($rowId, $this->myId());
      return redirect()->back();
   }

   public function decline (int $rowId)
   {
      (new Friend())->decline($rowId, $this->myId());
      return redirect()->back();
   }

   public function unfriend (int $otherId)
   {
      (new Friend())->unfriend($this->myId(), $otherId);
      return redirect()->back();
   }
}
