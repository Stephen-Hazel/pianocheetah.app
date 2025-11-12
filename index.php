<? # pianocheetah.app home page :)

require_once "_inc/app.php";

  global $UC;
   pg_head ("pianocheetah", "jqui app", "jqui jquery.jrumble app", 'home');
?>
 <script>
   $(function () {home ();});
 </script>
<? pg_body ([
      ["get it",   "doc/?pg=1", "how do i get pianocheetah ?"],
      ["docs",     "doc",       "what does pianocheetah even do ?"],
      ["piano",    "piano",     "what kinda piano should I get ?"],
      ["practice", "practice",  "goin about piano practice"],
      ["midi",     "midi",      "what the heck is MIDI ???"],
      ["linux",    "linux",     "stuff about linux"],
      ["meee",     "https://shaz.app/me", "bout Steve"]
   ]); ?>
<span id='logotxt'><center><span
 class='c0'>p</span><span class='c1'>i</span><span class='c2'>a</span><span
 class='c3'>n</span><span class='c4'>o</span><span class='c5'>c</span><span
 class='c6'>h</span><span class='c7'>e</span><span class='c8'>e</span><span
 class='c9'>t</span><span class='ca'>a</span><span class='cb'>h</span>
</center></span>
<a href="doc"><img id='logo' src="img/logo.png"></a>
<div id='blurb'><br>
 <h1>a linux app for playing pop songs on piano n drum.</h1>
 It's made for Steve.<br><br>

 It makes any song easy to play :)<br>
 Play it with your &nbsp;<b><i class='c9' id='feel'>feel</i></b> &nbsp;
 and way less notes.<br><br>
 Look at the pretty notes in my <a btn href="doc">docs</a> !<br><br>
 Oh, and it's <a href="misc/?pg=0"><img id="free" src="img/free.png"></a>
</div>
</main>

<div id='foot'>
 <div id='lft'>
  <a pop href="https://shaz.app/me">
   <img id="me" src="img/bot_me.png" title='...meee'></a>
 </div>
 <div id="mid">
  <h3>Happy to help!</h3>
  email me:
  <a btn href="mailto:sh@shaz.app?subject=pianocheetah">sh@shaz.app</a><br>
  facebook:
  <a btn pop href="https://facebook.com/PianoCheetah">blog</a>
 </div>
 <div id='rit'>
  <h3>...meh</h3>
  <a btn href="misc/?pg=1">privacy policy</a><br>
  <a btn href="misc/?pg=0">EULA</a><br>
  <a btn href="misc/?pg=2">uninstalling</a>
 </div>
</div>
<h4> Meet shorty :)  Think I can play Clair de Lune on this baby?</h4>
<img src="img/shorty.jpg">
</body></html>
