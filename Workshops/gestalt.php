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

<h2>Gestalt</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Code/5522440" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>These are notes for the first session of <em>C4: Media &amp; Interactivity</em> workshop at <a href="http://www.viviomediaarts.com">VIVO</a> starting May 6th, 2013.</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/gestaltHeader.png" alt="Four Gestalt Images" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_gestalt">1. Gestalt</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first session of this 4-night workshop will have you explore basic concepts of working with shapes (i.e. media objects) through the creation of gestalt imagery. We will build shapes, add them to the canvas, and work in various ways to create visual effects using circles, lines and polygons.</p></div>
<div class="paragraph"><p>This session will also be a general introduction to C4.</p></div>
<div class="sect2">
<h3 id="_what_you_will_come_away_with">1.1. What You Will Come Away With</h3>
<div class="paragraph"><p>By the end of this session you will learn how to:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
build, run and share code.
</p>
</li>
<li>
<p>
find code online, and where to ask questions
</p>
</li>
<li>
<p>
read examples and tutorials.
</p>
</li>
<li>
<p>
add shapes to the canvas
</p>
</li>
<li>
<p>
style shapes
</p>
</li>
<li>
<p>
position shapes
</p>
</li>
<li>
<p>
create complex shapes
</p>
</li>
<li>
<p>
work with a variety of different types of shapes
</p>
</li>
<li>
<p>
create basic animations
</p>
</li>
<li>
<p>
set default styles for shapes
</p>
</li>
</ol></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_basics_to_remember">2. Basics to Remember</h2>
<div class="sectionbody">
<div class="paragraph"><p>Here are a few  important things you might need to remember:</p></div>
<div class="sect2">
<h3 id="_add_to_the_canvas">2.1. Add to the Canvas</h3>
<div class="paragraph"><p>Here&#8217;s how you add a shape to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:aShape</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_add_to_a_shape">2.2. Add to a Shape</h3>
<div class="paragraph"><p>Here&#8217;s how you add a shape to a shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">mainShape</span> <span class="n">addShape:anotherShape</span><span class="p">]</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_adding_lots_to_the_canvas">2.3. Adding Lots to the Canvas</h3>
<div class="paragraph"><p>To add a bunch of objects to the canvas at the same time, you can put them in an array like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addObjects:</span><span class="err">@</span><span class="p">[</span><span class="n">shape1</span><span class="p">,</span><span class="n">shape2</span><span class="p">,</span><span class="n">shape3</span><span class="p">]];</span>
</pre></div></div></div>
<div class="paragraph"><p>The <tt>@[...]</tt> syntax is shorthand for making an array of objects.</p></div>
</div>
<div class="sect2">
<h3 id="_create_a_point">2.4. Create a Point</h3>
<div class="paragraph"><p>Here&#8217;s how you create a <tt>CGPoint</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">);</span> <span class="c1">//x,y coordinates</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_change_position">2.5. Change Position</h3>
<div class="paragraph"><p>Here are 2 ways to change the position of an object:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">);</span>
<span class="n">obj</span><span class="py">.center</span> <span class="o">=</span> <span class="n">p</span><span class="p">;</span>
<span class="n">obj</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">p</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_create_a_predefined_color">2.6. Create a Predefined Color</h3>
<div class="paragraph"><p>There are many predefined colors, here&#8217;s how to create a few of them:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIColor</span> <span class="o">*</span><span class="n">red</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">redColor</span><span class="p">];</span>
<span class="nc">UIColor</span> <span class="o">*</span><span class="n">blue</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">blueColor</span><span class="p">];</span>
<span class="nc">UIColor</span> <span class="o">*</span><span class="n">green</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">greenColor</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Have a look at the <a href="/tutorials/colorsInDepth.php">Colors In-Depth</a> tutorial for more.</p></div>
</div>
<div class="sect2">
<h3 id="_create_a_custom_color">2.7. Create a Custom Color</h3>
<div class="paragraph"><p>Here&#8217;s how you create a custom color:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIColor</span> <span class="o">*</span><span class="n">c</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithRed:</span><span class="mf">1.0f</span> <span class="n">green:</span><span class="mf">0.5f</span> <span class="n">blue:</span><span class="mf">0.25f</span> <span class="n">alpha:</span><span class="mf">1.0f</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Have a look at the <a href="/tutorials/colorsInDepth.php">Colors In-Depth</a> tutorial for more.</p></div>
</div>
<div class="sect2">
<h3 id="_3_c4_colors">2.8. 3 C4 Colors</h3>
<div class="paragraph"><p>You can use the following just like regular <tt>UIColor</tt> objects:</p></div>
<div class="ulist"><ul>
<li>
<p>
C4RED
</p>
</li>
<li>
<p>
C4BLUE
</p>
</li>
<li>
<p>
C4GREY
</p>
</li>
</ul></div>
</div>
<div class="sect2">
<h3 id="_change_the_fill_color">2.9. Change the Fill Color</h3>
<div class="paragraph"><p>To change the <tt>fillColor</tt> of a shape, do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_change_the_stroke_color">2.10. Change the Stroke Color</h3>
<div class="paragraph"><p>To change the <tt>strokeColor</tt> of a shape, do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_points_for_polygons">2.11. Points for Polygons</h3>
<div class="paragraph"><p>For lines, triangles and polygons, this is how you create a set of points:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">points</span><span class="p">[</span><span class="mi">3</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">),</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span><span class="mi">10</span><span class="p">),</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">50</span><span class="p">,</span><span class="mi">60</span><span class="p">)};</span>
</pre></div></div></div>
<div class="paragraph"><p>Remember, you can change the number of points (i.e. <tt>3</tt>) to any number, but when you do you should have the same amount of <tt>CGPointMake</tt> calls in between the <tt>{</tt> and <tt>}</tt> brackets.</p></div>
</div>
<div class="sect2">
<h3 id="_changing_default_styles">2.12. Changing Default Styles</h3>
<div class="paragraph"><p>You can change the default style of shapes like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">defaultStyle</span><span class="p">]</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>You can change other default styles in the same way! But remember to do this before creating your objects.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_triangle_polygon">3. Triangle (Polygon)</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to create the look of a white triangle that sits inside 3 circles. It looks like:</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/trianglePolygon.png" alt="Triangle with Polygons" />
</div>
</div>
<div class="paragraph"><p>The easiest way to create this effect is to place a triangle over top of 3 circles.</p></div>
<div class="sect2">
<h3 id="_three_circles">3.1. Three Circles</h3>
<div class="paragraph"><p>First things first. Create 3 circles:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">circle1</span><span class="p">,</span> <span class="o">*</span><span class="n">circle2</span><span class="p">,</span> <span class="o">*</span><span class="n">circle3</span><span class="p">;</span>

