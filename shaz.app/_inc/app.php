<? # _inc/app.php - common junk

$ROOT = "shaz.app";
$DESC = "Steve HAZel's dang website";
$UC = [
   "ar-lft"   => "&#9664;",
   "ar-rit"   => "&#9658;",
   "ar-up"    => "&#9650;",
   "ar-dn"    => "&#9660;",
   "home"     => "&#9700;"
];

#_______________________________________________________________________________
function Got ($fn)  {return file_exists ($fn);}
function Get ($fn)  {return file_get_contents ($fn);}
function Put ($fn, $s)     {file_put_contents ($fn, $s);}
function App ($fn, $s)     {file_put_contents ($fn, $s, FILE_APPEND);}
function Get1 ($fn)
{  $o = '';
   if ($h = fopen ($fn, 'r'))
      {$o = trim (fgets ($h));   fclose ($h);}
   return $o;
}

function LstDir ($p, $df, $ext = '')
{  $lxt = strlen ($ext);
   $lst = [];                          ## output list of fns
   $fd  = dir ($p);
   while (($fn = $fd->read ()) !== false) {
      $dr = is_dir ("$p/$fn");
      if ($df == 'd') {                ## do i want dir or file fns
         if (   $dr  && (! in_array ($fn, ['.','..'])))
            $lst [] = $fn;
      }
      else
         if ((! $dr) && ((! $lxt) || (substr ($fn, -$lxt, $lxt) == $ext)))
            $lst [] = $fn;
   }
   sort ($lst);
   return $lst;
}
#_______________________________________________________________________________
function now ()  {return date ('Y-m-d H:i:s');}

function nows ()
{  $uts = microtime (true);
   $ts = floor ($uts);
   $ms = round (($uts - $ts) * 1000000);
   $mss = sprintf ("%06d", $ms);
   $dts = date ("m/d.D.H:i:s.", $ts);
   return $dts . $mss;
}

function dbg ($s)  {App ("dbg.txt", nows () . " $s\n");}

function dump ($ttl, $h, $lvl = 0)
## dump a hash (usually) to dbg - but should do all types now :)
{  $ind = "";   for ($n = 0;  $n < $lvl;  $n++)  $ind .= "   ";
   if (is_array ($h)) {
      dbg ($ind . '{ ' . $ttl . '   count=' . count ($h));
      foreach ($h as $k => $v) {
         if      (is_array    ($v))  dump ($k, $v, $lvl + 1);
         else if (is_bool     ($v))  dbg ("$ind   $k="    .($v?"T":"F"));
         else if (is_resource ($v))  dbg ("$ind   $k=RES ".
                                                    get_resourc_type ($v));
         else if (is_object   ($v))  dbg ("$ind   $k=OBJ ".get_class ($v));
         else if (is_null     ($v))  dbg ("$ind   $k=NULL");
         else                        dbg ("$ind   $k=$v");
      }
      dbg ($ind . "}");
   }
   else if (is_bool     ($h))  dbg ("$ind$ttl="    .($h?"T":"F"));
   else if (is_resource ($h))  dbg ("$ind$ttl=RES ".get_resource_type ($h));
   else if (is_object   ($h))  dbg ("$ind$ttl=OBJ ".get_class ($h));
   else if (is_null     ($h))  dbg ("$ind$ttl=NULL");
   else                        dbg ("$ind$ttl=$h");
#dbg ($ttl."\n".print_r ($h, true));
}

function callstack ()
{  ob_start ();   debug_print_backtrace ();   $d = ob_get_contents ();
   ob_end_clean ();         dbg ("callstack:\n$d");
}
#_______________________________________________________________________________
function aHas ($a, $k)                 ## php8 sigh
{  if (! is_array ($a)) {
dbg("aHas NOT ARRAY k=$k\n".print_r (debug_backtrace (), true));
      return false;
   }
   return array_key_exists ($k, $a);   ## way too much to type :/
}

function  aGet ($a, $k, $dflt = '')
{  return aHas ($a, $k) ? $a [$k] : $dflt;  }

function aKill (&$a, $v)               ## i'm sick o doin dis
{  $o = array ();                      ## yeah i could splice :/
   foreach ($a as $r)  if ($r != $v)  $o[] = $r;
   $a = $o;
}

