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

<h2>Trigonometry</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5348431" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial I&#8217;ll take you <a href="trigonometry/stepbystep.jpeg">step by step</a> through the process of making a dynamic graph of a polygon in a circle. This app also updates various points of intersection between the shapes in the graph.</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometry.png" alt="A Dynamic Graph" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_variables">1. Variables</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to need a lot of variables to make this thing happen, but we&#8217;ll explain things as we go along. For now, you can copy all the following variables into the class implementation section of your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="nc">C4WorkSpace</span> <span class="p">{</span>
    <span class="n">CGPoint</span> <span class="n">A</span><span class="p">,</span> <span class="n">B</span><span class="p">,</span> <span class="n">C</span><span class="p">,</span> <span class="n">D</span><span class="p">,</span> <span class="n">M</span><span class="p">,</span> <span class="n">P</span><span class="p">,</span> <span class="n">Q</span><span class="p">,</span> <span class="n">X</span><span class="p">,</span> <span class="n">Y</span><span class="p">;</span>
    <span class="n">CGFloat</span> <span class="n">radius</span><span class="p">,</span> <span class="n">theta</span><span class="p">,</span> <span class="n">dX</span><span class="p">,</span> <span class="n">dY</span><span class="p">,</span> <span class="n">s</span><span class="p">,</span> <span class="n">slope</span><span class="p">,</span> <span class="n">b</span><span class="p">,</span> <span class="n">thetaA</span><span class="p">,</span> <span class="n">thetaB</span><span class="p">;</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">circle</span><span class="p">,</span> <span class="o">*</span><span class="n">poly</span><span class="p">,</span> <span class="o">*</span><span class="n">mPt</span><span class="p">,</span> <span class="o">*</span><span class="n">pPt</span><span class="p">,</span> <span class="o">*</span><span class="n">qPt</span><span class="p">,</span> <span class="o">*</span><span class="n">xPt</span><span class="p">,</span> <span class="o">*</span><span class="n">yPt</span><span class="p">,</span> <span class="o">*</span><span class="n">angleB</span><span class="p">,</span> <span class="o">*</span><span class="n">angleD</span><span class="p">,</span> <span class="o">*</span><span class="n">linePQ</span><span class="p">;</span>
    <span class="n">C4Label</span> <span class="o">*</span><span class="n">lblA</span><span class="p">,</span> <span class="o">*</span><span class="n">lblB</span><span class="p">,</span> <span class="o">*</span><span class="n">lblC</span><span class="p">,</span> <span class="o">*</span><span class="n">lblD</span><span class="p">,</span> <span class="o">*</span><span class="n">lblM</span><span class="p">,</span> <span class="o">*</span><span class="n">lblP</span><span class="p">,</span> <span class="o">*</span><span class="n">lblQ</span><span class="p">,</span> <span class="o">*</span><span class="n">lblX</span><span class="p">,</span> <span class="o">*</span><span class="n">lblY</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_circle_and_polygon">2. Circle and Polygon</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first thing we want to do is set up a circle and a polygon, making sure that the polygon fits so that each corner touches the edge of the circle.</p></div>
<div class="sect2">
<h3 id="_circle">2.1. Circle</h3>
<div class="paragraph"><p>Setting up the circle is the easiest part of this whole tutorial.</p></div>
<div class="paragraph"><p>Create a method called <tt>-(void)setupCircleAndPoly {}</tt>, it is in this method that we&#8217;ll create the circle and poly, adjust the position of the poly to fit the circle, and then add the poly to the circle (yup! as a subview of the circle).</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">createCircleAndPoly</span> <span class="p">{</span>
    <span class="c1">//magic happens here.</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Create a circle and give it some visual styling so that we can later reference this style for all other objects that we will add to the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">368</span><span class="p">,</span><span class="mi">368</span><span class="p">)];</span>
