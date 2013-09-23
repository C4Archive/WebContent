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

<h2>Shapes In-depth</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial I&#8217;ll show you a ton of things you can do with <tt>C4Shape</tt> objects. All the things we&#8217;ll show are unique to shapes and consistent for <em>all</em> shapes. There&#8217;s tons of style properties, many of which are animatable.</p></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapes.png" alt="A Basic Circle" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_shapes">1. Shapes</h2>
<div class="sectionbody">
<div class="paragraph"><p>Shapes are objects. They have all the capabilities of touch, movement, animation, and notifications. But, really, deep-down they are views with <em>paths</em> inside of them. What does this mean? It means that shapes are malleable objects (<a href="#anchor-malleable">more on this later</a>).</p></div>
<div class="paragraph"><p>There are 10 types of shapes that you can create:</p></div>
<div class="sect2">
<h3 id="_ellipses">1.1. Ellipses</h3>
<div class="paragraph"><p>Circles and ovals can be created by specifying a <em>frame</em> into which the shape will be drawn. You build an ellipse like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Shape</span> <span class="o">*</span><span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">)];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeCircle.png" alt="A Circle" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_rectangles">1.2. Rectangles</h3>
<div class="paragraph"><p>Squares and rectangles can be created by specifying a <em>frame</em> into which the shape will be drawn. You build a rectangle like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Shape</span> <span class="o">*</span><span class="n">square</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">rect</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">)];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeRect.png" alt="A Square" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_lines">1.3. Lines</h3>
<div class="paragraph"><p>Lines are polygons that can be created by specifying 2 points. You build a line like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">line</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">line</span><span class="o">:</span><span class="n">linePoints</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeLine.png" alt="A line" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_triangles">1.4. Triangles</h3>
<div class="paragraph"><p>Triangles are polygons that can be created by specifying 3 points. You build a triangle like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">trianglePoints</span><span class="p">[</span><span class="mi">3</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">triangle</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">triangle</span><span class="o">:</span><span class="n">trianglePoints</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeTriangle.png" alt="A Triangle" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_polygons">1.5. Polygons</h3>
<div class="paragraph"><p>Polygons can be created by specifying any number of points. You build a polygon like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">polygonPoints</span><span class="p">[</span><span class="n">n</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),...,...,</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">polygon</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">polygon</span><span class="o">:</span><span class="n">polygonPoints</span> <span class="n">pointCount</span><span class="o">:</span><span class="n">n</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePolygon.png" alt="A Polygon" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">Check out this <a href="https://gist.github.com/C4Tutorials/5358388">gist</a></td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_arcs">1.6. Arcs</h3>
<div class="paragraph"><p>Arcs can be thought of portions of circles, rotated around a center point. An arc&#8217;s shape doesn&#8217;t include the center point. The angles you specify for building an arc are measured in radians. You can also specify whether it draws clockwise or counter-clockwise. You can build an arc like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Shape</span> <span class="o">*</span><span class="n">arc</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">arcWithCenter</span><span class="o">:</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span> <span class="n">radius</span><span class="o">:</span><span class="mi">192</span> <span class="n">startAngle</span><span class="o">:</span><span class="mi">0</span> <span class="n">endAngle</span><span class="o">:</span><span class="n">PI</span> <span class="n">clockwise</span><span class="o">:</span><span class="nb">NO</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeArc.png" alt="An arc" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_wedges">1.7. Wedges</h3>
<div class="paragraph"><p>Wedges can be thought of as portions of circles, like pie-slices, they include the center point in their shape. The angles you specify for building an wedge are measured in radians. You can also specify whether it draws clockwise or counter-clockwise. You can build an wedge like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Shape</span> <span class="o">*</span><span class="n">wedge</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">wedgeWithCenter</span><span class="o">:</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span> <span class="n">radius</span><span class="o">:</span><span class="mi">192</span> <span class="n">startAngle</span><span class="o">:</span><span class="mi">0</span> <span class="n">endAngle</span><span class="o">:</span><span class="n">PI</span> <span class="n">clockwise</span><span class="o">:</span><span class="nb">NO</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeWedge.png" alt="A Wedge" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_curves">1.8. Curves</h3>
<div class="paragraph"><p>Curves can be thought of as lines bent around two control points. To create an arc you must specify 2 end points, and 2 control points. You can build a curve like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">endPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">CGPoint</span> <span class="n">ctlPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">curve</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">curve</span><span class="o">:</span><span class="n">endPoints</span> <span class="n">controlPoints</span><span class="o">:</span><span class="n">ctlPoints</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeCurve.png" alt="A Bezier Curve" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_quadratic_curves">1.9. Quadratic Curves</h3>
<div class="paragraph"><p>Curves can be thought of as lines bent towards a single control point. To create an arc you must specify 2 end points, and 1 control point. You can build a quadratic curve like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">endPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">),</span><span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">)};</span>
<span class="n">CGPoint</span> <span class="n">ctlPoint</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">);</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">quadCurve</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">quadCurve</span><span class="o">:</span><span class="n">endPoints</span> <span class="n">controlPoint</span><span class="o">:</span><span class="n">ctlPoint</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeCurve.png" alt="A Quadratic Curve" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_text_shapes">1.10. Text Shapes</h3>
<div class="paragraph"><p>Yes. Yes, you can. Text shapes can be built from a string and a font.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Font</span> <span class="n">fontWithName</span><span class="o">:</span><span class="s">@&quot;AvenirNext-Heavy&quot;</span> <span class="n">size</span><span class="o">:</span><span class="mi">240</span><span class="p">];</span>
<span class="n">C4Shape</span> <span class="o">*</span><span class="n">text</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">shapeFromString</span><span class="o">:</span><span class="s">@&quot;TEXT&quot;</span> <span class="n">withFont</span><span class="o">:</span><span class="n">font</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapeText.png" alt="A text shape" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">You can get font names from <a href="http://www.iosfonts.com">iosfonts.com</a></td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="anchor-malleable">2. Malleable Shapes</h2>
<div class="sectionbody">
<div class="paragraph"><p>A really powerful characteristic of shapes is that they can change dynamically. At any point in your application a shape can change from one kind to another. The reason for this is that shapes are actually built from 3 components: a view (i.e. a <tt>C4Control</tt>), a <tt>C4ShapeLayer</tt> and a <tt>UIBezierPath</tt>. When you create a shape you&#8217;re actually building all three components, the only difference is in the form of the <tt>UIBezierPath</tt>. You can actually specify the path of a shape and it will transform.</p></div>
<div class="sect2">
<h3 id="_boring">2.1. BORING!</h3>
<div class="paragraph"><p>Right, so the details above are boring.</p></div>
</div>
<div class="sect2">
<h3 id="_changing_on_the_fly">2.2. Changing on the Fly</h3>
<div class="paragraph"><p>What the above stuff really means is that you can do something like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesBegan</span> <span class="p">{</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">animationDuration</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
        <span class="p">[</span><span class="n">circle</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">368</span><span class="p">,</span><span class="mi">512</span><span class="p">)];</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/pathAnimation.png" alt="Animating a Path" />
</div>
</div>
<div class="paragraph"><p>The above code will animate a circle to an ellipse of a different size.</p></div>
<div class="paragraph"><p>But wait!!! There&#8217;s more:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">circle</span> <span class="n">rect</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">line</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">triangle</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">polygon</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">arc</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">wedge</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">curve</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">quadCurve</span><span class="o">:</span><span class="p">...];</span>
<span class="p">[</span><span class="n">circle</span> <span class="n">textShape</span><span class="o">:</span><span class="p">...];</span>
</pre></div></div></div>
<div class="paragraph"><p>All of those will do the same thing <strong>change from one shape to the other</strong>.</p></div>
<div class="paragraph"><p>If you set the <tt>animationDuration</tt> before calling any of the above methods, the transition will actually <strong>animate</strong>!</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">If you didn&#8217;t catch it above, the trick is NOT to say <tt>circle = [C4Shape ...]</tt> but rather to call the method directly on the object itself, like this: <tt>[circle rect:...]</tt></td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_properties_animatable">3. Properties (Animatable)</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are a ton of <em>animatable</em> properties for shapes&#8230; just about all of them. In this tutorial we won&#8217;t show you how to trigger the animations. You can set and adjust the following properties:</p></div>
<div class="sect2">
<h3 id="_fillcolor">3.1. fillColor</h3>
<div class="paragraph"><p>The internal color of a shape.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesFillColor.png" alt="Changing the fillColor" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_strokecolor">3.2. strokeColor</h3>
<div class="paragraph"><p>The color of a shape&#8217;s outline:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">strokeColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesStrokeColor.png" alt="Changing the strokeColor" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_linewidth">3.3. lineWidth</h3>
<div class="paragraph"><p>The line width specifies how thick the outline of a shape will be:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">lineWidth</span> <span class="o">=</span> <span class="mf">50.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesLineWidth.png" alt="Changing the lineWidth" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_strokeend">3.4. strokeEnd</h3>
<div class="paragraph"><p>The strokeEnd property is a measure of <em>where the end of a shape&#8217;s line occurs</em> with 1.0 being the very end of the shape, and 0.0 being the very beginning of the shape&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">strokeEnd</span> <span class="o">=</span> <span class="mf">0.66f</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesStrokeEnd.png" alt="Changing the strokeEnd" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">For all shapes, <tt>strokeEnd</tt> defaults to 1.0f</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_strokestart">3.5. strokeStart</h3>
<div class="paragraph"><p>The strokeStart property is a measure of <em>where the beginning of a shape&#8217;s line occurs</em> with 1.0 being the very end of the shape, and 0.0 being the very beginning of the shape&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">strokeStart</span> <span class="o">=</span> <span class="mf">0.33f</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesStrokeStart.png" alt="Changing the strokeStart" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">For all shapes, <tt>strokeStart</tt> defaults to 1.0f</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_end_points">3.6. End Points</h3>
<div class="paragraph"><p>If your shape is a line or a curve you can dynamically update the end points of the shape.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">line</span><span class="p">.</span><span class="n">pointA</span> <span class="o">=</span> <span class="p">...;</span>
<span class="n">curve</span><span class="p">.</span><span class="n">pointB</span> <span class="o">=</span> <span class="p">...;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_control_points">3.7. Control Points</h3>
<div class="paragraph"><p>If your shape is a curve, you can dynamically update the control point (or points) of the shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">quadCurve</span><span class="p">.</span><span class="n">pointA</span> <span class="o">=</span> <span class="p">...;</span> <span class="c1">//both quad and bezier curves</span>
<span class="n">curve</span><span class="p">.</span><span class="n">pointB</span> <span class="o">=</span> <span class="p">...;</span>     <span class="c1">//bezier curves only</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_path">3.8. Path</h3>
<div class="paragraph"><p>When you&#8217;ve got the chops, you&#8217;ll be able to actually create a <tt>CGPathRef</tt> on your own and set is as the path for a shape you&#8217;ve already created.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">path</span> <span class="o">=</span> <span class="n">path</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>You can also grab the path from a shape to share it or manipulate it or use it for other magic.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPathRef</span> <span class="n">aPath</span> <span class="o">=</span> <span class="n">shape</span><span class="p">.</span><span class="n">path</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>SNAAAAAAAAAP!</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_properties_non_animatable">4. Properties (Non-Animatable)</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are a ton of <em>non-animatable</em> properties for shapes&#8230; You can set and adjust the following properties:</p></div>
<div class="sect2">
<h3 id="_fillrule">4.1. fillRule</h3>
<div class="paragraph"><p>The fillRule property specifies how a shape with a winding path will fill its color.</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>FILLNORMAL</tt> is the default <tt>fillRule</tt> mode
</p>
</li>
<li>
<p>
<tt>FILLEVENODD</tt> will fill every <em>other</em> space of overlap
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">fillRule</span> <span class="o">=</span> <span class="n">FILLNORMAL</span><span class="p">;</span> <span class="c1">//Default</span>
<span class="n">shape</span><span class="p">.</span><span class="n">fillRule</span> <span class="o">=</span> <span class="n">FILLEVENODD</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesFillRule.png" alt="FILLNORMAL, FILLEVENODD" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_linejoin">4.2. lineJoin</h3>
<div class="paragraph"><p>The lineJoin property specifies how the shape between segments of a line will appear.</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>JOINMITER</tt> is the default lineJoin mode, it creates a point between line segments.
</p>
</li>
<li>
<p>
<tt>JOINBEVEL</tt> creates a squared-off angle between line segments.
</p>
</li>
<li>
<p>
<tt>JOINROUND</tt> creates a rounded angle between line segments.
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">lineJoin</span> <span class="o">=</span> <span class="n">JOINMITER</span><span class="p">;</span> <span class="c1">//Default</span>
<span class="n">shape</span><span class="p">.</span><span class="n">lineJoin</span> <span class="o">=</span> <span class="n">JOINBEVEL</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">lineJoin</span> <span class="o">=</span> <span class="n">JOINROUND</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesLineJoin.png" alt="JOINMITER, JOINBEVEL, JOINROUND" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_linecap">4.3. lineCap</h3>
<div class="paragraph"><p>The lineCap property specifies how the ends of lines will appear. You can specify the lineCap style like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">lineCap</span> <span class="o">=</span> <span class="n">CAPBUTT</span><span class="p">;</span>
<span class="n">newShape</span><span class="p">.</span><span class="n">lineCap</span> <span class="o">=</span> <span class="n">CAPROUND</span><span class="p">;</span>
<span class="n">newShape</span><span class="p">.</span><span class="n">lineCap</span> <span class="o">=</span> <span class="n">CAPSQUARE</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesLineCap.png" alt="CAPBUTT, CAPROUND, CAPSQUARE" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_linedashpattern">4.4. lineDashPattern</h3>
<div class="paragraph"><p>The lineDashPattern specifies the repeating pattern of dashes and spaces for a line.</p></div>
<div class="paragraph"><p>The way you order numbers in the pattern always has the form dash-gap-&#8230;, meaning that the first number you enter in the pattern will be the size of the first dash, the second will be the size of the first gap, and so on&#8230;</p></div>
<div class="paragraph"><p>To set the lineDashPattern, you need to specify an NSArray of number.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">NSArray</span> <span class="o">*</span><span class="n">pattern</span> <span class="o">=</span> <span class="err">@</span><span class="p">[</span><span class="err">@</span><span class="p">(</span><span class="mi">10</span><span class="p">),</span><span class="err">@</span><span class="p">(</span><span class="mi">20</span><span class="p">),</span><span class="err">@</span><span class="p">(</span><span class="mi">30</span><span class="p">),</span><span class="err">@</span><span class="p">(</span><span class="mi">40</span><span class="p">)];</span>
<span class="n">newShape</span><span class="p">.</span><span class="n">lineDashPattern</span> <span class="o">=</span> <span class="n">pattern</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="shapesInDepth/shapePropertiesDashPattern.png" alt="Setting the lineDashPattern" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">NSArrays &amp; NSNumbers</div>NSArrays can only take <strong>objects</strong> and cannot take normal float values, so we have to create special NSNumber objects with the values we want for the line pattern. We do this in short-hand by wrapping <tt>CGFloat</tt> or <tt>NSInteger</tt> values in <tt>@()</tt>. To create an array we use the short-hand <tt>@[]</tt> with a bunch of number values separated by commas.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_what_am_i">5. What am I?</h2>
<div class="sectionbody">
<div class="paragraph"><p>If you&#8217;ve been morphing shapes, and want to know what the shape is at a given moment, you can use the following to check:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">isLine</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">isArc</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">isWedge</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">isBezierCurve</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">isQuadCurve</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">isClosed</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Calling any one of the properties mentioned here will return either <tt>YES</tt> or <tt>NO</tt>.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_a_note_about_colors">6. A Note About Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial, we used three preset colors: <tt>C4RED</tt>, <tt>C4GREY</tt>, <tt>C4BLUE</tt></p></div>
<div class="paragraph"><p>The default style for shapes is:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4GREY</span><span class="p">;</span>
<span class="n">strokeColor</span> <span class="o">=</span> <span class="n">C4BLUE</span><span class="p">;</span>
<span class="n">lineWidth</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Wherever you see any of these three colors you can replace them with a <tt>UIColor</tt> instance.</p></div>
<div class="sect2">
<h3 id="_preset_uicolors">6.1. Preset UIColors</h3>
<div class="paragraph"><p>You can get preset UIColor objects:</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>blackColor</tt>
</p>
</li>
<li>
<p>
<tt>darkGrayColor</tt>
</p>
</li>
<li>
<p>
<tt>lightGrayColor</tt>
</p>
</li>
<li>
<p>
<tt>whiteColor</tt>
</p>
</li>
<li>
<p>
<tt>grayColor</tt>
</p>
</li>
<li>
<p>
<tt>redColor</tt>
</p>
</li>
<li>
<p>
<tt>greenColor</tt>
</p>
</li>
<li>
<p>
<tt>blueColor</tt>
</p>
</li>
<li>
<p>
<tt>cyanColor</tt>
</p>
</li>
<li>
<p>
<tt>yellowColor</tt>
</p>
</li>
<li>
<p>
<tt>magentaColor</tt>
</p>
</li>
<li>
<p>
<tt>orangeColor</tt>
</p>
</li>
<li>
<p>
<tt>purpleColor</tt>
</p>
</li>
<li>
<p>
<tt>brownColor</tt>
</p>
</li>
<li>
<p>
<tt>clearColor</tt>
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">magentaColor</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_custom_uicolors">6.2. Custom UIColors</h3>
<div class="paragraph"><p>You can create custom <tt>RGB</tt> colors using <tt>UIColor</tt> in the following way:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">newShape</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">colorWithRed</span><span class="o">:</span><span class="mf">0.0</span> <span class="n">green</span><span class="o">:</span><span class="mf">1.0</span> <span class="n">blue</span><span class="o">:</span><span class="mf">0.0</span> <span class="n">alpha</span><span class="o">:</span><span class="mf">1.0</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>The above code creates an opaque (i.e. no transparency / alpha) green color.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">For an in-depth look at colors, check the <a href="/examples/colorsInDepth.png">Colors In-Depth Tutorial</a></td>
</tr></table>
</div>
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
