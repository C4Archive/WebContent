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
<div id="header" class="span12">

<h2>Intro to Shapes</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Hi there.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_shapes">1. Creating Shapes</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create shapes&#8230;</p></div>
<div class="sect2">
<h3 id="_rectangles">1.1. Rectangles</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/rectangle.png" alt="Rectangle" height="600" />
</div>
<div class="title">Figure 1. Create a Rectangle</div>
</div>
<div class="paragraph"><p>The following will create and add a rectangle to the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
        <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">558</span><span class="p">,</span><span class="mi">200</span><span class="p">)];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example adds to the canvas a rectangle 558pts wide x 200pts high with an origin at {100,412}.</p></div>
<div class="sidebarblock">
<div class="content">
<div class="title">CGRect</div>
<div class="paragraph"><p>A common thing you will create in C4 is a CGRect. A CGRect contains a point {x,y} and a size {w,h} which define a rectangle, but is not actually a shape like you would add to the canvas. You create a CGRect like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGRect</span> <span class="n">newRect</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="n">x</span><span class="o">-</span><span class="n">position</span><span class="p">,</span> <span class="n">y</span><span class="o">-</span><span class="n">position</span><span class="p">,</span> <span class="n">width</span><span class="p">,</span> <span class="n">height</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>In iOS, the origin is in the upper-left corner and the rectangle extends towards the lower-right corner.</p></div>
</div></div>
</div>
<div class="sect2">
<h3 id="_ellipses">1.2. Ellipses</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/ellipse.png" alt="Create an Ellipse" height="600" />
</div>
<div class="title">Figure 2. Create an Ellipse</div>
</div>
<div class="paragraph"><p>The following will create and add an ellipse to the canvas, and fit it inside of the dimensions of a given rectangle.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
        <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">284</span><span class="p">,</span><span class="mi">412</span><span class="p">,</span><span class="mi">200</span><span class="p">,</span><span class="mi">200</span><span class="p">)];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example adds to the canvas an ellipse which is 200pts high x 200pts wide (a circle) with a center at {384,512} because the rectangle starts at {284,412}.</p></div>
</div>
<div class="sect2">
<h3 id="_lines">1.3. Lines</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/line.png" alt="Create a Line" height="600" />
</div>
<div class="title">Figure 3. Create a Line</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="nc">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">284</span><span class="p">,</span> <span class="mi">612</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">484</span><span class="p">,</span> <span class="mi">412</span><span class="p">)</span>
    <span class="p">};</span>

        <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example adds a line to the canvas from {284,612} to {484, 412}. In C4 we treat lines like polygons and specify their coordinates using a C-Array of CGPoints.</p></div>
</div>
<div class="sect2">
<h3 id="_triangles">1.4. Triangles</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/triangle.png" alt="Create a Triangle" height="600" />
</div>
<div class="title">Figure 4. Create a Triangle</div>
</div>
<div class="paragraph"><p>The following will create and add a triangle to the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
        <span class="nc">CGPoint</span> <span class="n">trianglePoints</span><span class="p">[</span><span class="mi">3</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
            <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">412</span><span class="p">),</span>
            <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">499</span><span class="p">,</span> <span class="mi">612</span><span class="p">),</span>
            <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">267</span><span class="p">,</span> <span class="mi">612</span><span class="p">)</span>
        <span class="p">};</span>

        <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">triangle:trianglePoints</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example adds to the canvas a triangle whose top point is at {384, 412}, right point is at {499,612}, and left point is at {267, 612}.</p></div>
</div>
<div class="sect2">
<h3 id="_polygons">1.5. Polygons</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/polygon.png" alt="Create a Polygon" height="600" />
</div>
<div class="title">Figure 5. Create a Polygon</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="nc">CGPoint</span> <span class="n">polygonPoints</span><span class="p">[</span><span class="mi">5</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">206</span><span class="p">,</span> <span class="mi">507</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">513</span><span class="p">,</span> <span class="mi">302</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">248</span><span class="p">,</span> <span class="mi">611</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">353</span><span class="p">,</span> <span class="mi">281</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">402</span><span class="p">,</span> <span class="mi">698</span><span class="p">)</span>
    <span class="p">};</span>

        <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">polygon:polygonPoints</span> <span class="n">pointCount:</span><span class="mi">5</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates a polygon from a given array of points. Creating a polygon is similar to creating a line, or a triangle, except you have to specify the number of points in the array.</p></div>