function srvr ()  {return        $_SERVER['SERVER_NAME'];}
function page ()  {return        $_SERVER['REQUEST_URI'];}
function me   ()  {return        $_SERVER['PHP_SELF'];}
function args ()  {return (($s = $_SERVER['QUERY_STRING'])=='') ? '' : "?$s";}
function arg  ($k, $def = '')  {return aGet ($_REQUEST, $k, $def);}
function argx ($n)             {return       $_REQUEST [$n];} ## raw arrays,etc
function sess ($k, $def = '')  {return aGet ($_SESSION, $k, $def);}
function setsess ($k, $val = '')            {$_SESSION [$k] = $val;}
function unsess  ($k)          {unset       ($_SESSION [$k]);     }
#_______________________________________________________________________________
## html is sooo uglyyy...   so...
function pg_css ($css, $p)
{  foreach (explode (' ', $css) as $c)      ## v= to not cache
      echo " <link href='$p"."_css/$c".".css?v=".filemtime ($p."_css/$c.css").
                       "' rel='stylesheet' />\n";
}

function pg_js ($js, $p)
{  foreach (explode (' ', $js) as $j) {
      if ($j == 'pg')  $p = '';             ## pg.js (n on) is local
      if (substr ($j, 0, 4) == 'http')      ## not on myyy site
           echo " <script type='text/javascript' src='$j'></script>\n";
      else echo " <script src='$p"."_js/$j".".js?v=".filemtime ($p."_js/$j.js").
                             "'></script>\n";
   }
}
#_______________________________________________________________________________
function pg_head ($ttl, $css, $js, $h = '')      ## html head title css js
{ global $ROOT, $DESC;
   header ("set Access-Control-Allow-Origin '*'");
   $pre = ($h != '') ? "" : "../";     ## home doesn't need ../ path prefix
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport"     content="width=device-width, initial-scale=1.0">
 <meta name="robots"       content="follow, all">
 <meta name="description"  content="<?= $DESC ?>">
 <meta property="og:type"  content="website">
 <meta property="og:url"   content="https://<?= $ROOT ?>">
 <meta property="og:title" content="<?= $DESC ?>">
 <meta property="og:image" content="https://<?= $ROOT ?>/img/favicon.ico">
 <title><?= $ttl ?></title>
 <link href="https://<?= $ROOT ?>/img/favicon.ico" rel="Shortcut Icon" />
<? require_once "google_tag_head.html";

   if (substr ($css, 0, 5) == 'jqui ') {
      $css = substr ($css, 5);
      pg_css ("jquery-ui jquery-ui.structure jquery-ui.theme", $pre);
   }
   pg_css ($css, $pre);
   if (substr ($js, 0, 3) == 'jq ')
      {$js  = substr ($js,  3);   pg_js  ("jquery", $pre);}
   if (substr ($js, 0, 5) == 'jqui ')
      {$js  = substr ($js,  5);   pg_js  ("jquery jquery-ui", $pre);}
   pg_js ($js, $pre);
}
#_______________________________________________________________________________
function pg_body ($nav, $h = '')       ## /head body nav /nav main
{ global $UC; ?>
</head>
<body>
<? require_once "google_tag_body.html"; ?>
<a   id='nav-open'><span class='c0'>m</span><span class='c3'>e</span><span
                         class='c6'>n</span><span class='c9'>u</span></a>
<div id='nav-shut'></div>
<nav id='nav'><ul>
<? foreach ($nav as $i => $n) {
      $tt = $n [0];   $ln = $n [1];   $tip = $n [2];
      $pop = (substr ($ln,0,4) == "http") ? "pop" : "";
      echo " <li class='tip' tip=\"$tip\"><a $pop href='$ln' class='nav-$i'>" .
                                                               "$tt</a></li>\n";
      if (($i == 0) && ($h == ''))     ## lil gap between home n rest
         echo " <li style='padding: 3px'></li>\n";
   }
?>
</ul></nav>
<main>
<?
}
#_______________________________________________________________________________
function pg_foot ()                    ## /main /body /html  (dumb for now)
{  echo "</main>\n\n</body></html>\n";  }
#_______________________________________________________________________________
function doc ($dir)
{ global $UC;
   $pLst = LstDir ("../$dir/txt", 'f');

   $ipg  = arg ('pg', 0);
   $pg   = $pLst [$ipg];
   $ttl  = substr ($pg, 3, -4);
   $pTtl = [];
   $nav = [ [$UC['home']." home",  "..",  "...take me back hooome"] ];
   foreach ($pLst as $i => $fn) {
      $p = substr ($fn, 3, -4);
      $pTtl [$i] = Get1 ("txt/$fn");
      $nav[] = ($ipg == $i) ? [$UC['ar-rit']." $p",  '',  $pTtl [$i]]
                            : [$p,  "?pg=$i",             $pTtl [$i]];
   }

   pg_head ($pTtl [$ipg], "jqui app", "jqui app");
?>
 <script>
   $(function () {init ();});
 </script>
<? pg_body ($nav);
   $aLn = explode ("\n", Get ("txt/$pg"));
   array_shift ($aLn);

   $out = "<h1>".$pTtl [$ipg]."</h1><br>\n";
   $li  = 0;
   foreach ($aLn as $i => $ln) {
#dbg("   $i \n$ln");
   ## look for `fmt`...` in line
      while (($p = strpos ($ln, '`'      )) !== false) {
         if (($b = strpos ($ln, '`', $p+1)) === false)  break;
         if (($e = strpos ($ln, '`', $b+1)) === false)  break;

         $pre = substr ($ln, 0, $p);
         $fmt = substr ($ln, $p+1, $b-$p-1);
         $mid = substr ($ln, $b+1, $e-$b-1);
         $suf = substr ($ln, $e+1);
#dbg("p=$p b=$b e=$e pre='$pre' fmt='$fmt' mid='$mid' suf='$suf'");
         $cl = '';
         if      ($fmt == 'b')  $tag = 'b';
         else if ($fmt == 'i')  $tag = 'i';
         else                {$tag = 'span';   $cl = " class='$fmt'";}
#dbg("tag=$tag cl=$cl");
         $ln = "$pre<$tag$cl>$mid</$tag>$suf";
#dbg($ln);
      }

   ## start list of bullets ?
      if ((strlen ($ln) > 3) && (substr ($ln, 0, 3) == " - ")) {
         if ($li)
            dbg("   unterm'd li !!  (needs cr)  line $i\n");
         $out .= "<div class='bul'>";
         $ln = substr ($ln, 3);   $li = 1;
      }

      $out .= $ln;

   ## end of list
      if (($ln == '') && $li)  {$out .= "</div>";   $li = 0;}

   ## done w line n start next bullet?
      if ((($i+1) >= count($aLn)) || (substr ($aLn [$i+1], 0, 1) != ' '))
         $out .= "<br>";

      $out .= "\n";
   }

## trail nav - link to next,home
   $out .= "<br>\n";
   if ($ipg+1 < count ($pLst))  $out .=
           "<a href='?pg=" . ($ipg+1) . "'>" . $UC['ar-rit'] . " next" .
           "</a> &nbsp; &nbsp; ";
   $out .= "<a href='../'>".$UC['home']." home</a>\n";

   echo "$out\n<br><br>\n";
   pg_foot ();
}
#_______________________________________________________________________________
function select ($id, $ls, $pik = '')
{  echo "<select id='$id' name='$id'>\n";
   foreach ($ls as $o) {
      $a = explode ('`', $o);
      $val = $lbl = $a [0];  if (COUNT ($a) == 2)  $lbl = $a [1];
      echo
        " <option value='$val'" . (($val == $pik)?" SELECTED":"") .
          ">$lbl</option>\n";
   }
   echo "</select>";
}

