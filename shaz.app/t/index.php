<? # t/index.php - test pickin n listin files

require_once ("../_inc/app.php");

   $PG = "song";
   pg_head ("jqui app", "jqui app");
?>
 <script>
async function listFiles ()
{  try {
     const dirH = await window.showDirectoryPicker ()
     const fl = []
      for await (const e of dirH.values ()) { fl.push (e.name) }
      return fl.sort ();
   }
   catch (error) {
dbg("error in listFiles: " + error)
      return []
   }
}

function test_list ()
{  listFiles ().then (fl => {
dbg("Files in directory: " + fl)
   })
}


//______________________________________________________________________________
$(function () {
   navInit ()
   $('a').button ();

   $('#tf').button ().click (test_list);
})
 </script>
<? pg_body (); ?>

<a id='tf'>test listfiles</a>

<? pg_foot ();