<span class="n">circle1</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">192</span><span class="p">)];</span>
<span class="n">circle2</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">192</span><span class="p">)];</span>
<span class="n">circle3</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">192</span><span class="p">)];</span>
</pre></div></div></div>
<div class="paragraph"><p>If you do nothing to the shapes, there will be a blue line around all of them. This is the default style for all shapes (i.e. a 5pt blue line). To get rid of this, we can set the default style for shapes like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">defaultStyle</span><span class="p">]</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Add that line <em>before</em> you create the three circles.</p></div>
</div>
<div class="sect2">
<h3 id="_position_their_centers">3.2. Position Their Centers</h3>
<div class="paragraph"><p>The next step is to position the three circles in the proper positions. We do this by setting the <tt>center</tt> positions of each object.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">circle1</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span> <span class="o">-</span> <span class="mi">110</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">+</span> <span class="mi">75</span><span class="p">);</span>
<span class="n">circle2</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span> <span class="o">+</span> <span class="mi">110</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">+</span> <span class="mi">75</span><span class="p">);</span>
<span class="n">circle3</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">-</span> <span class="mi">120</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>You&#8217;ll notice that we&#8217;re positioning based on the <tt>center</tt> of the canvas. The additional numbers (i.e. -110, +75, etc) I guessed and tweaked until the positions were right. This puts the center of the space between all three circles pretty close to the center of the screen of the device you&#8217;re using.</p></div>
</div>
<div class="sect2">
<h3 id="_create_a_triangle">3.3. Create A Triangle</h3>
<div class="paragraph"><p>You need to specify 3 points to create a triangle, to align the triangle with the circles all you need to do is use the positions of the centers of each shape like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">trianglePoints</span><span class="p">[</span><span class="mi">3</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
    <span class="n">circle1</span><span class="py">.center</span><span class="p">,</span>
    <span class="n">circle2</span><span class="py">.center</span><span class="p">,</span>
    <span class="n">circle3</span><span class="py">.center</span>
