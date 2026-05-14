<? # song/index.php - cast n play ma songs

require_once ("../_inc/app.php");

$shuf = arg ('shuf','Y');
$pick = [];
foreach (explode (',', arg ('pick')) as $p)  if ($p != '')  $pick[] = $p;

$dir = [];                             ## song dirs minus _z
foreach (LstDir ("song", 'd') as $d)  if ($d != '_z')  $dir[] = $d;
sort ($dir);

$dirp = [];                            ## picked song dirs
foreach ($dir as $i => $d)  if (in_array ($i, $pick))  $dirp[] = $d;

$did = ($shuf == 'N') ? [] : explode ("\n", Get ("did.txt"));

$pld = [];                             ## dirp's mp3s  (minus did[] if shuffle)
foreach ($dirp as $i => $d) {
   $pld [$i] = [];
   foreach (LstDir ("song/$d", 'f') as $fn)
      if (! in_array ("$d/$fn", $did))  $pld [$i][] = "$d/$fn";

   if (($shuf == 'Y') && (count ($pld [$i]) == 0)) {
      unlink ("did.txt");              ## time ta kill did.txt
dbg("killed did.txt");
      header ("Location: ?shuf=".$shuf."&pick=".arg ('pick'));
   }
}
$pl = [];                              ## final playlist
if ($shuf == 'Y') {                    ## interleave shuffled dirs
   foreach ($dirp as $i => $d)  shuffle ($pld [$i]);
   for ($i = 0;;  $i++) {
      $got = 0;
      foreach ($dirp as $j => $d)
         if (aHas ($pld [$j], $i))  {$got = 1;   $pl[] = $pld [$j][$i];}
      if (! $got)  break;
   }
}
else {                                 ## sort em all together
   foreach ($dirp as $i => $d)  foreach ($pld [$i] as $f)  $pl[] = $f;
   usort ($pl, function ($a, $b) {     ## skip dir name in sort
      $a1 = substr ($a, strpos ($a, '/')+1);
      $b1 = substr ($b, strpos ($b, '/')+1);
      if ($a1 == $b1)  return 0;
      return ($a1 < $b1) ? -1 : 1;
   });
}
$nm = [];
foreach ($pl as $i => $s) {            ## pretty up the name
   $d = substr ($s, 0, strpos ($s, '/'));
   $s = substr ($s, strlen ($d)+1);    ## toss leading dir/
   $s = substr ($s, 0, -4);            ## toss .mp3
   $s = str_replace ('_', ' ', $s);    ## _ => space
   $f = strpos  ($s, '-');
   $l = strrpos ($s, '-');
   if ($f !== false) {                 ## l musta been set too
      $g = substr ($s, 0, $f);         ## but they shouldn't be the same!
      $t = substr ($s, $l+1);
      $x = ($f == $l) ? '' : substr ($s, $f+1, $l-$f-1);
      $s = "$g\n$x\n$t\n$d";
   }
   else {
#dbg($s);
      $s = "?? $s $d";
   }
   $nm[] = $s;
}

pg_head ("song", "jqui app", "jqui app");
?>
<style>
google-cast-launcher {
   float:   right;
   margin:  10px 6px 14px 0px;
   width:   40px;
   height:  32px;
   opacity: 0.7;
   background-color: #000;
   border:  none;
   outline: none;
}
google-cast-launcher:hover {
   --disconnected-color: grey;
   --connected-color:    white;
}
body.dtop main {
   display: inline;
   width: 100%;
   margin: 0;
}
body.mobl main table {
   width: 100%;
   border-collapse: collapse;
   table-layout: fixed;
}
th,td {
   white-space: nowrap;
   overflow: hidden;
}
</style>
<script> // ___________________________________________________________________
let PL = <?= json_encode ($pl); ?>;    // play list
let Nm = <?= json_encode ($nm); ?>;    // split fn into group,title,etc,dir
let CastOK = false;

function shuf ()  {return $('#shuf').is (':checked') ? 'Y':'N';}

function pick ()                       // get checkboxed dirs into an array
{ let p = [];
   $("[id^='chk']:checked").each (function () {
      p.push ($(this).attr ('id').substr (3));
   });
   return p;
}

function redo (x = '')                 // get which dirs are picked n refresh
{  window.location = "?shuf=" + shuf () + "&pick=" + pick ().join (',')  +  x;
}

function chk ()  {redo ();}            // checkbox clicked - redo (w no args)


function kick (tk)
// song got clicked on - remake queue from there
{ let sess = cast.framework.CastContext.getInstance ().getCurrentSession ();
   if ((! sess) || (! CastOK))
      {alert ("ya ain't castin yet i think ?");   return;}

  let player = new cast.framework.RemotePlayer ();
  let plCtl  = new cast.framework.RemotePlayerController (player);
   plCtl.stop ();                      // SHUSH !

dbg("kick tk="+tk);
   if (tk >= PL.length)  return;

  let q = [];
   for (o = 0;  o < 100;  o++) {
     let i = tk+o;
      if (i >= PL.length)  break;

     let a = Nm [i].split ("\n");
     let m = {
         contentId:   'https://shaz.app/song/song/' + PL [i],
         contentType: 'audio/mpeg',
         metadata:    { metadataType: 0, artist: a [0], title: a [2] }
      };
      q [o] = { media: m, autoplay: true };
   }
dbg("queuein' "+q.length);
   sess.loadMedia ({ queueData: { items: q } }).then (() => {
      localStorage.setItem ('castPL', JSON.stringify ({
         pl: PL.slice (tk, tk + o),
         nm: Nm.slice (tk, tk + o)
      }));
      window.location = 'cast.php';
   });
}


window ['__onGCastApiAvailable'] = function (avail) {
// cast api init
   if (! avail)  return;

   cast.framework.CastContext.getInstance ().setOptions ({
      receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
      autoJoinPolicy:        chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
   });
   CastOK = true;
};


$(function () {                        // boot da page
   init ();

   if (! mobl ())  $('.mobl').hide ();
   $('input' ).checkboxradio ().click (chk);

   $('#castq').button ().click (function () {location = 'cast.php';});
   $('#info tbody').on ('click','tr',function () {
      kick ($(this).index ());
   });
});
</script>
<script src=
 "https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1">
</script>

<? pg_body ([ [$UC['home']." home",  "..",  "...take me back hooome"] ]); ?>
<span style="padding-left: 5em"></span>
<? check ('shuf', 'shuf', $shuf);
foreach ($dir as $i => $s)
   check ("chk$i", $s, in_array ($i, $pick) ? 'Y':''); ?>
   <span id='num'><?= count($nm) ?></span><br class='mobl'>
   <a id='castq'>castq</a>
   <google-cast-launcher></google-cast-launcher>

   <? $n2 = [];
foreach ($nm as $n) {
   $a = explode ("\n", $n);
   if (($shuf == 'N') && (count ($pick) >= 4))
        $n2[] = "<b>".$a [0]."</b>"." ".$a [1]." <b>".$a [2]."</b> ".$a [3];
   else $n2[] = "<b>".$a [2]."</b>"." ".$a [0]." <b>".$a [3]."</b> ".$a [1];
}
table1 ('info', '', $n2); ?>
<? pg_foot ();