<span class="n">circle</span><span class="p">.</span><span class="n">lineWidth</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
<span class="n">circle</span><span class="p">.</span><span class="n">strokeColor</span> <span class="o">=</span> <span class="n">C4GREY</span><span class="p">;</span>
<span class="n">circle</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_polygon">2.2. Polygon</h3>
<div class="paragraph"><p>To create the polygon we need to specify the locations of 4 points that will make up the corners of the shape. There are some conditions we can start out by assuming that we want:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
the top corners to start above the mid-point of the circle
</p>
</li>
<li>
<p>
the bottom corners to start below the mid-point of the circle
</p>
</li>
<li>
<p>
the shape to be symmetric
</p>
</li>
</ol></div>
<div class="paragraph"><p>We start by specifying two angles <tt>thetaA</tt> and <tt>thetaB</tt> for which we will specify values between <tt>0</tt> and <tt>2.0</tt> (i.e. we can later multiply these values by <tt>PI</tt> to get a range between <tt>0</tt> and <tt>TWO_PI</tt>).</p></div>
<div class="paragraph"><p>We already specified the points <tt>{A,B,C,D}</tt> as class variables. The following polar-coordinate math will give us 4 values for points that will make up a symmetric polygon. The math is essentially:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">x</span> <span class="o">=</span> <span class="n">r</span> <span class="o">*</span> <span class="n">sin</span><span class="p">(</span><span class="n">angle</span><span class="p">);</span>
<span class="n">y</span> <span class="o">=</span> <span class="n">r</span> <span class="o">*</span> <span class="n">cos</span><span class="p">(</span><span class="n">angle</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>Here&#8217;s the implementation:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">thetaA</span> <span class="o">=</span> <span class="mf">1.15f</span><span class="p">;</span>
<span class="n">thetaB</span> <span class="o">=</span> <span class="mf">0.3f</span><span class="p">;</span>

<span class="n">radius</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">width</span> <span class="o">/</span> <span class="mf">2.0f</span><span class="p">;</span>
<span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="n">thetaA</span><span class="p">;</span>
<span class="n">A</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

<span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="n">thetaB</span><span class="p">;</span>
<span class="n">B</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

<span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="p">(</span><span class="mf">2.0f</span><span class="o">-</span><span class="n">thetaA</span><span class="p">);</span>
<span class="n">C</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

<span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="p">(</span><span class="mf">2.0f</span><span class="o">-</span><span class="n">thetaB</span><span class="p">);</span>
<span class="n">D</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>
</pre></div></div></div>
<div class="paragraph"><p>Now we can create the polygon shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">polypts</span><span class="p">[</span><span class="mi">4</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">A</span><span class="p">,</span><span class="n">B</span><span class="p">,</span><span class="n">C</span><span class="p">,</span><span class="n">D</span><span class="p">};</span>
<span class="n">poly</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">polygon</span><span class="o">:</span><span class="n">polypts</span> <span class="n">pointCount</span><span class="o">:</span><span class="mi">4</span><span class="p">];</span>
<span class="n">poly</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span> <span class="c1">//using the circle&#39;s style</span>
<span class="n">poly</span><span class="p">.</span><span class="n">lineJoin</span> <span class="o">=</span> <span class="n">JOINBEVEL</span><span class="p">;</span>
<span class="p">[</span><span class="n">poly</span> <span class="n">closeShape</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_positioning_the_polygon">2.3. Positioning the Polygon</h3>
<div class="paragraph"><p>This is actually a tricky bit to accomplish. What we want to do is make sure that the polygon is positioned inside the circle so that all of its corners touch the circle.</p></div>
<div class="paragraph"><p>I tried first positioning the shape by saying:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">poly</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">circle</span><span class="p">.</span><span class="n">width</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">,</span><span class="n">circle</span><span class="p">.</span><span class="n">height</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;but this didn&#8217;t work because the <tt>frame</tt> of the polygon is not the same as that of the circle.</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryBadAlignment.png" alt="A Dynamic Graph" />
</div>
</div>
<div class="paragraph"><p>After a bit of hacking I figured out that I could do the translation like this:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
pick a point from <tt>{A,B,C,D}</tt>
</p>
</li>
<li>
<p>
normalize the position of the point so that ranges from <tt>0</tt> to <tt>1</tt>
</p>
</li>
<li>
<p>
set the <tt>anchorPoint</tt> of the polygon to the normalized point
</p>
</li>
<li>
<p>
center the polygon based on the center of the circle added to the point we pick
</p>
</li>
</ol></div>
<div class="paragraph"><p>This is how I did those steps:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">polyAnchor</span> <span class="o">=</span> <span class="n">A</span><span class="p">;</span>
<span class="n">polyAnchor</span><span class="p">.</span><span class="n">x</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">map</span><span class="o">:</span><span class="n">polyAnchor</span><span class="p">.</span><span class="n">x</span>
                   <span class="nl">fromMin:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">x</span>
                       <span class="nl">max:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">x</span><span class="o">+</span><span class="n">poly</span><span class="p">.</span><span class="n">width</span>
                     <span class="nl">toMin:</span><span class="mi">0</span> <span class="n">max</span><span class="o">:</span><span class="mi">1</span><span class="p">];</span>
<span class="n">polyAnchor</span><span class="p">.</span><span class="n">y</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">map</span><span class="o">:</span><span class="n">polyAnchor</span><span class="p">.</span><span class="n">y</span>
                   <span class="nl">fromMin:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">y</span>
                       <span class="nl">max:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">y</span><span class="o">+</span><span class="n">poly</span><span class="p">.</span><span class="n">height</span>
                     <span class="nl">toMin:</span><span class="mi">0</span> <span class="n">max</span><span class="o">:</span><span class="mi">1</span><span class="p">];</span>
<span class="n">poly</span><span class="p">.</span><span class="n">anchorPoint</span> <span class="o">=</span> <span class="n">polyAnchor</span><span class="p">;</span>
<span class="n">poly</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">circle</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">x</span><span class="o">+</span><span class="n">A</span><span class="p">.</span><span class="n">x</span><span class="p">,</span><span class="n">circle</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">x</span><span class="o">+</span><span class="n">A</span><span class="p">.</span><span class="n">y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>After centering the shape properly, you can now add it directly to the circle, then add the circle to the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">circle</span> <span class="n">addShape</span><span class="o">:</span><span class="n">poly</span><span class="p">];</span>
<span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">circle</span><span class="p">];</span>
<span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_setup_and_run">2.4. Setup and Run</h3>
<div class="paragraph"><p>Now you can call the <tt>createCircleAndPoly</tt> method from <tt>setup</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setupCircleAndPoly</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Running the application now will give you the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryAligned.png" alt="Aligned Poly in Circle" />
</div>
</div>
<div class="paragraph"><p>The next step is to start figuring out the points for the line.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_point_x">3. Point: X</h2>
<div class="sectionbody">
<div class="paragraph"><p>We want to draw a line that has the following characteristics:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
passes through the mid-point of the segment <tt>AD</tt>
</p>
</li>
<li>
<p>
passes through the intersection of the segments <tt>AB</tt> and <tt>CD</tt>
</p>
</li>
</ol></div>
<div class="paragraph"><p>So&#8230; How do we accomplish this? We start out by finding the easiest point.</p></div>
<div class="sect2">
<h3 id="_create_x">3.1. Create X</h3>
<div class="paragraph"><p>Just like we did with the style of the poly (i.e. copying the style of the circle), we&#8217;re going to build a point shape and use this as the basis for styling all other points in our diagram.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">xPt</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">7</span><span class="p">,</span> <span class="mi">7</span><span class="p">)];</span>
<span class="n">xPt</span><span class="p">.</span><span class="n">lineWidth</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
<span class="n">xPt</span><span class="p">.</span><span class="n">strokeColor</span> <span class="o">=</span> <span class="n">C4GREY</span><span class="p">;</span>
<span class="n">xPt</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>After putting this code in <tt>setup</tt>, create a new method called <tt>setX</tt>. We&#8217;ll do our calculations for the x-coordinate in this method.</p></div>
</div>
<div class="sect2">
<h3 id="_calculate_x">3.2. Calculate X</h3>
<div class="paragraph"><p>The x-point is simply the middle of A and D. You can calculate it&#8217;s position quite simply by doing the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setX</span> <span class="p">{</span>
    <span class="c1">//X is the mid-point of AD</span>
    <span class="n">X</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">((</span><span class="n">D</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="n">A</span><span class="p">.</span><span class="n">x</span><span class="p">)</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">,(</span><span class="n">D</span><span class="p">.</span><span class="n">y</span><span class="o">+</span><span class="n">A</span><span class="p">.</span><span class="n">y</span><span class="p">)</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">);</span>
    <span class="n">xPt</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">X</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryX.png" alt="The X Point" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_point_m">4. Point: M</h2>
