<? # pic/sum.php - year summary: 4 random pics per picset

require_once ("../_inc/app.php");

   $Year = LstDir ("idx", 'd');   sort ($Year);
   $y    = arg ('y', end ($Year));
   reset ($Year);

   if (! in_array ($y, $Year))  $y = end ($Year);

   $PSet = LstDir ("idx/$y", 'd');   sort ($PSet);

   pg_head ("pic sum", "jqui app", "jqui app");
?>
 <style>
#top {
   margin: 0.5em 1em;
}
.pset-block {
   margin: 1.5em 1em 0.5em;
}
.pset-label {
   font-size:   14pt;
   font-weight: bold;
   color:       #003050;
   margin-bottom: 0.3em;
}
.pset-pics {
   display: flex;
   flex-wrap: wrap;
   gap: 8px;
}
.pset-pics img {
   max-height: 300px;
   max-width:  100%;
   object-fit: contain;
}
 </style>
 <script>
function reYear ()
{  location.href = '?y=' + document.getElementById ('year').value; }

function refresh ()
{  location.href = location.href; }
 </script>
<? pg_body ([
      [$UC['home']." home", "..",  "home"],
      ["pics",               ".",  "pics"],
   ]); ?>
<div id='top'>
 <select id='year' onchange='reYear()'>
<? foreach ($Year as $yr) {
      $sel = ($yr == $y) ? ' selected' : '';
      echo "  <option value='$yr'$sel>$yr</option>\n";
   } ?>
 </select>
 <button onclick='refresh()'>Refresh</button>
</div>

<?
foreach ($PSet as $sStr) {
   $txtFile = "idx/$y/$sStr.txt";
   if (! Got ($txtFile))  continue;

   $lines = explode ("\n", Get ($txtFile));
   array_pop ($lines);   // remove trailing empty from last \n
   $lines = array_filter ($lines, fn($l) => trim ($l) !== '');
   $lines = array_values ($lines);

   if (count ($lines) == 0)  continue;

   $cnt  = min (4, count ($lines));
   $keys = array_rand ($lines, $cnt);
   if (! is_array ($keys))  $keys = [$keys];
?>
<div class='pset-block'>
 <div class='pset-label'><?= htmlspecialchars ($sStr) ?></div>
 <div class='pset-pics'>
<?
   foreach ($keys as $k) {
      $a  = explode ('|', $lines [$k]);
      $fn = $a [1];
      echo "  <img src='pic/$y/$sStr/$fn'>\n";
   }
?>
 </div>
</div>
<?
}
?>

<? pg_foot ();
