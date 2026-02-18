<? # skip.php - tack a song onto skip.txt

require_once ("../_inc/app.php");

#dump('skip.php', $_REQUEST);
Put ("skip.txt", Get ("skip.txt") . arg('it') . "\n");
