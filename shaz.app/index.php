<? # index.php - home

require_once ("_inc/app.php");

   pg_head ("shaz.app", "jqui app",  "jqui jquery.jrumble app", "shaz.app");
?>
 <script>
   $(function () {
      init ();
      jRum ('a', 10,  0,  0);
      jRum ('b',  0, 10,  0);
      jRum ('c',  0,  0, 10);
      jRum ('d',  5,  0, 10);
      jRum ('e',  0,  5, 10);
   });
 </script>
<? pg_body ([]); ?>
<h1><img src="img/logo.png" style="height:3em"> shaz.app</h1>
<br>

<a id='a' pop class='nav-1' href='https://pianocheetah.app'
 >pianocheetah</a>let's play some pianooo<br>

<a id='b'     class='nav-2' href='shazware'
 >shazware</a>some cool file utilities<br>

<a id='c'     class='nav-3' href='linux'
 >linux</a>...you should try it :)<br>

<br>

<a     class='nav-4' href="me"  >meee</a>way too much bout Steve<br>
<a     class='nav-5' href="pic" >pics</a>allll my pics<br>
<a     class='nav-6' href="song">songs</a>tunes i dig  (and Annie)"<br>
<a pop class='nav-7' href=https://www.youtube.com/@SteveHazel/videos"
                                >videos</a>i took<br>
<a pop class='nav-8' href="https://www.facebook.com/stephen.hazel"
                                >facebook</a>is dumb !!<br>
<br>
<img src="img/wilee.jpg">
<? pg_foot ();
