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
let _refreshTries = 0;

function parseFn (url) {          // https://shaz.app/song/song/d/f.mp3
   return url.substr (27);        // -> "d/f.mp3"
}

function parseName (fn) {
   let sl  = fn.indexOf  ('/');
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


function reTable ()
{
dbg("reTable");
  let tb = $('#info tbody');
   tb.empty ();
   for (let i = 0;  i < PL.length;  i++) {
     let a  = Nm [i].split ('\n');
     let td = '<b>' + a [2] + '</b> ' + a [0] +
             ' <b>' + a [3] + '</b> ' + a [1];
      tb.append ('<tr><td>' + td + '</td></tr>');
   }
   $('#info tbody tr').eq (0).css ('background-color', '#FFFF80');
   if (Nm.length > 0) {
     let a = Nm [0].split ('\n');
      document.title = a [2] + ' - ' + a [0];
   }
   $('#status').text (PL.length + ' songs left');
}


function loadQueue (items, curItemId)
{ let ci = 0;
   for (let i = 0;  i < items.length;  i++)
      if (items [i].itemId === curItemId)  {ci = i;  break;}
   if (! items [ci].media)  return;

  let curFn = parseFn (items [ci].media.contentId);
  let at    = PL.indexOf (curFn);
   if (at >= 0) {                      // in localStorage list - slice to it
      for (let i = 0; i < at; i++)
         $.get ('did.php', { did: PL [i] });
      PL = PL.slice (at);
      Nm = Nm.slice (at);
      reTable ();
   }
}


function refreshStatus ()
{ let ctx  = cast.framework.CastContext.getInstance ();
  let sess = ctx.getCurrentSession ();
  let ms   = sess && sess.getMediaSession ();
   if (! sess)  {_refreshTries = 0;   return;}

   if (! ms) {
      if (_refreshTries++ < 3)  setTimeout (refreshStatus, 2000);
      else                      _refreshTries = 0;
      return;
   }

   _refreshTries = 0;
   if      (ms.items && ms.items.length)
      loadQueue (ms.items, ms.currentItemId);
   else if (ms.media) {
     let fn = parseFn (ms.media.contentId);
     let at = PL.indexOf (fn);
      if (at >= 0) {
         for (let i = 0; i < at; i++)
            $.get ('did.php', { did: PL [i] });
         PL = PL.slice (at);
         Nm = Nm.slice (at);
      }
      else if (!PL.length) {
         PL = [fn];   Nm = [parseName (fn)];
      }
      reTable ();
   }
}


function lyr ()
{  if (!Nm.length)  return;

  let a = Nm [0].split ('\n');
   window.open (
      'https://google.com/search?q=lyrics "' + a [2] + '" "' + a [0] + '"',
      'lyrics');
}


window ['__onGCastApiAvailable'] = function (avail) {
dbg("cast init avail="+(avail?"y":"n"));
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
dbg("plCtl player_state_changed");
         if (event.value !== 'IDLE')  return;
dbg("   IDLE");
         if (! player.mediaInfo)      return;
dbg("   got mediaInfo");

         if (player.currentTime > 5) {
dbg("   curTime>5 so skip.php w "+player.mediaInfo.contentId);
            $.get ('skip.php', { it: parseFn (player.mediaInfo.contentId) });
         }
      }
   );

   plCtl.addEventListener (
      cast.framework.RemotePlayerEventType.MEDIA_INFO_CHANGED,
      function (event) {
dbg("plCtl media_info_changed");
         if (!player.mediaInfo)  return;
dbg("   got mediaInfo");

        let newFn = parseFn (player.mediaInfo.contentId);
dbg("   newFn="+newFn);
        let newTk = PL.indexOf (newFn);
dbg("   newTk="+newTk);
         if (newTk > 0) {              // songs before newTk have played
            for (let i = 0; i < newTk; i++) {
dbg("      did.php fn="+PL[i]);
               $.get ('did.php', { did: PL [i] });
            }
            PL = PL.slice (newTk);
            Nm = Nm.slice (newTk);
            reTable ();
         }
         else if (newTk < 0) {         // unknown song - resync from cast
dbg("      newTk=0 so refreshStatus");
            refreshStatus ();
         }
      }
   );

   ctx.addEventListener (
      cast.framework.CastContextEventType.SESSION_STATE_CHANGED,
      function (event) {
dbg("ctx session_state_changed");
        let SS = cast.framework.SessionState;
        let s  = event.sessionState;
dbg(SS);
dbg(s);
         if (s === SS.SESSION_RESUMED || s === SS.SESSION_STARTED) {
dbg("   session_resumed session_started");
            _refreshTries = 0;  refreshStatus ();
         }
         else if (s === SS.SESSION_ENDED) {
dbg("   session_ended");
            _refreshTries = 0;
         }
      }
   );

   if (ctx.getCurrentSession ())  refreshStatus ();
   setTimeout (refreshStatus,  4000);
   setTimeout (refreshStatus, 10000);
};


$(function () {
   init ();
   $('#lyr').click (lyr);
  let stored = localStorage.getItem ('castPL');
   if (stored) {
     let d = JSON.parse (stored);
      PL = d.pl;
      Nm = d.nm;
      reTable ();
   }
   setInterval (refreshStatus, 60000);
});
</script>
<script src=
   "https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1">
</script>

<? pg_body ([[$UC['home']." home", "..", "...take me back hooome"]]); ?>
<a id="lyr" style="margin-left: 6em">lyric</a><span id="status"></span>
<google-cast-launcher></google-cast-launcher>

<table id="info" name="info">
 <thead><tr><th></th></tr></thead>
 <tbody></tbody>
</table>
<? pg_foot ();
