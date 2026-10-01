<?php ## go.php - setup my site pic dir with index .txt files and thumbnails

function Got ($fn)  {return file_exists ($fn);}
function Get ($fn)  {return file_get_contents ($fn);}
function Put ($fn, $s)     {file_put_contents ($fn, $s);}

function LstDir ($p, $df)
{  $lst = [];
   $naw = ['.', '..'];
   $d = dir ($p);
   if ($d === false)  die ("can't open dir $p\n");
   while (($e = $d->read ()) !== false)
      if ( (($df == 'd') &&    is_dir ("$p/$e") && (! in_array ($e, $naw))) ||
           (($df != 'd') && (! is_dir ("$p/$e"))) )
         $lst [] = $e;
   $d->close ();
   return $lst;
}

$Top = __DIR__ . "/pic";
$Idx = __DIR__ . "/idx";

$x = LstDir ("$Top", 'd');
sort ($x);
$yLst = [];
foreach ($x as $dir)  if (substr ($dir,0,1) == '2')  $yLst[] = $dir;

foreach ($yLst as $y) {
echo "$y\n";
   if (   ! Got ("$Idx/$y"))     mkdir ("$Idx/$y");
   $setLst = LstDir ("$Top/$y", 'd');
   sort ($setLst);

   foreach ($setLst as $s) {
      if (! Got ("$Idx/$y/$s"))  mkdir ("$Idx/$y/$s");
echo "   $s\n";

      if (Got ("$Idx/$y/$s.txt")) {
echo "      got\n";
         continue;                          // it's fine as iz
      }

   // wanna rethumb if  idx/y/new_set.txt  cuz P/L change or del'd, etc
      if (Got (  "$Idx/$y/new_$s.txt")) {
         rename ("$Idx/$y/new_$s.txt", "$Idx/$y/$s.txt");
echo "      rethumb\n";
      }
      else {                                // make fresh idx/y/set.txt
echo "      makin\n";
         $fLst = LstDir ("$Top/$y/$s", 'f');
         sort ($fLst);
         $idx = [];
         foreach ($fLst as $f) {
            $i = "$Top/$y/$s/$f";
            $sz = @getimagesize ($i);
            if ($sz === false)  continue;   // not a pic - skip
            list ($w, $h) = $sz;
            $ex = @exif_read_data ($i);
            $or = 1;
            if ($ex && isset ($ex ['Orientation']))
               $or = $ex ['Orientation'];
            if ($or >= 5)  list ($w, $h) = [$h, $w];  // rotate 90

            if ($w > $h)  $LP = 'L';   else $LP = 'P';
            $idx[] = "$LP|$f|";
         }
         Put ("$Idx/$y/$s.txt", implode ("\n", $idx) . "\n");
      }

      system ('rm -f '. escapeshellarg ("$Idx/$y/$s") . '/*');
      foreach (explode ("\n", Get ("$Idx/$y/$s.txt")) as $x) {
         if ($x == '')  continue;      // price of always \n term'd lines
                                       // from text editors
         list ($LP, $f) = explode ('|', $x);
         $i  = "$Top/$y/$s/$f";
         $o  = "$Idx/$y/$s/$f";
         $sc = ($LP == 'L') ? 320 : 480;
         $c  = 'ffmpeg -nostdin -y -i ' . escapeshellarg ($i) .
                   " -vf scale=-1:$sc " . escapeshellarg ($o);
         system ("$c 2>/dev/null");
      }
   }
}
