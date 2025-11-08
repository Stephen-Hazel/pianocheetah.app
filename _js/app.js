// app.js - jquery init, jrumble, pop attribute, etc

let dbg = console.log.bind (console);  // don't make me type thatt

function menuOpen ()
{
}

function aPop ()  // any a with pop attribute gets target='_blank'
{  $("a").each (function (ind, ele) {
      if ($(this).attr ('pop') !== undefined)
         $(this).attr ('target', '_blank');
   });
}

function aBtn ()  // all a with btn attribute become jqui buttons
{  $("a").each (function (ind, ele) {
      if ($(this).attr ('btn') !== undefined)  $(this).button ();
   });
}

function home ()  // home page init
{  aPop ();   aBtn ();
   $('nav ul a').button ();
//   $('#menubtn').button ({event: "click hoverintent"});
   jRum ('logo', 10, 10, 4);
   jRum ('free',  2,  0, 0);
   jRum ('me',    0,  2, 0);
   jRum ('feel',  5,  5, 3);
}

function init ()  // for subpages
{  aPop ();
   $("a").button ();                   // all a become buttons
}


function jRum (id, ix, iy, irot)
{  jQuery('#'+id).jrumble ({x: ix, y: iy, rotation: irot});
   jQuery('#'+id).hover (
      function () { jQuery(this).trigger ('startRumble'); },
      function () { jQuery(this).trigger ('stopRumble' ); }
   );
}

$.event.special.hoverintent = {
   setup:    function () {
      $(this).bind   ("mouseover", jQuery.event.special.hoverintent.handler);
   },
   teardown: function () {
      $(this).unbind ("mouseover", jQuery.event.special.hoverintent.handler);
   },
   handler: function (event) {
     let currentX, currentY, timeout,
         args = arguments,
         target = $(event.target),
         previousX = event.pageX,
         previousY = event.pageY;
      function track (event) {
         currentX = event.pageX;
         currentY = event.pageY;
      };
      function clear () {
         target.unbind ("mousemove", track).unbind ("mouseout", clear);
         clearTimeout (timeout);
      }
      function handler () {
        let prop,
            orig = event;
         if ((Math.abs (previousX - currentX) +
              Math.abs (previousY - currentY)) < 7) {
            clear ();
            event = $.Event ("hoverintent");
            for (prop in orig)
               if (! (prop in event))  event [prop] = orig [prop];
         // Prevent accessing the original event since the new event
         // is fired asynchronously and the old event is no longer
         // usable (#6028)
            delete event.originalEvent;
            target.trigger (event);
         }
         else {
            previousX = currentX;
            previousY = currentY;
            timeout = setTimeout (handler, 100);
         }
      }
      timeout = setTimeout (handler, 100);
      target.bind ({ mousemove: track, mouseout: clear });
   }
};
