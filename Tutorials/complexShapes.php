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

<h2>Complex Shapes</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5391050" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Okay. So, you want to make a complex shape but the methods in C4 aren&#8217;t suitable for building things like &#8230; spirals, or other crazy shapes. You&#8217;re going to need to learn how to build paths from scratch. I&#8217;m going to introduce you to working with <tt>CGPathRef</tt> objects through building a spiral.</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapes.png" alt="Complex Shapes" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_get_a_spiral_8217_s_points">1. Get A Spiral&#8217;s Points</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to build spirals using a bit of polar coordinate math. In general, what you want to do is:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
set a radius
</p>
</li>
<li>
<p>
set an angle
</p>
</li>
<li>
<p>
get a point
</p>
</li>
<li>
<p>
do this for as many points as you want in your shape
</p>
</li>
</ol></div>
<div class="sect2">
<h3 id="_64_points">1.1. 64 Points</h3>
<div class="paragraph"><p>We&#8217;re going to construct a spiral out of 64 points, but before we make an actual shape let&#8217;s just create a bunch of little circles and put these on the screen to make sure our math is right.</p></div>
<div class="paragraph"><p>First, create a <tt>for</tt> loop with 64 iterations:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">64</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="c1">//spiral wizardry...</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Next, set the radius and angle for the current point (e.g. i):</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">radius</span> <span class="o">=</span> <span class="mi">4</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
<span class="nc">CGFloat</span> <span class="n">angle</span> <span class="o">=</span> <span class="n">TWO_PI</span> <span class="o">/</span> <span class="mi">16</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Then, calculate the current position using polar coordinate math:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">currentPosition</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:angle</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:angle</span><span class="p">]);</span>
<span class="n">currentPosition</span><span class="py">.x</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.center.x</span><span class="p">;</span>
<span class="n">currentPosition</span><span class="py">.y</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.center.y</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Finally, create a point shape, center it to the current position and add it to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">point</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">8</span><span class="p">,</span> <span class="mi">8</span><span class="p">)];</span>
<span class="n">point</span><span class="py">.center</span> <span class="o">=</span> <span class="n">currentPosition</span><span class="p">;</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:point</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>We get this image:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesPoints.png" alt="A Spiral of Points" />
</div>
</div>
<div class="paragraph"><p>C&#8217;est tout.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_try_a_polygon">2. Try A Polygon</h2>
<div class="sectionbody">
<div class="paragraph"><p>Okay, our points look good the 64 little ellipses we put on the canvas are all spiralled out. First thing to try is to build a polygon using those points. Why? Because its probably the easiest way to build a shape. So, let&#8217;s do it.</p></div>
<div class="paragraph"><p>If we simplify our code above into creating a set of points and then a polygon from those points we get:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="nc">CGPoint</span> <span class="n">polyPts</span><span class="p">[</span><span class="mi">64</span><span class="p">];</span>

    <span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">64</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="n">polyPts</span><span class="p">[</span><span class="n">i</span><span class="p">]</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">4</span><span class="o">*</span><span class="n">i</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:TWO_PI</span><span class="o">/</span><span class="mi">16</span><span class="o">*</span><span class="n">i</span><span class="p">],</span> <span class="mi">4</span><span class="o">*</span><span class="n">i</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:TWO_PI</span><span class="o">/</span><span class="mi">16</span><span class="o">*</span><span class="n">i</span><span class="p">]);</span>
        <span class="n">polyPts</span><span class="p">[</span><span class="n">i</span><span class="p">]</span><span class="py">.x</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.center.x</span><span class="p">;</span>
        <span class="n">polyPts</span><span class="p">[</span><span class="n">i</span><span class="p">]</span><span class="py">.y</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.center.y</span><span class="p">;</span>
    <span class="p">}</span>

    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">spiral</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">polygon:polyPts</span> <span class="n">pointCount:</span><span class="mi">64</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:spiral</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Which produces the following image:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesPolygon.png" alt="A Polygon Spiral" />
