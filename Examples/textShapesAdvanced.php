<?php
// Include WordPress
define('WP_USE_THEMES', false);
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/new/wp-load.php";
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

<h2>C4Shape: Text Shapes</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3183355" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>You can create shapes that look like text by using a font and a string. This example combines creating a text shape and animating a line dash pattern around its edge, like <a href="lineDashPhase2.php">this example</a>.</p></div>
<div class="imageblock">
<div class="content">
<img src="textShapesAdvanced/textShapesAdvanced.png" alt="Text Shapes Advanced" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_shapes">1. The Shapes</h2>
<div class="sectionbody">
<div class="paragraph"><p>The text shape is contained within a square, so we first set up this shape with its own dash pattern and style. We then create the text shape and style it as well. When we&#8217;re ready, we animate the both of them&#8230;</p></div>
<div class="sect2">
<h3 id="_the_square">1.1. The Square</h3>
<div class="paragraph"><p>Setting up the square is fairly straightforward. We align it to the center of the screen, clear its fill color, and add a dash pattern. The line is given a CAPROUND style, which adds rounded ends to all the dashes.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rect</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">200</span><span class="p">,</span> <span class="mi">200</span><span class="p">)];</span>
<span class="n">rect</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>

<span class="n">patternWidth</span> <span class="o">=</span> <span class="mi">4</span><span class="o">*</span><span class="n">rect</span><span class="py">.width</span><span class="p">;</span>
<span class="nc">CGFloat</span> <span class="n">dashPattern</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="mi">5</span><span class="p">,</span><span class="mi">20</span><span class="p">};</span>

<span class="c1">//thicken the line and set its dash pattern</span>
<span class="n">rect</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
<span class="n">rect</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
<span class="n">rect</span><span class="py">.lineCap</span> <span class="o">=</span> <span class="n">CAPROUND</span><span class="p">;</span>
<span class="p">[</span><span class="n">rect</span> <span class="n">setDashPattern:dashPattern</span> <span class="n">pointCount:</span><span class="mi">2</span><span class="p">];</span>

<span class="c1">//add the line to the canvas</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:rect</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_the_text_shape">1.2. The Text Shape</h3>
<div class="paragraph"><p>We create the text shape using the <tt><strong>ArialRoundedMTBold</strong></tt> font, and take the same steps to center it, style it and set its dash pattern.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="c1">//create a font for the text shape</span>
<span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">f</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;ArialRoundedMTBold&quot;</span> <span class="n">size:</span><span class="mi">320</span><span class="p">];</span>

<span class="c1">//create the text shape and center it</span>
<span class="n">star</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">shapeFromString:</span><span class="s">@&quot;*&quot;</span> <span class="n">withFont:f</span><span class="p">];</span>
<span class="n">star</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>

<span class="c1">//style the text shape and set its dash pattern</span>
<span class="n">star</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
<span class="n">star</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
<span class="n">star</span><span class="py">.lineCap</span> <span class="o">=</span> <span class="n">CAPROUND</span><span class="p">;</span>
<span class="p">[</span><span class="n">star</span> <span class="n">setDashPattern:dashPattern</span> <span class="n">pointCount:</span><span class="mi">2</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_animating_the_shapes">1.3. Animating the Shapes</h3>
<div class="paragraph"><p>When we&#8217;re ready to animate we call the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animate</span> <span class="p">{</span>
    <span class="n">rect</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
    <span class="n">rect</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">AUTOREVERSE</span> <span class="o">|</span> <span class="n">REPEAT</span><span class="p">;</span>
    <span class="n">rect</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>

    <span class="n">star</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
    <span class="n">star</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">AUTOREVERSE</span> <span class="o">|</span> <span class="n">REPEAT</span><span class="p">;</span>
    <span class="n">star</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>

    <span class="c1">//set the final dash phase to the entire width of the pattern</span>
    <span class="n">rect</span><span class="py">.lineDashPhase</span> <span class="o">=</span> <span class="n">patternWidth</span><span class="p">;</span>
    <span class="n">star</span><span class="py">.lineDashPhase</span> <span class="o">=</span> <span class="n">patternWidth</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>The above creates a 10-second repeating animation (in both directions) that animates the dash phase and color of the lines for each shape.</p></div>
</div>
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