<div class="sectionbody">
<div class="paragraph"><p>The next point to calculate is <tt>M</tt> because of a few reasons. First, we know all the things we need to calculate it&#8230; That is, we have 2 defined lines <tt>AB</tt> and <tt>CD</tt>. Second, we don&#8217;t actually have enough information yet to calculate <tt>Y</tt>&#8230; We need <tt>M</tt> in order to figure out <tt>Y</tt>.</p></div>
<div class="sect2">
<h3 id="_create_m">4.1. Create M</h3>
<div class="paragraph"><p>This step is dead-easy. The following will duplicate <tt>xPt</tt>, we don&#8217;t even need to copy styles.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">mPt</span> <span class="o">=</span> <span class="p">[</span><span class="n">xPt</span> <span class="n">copy</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_calculate_m">4.2. Calculate M</h3>
<div class="paragraph"><p>Finding the intersection between two lines seems pretty trivial. However, I had never done it before (especially in code) so I had to start looking for theory.</p></div>
<div class="paragraph"><p>I checked a few places and found great explanations from <a href="http://en.wikipedia.org/wiki/Line-line_intersection">Wikipedia</a> and <a href="http://mathworld.wolfram.com/Line-LineIntersection.html">Wolfram</a>. What I found, though, didn&#8217;t totally help me because the "explanations" looked like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryLineLineMath.png" alt="Line-Line Intersection Math" />
</div>
</div>
<div class="paragraph"><p>So, I started looking for answers that included code. The <a href="http://wiki.processing.org/w/Line-Line_intersection">Processing</a> wiki had a good write up, and so did <a href="http://stackoverflow.com/questions/4543506/algorithm-for-intersection-of-2-lines">StackOverflow</a>. The accepted answer on S.O was pretty straightforward and using all the other references I built up the following set of code that isolates <tt>M</tt> precisely.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setM</span> <span class="p">{</span>
    <span class="c1">//M is the intersection of the two internal lines of the polygon</span>
    <span class="n">CGFloat</span> <span class="n">a1</span> <span class="o">=</span> <span class="n">B</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="n">A</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
    <span class="n">CGFloat</span> <span class="n">b1</span> <span class="o">=</span> <span class="n">A</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="n">B</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
    <span class="n">CGFloat</span> <span class="n">c1</span> <span class="o">=</span> <span class="n">a1</span><span class="o">*</span><span class="n">A</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="n">b1</span><span class="o">*</span><span class="n">A</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>

    <span class="n">CGFloat</span> <span class="n">a2</span> <span class="o">=</span> <span class="n">D</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="n">C</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
    <span class="n">CGFloat</span> <span class="n">b2</span> <span class="o">=</span> <span class="n">C</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="n">D</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
    <span class="n">CGFloat</span> <span class="n">c2</span> <span class="o">=</span> <span class="n">a2</span><span class="o">*</span><span class="n">C</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="n">b2</span><span class="o">*</span><span class="n">C</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>

    <span class="n">CGFloat</span> <span class="n">det</span> <span class="o">=</span> <span class="n">a1</span><span class="o">*</span><span class="n">b2</span> <span class="o">-</span> <span class="n">a2</span> <span class="o">*</span> <span class="n">b1</span><span class="p">;</span>
    <span class="n">M</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">((</span><span class="n">b2</span><span class="o">*</span><span class="n">c1</span> <span class="o">-</span> <span class="n">b1</span><span class="o">*</span><span class="n">c2</span><span class="p">)</span><span class="o">/</span><span class="n">det</span><span class="p">,</span> <span class="p">(</span><span class="n">a1</span><span class="o">*</span><span class="n">c2</span> <span class="o">-</span> <span class="n">a2</span><span class="o">*</span><span class="n">c1</span><span class="p">)</span><span class="o">/</span><span class="n">det</span><span class="p">);</span>
    <span class="n">mPt</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">M</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryXM.png" alt="X and M Points" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">This did take me a while to get right.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_point_y">5. Point: Y</h2>
<div class="sectionbody">
<div class="paragraph"><p>With <tt>X</tt> and <tt>M</tt> defined I could now start figuring out the coordinate for the <tt>Y</tt> point. I hacked around for a while, and after trying a couple of different approaches I realized that I should be using the <a href="http://en.wikipedia.org/wiki/Law_of_sines">Law of Sines</a>. This was a good throwback to high-school math that I&#8217;d forgotten!</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryLawOfSines.png" alt="Math for the Law of Sines" />
</div>
</div>
<div class="sect2">
<h3 id="_create_y">5.1. Create Y</h3>
<div class="paragraph"><p>Again, just copy the previous point.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">yPt</span> <span class="o">=</span> <span class="p">[</span><span class="n">mPt</span> <span class="n">copy</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_calculate_y">5.2. Calculate Y</h3>
<div class="paragraph"><p>To calculate <tt>Y</tt> we first need to solve for 3 angles, two of which we&#8217;ll use <tt>C4Vector</tt> to do the calculation for us.</p></div>
<div class="paragraph"><p>To make things a bit cleaner and easier, I suggest writing the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="n">CGFloat</span><span class="p">)</span><span class="nf">angleFromA:</span><span class="p">(</span><span class="n">CGPoint</span><span class="p">)</span><span class="nv">pt1</span> <span class="nf">b:</span><span class="p">(</span><span class="n">CGPoint</span><span class="p">)</span><span class="nv">pt2</span> <span class="nf">c:</span><span class="p">(</span><span class="n">CGPoint</span><span class="p">)</span><span class="nv">pt3</span> <span class="p">{</span>
    <span class="n">pt1</span><span class="p">.</span><span class="n">x</span> <span class="o">-=</span> <span class="n">pt2</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
    <span class="n">pt1</span><span class="p">.</span><span class="n">y</span> <span class="o">-=</span> <span class="n">pt2</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
    <span class="n">pt3</span><span class="p">.</span><span class="n">x</span> <span class="o">-=</span> <span class="n">pt2</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
    <span class="n">pt3</span><span class="p">.</span><span class="n">y</span> <span class="o">-=</span> <span class="n">pt2</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>

    <span class="k">return</span> <span class="p">[</span><span class="n">C4Vector</span> <span class="n">angleBetweenA</span><span class="o">:</span><span class="n">pt1</span> <span class="n">andB</span><span class="o">:</span><span class="n">pt3</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>What this does is take 2 points, displaced by an intermediate point so that we can get the angle between <tt>pt1</tt> and <tt>pt2</tt> based on <tt>{0,0}</tt>.</p></div>
<div class="paragraph"><p>We&#8217;re interested in the triangle that is defined by the points: <tt>C</tt>, <tt>M</tt> and <tt>Y</tt>, and we already have enough information to get the angles for <tt>MCB</tt> and <tt>CMY</tt>.</p></div>
<div class="paragraph"><p>Now, create a <tt>setY</tt> method and start out by calculating the angles for <tt>MCB</tt> and <tt>CMY</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setY</span> <span class="p">{</span>
        <span class="n">CGFloat</span> <span class="n">angleMCB</span> <span class="o">=</span> <span class="p">[</span><span class="n">self</span> <span class="n">angleFromA</span><span class="o">:</span><span class="n">M</span> <span class="n">b</span><span class="o">:</span><span class="n">C</span> <span class="n">c</span><span class="o">:</span><span class="n">B</span><span class="p">];</span>
        <span class="n">CGFloat</span> <span class="n">angleCMY</span> <span class="o">=</span> <span class="p">[</span><span class="n">self</span> <span class="n">angleFromA</span><span class="o">:</span><span class="n">X</span> <span class="n">b</span><span class="o">:</span><span class="n">M</span> <span class="n">c</span><span class="o">:</span><span class="n">D</span><span class="p">];</span> <span class="c1">//because they&#39;re equal</span>
        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">We use the angle <tt>XMD</tt> to calculate for two reasons: 1) because we don&#8217;t <tt>Y</tt> yet, 2) because the <tt>XMD</tt> and <tt>CMY</tt> angles are equal to one another (i.e. the diagram is symmetric)</td>
</tr></table>
</div>
<div class="paragraph"><p>From here, calculating the angle <tt>CYM</tt> is easy:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">angleCYM</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">-</span> <span class="n">angleMCB</span> <span class="o">-</span> <span class="n">angleCMY</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Now that we know 3 angles, we should calculate the length of the sides <tt>CM</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">dCM</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Vector</span> <span class="n">distanceBetweenA</span><span class="o">:</span><span class="n">C</span> <span class="n">andB</span><span class="o">:</span><span class="n">M</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>With this we can now calculate the length of the side <tt>MY</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">dMY</span> <span class="o">=</span> <span class="n">dCM</span><span class="o">/</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">angleCYM</span><span class="p">]</span> <span class="o">*</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">angleMCB</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">this is actually a coded version of <tt>b = a/sin(A) * sin(B)</tt></td>
</tr></table>
</div>
<div class="paragraph"><p>At this point I started messing around with different equations and tricks, and again like before, wasted a bunch of time until I realized that calculating the position of <tt>Y</tt> was really easy if I just extended the line from <tt>X</tt> to <tt>M</tt>.</p></div>
<div class="paragraph"><p>Calculate the distance between <tt>X</tt> and <tt>M</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">dMX</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Vector</span> <span class="n">distanceBetweenA</span><span class="o">:</span><span class="n">M</span> <span class="n">andB</span><span class="o">:</span><span class="n">X</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, the distance from <tt>X</tt> to <tt>Y</tt> is going to be <tt>dMX</tt> + <tt>dMY</tt>, and since we already know both of these we can add them up and divide them by <tt>dMX</tt> to get a multiplier that will extend <tt>MX</tt> to <tt>XY</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">multiplier</span> <span class="o">=</span> <span class="p">(</span><span class="n">dMX</span> <span class="o">+</span> <span class="n">dMY</span><span class="p">)</span><span class="o">/</span><span class="n">dMX</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>From here, calculating <tt>Y</tt> is a cinch:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">Y</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">X</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="n">dX</span> <span class="o">*</span> <span class="n">multiplier</span><span class="p">,</span> <span class="n">X</span><span class="p">.</span><span class="n">y</span> <span class="o">+</span> <span class="n">dY</span> <span class="o">*</span> <span class="n">multiplier</span><span class="p">);</span>
<span class="n">yPt</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">Y</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>And we get a new point at Y&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryXMY.png" alt="X, M and Y Points" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_points_p_q">6. Points: P, Q</h2>
<div class="sectionbody">
<div class="paragraph"><p>Ok, finally we&#8217;re getting somewhere. The last step is to check for the intersections of the line <tt>XY</tt> with the edges of the circle. Straightforward? NO! (well&#8230; maybe now, but definitely not when I started).</p></div>
<div class="sect2">
<h3 id="_create_p_and_q">6.1. Create P and Q</h3>
<div class="paragraph"><p>Easy.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">pPt</span> <span class="o">=</span> <span class="p">[</span><span class="n">yPt</span> <span class="n">copy</span><span class="p">];</span>
<span class="n">qPt</span> <span class="o">=</span> <span class="p">[</span><span class="n">pPt</span> <span class="n">copy</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_calculate_p_and_q">6.2. Calculate P and Q</h3>
<div class="paragraph"><p>So, I went back to my searches for how to do this and like the first time I ended up finding a good description at <a href="http://mathworld.wolfram.com/Circle-LineIntersection.html">Wolfram</a>. I found the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryLineCircleMath.png" alt="Math for Line-Circle Intersection" />
</div>
</div>
<div class="paragraph"><p>This time, however, I was used to taking equations and turning them into code. I knew I had to first solve for 4 variables: <tt>dX</tt>, <tt>dY</tt>, <tt>dr</tt> and <tt>D</tt> (which I call <tt>bigD</tt> in code). I also needed the slope of the line and its displacement (i.e. from <tt>y = mx + b</tt>). Create a <tt>setPQ</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setPQ</span> <span class="p">{</span>
    <span class="n">slope</span> <span class="o">=</span> <span class="n">dY</span> <span class="o">/</span> <span class="n">dX</span><span class="p">;</span>
    <span class="n">b</span> <span class="o">=</span> <span class="n">M</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">we already set <tt>dX</tt> and <tt>dY</tt> in our <tt>setY</tt> method, so we can just mosey along here.</td>
</tr></table>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">dr</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">sqrt</span><span class="o">:</span><span class="p">(</span><span class="n">dX</span><span class="o">*</span><span class="n">dX</span><span class="o">+</span><span class="n">dY</span><span class="o">*</span><span class="n">dY</span><span class="p">)];</span>
<span class="n">CGFloat</span> <span class="n">bigD</span> <span class="o">=</span> <span class="n">X</span><span class="p">.</span><span class="n">x</span><span class="o">*</span><span class="n">M</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="n">M</span><span class="p">.</span><span class="n">x</span><span class="o">*</span><span class="n">X</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>We&#8217;re now going to calculate the position for <tt>P</tt> using the + sign of <tt>y</tt>. Since we don&#8217;t know what the first sign will be (i.e. positive or negative) we calculate it:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">sgn</span> <span class="o">=</span> <span class="n">dY</span> <span class="o">/</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">absf</span><span class="o">:</span><span class="n">dY</span><span class="p">];</span>
<span class="n">x</span> <span class="o">=</span> <span class="p">(</span><span class="n">bigD</span><span class="o">*</span><span class="n">dY</span><span class="o">+</span><span class="n">sgn</span><span class="o">*</span><span class="n">dX</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sqrt</span><span class="o">:</span><span class="n">radius</span><span class="o">*</span><span class="n">radius</span><span class="o">*</span><span class="n">dr</span><span class="o">*</span><span class="n">dr</span><span class="o">-</span><span class="n">bigD</span><span class="o">*</span><span class="n">bigD</span><span class="p">])</span><span class="o">/</span><span class="p">(</span><span class="n">dr</span><span class="o">*</span><span class="n">dr</span><span class="p">);</span>
<span class="n">y</span> <span class="o">=</span> <span class="n">slope</span><span class="o">*</span><span class="n">x</span> <span class="o">+</span> <span class="n">b</span><span class="p">;</span>
<span class="n">P</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span> <span class="n">y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;and now we calculate <tt>Q</tt> for the - sign of <tt>y</tt></p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">x</span> <span class="o">=</span> <span class="p">(</span><span class="n">bigD</span><span class="o">*</span><span class="n">dY</span><span class="o">-</span><span class="n">sgn</span><span class="o">*</span><span class="n">dX</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sqrt</span><span class="o">:</span><span class="n">radius</span><span class="o">*</span><span class="n">radius</span><span class="o">*</span><span class="n">dr</span><span class="o">*</span><span class="n">dr</span><span class="o">-</span><span class="n">bigD</span><span class="o">*</span><span class="n">bigD</span><span class="p">])</span><span class="o">/</span><span class="p">(</span><span class="n">dr</span><span class="o">*</span><span class="n">dr</span><span class="p">);</span>
<span class="n">y</span> <span class="o">=</span> <span class="n">slope</span><span class="o">*</span><span class="n">x</span> <span class="o">+</span> <span class="n">b</span><span class="p">;</span>
<span class="n">Q</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span> <span class="n">y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, a neat little trick we have to do to keep P and Q in their proper positions is to invert them if the slope of the line <tt>PQ</tt> is less than <tt>0</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">(</span><span class="n">slope</span> <span class="o">&gt;</span> <span class="mi">0</span><span class="p">)</span> <span class="p">{</span>
    <span class="n">CGPoint</span> <span class="n">temp</span> <span class="o">=</span> <span class="n">P</span><span class="p">;</span>
    <span class="n">P</span> <span class="o">=</span> <span class="n">Q</span><span class="p">;</span>
    <span class="n">Q</span> <span class="o">=</span> <span class="n">temp</span><span class="p">;</span>
<span class="p">}</span>

<span class="n">pPt</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">P</span><span class="p">;</span>
<span class="n">qPt</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">Q</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Our diagram should now have all 5 points on it!</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryXMYPQ.png" alt="X,M,Y,P and Q points" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_line">7. The Line</h2>
<div class="sectionbody">
<div class="paragraph"><p>Adding the line is a cinch:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">linePts</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">P</span><span class="p">,</span><span class="n">Q</span><span class="p">};</span>
<span class="n">linePQ</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">line</span><span class="o">:</span><span class="n">linePts</span><span class="p">];</span>
<span class="n">linePQ</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Pretty easy right? Now we have:</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryLine.png" alt="Graph with a line" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_angles">8. Angles!</h2>
<div class="sectionbody">
<div class="paragraph"><p>For a little style, we&#8217;re going to put two angle diagrams in the bottom points of the polygon. To do this we&#8217;re going to add <em>circles</em> to the diagram, but only adjust their <tt>strokeStart</tt> and <tt>strokeEnd</tt> points so that they "look" like arcs.</p></div>
<div class="sect2">
<h3 id="_create_b_and_d">8.1. Create B and D</h3>
<div class="paragraph"><p>To create the angle shapes for <tt>B</tt> and <tt>D</tt> you can do the following in <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">angleD</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">80</span><span class="p">,</span> <span class="mi">80</span><span class="p">)];</span>
<span class="n">angleD</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span>
<span class="n">angleB</span> <span class="o">=</span> <span class="p">[</span><span class="n">angleD</span> <span class="n">copy</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_calculate_b_and_d">8.2. Calculate B and D</h3>
<div class="paragraph"><p>Next, create a <tt>setAngleBD</tt> method and calculate the angle <tt>ADC</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setAngleBD</span> <span class="p">{</span>
    <span class="n">theta</span> <span class="o">=</span> <span class="p">[</span><span class="n">self</span> <span class="n">angleFromA</span><span class="o">:</span><span class="n">A</span> <span class="n">b</span><span class="o">:</span><span class="n">D</span> <span class="n">c</span><span class="o">:</span><span class="n">C</span><span class="p">];</span>
        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Now that we know the value of the angle, we have to simply calculate the angle <em>to</em> the beginning of the curve so that we can properly set the <tt>strokeStart</tt> and <tt>strokeEnd</tt> values for each shape.</p></div>
</div>
<div class="sect2">
<h3 id="_wait_let_8217_s_put_a_little_style_into_this">8.3. Wait. Let&#8217;s put a little style into this!</h3>
<div class="paragraph"><p>Ok, so after I first created this little demo I realized that I had hard-coded the size of the angle shapes. This ended up being a visual bug where the shape would extend out of the polygon if the polygon was really really thin.</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryVisualBug.png" alt="A Visual Bug" />
</div>
</div>
<div class="paragraph"><p>So, how do we fix this? We change the size of the shape every time we adjust the polygon, making the size of the angle shape a fraction of the length of the side <tt>AD</tt>. To do this efficiently, we&#8217;re going to remove the angle shapes from the polygon and recreate them.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">angleD</span> <span class="n">removeFromSuperview</span><span class="p">];</span>
<span class="n">angleD</span> <span class="o">=</span> <span class="nb">nil</span><span class="p">;</span>

<span class="p">[</span><span class="n">angleB</span> <span class="n">removeFromSuperview</span><span class="p">];</span>
<span class="n">angleB</span> <span class="o">=</span> <span class="nb">nil</span><span class="p">;</span>

<span class="n">CGFloat</span> <span class="n">dAD</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Vector</span> <span class="n">distanceBetweenA</span><span class="o">:</span><span class="n">A</span> <span class="n">andB</span><span class="o">:</span><span class="n">D</span><span class="p">];</span>
<span class="n">CGFloat</span> <span class="n">r</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">maxOfA</span><span class="o">:</span><span class="mi">80</span> <span class="n">B</span><span class="o">:</span><span class="mf">10.0f</span><span class="p">];</span>

<span class="n">angleD</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="n">r</span><span class="p">,</span> <span class="n">r</span><span class="p">)];</span>
<span class="n">angleD</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span>
<span class="n">angleD</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">D</span><span class="p">;</span>