<span class="p">};</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">triangle</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">triangle:trianglePoints</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_fill_it">3.4. Fill It!</h3>
<div class="paragraph"><p>You now need to change the <tt>fillColor</tt> of the triangle so that it matches the white background of the app.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">triangle</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">whiteColor</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_add_them_all">3.5. Add Them All!</h3>
<div class="paragraph"><p>Finally, add all the shapes to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addObjects:</span><span class="err">@</span><span class="p">[</span><span class="n">circle1</span><span class="p">,</span> <span class="n">circle2</span><span class="p">,</span> <span class="n">circle3</span><span class="p">,</span> <span class="n">triangle</span><span class="p">]];</span>
</pre></div></div></div>
<div class="paragraph"><p>Finito.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_triangle_wedges">4. Triangle (Wedges)</h2>
<div class="sectionbody">
<div class="paragraph"><p>The other way to create the effect of a triangle sitting in 3 circles is to create wedges that are aligned.</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/triangleWedges.png" alt="A Triangle of Wedges" />
</div>
</div>
<div class="sect2">
<h3 id="_create_3_wedges">4.1. Create 3 Wedges</h3>
<div class="paragraph"><p>Creating a wedge is a little different than creating a circle&#8230; Instead of specifying a frame you have to give it a bunch of parameters and it constructs the shape for you. First, set up the default style and create references for the 3 wedges:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">defaultStyle</span><span class="p">]</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">wedge1</span><span class="p">,</span> <span class="o">*</span><span class="n">wedge2</span><span class="p">,</span> <span class="o">*</span><span class="n">wedge3</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_3_angles">4.2. 3 Angles</h3>
<div class="paragraph"><p>Creating a wedge requires the following:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
a center point
</p>
</li>
<li>
<p>
a radius
</p>
</li>
<li>
<p>
a start angle
</p>
</li>
<li>
<p>
an end angle
</p>
</li>
<li>
<p>
flag for drawing clockwise or counterclockwise
</p>
</li>
</ol></div>
<div class="paragraph"><p>We&#8217;re going to draw 3 wedges that have different start / end angles each time we do we&#8217;re going to update a <tt>CGPoint</tt> that we call <tt>currentCenter</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">currentCenter</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span> <span class="o">-</span> <span class="mi">110</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">+</span> <span class="mi">75</span><span class="p">);</span>
<span class="n">wedge1</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">wedgeWithCenter:currentCenter</span>
                           <span class="n">radius:</span><span class="mi">96</span>
                       <span class="n">startAngle:</span><span class="mi">0</span>
                         <span class="n">endAngle:TWO_PI</span> <span class="o">*</span> <span class="mi">5</span><span class="o">/</span><span class="mi">6</span>
                        <span class="n">clockwise:</span><span class="nb">YES</span><span class="p">];</span>

<span class="n">currentCenter</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span> <span class="o">+</span> <span class="mi">110</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">+</span> <span class="mi">75</span><span class="p">);</span>
<span class="n">wedge2</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">wedgeWithCenter:currentCenter</span>
                           <span class="n">radius:</span><span class="mi">96</span>
                       <span class="n">startAngle:TWO_PI</span> <span class="o">*</span> <span class="mi">4</span><span class="o">/</span><span class="mi">6</span>
                         <span class="n">endAngle:PI</span>
                        <span class="n">clockwise:</span><span class="nb">YES</span><span class="p">];</span>

<span class="n">currentCenter</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span> <span class="o">-</span> <span class="mi">120</span><span class="p">);</span>
<span class="n">wedge3</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">wedgeWithCenter:currentCenter</span>
                           <span class="n">radius:</span><span class="mi">96</span>
                       <span class="n">startAngle:TWO_PI</span><span class="o">*</span><span class="mi">2</span><span class="o">/</span><span class="mi">6</span>
                         <span class="n">endAngle:TWO_PI</span><span class="o">/</span><span class="mi">6</span>
                        <span class="n">clockwise:</span><span class="nb">YES</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Notice? We used the same points as in the previous example.</p></div>