</div>
</div>
<div class="paragraph"><p>Now, this isn&#8217;t quite what we&#8217;re looking for. True, it <strong>is</strong> a spiral but it&#8217;s really square because we&#8217;re only drawing lines from one point to another. Ideally, what we&#8217;d like to have is a nice smooth curve between points as well.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_try_a_cgpath">3. Try A CGPath</h2>
<div class="sectionbody">
<div class="paragraph"><p>So, the polygon trick didn&#8217;t work. Let&#8217;s try building a path ourselves and see where that gets us.</p></div>
<div class="sect2">
<h3 id="_what_8217_s_that">3.1. What&#8217;s That???</h3>
<div class="paragraph"><p>A <tt>CGPath</tt> is an object that represents a graphics path, and it is defined as a part of Core Graphics (hence the CG). A graphics path is a mathematical description of a series of shapes or lines. Each figure in the graphics path is constructed with a connected set of lines and Bézier curves, called a subpath.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the API for <a href="https://developer.apple.com/library/ios/#documentation/graphicsimaging/Reference/CGPath/Reference/reference.html">CGPath</a>.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_build_a_mutable_path">3.2. Build A Mutable Path</h3>
<div class="paragraph"><p>As we iterate through our for loop we&#8217;re going to want to continuously add points to a path that we&#8217;re building. So, we&#8217;re going to use a <tt>CGMutablePathRef</tt> object because we want to change it with every step.</p></div>
<div class="paragraph"><p>Start by creating a path at the {0,0} point of the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="nc">CGMutablePathRef</span> <span class="n">spiralPath</span> <span class="o">=</span> <span class="nc">CGPathCreateMutable</span><span class="p">();</span>
    <span class="nc">CGPathMoveToPoint</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">);</span>
        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>See how we run <tt>CGPathMoveToPoint()</tt>? You&#8217;ll always have to do this to get the path to move to the point where you want to start its drawing. Always.</p></div>
</div>
<div class="sect2">
<h3 id="_populate_the_path">3.3. Populate The Path</h3>
<div class="paragraph"><p>Just like we did before, we&#8217;re going to use a <tt>for</tt> loop to populate the path. To do this, let&#8217;s first create the loop and calculate the <tt>radius</tt> and <tt>angle</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">64</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">CGFloat</span> <span class="n">radius</span> <span class="o">=</span> <span class="mi">4</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
    <span class="nc">CGFloat</span> <span class="n">angle</span> <span class="o">=</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">16</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Then, calculate the <tt>nextPoint</tt> that we&#8217;re going to add to the path:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">nextPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:angle</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:angle</span><span class="p">]);</span>
