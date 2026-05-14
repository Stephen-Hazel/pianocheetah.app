<? # pic/sum.php - year summary: 4 random pics per picset

require_once ("../_inc/app.php");

   $y = arg ('y', '');
   $Year = LstDir ("idx",       'd');   sort ($Year);
   $yStr = $Year [$y];

   $PSet = LstDir ("idx/$yStr", 'd');   sort ($PSet);

   pg_head ("pic sum", "jqui app", "jqui app");
?>
 <style>
#top {
   margin-left: 5em;
}
.comment {
   max-width:        640px;
   font-size:        14pt;
   color:            #003050;
   background-color: #00F0FF;
   padding:          5px;
}
.piccomment {
   font-size: 10pt;
   color:     #003050;
   max-width: 150px;
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
{  location.href = '?y=' + $("#year").prop ('selectedIndex');  }

function refresh ()  {location.href = location.href;}

$(function ()
{  $('#redo').button ().click (refresh);
   $('#year').selectmenu ({ change: reYear, width: 120 });
});
 </script>

<? pg_body ([
      [$UC['home']." home", "..",  "...take me back hooome"]
   ]); ?>
<span id='top'>
<? select ('year', $Year, $yStr); ?>
 <button id="redo" title="redo">>ReDo</button>
</span>

<? foreach ($PSet as $sStr) {
      $txtFile = "idx/$yStr/$sStr.txt";
      if (! Got ($txtFile))  continue;

      $lines = explode ("\n", Get ($txtFile));
      array_pop ($lines);              // remove trailing empty from last \n
      $lines = array_filter ($lines, fn ($l) => trim ($l) !== '');
      $lines = array_values ($lines);
      if (count ($lines) == 0)  continue;

      $r0   = explode ('|', $lines [0]);
      $pCom = isset ($r0 [3]) ? $r0 [3] : '';

      $cnt  = min (4, count ($lines));
      $keys = array_rand ($lines, $cnt);
      if (! is_array ($keys))  $keys = [$keys];
?>
<div class='pset-block'>
 <div class='pset-label'><?= htmlspecialchars ($sStr) ?></div>
<? if ($pCom != '')  echo "<div class='comment'>$pCom</div>\n"; ?>
 <div class='pset-pics'>
<?    foreach ($keys as $k) {
         $a  = explode ('|', $lines [$k]);
         $fn = $a [1];
         $cm = isset ($a [2]) ? $a [2] : '';
         echo "  <div>\n" .
              "   <img src='idx/$yStr/$sStr/$fn'>\n" .
              ($cm ? "   <div class='piccomment'>$cm</div>\n" : "") .
              "  </div>\n";
      }
?>
 </div>
</div>
<? }

pg_foot ();
