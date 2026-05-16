<? # song/cast.php - monitor current cast session playlist

require_once ("../_inc/app.php");

pg_head ("cast", "jqui app", "jqui app");
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
let PL = [];   // filenames "dir/song.mp3", that index.php gave us
let Nm = [];   // "group\nextra\ntitle\ndir" for each PL entry
let Tk = 0;
let Tries = 0;

// https://shaz.app/song/song/d/f.mp3 => d/f.mp3
function unroot (url)  {return url.substr (27);}

function fn2nm (fn)
{ let sl  = fn.indexOf  ('/');
  let dir = fn.substring (0, sl);
  let s   = fn.substring (sl + 1, fn.length - 4);
   s = s.replace (/_/g, ' ');
  let f   = s.indexOf     ('-');
  let l   = s.lastIndexOf ('-');
   if (f !== -1) {
     let g = s.substring (0, f);
     let t = s.substring (l + 1);
     let x = (f === l) ? '' : s.substring (f + 1, l);
      return g + '\n' + x + '\n' + t + '\n' + dir;
   }
   return '??\n\n' + s + '\n' + dir;
}


function newTk ()
{  $('#info tbody tr').css         ('background-color', '');
   $('#info tbody tr').eq (Tk).css ('background-color', '#FFFF80');
   if (Tk < Nm.length) {
     let a = Nm [Tk].split ('\n');
      document.title = a [2] + ' - ' + a [0];
   }
}


function reCheck ()
// see if we're onna new song (other than Tk)
{
dbgx("reCheck");
  let ctx  = cast.framework.CastContext.getInstance ();
  let sess = ctx.getCurrentSession ();
  let ms   = sess && sess.getMediaSession ();
   if (! sess)  {
dbgx("no sess");
      Tries = 0;   return;}

   if (! ms) {
dbgx(" no mediaSess tries="+Tries);
      if (Tries++ < 3)  setTimeout (reCheck, 2000);
      else               Tries = 0;
      return;
   }

   Tries = 0;
dbgx("Tries:= 0");
   if (ms.media) {
dbgx("got ms.media");
     let fn = unroot (ms.media.contentId);
     let at = PL.indexOf (fn);
dbgx("fn="+fn+" at="+at);
      if (at != Tr) {
         for (let i = Tk;  i < at;  i++)  $.get ('did.php', { did: PL [i] });
         Tk = at;
      }
      newTk ();
   }
}


function lyr ()
{  if (Tk >= Nm.length)  return;

  let a = Nm [Tk].split ('\n');
   window.open (
      'https://google.com/search?q=lyrics "' + a [2] + '" "' + a [0] + '"',
      'lyrics');
}


window ['__onGCastApiAvailable'] = function (avail) {
dbgx("cast init avail="+(avail?"y":"n"));
   if (! avail)  return;

  let ctx = cast.framework.CastContext.getInstance ();
   ctx.setOptions ({
      receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
      autoJoinPolicy:        chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
   });
  let player = new cast.framework.RemotePlayer ();
  let plCtl  = new cast.framework.RemotePlayerController (player);
   plCtl.addEventListener (
      cast.framework.RemotePlayerEventType.PLAYER_STATE_CHANGED,
      function (event) {
dbgx("plCtl player_state_changed");
         if (event.value !== 'IDLE')  return;
dbgx("   IDLE");
         if (! player.mediaInfo)      return;
dbgx("   got mediaInfo");

         if (player.currentTime > 5) {
dbgx("   curTime>5 so skip.php w "+player.mediaInfo.contentId);
            $.get ('skip.php', { it: unroot (player.mediaInfo.contentId) });
         }
      }
   );

   plCtl.addEventListener (
      cast.framework.RemotePlayerEventType.MEDIA_INFO_CHANGED,
      function (event) {
dbgx("plCtl media_info_changed");
         if (!player.mediaInfo)  return;
dbgx("   got mediaInfo");

        let newFn = unroot (player.mediaInfo.contentId);
dbgx("   newFn="+newFn);
        let newTk = PL.indexOf (newFn);
dbgx("   newTk="+newTk);
         if (newTk > Tk) {
            for (let i = Tk;  i < newTk;  i++) {
dbgx("   did.php fn="+PL[i]);
               $.get ('did.php', { did: PL [i] });
            }
            Tk = newTk;
            newTk ();
         }
         else if (newTk < 0) {         // unknown song - resync from cast
dbgx("   newTk<0 so reCheck");
            reCheck ();
         }
      }
   );

   ctx.addEventListener (
      cast.framework.CastContextEventType.SESSION_STATE_CHANGED,
      function (event) {
dbgx("ctx session_state_changed");
        let SS = cast.framework.SessionState;
        let s  = event.sessionState;
dbgx(s);
         if      (s === SS.SESSION_RESUMED || s === SS.SESSION_STARTED) {
dbgx("   resumed|started");
            Tries = 0;  reCheck ();
         }
         else if (s === SS.SESSION_ENDED) {
dbgx("   ended");
            Tries = 0;
         }
      }
   );

   if (ctx.getCurrentSession ())  reCheck ();
   setTimeout (reCheck,  4000);
   setTimeout (reCheck, 10000);
};


$(function () {
dbgx("cast.php ready");
   init ();
   $('#lyr').click (lyr);
  let stored = localStorage.getItem ('castPL');
   if (stored) {
     let d = JSON.parse (stored);
      PL = d.pl;
      Nm = d.nm;
dbg("PL");dbg(PL);
     let tb = $('#info tbody');
      tb.empty ();
      for (let i = 0;  i < PL.length;  i++) {
        let a  = Nm [i].split ('\n');
        let td = '<b>' + a [2] + '</b> ' + a [0] +
                ' <b>' + a [3] + '</b> ' + a [1];
         tb.append ('<tr><td>' + td + '</td></tr>');
      }
      $('#info tbody tr').eq (Tk).css ('background-color', '#FFFF80');
      if (Tk < Nm.length) {
        let a = Nm [Tk].split ('\n');
         document.title = a [2] + ' - ' + a [0];
      }
   }
   setInterval (reCheck, 60000);
});
</script>
<script src=
   "https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1">
</script>

<? pg_body ([[$UC['home']." home", "..", "...take me back hooome"]]); ?>
<span style="padding-left: 5em"></span>
<a id="lyr">lyric</a><google-cast-launcher></google-cast-launcher>
<table id="info" name="info"><tbody></tbody></table>
<? pg_foot ();
