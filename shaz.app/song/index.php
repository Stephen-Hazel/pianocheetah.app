<? # song/index.php - play ma songs

require_once ("../_inc/app.php");

   $shuf = arg ('shuf','Y');
   $pick = [];
   foreach (explode (',', arg ('pick')) as $p)  if ($p != '')  $pick[] = $p;

## doin a scoot?
   if (($fr = arg ('sc')) != '')       ## $to needs from dir chopped off
      {$to = substr ($fr, 3);   rename ("song/$fr", "song/_z/$to");}

## build dir[] from song dirs minus _z,__misc
   $dir = [];
   foreach (LstDir ("song", 'd') as $d)  $dir[] = $d;
   sort ($dir);

## build pl[] given picked dirs minus did[] (if shuffle)
   $pl = [];
   $did = ($shuf == 'N') ? [] : explode ("\n", Get ("did.txt"));
   foreach ($dir as $i => $d)  if (in_array ($i, $pick)) {
      $mp3 = LstDir ("song/$d", 'f');
      foreach ($mp3 as $fn)  if (! in_array ("$d/$fn", $did))
         $pl[] = "$d/$fn";
   }
   if ((count ($pick) > 0) && (count ($pl) == 0)) {
      unlink ("did.txt");              ## time ta kill did.txt
      header ("Location: ?shuf=".$shuf."&pick=".arg ('pick'));
   }
   if ($shuf == 'Y')  shuffle ($pl);
   else {                              ## ^chop rows if shuffle
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
      if (count ($pick) == 1) $d = '';      ## no need for dir if only 1
      $s = substr ($s, 0, -4);              ## toss .mp3
      $s = str_replace ('_', ' ', $s);      ## _ => space
      $f = strpos  ($s, '-');
      $l = strrpos ($s, '-');
      if ($f !== false) {                   ## l musta been set too
         $g = substr ($s, 0, $f);           ## but they shouldn't be the same!
         $t = substr ($s, $l+1);            ## TODO
         $x = ($f == $l) ? '' : substr ($s, $f+1, $l-$f-1);
#         if ($d != '')  $d = "|$d";
#         $s = "$g|$x|$t$d\t$g\n$x\n$t\n".(($d=='')?'':substr($d,1));
         if ($d != '')  $d .= "|";
         $s = "$t|$d$g|$x\t$g\n$x\n$t\n".(($d=='')?'':substr($d,0,-1));
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
let Nm = <?= json_encode ($nm); ?>;    // prettier names group|title|etc
let Tk = 0,  Au;                       // pos of track we're on, audio element

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


function next (newtk = -1)
{ let sh = shuf ();
   Au.pause ();                        // shush
   $('#info tbody tr').eq (Tk).css ("background-color", "");    // unhilite
   if (newtk == Tk)  return;           // shortcut to pause

   if (newtk != -1)  Tk = newtk;       // song got clicked on
   else {                              // this guy is dooone - mark it
      $.get ("did.php", { did: PL [Tk] });

      if (sh == 'Y') {                 // take outa PL and table
         PL.splice (Tk, 1);
         Nm.splice (Tk, 1);
         $('#info tbody tr').eq (Tk).remove ();
         $('#info thead tr th b').html (PL.length + " songs");
      }
      else
         Tk++;                         // gotta bump pos for noshuf (all)

      if (Tk >= PL.length) {           // end of list?  restart
         Tk = 0;
         $('#info tbody tr').eq (Tk).get (0)
                                    .scrollIntoView ({ behavior: 'smooth' });
         if ((sh == 'Y') && (PL.length == 0))  redo ();
      }                                // completely redo if shuf n empty
   }
   play ('y');
}

function play (go = 'y')
{  if ((pick ().length > 0) && (PL.length == 0))  redo (); // outa songs!
   if (Tk >= PL.length)  return;

   document.title = Nm [Tk];
   Au.src = 'song/' + PL [Tk];
   if (go == 'y') {
      Au.play ();
      $('#info tbody tr').eq (Tk).css ("background-color", "#FFFF80;");
   }
}

function lyr ()                        // hit google lookin fo lyrics
{  if (Tk >= PL.length)  return;
   a = Nm [Tk].split ('|');
   tt = a [0];   gr = (a [1].length > 2) ? a [1] : a [2];
   window.open (
      'https://google.com/search?q=lyrics "'+tt+'" "'+gr+'"', "_blank");
}

function scoot ()  { redo ('&sc=' + PL [Tk]); }

$(function () {                        // boot da page
   init ();

   Au = tag ('audio');
   if (! mobl ()) {
      Au.volume = 0.2;                 // desktop shouldn't have max volume :/
      $('.mobl').hide ();
   }
   $('input' ).checkboxradio ().click (chk);
   $('#scoot').button ().click (scoot);
   $('#lyr'  ).button ().click (lyr);
   $('#info tbody').on ('click', 'tr', function () {
                                         next ($(this).index ());
                                       });
   Au.addEventListener ('ended', () => { next (); });
   play ('n');  // setup audio but can't aaactually play till click
});
 </script>
<? pg_body ([
      [$UC['home']." home",  "..",  "...take me back hooome"],
   ]); ?> <audio controls></audio><br class='mobl'>
<? check ('shuf', 'shuf', $shuf); ?> <a id='scoot'>skip</a>
                                     <a id='lyr'>lyric</a> --<br class='mobl'>
<? foreach ($dir as $i => $s)
      check ("chk$i", $s, in_array ($i, $pick) ? 'Y':''); ?><br>

<? $n2 = [];
   foreach ($nm as $n) {
      $b = strpos ($n, '|');
      if ($b !== false)  $n = "<b>" . substr ($n, 0, $b) .
                             "</b>" . substr ($n, $b);
      $n2[] = $n;
   }
   table1 ('info', count ($nm)." songs", $n2); ?>
<? pg_foot ();