</div>
<div class="sect2">
<h3 id="_arcs">1.6. Arcs</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/arc.png" alt="Create an Arc" height="600" />
</div>
<div class="title">Figure 6. Create an Arc</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">arcWithCenter:</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">512</span><span class="p">)</span>
                                        <span class="n">radius:</span><span class="mi">150</span>
                                    <span class="n">startAngle:</span><span class="mi">0</span>
                                      <span class="n">endAngle:PI</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates an arc whose center point is at {384, 512}, having a radius of 150 (or diameter of 300), and shows up as a half-circle because it draws from 0 to PI (with PI being equal to 180 degrees).</p></div>
</div>
<div class="sect2">
<h3 id="_curves">1.7. Curves</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/curve.png" alt="Create a Curve" height="600" />
</div>
<div class="title">Figure 7. Create a Curve</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="nc">CGPoint</span> <span class="n">controlPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">100</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">668</span><span class="p">,</span> <span class="mi">100</span><span class="p">)</span>
    <span class="p">};</span>

    <span class="nc">CGPoint</span> <span class="n">beginEndPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">334</span><span class="p">,</span> <span class="mi">512</span><span class="p">),</span>
        <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">434</span><span class="p">,</span> <span class="mi">512</span><span class="p">)</span>
    <span class="p">};</span>

    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">curve:beginEndPoints</span> <span class="n">controlPoints:controlPoints</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates a basic bezier curve from {334,512} to {434,512} using {100,100} and {668, 100} as control points to define the bend between the beginning and end points.</p></div>
</div>
<div class="sect2">
<h3 id="_text">1.8. Text</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/text.png" alt="Create a Text Shape" height="600" />
</div>
<div class="title">Figure 8. Create a Text Shape</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">helvetica110</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;helvetica&quot;</span> <span class="n">size:</span><span class="mi">110</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">shapeFromString:</span><span class="s">@&quot;Some Text!&quot;</span> <span class="n">withFont:helvetica110</span><span class="p">];</span>
    <span class="n">newShape</span><span class="py">.origin</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">512</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newShape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates a shape from a string of text. Even though the letters are separate, C4 will treat them as a single shape.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_images">2. Creating Images</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create and add images to the canvas.</p></div>
<div class="sect2">
<h3 id="_single_image">2.1. Single Image</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/singleImage.png" alt="Create a Single Image" height="600" />
</div>
<div class="title">Figure 9. Create a Single Image</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">newImage</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;C4Sky.png&quot;</span><span class="p">];</span>
    <span class="n">newImage</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">512</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:newImage</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates and adds a single image to the canvas, it also moves the center of the image to the center of the canvas.</p></div>
</div>
<div class="sect2">
<h3 id="_multiple_images">2.2. Multiple Images</h3>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/twoImages.png" alt="Create Two Images" height="600" />
</div>
<div class="title">Figure 10. Create Two Images</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">newImage</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;C4Sky.png&quot;</span><span class="p">];</span>
    <span class="n">newImage</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">350</span><span class="p">);</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">anotherNewImage</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;C4Table.png&quot;</span><span class="p">];</span>
    <span class="n">anotherNewImage</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">675</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:newImage</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:anotherNewImage</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates and adds two images to the canvas.</p></div>
<div class="sidebarblock">
<div class="content">
<div class="title">Included Images</div>
<div class="paragraph"><p>Included in C4 are two default images, they can be found in the media folder, and are named:</p></div>
<div class="ulist"><ul>
<li>
<p>
C4Sky.png
</p>
</li>
<li>
<p>
C4Table.png
</p>
</li>
</ul></div>
<div class="paragraph"><p>You can use these or delete them from you project if you don&#8217;t want them hanging around.</p></div>
</div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_movies">3. Creating Movies</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create and add movies to the canvas. Adding movies is very similar to adding images.</p></div>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/movie.png" alt="Create a Movie" height="600" />
</div>
<div class="title">Figure 11. Create a Movie</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Movie</span> <span class="o">*</span><span class="n">newMovie</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Movie</span> <span class="n">movieNamed:</span><span class="s">@&quot;inception.mov&quot;</span><span class="p">];</span>
    <span class="n">newMovie</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span><span class="mi">512</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addMovie:newMovie</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates and adds a movie to the canvas, it also moves the center of the movie to the center of the canvas.</p></div>
