<?php
// Include WordPress
define('WP_USE_THEMES', false);
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/wp-load.php";
get_header();
?>

<link rel="stylesheet" href="/css/c4tutorial.css" type="text/css" />
<link rel="stylesheet" href="/css/pygments.css" type="text/css" />


<script type="text/javascript">
/*<![CDATA[*/
var asciidoc = {  // Namespace.

/////////////////////////////////////////////////////////////////////
// Table Of Contents generator
/////////////////////////////////////////////////////////////////////

/* Author: Mihai Bazon, September 2002
 * http://students.infoiasi.ro/~mishoo
 *
 * Table Of Content generator
 * Version: 0.4
 *
 * Feel free to use this script under the terms of the GNU General Public
 * License, as long as you do not remove or alter this notice.
 */

 /* modified by Troy D. Hanson, September 2006. License: GPL */
 /* modified by Stuart Rackham, 2006, 2009. License: GPL */

// toclevels = 1..4.
toc: function (toclevels) {

  function getText(el) {
    var text = "";
    for (var i = el.firstChild; i != null; i = i.nextSibling) {
      if (i.nodeType == 3 /* Node.TEXT_NODE */) // IE doesn't speak constants.
        text += i.data;
      else if (i.firstChild != null)
        text += getText(i);
    }
    return text;
  }

  function TocEntry(el, text, toclevel) {
    this.element = el;
    this.text = text;
    this.toclevel = toclevel;
  }

  function tocEntries(el, toclevels) {
    var result = new Array;
    var re = new RegExp('[hH]([1-'+(toclevels+1)+'])');
    // Function that scans the DOM tree for header elements (the DOM2
    // nodeIterator API would be a better technique but not supported by all
    // browsers).
    var iterate = function (el) {
      for (var i = el.firstChild; i != null; i = i.nextSibling) {
        if (i.nodeType == 1 /* Node.ELEMENT_NODE */) {
          var mo = re.exec(i.tagName);
          if (mo && (i.getAttribute("class") || i.getAttribute("className")) != "float") {
            result[result.length] = new TocEntry(i, getText(i), mo[1]-1);
          }
          iterate(i);
        }
      }
    }
    iterate(el);
    return result;
  }

  var toc = document.getElementById("toc");
  if (!toc) {
    return;
  }

  // Delete existing TOC entries in case we're reloading the TOC.
  var tocEntriesToRemove = [];
  var i;
  for (i = 0; i < toc.childNodes.length; i++) {
    var entry = toc.childNodes[i];
    if (entry.nodeName.toLowerCase() == 'div'
     && entry.getAttribute("class")
     && entry.getAttribute("class").match(/^toclevel/))
      tocEntriesToRemove.push(entry);
  }
  for (i = 0; i < tocEntriesToRemove.length; i++) {
    toc.removeChild(tocEntriesToRemove[i]);
  }

  // Rebuild TOC entries.
  var entries = tocEntries(document.getElementById("content"), toclevels);
  for (var i = 0; i < entries.length; ++i) {
    var entry = entries[i];
    if (entry.element.id == "")
      entry.element.id = "_toc_" + i;
    var a = document.createElement("a");
    a.href = "#" + entry.element.id;
    a.appendChild(document.createTextNode(entry.text));
    var div = document.createElement("div");
    div.appendChild(a);
    div.className = "toclevel" + entry.toclevel;
    toc.appendChild(div);
  }
  if (entries.length == 0)
    toc.parentNode.removeChild(toc);
},


/////////////////////////////////////////////////////////////////////
// Footnotes generator
/////////////////////////////////////////////////////////////////////

/* Based on footnote generation code from:
 * http://www.brandspankingnew.net/archive/2005/07/format_footnote.html
 */

footnotes: function () {
  // Delete existing footnote entries in case we're reloading the footnodes.
  var i;
  var noteholder = document.getElementById("footnotes");
  if (!noteholder) {
    return;
  }
  var entriesToRemove = [];
  for (i = 0; i < noteholder.childNodes.length; i++) {
    var entry = noteholder.childNodes[i];
    if (entry.nodeName.toLowerCase() == 'div' && entry.getAttribute("class") == "footnote")
      entriesToRemove.push(entry);
  }
  for (i = 0; i < entriesToRemove.length; i++) {
    noteholder.removeChild(entriesToRemove[i]);
  }

  // Rebuild footnote entries.
  var cont = document.getElementById("content");
  var spans = cont.getElementsByTagName("span");
  var refs = {};
  var n = 0;
  for (i=0; i<spans.length; i++) {
    if (spans[i].className == "footnote") {
      n++;
      var note = spans[i].getAttribute("data-note");
      if (!note) {
        // Use [\s\S] in place of . so multi-line matches work.
        // Because JavaScript has no s (dotall) regex flag.
        note = spans[i].innerHTML.match(/\s*\[([\s\S]*)]\s*/)[1];
        spans[i].innerHTML =
          "[<a id='_footnoteref_" + n + "' href='#_footnote_" + n +
          "' title='View footnote' class='footnote'>" + n + "</a>]";
        spans[i].setAttribute("data-note", note);
      }
      noteholder.innerHTML +=
        "<div class='footnote' id='_footnote_" + n + "'>" +
        "<a href='#_footnoteref_" + n + "' title='Return to text'>" +
        n + "</a>. " + note + "</div>";
      var id =spans[i].getAttribute("id");
      if (id != null) refs["#"+id] = n;
    }
  }
  if (n == 0)
    noteholder.parentNode.removeChild(noteholder);
  else {
    // Process footnoterefs.
    for (i=0; i<spans.length; i++) {
      if (spans[i].className == "footnoteref") {
        var href = spans[i].getElementsByTagName("a")[0].getAttribute("href");
        href = href.match(/#.*/)[0];  // Because IE return full URL.
        n = refs[href];
        spans[i].innerHTML =
          "[<a href='#_footnote_" + n +
          "' title='View footnote' class='footnote'>" + n + "</a>]";
      }
    }
  }
},

install: function(toclevels) {
  var timerId;

  function reinstall() {
    asciidoc.footnotes();
    if (toclevels) {
      asciidoc.toc(toclevels);
    }
  }

  function reinstallAndRemoveTimer() {
    clearInterval(timerId);
    reinstall();
  }

  timerId = setInterval(reinstall, 500);
  if (document.addEventListener)
    document.addEventListener("DOMContentLoaded", reinstallAndRemoveTimer, false);
  else
    window.onload = reinstallAndRemoveTimer;
}

}
asciidoc.install(2);
/*]]>*/
</script>
</head>
<div class="row">
<div id="header" class="span8">

<h2>Advanced Panning</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5423345" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684733" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;ve seen how to <a href="/examples/interactionPanning.php">override the move:</a> method, now lets dig into one of the unique characteristics of the <tt>PAN</tt> gesture: translation.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_intro">1. Intro</h2>
<div class="sectionbody">
<div class="paragraph"><p>Each gesture – <tt>PAN</tt>, <tt>TAP</tt>, <tt>SWIPE</tt> – has a set of methods that will return information that&#8217;s important for its type. In the case of panning, you&#8217;ll generally want to know how far it has moved and sometimes even how quick the move has been. In this tutorial we&#8217;re going to access the <tt>translation</tt> of the gesture to affect the properties of a shape.</p></div>
<div class="sect2">
<h3 id="_a_simple_setup">1.1. A Simple Setup</h3>
<div class="paragraph"><p>We&#8217;re going to add a shape to the center of the canvas. After that, we&#8217;re going to attach a gesture tot he canvas and have the qualities of the gesture affect the look of the shape.</p></div>
<div class="paragraph"><p>Add the following setup to your projects:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">circle</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">386</span><span class="p">,</span> <span class="mi">386</span><span class="p">)];</span>
    <span class="n">circle</span><span class="py">.userInteractionEnabled</span> <span class="o">=</span> <span class="nb">NO</span><span class="p">;</span>
    <span class="n">circle</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:circle</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:PAN</span> <span class="n">name:</span><span class="s">@&quot;pan&quot;</span> <span class="n">action:</span><span class="s">@&quot;modifyLineWidth:&quot;</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_how_much_drag">2. How Much Drag?</h2>
