<? # pic/sum.php - year summary: all pics per picset

require_once ("../_inc/app.php");

   $y = arg ('y', '');
   $Year = LstDir ("idx",       'd');   sort ($Year);
   $yStr = $Year [$y];

   $PSet = LstDir ("idx/$yStr", 'd');   sort ($PSet);

   pg_head ("pic sum", "jqui app", "jqui app");
?>
 <style>
body.dtop main {
   width: 100%;
}
#top {
   margin-left: 5em;
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
.pset-com {
   font-size:   12pt;
   font-weight: normal;
}
.piccomment {
   font-size: 10pt;
   color:     #003050;
}
.pset-pics {
   display: flex;
   flex-wrap: wrap;
   gap: 8px;
}
.pset-pics > div {
   flex:   0 0 auto;
   cursor: pointer;
}
.pset-pics img {
   max-height: 120px;
   width:      auto;
   object-fit: contain;
}
#big {
   display:             flex;
   justify-content:     center;
   align-items:         center;
   background-size:     contain;
   background-position: center;
   background-repeat:   no-repeat;
}
#bigtxt {
   position:    absolute;
   z-index:     1;
   top:         0;
   left:        0;
   color:       white;
   font-size:   14pt;
   background:  rgba(0,0,0,.5);
   text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
}
 </style>
 <script>
function reYear ()
{  location.href = '?y=' + $("#year").prop ('selectedIndex');  }

function full ()
{ const it = document.querySelector ('#big');
   if            (it.requestFullScreen)       it.requestFullscreen ();
   else if (it.webkitRequestFullscreen) it.webkitRequestFullscreen ();
   else if     (it.msRequestFullscreen)     it.msRequestFullscreen ();
   if            (document.fullscreenElement)       document.exitFullscreen ();
   else if (document.webkitFullscreenElement) document.webkitExitFullscreen ();
   else if     (document.msFullscreenElement)     document.msExitFullscreen ();
}

function un ()  {$("#full").html ('');}

function big2 (path, fn, cm)
{ let or = screen.orientation.type.substr (0, 4);
  let h  = "<center>\n" +
            "<div id='big' onclick='full(); un();'>\n" +
            " <p id='bigtxt'>" + cm + "</p>\n" +
            "</div>\n</center>\n";
   $("#full").html (h);
   if (or == 'land')  $('#big').css ('height', '94vh');
   else               $('#big').css ('width',  '100vw');
   $('#big').css ('background-image', 'url("' + path + fn + '")');
   full ();
}

$(function ()
{  init ();
   $('#year').selectmenu ({ change: reYear, width: 120 });
});
 </script>

<? pg_body ([
      [$UC['home']." home", "..",  "...take me back hooome"]
   ]); ?>
<span id='top'>
<? select ('year', $Year, $yStr); ?>
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

?>
<div class='pset-block'>
 <div class='pset-label'><?= htmlspecialchars ($sStr) ?>
<?    if ($pCom != '')  echo " <span class='pset-com'>&mdash; $pCom</span>"; ?>
 </div>
 <div class='pset-pics'>
<?    foreach ($lines as $line) {
         $a   = explode ('|', $line);
         $fn  = $a [1];
         $cm  = isset ($a [2]) ? $a [2] : '';
         $esc = htmlspecialchars ($cm, ENT_QUOTES);
         echo "  <div onclick=\"big2('pic/$yStr/$sStr/','$fn','$esc')\">\n" .
              "   <img src='idx/$yStr/$sStr/$fn'>\n" .
              ($cm ? "   <div class='piccomment'>$cm</div>\n" : "") .
              "  </div>\n";
      }
?>
 </div>
</div>
<? }
?>
<span id='full'></span>
<?
pg_foot ();