<span class="n">angleB</span> <span class="o">=</span> <span class="p">[</span><span class="n">angleD</span> <span class="n">copy</span><span class="p">];</span>
<span class="n">angleB</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">B</span><span class="p">;</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Your devices will be able to handle this remove and rebuild very efficiently, so don&#8217;t worry about it.</td>
</tr></table>
</div>
<div class="paragraph"><p>The next thing we&#8217;re going to do to these angles is to calculate the angles of rotations to set the <tt>strokeStart</tt> and <tt>strokeEnd</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">rot</span> <span class="o">=</span> <span class="p">[</span><span class="n">self</span> <span class="n">angleFromA</span><span class="o">:</span><span class="n">C</span> <span class="n">b</span><span class="o">:</span><span class="n">D</span> <span class="n">c</span><span class="o">:</span><span class="n">B</span><span class="p">];</span>
<span class="n">angleD</span><span class="p">.</span><span class="n">strokeStart</span> <span class="o">=</span> <span class="mi">1</span> <span class="o">-</span> <span class="n">theta</span><span class="o">/</span><span class="n">TWO_PI</span> <span class="o">-</span> <span class="n">rot</span><span class="o">/</span><span class="n">TWO_PI</span><span class="p">;</span>
<span class="n">angleD</span><span class="p">.</span><span class="n">strokeEnd</span> <span class="o">=</span> <span class="n">angleD</span><span class="p">.</span><span class="n">strokeStart</span> <span class="o">+</span> <span class="n">theta</span><span class="o">/</span><span class="n">TWO_PI</span><span class="p">;</span>

