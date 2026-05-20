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
let Player, PlCtl;

// https://shaz.app/song/song/d/f.mp3 => d/f.mp3
function unroot (url)  {return url.substr (27);}


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
   if (Player.mediaInfo) {
     let fn = unroot (Player.mediaInfo.contentId);
     let at = PL.indexOf (fn);
dbgx("   fn="+fn+" at="+at);
      if (at > Tk) {
         for (let i = Tk;  i < at;  i++) {
dbgx("   did "+PL [i]);
            $.get ('did.php', { did: PL [i] });
         }
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
dbgx("cast state after setOptions: "+ctx.getCastState());
   ctx.addEventListener (
      cast.framework.CastContextEventType.SESSION_STATE_CHANGED,
      function (event) {
dbgx("session_state_changed: "+event.sessionState);
      }
   );
  Player = new cast.framework.RemotePlayer ();
  PlCtl  = new cast.framework.RemotePlayerController (Player);

   PlCtl.addEventListener (
      cast.framework.RemotePlayerEventType.PLAYER_STATE_CHANGED,
      function (event) {
dbgx("PlCtl player_state_changed"); dbgx(event.value);
         if (event.value !== 'IDLE')  return;
dbgx("   IDLE");
         if (! Player.mediaInfo)      return;
dbgx("   got mediaInfo");

         if (Player.currentTime > 5) {
           let fn = unroot (Player.mediaInfo.contentId);
dbgx("   curTime>5 so skip.php w "+fn);
            $.get ('skip.php', { it: fn });
         }
      }
   );
   PlCtl.addEventListener (
      cast.framework.RemotePlayerEventType.MEDIA_INFO_CHANGED,
      function (event) {
dbgx("PlCtl media_info_changed"); dbgx(event.value);
         if (! Player.mediaInfo)  return;
dbgx("   got mediaInfo");

        let newFn = unroot (Player.mediaInfo.contentId);
dbgx("   newFn="+newFn);
        let nxTk = PL.indexOf (newFn);
dbgx("   nxTk="+nxTk);
         if (nxTk > Tk) {
            for (let i = Tk;  i < nxTk;  i++) {
dbgx("   did.php fn="+PL[i]);
               $.get ('did.php', { did: PL [i] });
            }
            Tk = nxTk;
            newTk ();
         }
      }
   );
};


$(function (){
dbgx("cast.php page ready");
   init ();
   $('#lyr').click (lyr);
  let stored = localStorage.getItem ('castPL');
   if (stored) {
     let d = JSON.parse (stored);
      PL = d.pl;
      Nm = d.nm;
dbgx("PL count="+PL.length);
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
dbgx("starting setInterval");
   setInterval (reCheck, 15000);

   document.addEventListener ('visibilitychange', function () {
      if (document.visibilityState !== 'visible')  return;
      if (! Player)  return;

dbgx("visibilitychange");
     let ctx  = cast.framework.CastContext.getInstance ();
     let sess = ctx.getCurrentSession ();
      if (! sess)  return;

     let ms = sess.getMediaSession ();
      if (! ms || ! ms.media)  return;

     let fn = unroot (ms.media.contentId);
     let at = PL.indexOf (fn);
dbgx("   fn="+fn+" at="+at);
      if (at > Tk) {
         for (let i = Tk;  i < at;  i++)
            $.get ('did.php', { did: PL [i] });
         Tk = at;
         newTk ();
      }
   });
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
