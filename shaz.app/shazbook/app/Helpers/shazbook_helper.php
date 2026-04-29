<?php

if (!function_exists('avatar_url')) {
   function avatar_url (?string $avatar) : string
   {
      if (!$avatar) {
         return base_url('css/default-avatar.svg');
      }
      // Google avatar (or any absolute URL) — use as-is
      if (str_starts_with($avatar, 'http')) {
         return $avatar;
      }
      return base_url($avatar);
   }
}