<span class="n">angleB</span><span class="p">.</span><span class="n">strokeStart</span> <span class="o">=</span>  <span class="n">rot</span><span class="o">/</span><span class="n">TWO_PI</span> <span class="o">+</span> <span class="mf">0.5f</span><span class="p">;</span>
<span class="n">angleB</span><span class="p">.</span><span class="n">strokeEnd</span> <span class="o">=</span> <span class="n">angleB</span><span class="p">.</span><span class="n">strokeStart</span> <span class="o">+</span> <span class="n">theta</span><span class="o">/</span><span class="n">TWO_PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Okay, once we add these angles to the polygon we have:</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryAngles.png" alt="Graph with angles" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_labels">9. Labels</h2>
<div class="sectionbody">
<div class="paragraph"><p>Finally, we&#8217;re going to add labels to the diagram. It&#8217;s pretty easy because we know all the points.</p></div>
<div class="sect2">
<h3 id="_create_labels">9.1. Create Labels</h3>
<div class="paragraph"><p>Create a method like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">createLabels</span> <span class="p">{</span>
    <span class="n">lblA</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;A&quot;</span> <span class="n">font</span><span class="o">:</span><span class="p">[</span><span class="n">C4Font</span> <span class="n">fontWithName</span><span class="o">:</span><span class="s">@&quot;TimesNewRomanPS-ItalicMT&quot;</span> <span class="n">size</span><span class="o">:</span><span class="mf">16.0f</span><span class="p">]];</span>
    <span class="n">lblB</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;B&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblC</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;C&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblD</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;D&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblP</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;P&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblQ</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;Q&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblM</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;M&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblX</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;X&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
    <span class="n">lblY</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;Y&quot;</span> <span class="n">font</span><span class="o">:</span><span class="n">lblA</span><span class="p">.</span><span class="n">font</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_position_labels">9.2. Position Labels</h3>
