<? # song/index.php - play ma songs

require_once ("../_inc/app.php");

   $shuf = arg ('shuf','Y');
   $pick = [];
   foreach (explode (',', arg ('pick')) as $p)  if ($p != '')  $pick[] = $p;

## doin a scoot?
   if (($fr = arg ('sc')) != '')       ## $to needs from dir chopped off
      {$to = substr ($fr, 3);   rename ("song/$fr", "song/_z/$to");}

   $dir = [];                          ## build dir[] from song dirs minus _z
   foreach (LstDir ("song", 'd') as $d)  if ($d != '_z')  $dir[] = $d;
   sort ($dir);
   $dirp = [];                         ## dirp is names of picked dirs
   foreach ($dir as $i => $d)  if (in_array ($i, $pick))  $dirp[] = $d;

## build pld[] given dirp's files  (minus did[] if shuffle)
   $did = ($shuf == 'N') ? [] : explode ("\n", Get ("did.txt"));
   $pld = [];
   foreach ($dirp as $i => $d) {
      $pld [$i] = [];
      foreach (LstDir ("song/$d", 'f') as $fn)
         if (! in_array ("$d/$fn", $did))  $pld [$i][] = "$d/$fn";
      if (($shuf == 'Y') && (count ($pld [$i]) == 0)) {
         unlink ("did.txt");           ## time ta kill did.txt
         header ("Location: ?shuf=".$shuf."&pick=".arg ('pick'));
      }
   }
   $pl = [];
   if ($shuf == 'Y') {                 ## shuffle and interleave dirs
      foreach ($dirp as $i => $d)  shuffle ($pld [$i]);
      for ($i = 0;;  $i++) {
         $got = 0;
         foreach ($dirp as $j => $d)
            if (aHas ($pld [$j], $i))  {$got = 1;   $pl[] = $pld [$j][$i];}
         if (! $got)  break;
      }
   }
   else {
      foreach ($dirp as $i => $d)  foreach ($pld [$i] as $f)  $pl[] = $f;
      usort ($pl, function ($a, $b) {  ## skip dir name in sort
         $a1 = substr ($a, strpos ($a, '/')+1);
         $b1 = substr ($b, strpos ($b, '/')+1);
         if ($a1 == $b1)  return 0;
         return ($a1 < $b1) ? -1 : 1;
      });
   }
   $nm = [];
   foreach ($pl as $i => $s) {         ## pretty up the name
      $d = substr ($s, 0, strpos ($s, '/'));
      $s = substr ($s, strlen ($d)+1);      ## toss leading dir/
      $s = substr ($s, 0, -4);              ## toss .mp3
      $s = str_replace ('_', ' ', $s);      ## _ => space
      $f = strpos  ($s, '-');
      $l = strrpos ($s, '-');
      if ($f !== false) {                   ## l musta been set too
         $g = substr ($s, 0, $f);           ## but they shouldn't be the same!
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
      --disconnected-color: white;
      --connected-color: white;
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
let PL = <?= json_encode ($pl); ?>;    // play list array

let Nm = <?= json_encode ($nm); ?>;    // prettier names w group,title,etc,dir

let Tk = 0;                            // pos of track we're on

function shuf ()  {return $('#shuf').is (':checked') ? 'Y':'N';}

function pick ()                       // get checkboxed dirs into an array
{ let p = [];
   $("[id^='chk']:checked").each (function () {
      p.push ($(this).attr ('id').substr (3));
   });
   return p;
}

function redo (x = '')                 // get which dirs are picked n refresh
{  window.location = "?shuf=" + shuf () +
                     "&pick=" + pick ().join (',')  +  x;
}

function chk ()  {redo ();}            // checkbox clicked - redo (w no args)


function kick (newtk)
// song got clicked on - make remake queue from there
{ const cSess = cast.framework.CastContext.getInstance ().getCurrentSession ();
   if (! cSess)  {alert ("ya ain't castin yet i think ?");   return;}

  let player = new cast.framework.RemotePlayer ();
  let plCtl  = new cast.framework.RemotePlayerController (player);
   plCtl.stop ();                      // SHUSH !

dbg("kick newtk="+newtk);
   $('#info tbody tr').eq (Tk).css ("background-color", "");    // unhilite
   Tk = newtk;

   if ((pick ().length > 0) && (PL.length == 0))  redo (); // outa songs!
   if (Tk >= PL.length)  return;

  let mo = [];
   for (o = 0;  o < 100;  o++) {
     let i = Tk+o;
      if (i >= PL.length)  break;

dbg("i=");dbg(i);dbg("Nm[i]");dbg(Nm[i]);
     let ar = Nm [i].split ("\n");
      if (o == 0) {
         document.title = ar [2] + ' - ' + ar [0];
         $('#info tbody tr').eq (Tk).css ("background-color", "#FFFF80;");
      }
     let mi = new chrome.cast.media.MediaInfo (
                     'https://shaz.app/song/song/' + PL [i], 'audio/mpeg');
      mi.metadata = new chrome.cast.media.GenericMediaMetadata ();
      mi.metadata.artist = ar [0];
      mi.metadata.title  = ar [2];
     let qi = new chrome.cast.media.QueueItem (mi);
      qi.autoplay = true;
      mo [o] = qi;
   }
dbg("queuein' "+mo.length);
  let req = new chrome.cast.media.QueueLoadRequest (mo);
   cSess.getSessionObj ().queueLoad (req)
dbg('playin!');
}

/*
function lyr ()                        // hit google lookin fo lyrics
{  if (Tk >= PL.length)  return;

   a = Nm [Tk].split ("\n");   tt = a [2];   gr = a [0];
   window.open ('https://google.com/search?q=lyrics "'+tt+'" "'+gr+'"',
                                                                      "_blank");
}
*/

function scoot ()  { redo ('&sc=' + PL [Tk]); }


window ['__onGCastApiAvailable'] = function (avail) {
   if (! avail)  return;

   cast.framework.CastContext.getInstance ().setOptions ({
      receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
      autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
   });

  let player = new cast.framework.RemotePlayer ();
  let plCtl  = new cast.framework.RemotePlayerController (player);
   plCtl.addEventListener (
      cast.framework.RemotePlayerEventType.PLAYER_STATE_CHANGED,
      (event) => {
         if (event.value == 'IDLE') {
           let fn = player.mediaInfo.contentId.substr (27);
dbg("done='"+fn+"'");
            $.get ("did.php", { did: fn });
            if (player.currentTime > 5) {
               $.get ("skip.php", { it: fn });
dbg("   WAS SKIPPED!");
            }

         // unhilite old
            $('#info tbody tr').eq (Tk).css ("background-color", "");

            Tk += 1;
            if (Tk >= PL.length)  return;

         // title and hilite
           let ar = Nm [Tk].split ("\n");
           a = Nm [Tk].split ("\n");   tt = a [2];   gr = a [0];
            document.title = tt + ' - ' + gr;

            $('#info tbody tr').eq (Tk).css ("background-color", "#FFFF80;");

            window.open ('https://google.com/search?q=lyrics "'+tt+'" "'+gr+'"',
                         "_blank");
         }
//dbg(event); dbg(player);
      }
   );
};


$(function () {                        // boot da page
   init ();

   if (! mobl ())  $('.mobl').hide ();
   $('input' ).checkboxradio ().click (chk);
/* $('#play' ).button ().click (play);
   $('#lyr'  ).button ().click (lyr);
   $('#scoot').button ().click (scoot); */
   $('#info tbody').on ('click','tr',function ()  { kick ($(this).index); });
});
/*
"https://www.gstatic.com/cast/sdk/libs/caf_sender/v3/cast_framework.js"
*/
 </script>
 <script src=
"https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"
  ></script>

<? pg_body ([ [$UC['home']." home",  "..",  "...take me back hooome"] ]); ?>
<span style="padding-left: 5em"></span>
<? check ('shuf', 'shuf', $shuf); # <a id='scoot'>skip</a>
   foreach ($dir as $i => $s)
      check ("chk$i", $s, in_array ($i, $pick) ? 'Y':''); ?>
<span id='num'><?= count($nm) ?></span><br class='mobl'>
<?/*
<a id='play'>play</a>
<a id='lyr'>lyric</a>
*/?>
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