</pre></div></div></div>
<div class="paragraph"><p>Finally, add that point the the path:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>Wham. That&#8217;s it, all the 64 points we created for the images above will now be part of our path.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_make_a_shape">4. Make A Shape</h2>
<div class="sectionbody">
<div class="paragraph"><p>With our path in hand, let&#8217;s create a shape and the swap in our path. The first thing we want to do is create a shape using the frame of our path.</p></div>
<div class="paragraph"><p>Right after the <tt>for</tt> loop, grab the frame of the mutable path like this and use it to build a shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGRect</span> <span class="n">frame</span> <span class="o">=</span> <span class="nc">CGPathGetBoundingBox</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">);</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">spiral</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:frame</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>We&#8217;re using a <tt>rect</tt> here, but it doesn&#8217;t really matter (we could have also used an ellipse). It doesn&#8217;t matter what the shape actually is, what&#8217;s important is that the frame is the right size.</p></div>
<div class="sect2">
<h3 id="_set_the_path">4.1. Set The Path</h3>
<div class="paragraph"><p>Now that we have a shape with the right frame, let&#8217;s swap in our <tt>path</tt> element and add it to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">spiral</span><span class="py">.path</span> <span class="o">=</span> <span class="n">spiralPath</span><span class="p">;</span>
<span class="n">spiral</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:spiral</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>We get this:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesSpiralOffset.png" alt="A Spiral Path Offset" />
</div>
</div>
<div class="paragraph"><p>&#8230;which doesn&#8217;t look right! We expect the spiral to be centered right? Let&#8217;s change the background color of the shape to see what&#8217;s going on:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesSpiralOffsetBackground.png" alt="A Spiral Path Offset" />
</div>
</div>
<div class="paragraph"><p>&#8230;It looks like the spiral starts at the {0,0} point of the shape, which actually makes sense. We built the spiral starting at {0,0} and since we&#8217;re adding it to the shape it&#8217;s actually starting at the right point.</p></div>
</div>
<div class="sect2">
<h3 id="_translate_the_path">4.2. Translate The Path</h3>
<div class="paragraph"><p>To get the spiral path into the right position in the shape we&#8217;re going to apply a translation to the path. The translation will shift all the points so that they don&#8217;t start anymore at the {0,0}.</p></div>
<div class="paragraph"><p>Before setting the <tt>spiral.path = ...;</tt> add the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">const</span> <span class="nc">CGAffineTransform</span> <span class="n">translate</span> <span class="o">=</span> <span class="nc">CGAffineTransformMakeTranslation</span><span class="p">(</span><span class="o">-</span><span class="n">frame</span><span class="py">.origin.x</span><span class="p">,</span> <span class="o">-</span><span class="n">frame</span><span class="py">.origin.y</span><span class="p">);</span>
<span class="n">spiral</span><span class="py">.path</span> <span class="o">=</span> <span class="nc">CGPathCreateCopyByTransformingPath</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="o">&amp;</span><span class="n">translate</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>This builds a <tt>CGAffineTransform</tt> to the path and uses create a <strong>copy</strong> of the original <tt>spiralPath</tt> which is offset by the value of the translation.</p></div>
<div class="paragraph"><p>Now we get:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesSpiralCenteredBackground.png" alt="A Spiral Path Centered" />
</div>
</div>
<div class="paragraph"><p>&#8230;And, taking away the background color we get our spiral:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesSpiralCentered.png" alt="A Spiral Path Centered" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_uhhhhhhh_8230">4.3. Uhhhhhhh&#8230;</h3>
<div class="paragraph"><p>It&#8217;s pretty obvious that we&#8217;ve taken a lot of steps just to produce the same thing as calling <tt>[C4Shape polygon:...]</tt> which seems like a waste of time? Well, it isn&#8217;t. What we&#8217;ve done is exactly what the internals of the polygon method does when you create a shape this way, so you&#8217;ve learned a pretty complex way of making shapes.</p></div>
<div class="paragraph"><p>But! With our polygon we can actually make some modifications to our code and get some fancier shapes that we couldn&#8217;t get with any of the C4 methods.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_modifying_the_path">5. Modifying The Path</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are quite a few functions you can call to add points, lines and shapes to a <tt>CGMutablePathRef</tt> object. In the previous section we used this function:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>This simply adds a line from one point to another. What we really want to do, though is have a slight curve between points. To do this we&#8217;re going to use the following trick:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">nextPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:angle</span><span class="p">],</span> <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:angle</span><span class="p">]);</span>
<span class="nc">CGPoint</span> <span class="n">controlPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:angle</span> <span class="o">-</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">32</span><span class="p">],</span>
                                   <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:angle</span> <span class="o">-</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">32</span><span class="p">]);</span>