</div>
<div class="sect2">
<h3 id="_add_them">4.3. Add Them!</h3>
<div class="paragraph"><p>Add all the wedges to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addObjects:</span><span class="err">@</span><span class="p">[</span><span class="n">wedge1</span><span class="p">,</span><span class="n">wedge2</span><span class="p">,</span><span class="n">wedge3</span><span class="p">]];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cube_1">5. Cube 1</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to create a shape that looks like a wireframe cube seen from an angle. A pretty classic drawing:</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/cube1.png" alt="Cube 1" />
</div>
</div>
<div class="sect2">
<h3 id="anchor-cube">5.1. The Cube Shape</h3>
<div class="paragraph"><p>To do this we&#8217;re going to start by creating a shape that represents the background of the cube. This outline has 6 points on it, so we&#8217;re going to build the shape as a polygon:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">6</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">0</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">200</span><span class="p">,</span> <span class="mi">50</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">200</span><span class="p">,</span> <span class="mi">164</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">214</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">164</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">50</span><span class="p">)</span>
<span class="p">};</span>

<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">cubeOutline</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">polygon:cubePolyPoints</span> <span class="n">pointCount:</span><span class="mi">6</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">closeShape</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../../images//icons/note.png" alt="Note" />
</td>
<td class="content">We didn&#8217;t draw the shape in the center of the canvas. We&#8217;ll shift the whole thing over later&#8230;</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_draw_the_lines">5.2. Draw the Lines</h3>
<div class="paragraph"><p>This image actually draws 3 lines that cross at the center of the shape. Lines are simply 2-point polygons, so we&#8217;re going to make them in sort of the same way as we did the background shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">0</span><span class="p">],</span><span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">3</span><span class="p">]};</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">line1</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
<span class="n">linePoints</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">line2</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
<span class="n">linePoints</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">5</span><span class="p">];</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">line3</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../../images//icons/note.png" alt="Note" />
</td>
<td class="content">We <em>reuse</em> the array of points&#8230; This makes our code cleaner and easier to read.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_add_them_2">5.3. Add Them!</h3>
<div class="paragraph"><p>Now, we&#8217;re going to do something tricky here. You&#8217;ve already seen that we can add things to the canvas&#8230; But! Shapes can actually be added <em>to other shapes</em>!!! When we do this we&#8217;re going to be able to shift everything to the center as one object.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addObjects:</span><span class="err">@</span><span class="p">[</span><span class="n">line1</span><span class="p">,</span><span class="n">line2</span><span class="p">,</span><span class="n">line3</span><span class="p">]];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_shift_add">5.4. Shift, Add.</h3>
<div class="paragraph"><p>Finally, shift the position of the cube to the center of the canvas and add it to the screen.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">cubeOutline</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">[</span><span class="k">self</span> <span class="n">addShape:cubeOutline</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cube_2">6. Cube 2</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to draw <em>another</em> cube that looks a bit shifted in terms of its perspective. Another classic image.</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/cube2.png" alt="Cube 2" />
</div>
</div>
<div class="paragraph"><p>This is definitely trickier&#8230;</p></div>
<div class="sect2">
<h3 id="_the_cube_shape">6.1. The Cube Shape</h3>
<div class="paragraph"><p>First things first, just like the first cube example, we&#8217;re going to create a cube shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">6</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">50</span><span class="p">,</span> <span class="mi">0</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">150</span><span class="p">,</span> <span class="mi">0</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">150</span><span class="p">,</span> <span class="mi">100</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">150</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">150</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">50</span><span class="p">)</span>
<span class="p">};</span>

