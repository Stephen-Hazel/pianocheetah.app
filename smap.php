<?php
// Run from CLI: php gen_sitemap.php

$base_url = 'https://pianocheetah.app';
$root     = __DIR__;
$out_file = $root . '/sitemap.xml';
$self     = basename (__FILE__);

$skip_dirs = [
   'shaz.app',
   'zEtc',
   'tMidi',
   'download',
];

$skip_abs = array_map (fn ($d) => $root . '/' . $d, $skip_dirs);

function walk ($dir, $skip_abs) {
   $files = [];
   foreach (scandir ($dir) as $item) {
      if ($item === '.' || $item === '..') continue;
      if ($item [0] === '.' || $item [0] === '_') continue;
      $full = $dir . '/' . $item;
      if (is_dir ($full) && !is_link ($full)) {
         if (in_array ($full, $skip_abs)) continue;
         $files = array_merge ($files, walk ($full, $skip_abs));
      }
      else if (!is_dir ($full) && in_array (
         pathinfo ($item, PATHINFO_EXTENSION), ['php', 'html']
      )) {
         $files [] = $full;
      }
   }
   return $files;
}

$files = walk ($root, $skip_abs);
sort ($files);

$urls = [];
foreach ($files as $file) {
   if (basename ($file) === $self) continue;

   $rel   = ltrim (substr ($file, strlen ($root)), '/');
   $parts = explode ('/', $rel);
   $depth = count ($parts) - 1;

   if (basename ($file) === 'index.php') {
      $dir_rel  = dirname ($rel);
      $url_path = $dir_rel === '.' ? '' : $dir_rel . '/';
   }
   else {
      $url_path = $rel;
   }

   $priority = match (true) {
      $depth === 0 => '1.00',
      $depth === 1 => '0.80',
      $depth === 2 => '0.64',
      default      => '0.51',
   };

   $urls [] = [
      'loc'      => $base_url . '/' . $url_path,
      'lastmod'  => date ('c', filemtime ($file)),
      'priority' => $priority,
   ];
}

$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n";
$xml .= "        xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
$xml .= "        xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n";
$xml .= "              http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";

foreach ($urls as $u) {
   $xml .= "\n<url>\n";
   $xml .= "  <loc>{$u['loc']}</loc>\n";
   $xml .= "  <lastmod>{$u['lastmod']}</lastmod>\n";
   $xml .= "  <priority>{$u['priority']}</priority>\n";
   $xml .= "</url>\n";
}

$xml .= "\n</urlset>\n";

file_put_contents ($out_file, $xml);
echo "Written: $out_file (" . count ($urls) . " URLs)\n";