<div class="sectionbody">
<div class="paragraph"><p>The <tt>PAN</tt> gesture looks for dragging movements, the user must be pressing one or more fingers on a visual object (e.g. C4Control) while they pan it.</p></div>
<div class="paragraph"><p>A panning gesture is continuous. It begins (<tt>UIGestureRecognizerStateBegan</tt>) when the minimum number of fingers allowed (<tt>minimumNumberOfTouches</tt>) has moved enough to be considered a pan. It changes (<tt>UIGestureRecognizerStateChanged</tt>) when a finger moves while at least the minimum number of fingers are pressed down. It ends (<tt>UIGestureRecognizerStateEnded</tt>) when all fingers are lifted.</p></div>
<div class="sect2">
<h3 id="_translationinview">2.1. translationInView</h3>
<div class="paragraph"><p>When you want to know how far a drag gesture has moved you need to look at its <tt>translationInView</tt>.</p></div>
<div class="paragraph"><p>You can get the translation value like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">translation</span> <span class="o">=</span> <span class="p">[</span><span class="n">recognizer</span> <span class="n">translationInView:view</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>The value you&#8217;re going to get back is the total difference between the current position and the initial position. Basically, this means that you can start a pan gesture and drag around the screen and whenever you get back to the original position of the gesture the <tt>translationInView</tt> will end up being {0,0}.</p></div>
</div>
<div class="sect2">
<h3 id="_modifylinewidth">2.2. modifyLineWidth</h3>
<div class="paragraph"><p>Now that we know how grab the translation, let&#8217;s use it for something. Add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">modifyLineWidth:</span><span class="p">(</span><span class="nc">UIPanGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="n">recognizer</span> <span class="p">{</span>
    <span class="nc">CGPoint</span> <span class="n">translation</span> <span class="o">=</span> <span class="p">[</span><span class="n">recognizer</span> <span class="n">translationInView:</span><span class="k">self</span><span class="py">.canvas</span><span class="p">];</span>

    <span class="nc">CGFloat</span> <span class="n">lineWidth</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">absf:translation</span><span class="py">.x</span><span class="p">]</span> <span class="o">+</span> <span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">absf:translation</span><span class="py">.y</span><span class="p">];</span>
    <span class="n">circle</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">constrainf:lineWidth</span> <span class="n">min:</span><span class="mi">5</span> <span class="n">max:</span><span class="mi">150</span><span class="p">];</span>

    <span class="p">[</span><span class="n">recognizer</span> <span class="n">setTranslation:</span><span class="nc">CGPointZero</span> <span class="n">inView:</span><span class="k">self</span><span class="py">.canvas</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This grabs the current value of the translation then takes the value of both its <tt>x</tt> and <tt>y</tt> positions to calculate a new value for the <tt>lineWidth</tt> of the circle. It constrains the values of the <tt>lineWidth</tt> to anything between 5 and 150 points.</p></div>
<div class="paragraph"><p>Then, there&#8217;s this <tt>setTranslation:inView:</tt> method. This method allows you to reset the value of the current translation. If you want to keep track only of the movement between calls to <tt>translationInView:</tt> then you can set the value of the translation to <tt>CGPointZero</tt>.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">It&#8217;s possible to always calculate the current translation against the original point but <em>if you don&#8217;t need the translation for anything else</em> I find it&#8217;s easier to just set it to zero rather than keeping track and comparing things.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">3. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This was a short but sweet little tutorial. One of the most useful tricks when working with gestures is being able to get the defining values out of each one. Have a look at the other interaction tutorials to see different ways of working with gestures.</p></div>
<div class="paragraph"><p>Tchussi.</p></div>
</div>
</div>
  </div>
  <div class="span3">
  <div id="toc">
    <div id="toctitle">Table of Contents</div>
    <noscript><p><b>JavaScript must be enabled in your browser to display the table of contents.</b></p></noscript>
  </div>
</div>
</div>
</div>

<?php get_footer(); ?>