<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">cubeOutline</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">polygon:cubePolyPoints</span> <span class="n">pointCount:</span><span class="mi">6</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">closeShape</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;all closed up and ready to go. This is basically the same approach as in our first cube example.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../../images//icons/tip.png" alt="Tip" />
</td>
<td class="content">Polygons are kept "open" by default, meaning that if you create a polygon from 3 points you&#8217;ll be making a <strong>V</strong> instead of a triangle. To close the polygon you just use the method above.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_connection_points">6.2. Connection Points</h3>
<div class="paragraph"><p>We&#8217;ve got a bit of a different problem here in drawing the inner lines for this cube. Because we&#8217;ve "shifted" the perspective of the cube we actually have 2 points where lines will converge. At each of these points 3 lines will converge, and because of their offset this will give us our gestalt effect.</p></div>
<div class="paragraph"><p>Create these points now:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">connectionPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">50</span><span class="p">,</span> <span class="mi">100</span><span class="p">),</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">50</span><span class="p">)};</span>
</pre></div></div></div>
<div class="paragraph"><p>We&#8217;re going to reuse this array over and over again, so it&#8217;s good to define it like this now.</p></div>
</div>
<div class="sect2">
<h3 id="_the_lines">6.3. The Lines</h3>
<div class="paragraph"><p>We&#8217;re going to create an array of line points that we&#8217;ll reuse to build each of the 6 lines that will be added to the cube shape.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">0</span><span class="p">],</span><span class="n">connectionPoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]};</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
<span class="n">linePoints</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="o">=</span> <span class="n">connectionPoints</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">3</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">5</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>
</pre></div></div></div>
<div class="paragraph"><p>The first three lines are drawn from points <tt>0, 2, 4</tt> of the polygon to the first connection point. The remaining lines are drawn from points <tt>1, 3, 5</tt> of the polygon to the second connection point.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../../images//icons/note.png" alt="Note" />
</td>
<td class="content">We switched to the second connection point in the line of code that specifies <tt>linePoints[1] = ...</tt></td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_center_add">6.4. Center, Add</h3>
<div class="paragraph"><p>Finally, center the shape and add it to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">cubeOutline</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:cubeOutline</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cube_3">7. Cube 3</h2>
<div class="sectionbody">
<div class="paragraph"><p>The last example we&#8217;re going to do is to create the illusion of a wireframe cube from cut-outs of various circles.</p></div>
<div class="paragraph"><p><span class="image">
<img src="gestalt/cube3.png" alt="Cube 3" />
</span></p></div>
<div class="paragraph"><p>This one is certainly the trickiest.</p></div>
<div class="sect2">
<h3 id="_the_cube_shape_2">7.1. The Cube Shape</h3>
<div class="paragraph"><p>Yes. Yes, we&#8217;re actually going to create another cube shape.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">defaultStyle</span><span class="p">]</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
<span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">defaultStyle</span><span class="p">]</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">whiteColor</span><span class="p">];</span>

<span class="nc">CGPoint</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">6</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">50</span><span class="p">,</span> <span class="mi">0</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">150</span><span class="p">,</span> <span class="mi">0</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">150</span><span class="p">,</span> <span class="mi">100</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">150</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">150</span><span class="p">),</span>
    <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">50</span><span class="p">)</span>
<span class="p">};</span>

<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">cubeOutline</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">polygon:cubePolyPoints</span> <span class="n">pointCount:</span><span class="mi">6</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">closeShape</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>We added some default styles to our shapes to get things started, and then created the cube shape like we did in our other examples.</p></div>
</div>
<div class="sect2">
<h3 id="_the_container">7.2. The Container</h3>
<div class="paragraph"><p>Remember how we added the lines to the shape in the first <a href="#anchor-cube">cube example</a>? If we take the same approach and add the little circles to our shape then they&#8217;re actually going to sit <em>on top</em> of everything that we initially draw.</p></div>
<div class="paragraph"><p>What we want is for them to be <em>underneath</em> the main shape. How do we do that? By putting them <em>under</em> the main shape&#8230; This means that we&#8217;re going to have to add our main shape to something else&#8230;</p></div>
<div class="paragraph"><p>Build a container!</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">container</span> <span class="o">=</span> <span class="p">[</span><span class="n">cubeOutline</span> <span class="n">copy</span><span class="p">];</span>
<span class="n">container</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
<span class="n">container</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:container</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates us a copy of our shape, and then makes sure that it is "invisible"&#8230; It&#8217;s not actually invisible though, because shapes are really views that have backing layers that draw content. What we&#8217;re doing is making sure that the copied shape&#8217;s path is invisible.</p></div>
<div class="paragraph"><p>When we add other shapes to this container shape we&#8217;ll still be able to see them.</p></div>
</div>
<div class="sect2">
<h3 id="_add_the_circles">7.3. Add the Circles</h3>
<div class="paragraph"><p>The next step is to add the little circles to the container. We do this now so that later on we can add the main cube shape <em>over top</em> of the circles!</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGRect</span> <span class="n">circleFrame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">36</span><span class="p">,</span> <span class="mi">36</span><span class="p">);</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">point</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:circleFrame</span><span class="p">];</span>
<span class="n">point</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
<span class="n">point</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>

