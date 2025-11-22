// note - the CP33 has activesense and clock messages.
// i'm pretty sure they hose up WebMidi cuz it sends junk in midi input.

function dbg (o)  {console.log (o);}

var MIDI;


if (navigator.requestMIDIAccess)
   { dbg ("can do midi :)");
     navigator.requestMIDIAccess ().then (mOk, mFail); }
else dbg ("can't do midi :(");

function mFail ()  {dbg ("midi failed :(");}

function mOk (midi)
{  dbg ("midi ok :)");
   MIDI = midi;

   dbg ("input devices:");
  var iLs = midi.inputs.values ();
   for (var i = iLs.next ();  i && ! i.done;  i = iLs.next ()) {
     var v = i.value;
      dbg (' ' + v.name + ' (' + v.manufacturer + ',' +
                                 v.state + ',' + v.connection + ')');
      if (v.name.indexOf ('MIDISPORT') < 0)
            v.onmidimessage = onMidi;
      else  dbg (' ^skippin cuz my MIDISPORT/cp33 kills chrome WebMidi');
   }

   dbg ("output devices:");
  var oLs = midi.outputs.values ();
   for (var o = oLs.next ();  o && ! o.done;  o = oLs.next ()) {
     var v = o.value;
      dbg (' ' + v.name + ' (' + v.manufacturer + ',' +
                                 v.state + ',' + v.connection + ')');
   }
   testOut ("MiniNova: Port 1");
}


function testOut (dv)
{ var oLs = MIDI.outputs.values ();
   for (var o = oLs.next ();  o && ! o.done;  o = oLs.next ()) {
     var v = o.value;
      if (v.name == dv) {
        var nt = [0x90, (4+1)*12, 0x7F];
         v.send (nt);   nt [0] = 0x80;      // noteon=>noteoff
         v.send (nt, window.performance.now () + 1000.0);
      }
   }
}


var NtS = ['c','c#','d','d#','e','f','f#','g','g#','a','a#','b'];

function no (n)  {return n.toString ();}

function nt (b)  {return no (Math.floor (b / 12) - 1) + NtS [b % 12];}

function cc (b)
{  switch (b) {                        // name the usual suspects
      case  0:  return 'Bank';         case 32:  return 'BankL';
      case  1:  return 'Mod';          case  2:  return 'Brth';
      case  4:  return 'Pedl';         case  7:  return 'Vol';
      case 11:  return 'Expr';         case 10:  return 'Pan';
      case  8:  return 'Bal';          case 64:  return 'Hold';
      case 69:  return 'Hld2';         case 67:  return 'Soft';
      case 66:  return 'Sust';         case 68:  return 'Lega';
      case 91:  return 'Rvrb';         case 93:  return 'Chor';
      case 120: return '*SndX';        case 121: return '*CtlX';
      case 123: return '*NtX';         case 122: return 'Locl';
      case 98:  return 'NRPL';         case 99:  return 'NRPH';
      case 100: return 'RPL';          case 101: return 'RPH';
      case 38:  return 'DatL';         case  6:  return 'DatH';
   }
   return 'cc' + no (b);               // else give up with cc#
}


function onMidi (m)
{ var d = m.data;
// ignore active sense, clock, junkk !  (and anthing<0x80: wtf)
   if ((d [0] >= 0x00F0) || (d [0] < 0x0080))  return;
//dbg("d[0]=" + no (d [0]) + " d[1]=" + no (d [1]));
  var cm = d [0] & 0x00F0,  ch = d [0] & 0x000F,  s;
   if (cm < 0x00B0) {
      switch (cm) {                    // note.  else fall thru to note off
         case 0x90: if (d [2] > 0)  {s = '_';   break;}
         case 0x80: s = '^';   break;
         default:   s = '~';
      }
      s = nt (d [1]) + s + no (d [2]);
   }
   else
      switch (cm) {
         case 0xB0: s = cc (d [1]) + '=' + no (d [2]);   break;
         case 0xC0: s = 'Prog=' +          no (d [1]);   break;
         case 0xD0: s = 'Prss=' +          no (d [1]);   break;
         default:   s = 'PBnd=' +          no (d [2]);
      }
// eh, ignore m.timeStamp
  var ul = document.querySelector ('#midi-data ul'),
      li = document.createElement ('li');
   li.appendChild (document.createTextNode (
                                       m.srcElement.name + '.' + ch + ' ' + s));
   ul.appendChild (li);
}