function check ($id, $lbl, $on = '')
{  echo "<input id='$id' name='$id' type='checkbox'" .
                (($on=='Y')?" checked":"") . ">" .
        "<label for='$id'>$lbl</label>";
}

function table ($id, $hdr, $row)
{  echo
"<table id='$id' name='$id'>\n" .
" <thead>\n" .
"  <tr>\n";
   foreach ($hdr as $c)  echo
"   <th>$c</th>\n";
   echo
"  </tr>\n" .
" </thead>\n" .
" <tbody>\n";
   foreach ($row as $r => $ro) {
      echo
"  <tr>\n";
      foreach ($ro as $c => $co)  echo
"   <td>$co</td>\n";
      echo
"  </tr>\n";
   }
   echo
" </tbody>\n" .
"</table>";
}

function table1 ($id, $hdr, $row)
## table w only 1 column
{  echo
"<table id='$id' name='$id'>\n" .
" <thead><tr><th><b>$hdr</b></th></tr></thead>\n" .
" <tbody>\n";
   foreach ($row as $r => $ro) {
      $a = explode ("\t", $ro);
      $s = $a [0];   $t = aGet ($a, 1);
      echo
"  <tr><td title='$t'>$s</td></tr>\n";
   }
   echo
" </tbody>\n" .
"</table>";
}