<span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">6</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">point</span> <span class="n">copy</span><span class="p">];</span>
    <span class="n">circle</span><span class="py">.center</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="n">container</span> <span class="n">addShape:circle</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This little <tt>for</tt> loop cycles through each of the 6 points we used for building the cube shape and creates a copy of a small circle, centered at each of the points. It then adds these shapes to the container.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../../images//icons/tip.png" alt="Tip" />
</td>
<td class="content">Because shapes (and all objects in C4) are actually <em>views</em> you can layer them very easily. You can add them to one another in particular orders to get interesting effects too.</td>
</tr></table>
</div>
<div class="paragraph"><p>Now, we just have to add two circles (in the exact same way) but at the connection points.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">connectionPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">50</span><span class="p">,</span> <span class="mi">100</span><span class="p">),</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">100</span><span class="p">,</span> <span class="mi">50</span><span class="p">)};</span>
<span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">2</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">point</span> <span class="n">copy</span><span class="p">];</span>
    <span class="n">circle</span><span class="py">.center</span> <span class="o">=</span> <span class="n">connectionPoints</span><span class="p">[</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="n">container</span> <span class="n">addShape:circle</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_add_the_cube_shape">7.4. Add the Cube Shape</h3>
<div class="paragraph"><p>Finally, we&#8217;re going to create and add a cube shape in just the same way as we did in our second cube example:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">0</span><span class="p">],</span><span class="n">connectionPoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]};</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
<span class="n">linePoints</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="o">=</span> <span class="n">connectionPoints</span><span class="p">[</span><span class="mi">1</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">3</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="o">=</span> <span class="n">cubePolyPoints</span><span class="p">[</span><span class="mi">5</span><span class="p">];</span>
<span class="p">[</span><span class="n">cubeOutline</span> <span class="n">addShape:</span><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">]];</span>

<span class="p">[</span><span class="n">container</span> <span class="n">addShape:cubeOutline</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Instead of adding things to the canvas, we add them to the container and then add the entire container to the canvas!</p></div>
</div>
<div class="sect2">
<h3 id="_add_the_container">7.5. Add the Container</h3>
<div class="paragraph"><p>&#8230;Do it.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">container</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:container</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_for_kicks">7.6. For Kicks</h3>
<div class="paragraph"><p>Just for kicks, add the following line of code after adding the container to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">runMethod:</span><span class="s">@&quot;animate:&quot;</span> <span class="n">withObject:container</span> <span class="n">afterDelay:</span><span class="mf">1.0f</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This line basically says "after waiting for 1 second, run a method called <tt>animate:</tt> and pass it the container object as its argument"&#8230;</p></div>
<div class="paragraph"><p>You&#8217;ll also have to add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animate:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">shape</span> <span class="p">{</span>
    <span class="n">shape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">LINEAR</span> <span class="o">|</span> <span class="n">REPEAT</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">TWO_PI</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method takes a shape and adds an animation duration, with special linear and repeat options, then triggers the shape to perform a full rotation.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">8. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This was a pretty thorough introduction to working with shapes. In going through this tutorial you&#8217;ve learned a lot about properties, working with <em>media objects</em>, layering and different ways of thinking about creating complex objects. Many of the things you&#8217;ve done in this tutorial are <em>widely applicable to all other objects in C4</em>.</p></div>
<div class="paragraph"><p>C4 is starting to become a very powerful API for quickly building rich, expressive applications for the iOS platform. It&#8217;s really quite hard to sum up everything in one shot (actually, impossible is probably the word). So, I hope that this tutorial provides you the basic understand of a few things that we&#8217;ll be able to build on in the next session.</p></div>
<div class="paragraph"><p>You Outta Here? High-Five.</p></div>
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