<span class="nc">CGPathAddQuadCurveToPoint</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">controlPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">controlPoint</span><span class="py">.y</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>What this does it create a quadratic curve between each of the 64 points. It does so by calculating a mid-point (e.g. the next point minus half a rotation) between the current and next points. Afterwards it uses this new point as a control for shaping the quadratic curve.</p></div>
<div class="paragraph"><p>What we get is:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapes.png" alt="A Smooth Spiral" />
</div>
</div>
<div class="paragraph"><p><a href="complexShapes/smooth.jpg">Smoooooooth</a>.</p></div>
<div class="sect2">
<h3 id="_alternatives">5.1. Alternatives</h3>
<div class="paragraph"><p>While I was making this tutorial I tried out a few alternatives to the smooth version of the spiral. If we modify the addition of points in the following ways we get a variety of different looks to our shape. The important thing to note is that all these variations actually create <em>single paths</em>.</p></div>
<div class="paragraph"><p>If we use the <tt>CGPathAddRelativeArc</tt> function like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddRelativeArc</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">,</span> <span class="n">radius</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">16</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>We get this shape:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesRelativeArc.png" alt="Spiral Relative Arcs" />
</div>
</div>
<div class="paragraph"><p>If we change the value <tt>0</tt> to that of <tt>angle</tt> like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddRelativeArc</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">,</span> <span class="n">radius</span><span class="p">,</span> <span class="n">angle</span><span class="p">,</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">16</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Then, we get this shape:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesRelativeArcAngle.png" alt="Spiral Relative Arcs" />
</div>
</div>
<div class="paragraph"><p>If we change the value of the <tt>nextPoint</tt> so that all the drawing happens around the {0,0} point, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddRelativeArc</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span> <span class="n">radius</span><span class="p">,</span> <span class="n">angle</span><span class="p">,</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">16</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>Then we get a saw-tooth look like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesRelativeArcZeroPoint.png" alt="Spiral Relative Arcs" />
</div>
</div>
<div class="paragraph"><p>Finally, if we take our smooth arc approach and modify the control point so that it&#8217;s way out compared to the current points, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">radius</span> <span class="o">*=</span> <span class="mf">1.5f</span><span class="p">;</span>
<span class="nc">CGPoint</span> <span class="n">controlPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">sin:angle</span> <span class="o">-</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">32</span><span class="p">],</span>
                                   <span class="n">radius</span><span class="o">*</span><span class="p">[</span><span class="n-ProjectClass">C4Math</span> <span class="n">cos:angle</span> <span class="o">-</span> <span class="n">TWO_PI</span><span class="o">/</span><span class="mi">32</span><span class="p">]);</span>
<span class="nc">CGPathAddQuadCurveToPoint</span><span class="p">(</span><span class="n">spiralPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="n">controlPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">controlPoint</span><span class="py">.y</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.x</span><span class="p">,</span> <span class="n">nextPoint</span><span class="py">.y</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;we get the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="complexShapes/complexShapesDistortedQuad.png" alt="Spiral Distorted Quad Curves" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">6. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This tutorial took you through the process of building your own path and then swapping that into a shape. Along the way you actually learned how C4 builds polygon shapes (including translating the path into the right position). From there you were able to use different <tt>CGPath</tt> functions to create variations on your shape.</p></div>
<div class="sect2">
<h3 id="_other_functions">6.1. Other Functions</h3>
<div class="paragraph"><p>Though this isn&#8217;t a full explanation of <tt>CGPath</tt> and all its functions, you&#8217;ve gotten enough of a taste to try things out on your own. Other path-building functions that you can play around with are:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPathAddArc</span>
<span class="nc">CGPathAddRelativeArc</span>
<span class="nc">CGPathAddArcToPoint</span>
<span class="nc">CGPathAddCurveToPoint</span>
<span class="nc">CGPathAddLines</span>
<span class="nc">CGPathAddLineToPoint</span>
<span class="nc">CGPathAddPath</span>
<span class="nc">CGPathAddQuadCurveToPoint</span>
<span class="nc">CGPathAddRect</span>
<span class="nc">CGPathAddRects</span>
<span class="nc">CGPathApply</span>
<span class="nc">CGPathMoveToPoint</span>
<span class="nc">CGPathCloseSubpath</span>
<span class="nc">CGPathAddEllipseInRect</span>
</pre></div></div></div>
<div class="paragraph"><p>Go Forth.</p></div>
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
