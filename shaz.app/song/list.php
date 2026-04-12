<? # song/list.php - dump ma songs

require_once ("../_inc/app.php");

$dir = [];                          ## build dir[] from song dirs minus _z
foreach (LstDir ("song", 'd') as $d)  if ($d != '_z')  $dir[] = $d;
sort ($dir);
$pl = [];
foreach ($dir as $i => $d)
   foreach (LstDir ("song/$d", 'f') as $fn)  $pl[] = "$d/$fn";
foreach ($pl as $fn)  echo "$fn\n";