<div class="paragraph"><p>Positioning the labels is pretty easy. Create a method like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setLabelPositions</span> <span class="p">{</span>
    <span class="n">lblA</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">A</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="mf">6.0f</span><span class="p">,</span>  <span class="n">A</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="mf">8.0f</span><span class="p">);</span>
    <span class="n">lblB</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">B</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="mf">5.0f</span><span class="p">,</span>  <span class="n">B</span><span class="p">.</span><span class="n">y</span> <span class="o">+</span> <span class="mf">8.0f</span><span class="p">);</span>
    <span class="n">lblC</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">C</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="mf">6.0f</span><span class="p">,</span>  <span class="n">C</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="mf">8.0f</span><span class="p">);</span>
    <span class="n">lblD</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">D</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="mf">6.0f</span><span class="p">,</span>  <span class="n">D</span><span class="p">.</span><span class="n">y</span> <span class="o">+</span> <span class="mf">8.0f</span><span class="p">);</span>
    <span class="n">lblP</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">P</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="mf">16.0f</span><span class="p">,</span> <span class="n">P</span><span class="p">.</span><span class="n">y</span> <span class="o">+</span> <span class="mf">4.0f</span><span class="p">);</span>
    <span class="n">lblQ</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">Q</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="mf">14.0f</span><span class="p">,</span> <span class="n">Q</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span> <span class="mf">4.0f</span><span class="p">);</span>
    <span class="n">lblM</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">M</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="mf">1.0f</span><span class="p">,</span>  <span class="n">M</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span><span class="mf">16.0f</span><span class="p">);</span>
    <span class="n">lblX</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">X</span><span class="p">.</span><span class="n">x</span> <span class="o">-</span> <span class="mf">10.0f</span><span class="p">,</span> <span class="n">X</span><span class="p">.</span><span class="n">y</span> <span class="o">-</span><span class="mf">8.0f</span><span class="p">);</span>
    <span class="n">lblY</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">Y</span><span class="p">.</span><span class="n">x</span> <span class="o">+</span> <span class="mf">12.0f</span><span class="p">,</span> <span class="n">Y</span><span class="p">.</span><span class="n">y</span> <span class="o">+</span><span class="mf">8.0f</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">We add a couple of points here and there to offset the labels so that they don&#8217;t interfere with the diagram&#8230; I did this by hand, trial and error.</td>
</tr></table>
</div>
<div class="paragraph"><p>AAAAAAAAAND we have our diagram&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometry.png" alt="A Dynamic Graph" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_keeping_things_tidy">10. Keeping Things Tidy</h2>
<div class="sectionbody">
<div class="paragraph"><p>To keep things tidy, and to help with simplifying the next few steps, I created a couple of methods that consolidate the addition of shapes to our diagram. I created the following three methods:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">addShapesToPoly</span> <span class="p">{</span>
    <span class="n">CGPoint</span> <span class="n">linePts</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">P</span><span class="p">,</span><span class="n">Q</span><span class="p">};</span>
    <span class="n">linePQ</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">line</span><span class="o">:</span><span class="n">linePts</span><span class="p">];</span>
    <span class="n">linePQ</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span>

    <span class="p">[</span><span class="n">poly</span> <span class="n">addObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">angleB</span><span class="p">,</span><span class="n">angleD</span><span class="p">]];</span>
    <span class="p">[</span><span class="n">poly</span> <span class="n">addObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">linePQ</span><span class="p">,</span><span class="n">mPt</span><span class="p">,</span><span class="n">xPt</span><span class="p">,</span><span class="n">yPt</span><span class="p">,</span><span class="n">pPt</span><span class="p">,</span><span class="n">qPt</span><span class="p">]];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">addLabelsToPoly</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">poly</span> <span class="n">addObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">lblA</span><span class="p">,</span> <span class="n">lblB</span><span class="p">,</span> <span class="n">lblC</span><span class="p">,</span> <span class="n">lblD</span><span class="p">,</span> <span class="n">lblM</span><span class="p">,</span> <span class="n">lblP</span><span class="p">,</span> <span class="n">lblQ</span><span class="p">,</span> <span class="n">lblX</span><span class="p">,</span> <span class="n">lblY</span><span class="p">]];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_interaction">11. INTERACTION!</h2>
<div class="sectionbody">
<div class="paragraph"><p>I wasn&#8217;t happy with just having a static image, so I decided to put 3 levels of interaction on this diagram. You can:</p></div>
<div class="ulist"><ul>
<li>
<p>
adjust <tt>A</tt> and <tt>C</tt> by <em>dragging</em> back and forth across the top (1 finger)
</p>
</li>
<li>
<p>
adjust <tt>B</tt> and <tt>D</tt> by <em>dragging</em> back and forth across the bottom (1 finger)
</p>
</li>
<li>
<p>
rotate the diagram by <em>dragging</em> back and forth anywhere (2 fingers)
</p>
</li>
</ul></div>
<div class="paragraph"><p>To do this we add 2 gestures to the canvas and modify them in the following way:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">self</span> <span class="n">addGesture</span><span class="o">:</span><span class="n">PAN</span> <span class="n">name</span><span class="o">:</span><span class="s">@&quot;adjust&quot;</span> <span class="n">action</span><span class="o">:</span><span class="s">@&quot;adjust:&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="n">self</span> <span class="n">maximumNumberOfTouches</span><span class="o">:</span><span class="mi">1</span> <span class="n">forGesture</span><span class="o">:</span><span class="s">@&quot;adjust&quot;</span><span class="p">];</span>

<span class="p">[</span><span class="n">self</span> <span class="n">addGesture</span><span class="o">:</span><span class="n">PAN</span> <span class="n">name</span><span class="o">:</span><span class="s">@&quot;rotate&quot;</span> <span class="n">action</span><span class="o">:</span><span class="s">@&quot;rotate:&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="n">self</span> <span class="n">minimumNumberOfTouches</span><span class="o">:</span><span class="mi">2</span> <span class="n">forGesture</span><span class="o">:</span><span class="s">@&quot;rotate&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="sect2">
<h3 id="_adjust">11.1. adjust:</h3>
<div class="paragraph"><p>The adjust method handles the changing of points and updating the diagram. The method is quite simple:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">adjust:</span><span class="p">(</span><span class="n">UIPanGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="nv">gesture</span> <span class="p">{</span>
    <span class="n">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="p">[</span><span class="n">gesture</span> <span class="n">locationInView</span><span class="o">:</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">];</span>
    <span class="n">CGFloat</span> <span class="n">rot</span> <span class="o">=</span> <span class="p">(</span><span class="n">p</span><span class="p">.</span><span class="n">x</span> <span class="o">/</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">width</span><span class="p">)</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">;</span>

    <span class="k">if</span><span class="p">(</span><span class="n">p</span><span class="p">.</span><span class="n">y</span> <span class="o">&lt;</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">y</span><span class="p">)</span> <span class="p">[</span><span class="n">self</span> <span class="n">setThetaA</span><span class="o">:</span><span class="n">rot</span><span class="o">+</span><span class="mi">1</span> <span class="n">thetaB</span><span class="o">:</span><span class="n">thetaB</span><span class="p">];</span>
    <span class="k">else</span> <span class="p">[</span><span class="n">self</span> <span class="n">setThetaA</span><span class="o">:</span><span class="n">thetaA</span> <span class="n">thetaB</span><span class="o">:</span><span class="n">rot</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometryAdjust.png" alt="Adjusted top and bottom points" />
</div>
</div>
<div class="paragraph"><p>&#8230;it&#8217;s simple because it calls the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setThetaA:</span><span class="p">(</span><span class="n">CGFloat</span><span class="p">)</span><span class="nv">_thetaA</span> <span class="nf">thetaB:</span><span class="p">(</span><span class="n">CGFloat</span><span class="p">)</span><span class="nv">_thetaB</span> <span class="p">{</span>
    <span class="n">thetaA</span> <span class="o">=</span> <span class="n">_thetaA</span><span class="p">;</span>
    <span class="n">thetaB</span> <span class="o">=</span> <span class="n">_thetaB</span><span class="p">;</span>

    <span class="p">[</span><span class="n">poly</span> <span class="n">removeFromSuperview</span><span class="p">];</span>

    <span class="n">radius</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">width</span> <span class="o">/</span> <span class="mf">2.0f</span><span class="p">;</span>
    <span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="n">thetaA</span><span class="p">;</span>
    <span class="n">A</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

    <span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="n">thetaB</span><span class="p">;</span>
    <span class="n">B</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

    <span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="p">(</span><span class="mf">2.0f</span><span class="o">-</span><span class="n">thetaA</span><span class="p">);</span>
    <span class="n">C</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

    <span class="n">theta</span> <span class="o">=</span> <span class="n">PI</span> <span class="o">*</span> <span class="p">(</span><span class="mf">2.0f</span><span class="o">-</span><span class="n">thetaB</span><span class="p">);</span>
    <span class="n">D</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">sin</span><span class="o">:</span><span class="n">theta</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">cos</span><span class="o">:</span><span class="n">theta</span><span class="p">]);</span>

    <span class="n">CGPoint</span> <span class="n">polypts</span><span class="p">[</span><span class="mi">4</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">A</span><span class="p">,</span><span class="n">B</span><span class="p">,</span><span class="n">C</span><span class="p">,</span><span class="n">D</span><span class="p">};</span>

    <span class="n">poly</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">polygon</span><span class="o">:</span><span class="n">polypts</span> <span class="n">pointCount</span><span class="o">:</span><span class="mi">4</span><span class="p">];</span>
    <span class="n">poly</span><span class="p">.</span><span class="n">style</span> <span class="o">=</span> <span class="n">circle</span><span class="p">.</span><span class="n">style</span><span class="p">;</span>
    <span class="n">poly</span><span class="p">.</span><span class="n">lineJoin</span> <span class="o">=</span> <span class="n">JOINBEVEL</span><span class="p">;</span>

    <span class="n">CGPoint</span> <span class="n">polyAnchor</span> <span class="o">=</span> <span class="n">A</span><span class="p">;</span>
    <span class="n">polyAnchor</span><span class="p">.</span><span class="n">x</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">map</span><span class="o">:</span><span class="n">polyAnchor</span><span class="p">.</span><span class="n">x</span> <span class="n">fromMin</span><span class="o">:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">x</span> <span class="n">max</span><span class="o">:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">x</span><span class="o">+</span><span class="n">poly</span><span class="p">.</span><span class="n">width</span> <span class="n">toMin</span><span class="o">:</span><span class="mi">0</span> <span class="n">max</span><span class="o">:</span><span class="mi">1</span><span class="p">];</span>
    <span class="n">polyAnchor</span><span class="p">.</span><span class="n">y</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Math</span> <span class="n">map</span><span class="o">:</span><span class="n">polyAnchor</span><span class="p">.</span><span class="n">y</span> <span class="n">fromMin</span><span class="o">:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">y</span> <span class="n">max</span><span class="o">:</span><span class="n">poly</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">y</span><span class="o">+</span><span class="n">poly</span><span class="p">.</span><span class="n">height</span> <span class="n">toMin</span><span class="o">:</span><span class="mi">0</span> <span class="n">max</span><span class="o">:</span><span class="mi">1</span><span class="p">];</span>
    <span class="n">poly</span><span class="p">.</span><span class="n">anchorPoint</span> <span class="o">=</span> <span class="n">polyAnchor</span><span class="p">;</span>

    <span class="n">poly</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">circle</span><span class="p">.</span><span class="n">width</span><span class="o">/</span><span class="mf">2.0f</span><span class="o">+</span><span class="n">A</span><span class="p">.</span><span class="n">x</span><span class="p">,</span><span class="n">circle</span><span class="p">.</span><span class="n">height</span><span class="o">/</span><span class="mf">2.0f</span><span class="o">+</span><span class="n">A</span><span class="p">.</span><span class="n">y</span><span class="p">);</span>
    <span class="p">[</span><span class="n">poly</span> <span class="n">closeShape</span><span class="p">];</span>

    <span class="p">[</span><span class="n">circle</span> <span class="n">addShape</span><span class="o">:</span><span class="n">poly</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">circle</span><span class="p">];</span>
    <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>

    <span class="p">[</span><span class="n">self</span> <span class="n">setX</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setM</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setY</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setPQ</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setAngleBD</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setLabelPositions</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">addShapesToPoly</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setLabelPositions</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">addLabelsToPoly</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>There&#8217;s nothing new in the <tt>setThetaA:thetaB:</tt> method, just a copy and paste job without creating shapes.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">This method may <em>look</em> long, but really the device handles it very very very fast.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_rotate">11.2. rotate:</h3>
<div class="paragraph"><p>Compared to the <tt>adjust:</tt> method this one is a cinch. Since we added all the points, lines and labels to the polygon, and we added the polygon to the circle, all we have to do is rotate the circle.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">rotate:</span><span class="p">(</span><span class="n">UIPanGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="nv">gesture</span> <span class="p">{</span>
    <span class="n">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="p">[</span><span class="n">gesture</span> <span class="n">translationInView</span><span class="o">:</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">];</span>
    <span class="n">p</span><span class="p">.</span><span class="n">x</span> <span class="o">*=</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">width</span><span class="p">;</span>

    <span class="n">circle</span><span class="p">.</span><span class="n">rotation</span> <span class="o">+=</span> <span class="n">p</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
    <span class="p">[</span><span class="n">gesture</span> <span class="n">setTranslation</span><span class="o">:</span><span class="n">CGPointZero</span> <span class="n">inView</span><span class="o">:</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">];</span>

    <span class="n">lblA</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblB</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblC</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblD</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblM</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblP</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblQ</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblX</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
    <span class="n">lblY</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">circle</span><span class="p">.</span><span class="n">rotation</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Oh yeah, we also have to update the rotation of the labels so that they remain upright and readable.</p></div>
<div class="imageblock">
<div class="content">
<img src="trigonometry/trigonometry.png" alt="Rotated graph" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">12. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This was a complex example showing many levels of geometry, trigonometry and other tricks for adding shapes to shapes and adjusting everything. Our app is dynamic, fast and uses gesture recognition to provide the interaction. It took me a long time to get this one running smoothly, and I spent as much time cleaning up the code afterwards so that it flows better and makes sense.</p></div>
<div class="paragraph"><p>I hope the tutorial is clear, and concise and that by looking at the <a href="https://gist.github.com/C4Tutorials/5348431">code</a> that you&#8217;ve been able to easily follow along.</p></div>
<div class="paragraph"><p>Until Later.</p></div>
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
