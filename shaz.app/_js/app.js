// app.js
// less typin'  (sorry not sorry) :/
let dbg = console.log.bind (console);
function el  (s)  {return document.getElementById   (s);}
function tag (s)  {return document.getElementsByTagName (s)[0];}
function all (s)  {return document.querySelectorAll (s);}
function hasAt (e,a)  {return e.hasAttribute (a);}
function setAt (e,a,v)       {e.setAttribute (a, v);}
function clrAt (e,a)      {e.removeAttribute (a);}

function navOpen ()  {$('#nav-open').hide ();   $('nav').show ();
                      $('#nav-shut').show ();}
function navShut ()  {$('#nav-open').show ();   $('nav').hide ();
                      $('#nav-shut').hide ();}

let pMobl = 99  ;                      // force navUpd to kick on init

function mobl ()  {return pMobl;}

function navUpd ()
{ let m = ($(window).width () < 700);
   if (pMobl != m) {
      $('body').removeClass (    'mobl     dtop');
      $('body'   ).addClass (m ? 'mobl' : 'dtop');

      if ($('nav li').length <= 1) {   // only a ^home so SUUUper simple
         $('nav').css ({'position': 'static',
                        'display':  'inline'});
         $('nav ul'   ).css ('display', 'inline');
         $('nav ul li').css ('display', 'inline');
      }
      else {
         $('nav').css ({'position': 'fixed',
                        'display':  'block'});
         $('nav ul'   ).css ('display', 'flex');
         $('nav ul li').css ('display', 'flex');
         if (m)  navShut ();
         else   {navOpen ();   $('#nav-shut').hide ();}
      }
      pMobl = m;
   }
}

function navInit ()
{  navUpd ();   $(window).resize (navUpd);
   $('#nav-open').button ().click (navOpen);
   $('#nav-shut'          ).click (navShut);
}

function jRum (id, ix, iy, irot)       // sorry i like ittt
{  $('#'+id).jrumble ({x: ix, y: iy, rotation: irot});
   $('#'+id).hover (function () { $(this).trigger ('startRumble'); },
                    function () { $(this).trigger ('stopRumble' ); }
   );
}

function aPop ()  // any a with pop attribute gets target='_blank'
{  $("a").each (function (ind, ele) {
      if ($(this).attr ('pop') !== undefined)
         $(this).attr ('target', '_blank');
   });
}

function aBtn ()  // all a with btn attribute become jqui buttons
{  $("a").each (function (ind, ele) {
      if ($(this).attr ('btn') !== undefined)
         $(this).button ();
   });
}

function init ()  // for subpages
{  aPop ();
   $("a").button ();                   // all a become buttons
   $("[tip]").addClass ('tip');        // git all tags w tip= attrib class=tip
   navInit ();
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
