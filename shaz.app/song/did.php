<? # did.php - tack a song onto did.txt

require_once ("../_inc/app.php");

#dump('did.php', $_REQUEST);
$fn = arg ('did');
Put ("did.txt", Get ("did.txt") . "$fn\n");      ## old school did.txt

$d = dirname ($fn);
dbgx("did.php fn=$fn d=$d");

$adid = explode ("\n", $did = Get ("did/$d.txt"));
dbgx("ndid=".count($adid));
$all = LstDir ("song/$d", 'f');
dbgx("nall=".count($all));

$x = "";
foreach ($all as $f)  if (! in_array ($f, $adid))  {$x = $f;   break;}
dbgx("x=$x");

if ($x == '')  unlink ("did/$d.txt");
else           Put    ("did/$d.txt", "$did$fn\n");