<div class="sidebarblock">
<div class="content">
<div class="title">Autoplay / Autosize</div>
<div class="paragraph"><p>When you add a movie to the canvas it will play as soon as its loaded (unless you pause it first)&#8230;</p></div>
<div class="paragraph"><p>It will also present itself at the size of the original movie file.</p></div>
</div></div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_audio_samples">4. Creating Audio Samples</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create and add audio samples to your project.</p></div>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/audioSample.png" alt="Create an Audio Sample" height="600" />
</div>
<div class="title">Figure 12. Create an Audio Sample</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Sample</span> <span class="o">*</span><span class="n">newAudioSample</span><span class="p">;</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">newAudioSample</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Sample</span> <span class="n">sampleNamed:</span><span class="s">@&quot;C4Loop.aif&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="n">newAudioSample</span> <span class="n">prepareToPlay</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan:</span><span class="p">(</span><span class="nc">NSSet</span> <span class="o">*</span><span class="p">)</span><span class="n">touches</span> <span class="n">withEvent:</span><span class="p">(</span><span class="nc">UIEvent</span> <span class="o">*</span><span class="p">)</span><span class="n">event</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">newAudioSample</span> <span class="n">play</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example sets up the sample and prepares it by loading it into memory.</p></div>
<div class="paragraph"><p>It will play the sound when you touch the screen.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_opengl">5. Creating OpenGL</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create and add OpenGL to the canvas.</p></div>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/opengl.png" alt="Create OpenGL" height="600" />
</div>
<div class="title">Figure 13. Create OpenGL</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4GL</span> <span class="o">*</span><span class="n">newGL</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4GL</span> <span class="n">new</span><span class="p">];</span>
    <span class="n">newGL</span><span class="py">.frame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">84</span><span class="p">,</span> <span class="mi">312</span><span class="p">,</span> <span class="mi">600</span><span class="p">,</span> <span class="mi">400</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addGL:newGL</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example creates an OpenGL object and sets its visible frame.</p></div>
<div class="paragraph"><p>You can specify a custom renderer that you create, but if you don&#8217;t the C4 Logo will show up, drawn using GLLINES.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_text_labels">6. Creating Text Labels</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section shows you how to create and add text labels to the canvas.</p></div>
<div class="paragraph"><p>Labels are different than creating text shapes (i.e. using the shapeFromString: method in C4Shape) in that they actually work with text which can be selectable, edited, etc.. on the fly.</p></div>
<div class="imageblock">
<div class="content">
<img src="shapesIntro/label.png" alt="Creating a Text Label" height="600" />
</div>
<div class="title">Figure 14. Creating a Text Label</div>
</div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">newFont</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;helvetica&quot;</span> <span class="n">size:</span><span class="mi">75</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">newLabel</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot; This is a label. &quot;</span><span class="p">];</span>
    <span class="n">newLabel</span><span class="py">.font</span> <span class="o">=</span> <span class="n">newFont</span><span class="p">;</span>
    <span class="p">[</span><span class="n">newLabel</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">newLabel</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">384</span><span class="p">,</span> <span class="mi">512</span><span class="p">);</span>
    <span class="n">newLabel</span><span class="py">.backgroundColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
    <span class="n">newLabel</span><span class="py">.textColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:newLabel</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Setting up a label is a bit different than setting up a text shape, but you can do more in terms of displaying text and changing it than you can with shapes.</p></div>
<div class="paragraph"><p>If you&#8217;re just labelling things in your application then use this object to handle text for you.</p></div>
<div class="sidebarblock">
<div class="content">
<div class="title">sizeToFit</div>
<div class="paragraph"><p>Calling the sizeToFit method on a label will make sure the label&#8217;s background fits the entire amount of text written in the label. If you don&#8217;t call this, you might not ever see the text&#8230; or, any text that extends beyond the current background size of the label will be cut off and replaced with an ellipsis.</p></div>
</div></div>
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
