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

<h2>Geometry</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Have you ever asked yourself "Where&#8217;s the {0,0}?"&#8230; Maybe not. C4 adopts iOS geometry for adjusting things using geometric points and sizes. This tutorial will explain all the little details you need to know about geometry in C4.</p></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryStructures.png" alt="The three main geometric structures in C4" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cgwhat">1. CGWhat?</h2>
<div class="sectionbody">
<div class="paragraph"><p>The way we&#8217;re going to work with geometries in C4 is to access a few basic structures and methods from <a href="https://developer.apple.com/library/ios/#documentation/graphicsimaging/reference/CGGeometry/Reference/reference.html">CGGeometry</a>. All the structures we&#8217;re going to use will start with the prefix <strong>CG</strong>, with the main structures being:</p></div>
<div class="ulist"><ul>
<li>
<p>
CGPoint
</p>
</li>
<li>
<p>
CGSize
</p>
</li>
<li>
<p>
CGRect
</p>
</li>
</ul></div>
<div class="paragraph"><p>&#8230;That&#8217;s it really, pretty simple. The data structure <strong>CGPoint</strong> represents a point in a two-dimensional coordinate system. The data structure <strong>CGRect</strong> represents the location and dimensions of a rectangle. The data structure <strong>CGSize</strong> represents the dimensions of width and height.</p></div>
<div class="paragraph"><p>These structures provide the basic foundation for moving objects, resizing them, and so on. There are a few tweaks in C4 to make things easier, like the <tt>movie.width</tt> property, but for the most part you will be working with CGGeometry every time you create something.</p></div>
<div class="sect2">
<h3 id="_making_points">1.1. Making Points</h3>
<div class="paragraph"><p>Making a point is simple:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="n">CGPointZero</span><span class="p">;</span> <span class="c1">// {0,0}</span>
<span class="n">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">15</span><span class="p">);</span> <span class="c1">//with numbers</span>
<span class="n">CGPoint</span> <span class="n">p</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">);</span> <span class="c1">//with variables</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_making_sizes">1.2. Making Sizes</h3>
<div class="paragraph"><p>Making a size is simple:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGSize</span> <span class="n">s</span> <span class="o">=</span> <span class="n">CGSizeZero</span><span class="p">;</span>
<span class="n">CGSize</span> <span class="n">s</span> <span class="o">=</span> <span class="n">CGSizeMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">15</span><span class="p">);</span>
<span class="n">CGSize</span> <span class="n">s</span> <span class="o">=</span> <span class="n">CGSizeMake</span><span class="p">(</span><span class="n">w</span><span class="p">,</span><span class="n">h</span><span class="p">);</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_making_rects">1.3. Making Rects</h3>
<div class="paragraph"><p>Making a rect is simple:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGRect</span> <span class="n">r</span> <span class="o">=</span> <span class="n">CGRectZero</span><span class="p">;</span>
<span class="n">CGRect</span> <span class="n">r</span> <span class="o">=</span> <span class="n">CGRectMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">);</span>
<span class="n">CGRect</span> <span class="n">r</span> <span class="o">=</span> <span class="n">CGRectMake</span><span class="p">(</span><span class="n">x</span><span class="p">,</span><span class="n">y</span><span class="p">,</span><span class="n">w</span><span class="p">,</span><span class="n">h</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>A rect is actually a structure with 2 different structures inside it: a point and a size.</p></div>
</div>
<div class="sect2">
<h3 id="_grabbing_values">1.4. Grabbing Values</h3>
<div class="paragraph"><p>You can grab values from any geometry structure very easily:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">x</span> <span class="o">=</span> <span class="n">point</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">y</span> <span class="o">=</span> <span class="n">point</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">w</span> <span class="o">=</span> <span class="n">size</span><span class="p">.</span><span class="n">width</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">h</span> <span class="o">=</span> <span class="n">size</span><span class="p">.</span><span class="n">height</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Grabbing values from a rect is also easy, you just have to specify the structure in the rect you&#8217;re pinging:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">x</span> <span class="o">=</span> <span class="n">rect</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">x</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">y</span> <span class="o">=</span> <span class="n">rect</span><span class="p">.</span><span class="n">origin</span><span class="p">.</span><span class="n">y</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">w</span> <span class="o">=</span> <span class="n">rect</span><span class="p">.</span><span class="n">size</span><span class="p">.</span><span class="n">width</span><span class="p">;</span>
<span class="n">CGFloat</span> <span class="n">h</span> <span class="o">=</span> <span class="n">rect</span><span class="p">.</span><span class="n">size</span><span class="p">.</span><span class="n">height</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cgwhy">2. CGWhy?!?!</h2>
<div class="sectionbody">
<div class="paragraph"><p>A great question is "Why can&#8217;t I just say object.x = 5.0?"&#8230; "Why do I have to first create a point?"</p></div>
<div class="paragraph"><p>This was a big decision for me when I was creating C4. After a long time deciding whether to have x, y, w, and h properties on objects I decided in favour of using geometries. There were a two main reasons that sold me on this technique.</p></div>
<div class="sect2">
<h3 id="_you_8217_re_learning_ios">2.1. You&#8217;re Learning iOS</h3>
<div class="paragraph"><p>I wanted C4 to be the kind of API that would springboard people into programming native iOS applications. Since Objective-C relies on CG it just made sense to have these geometry structures be one of the ties to get you into native programming.</p></div>
</div>
<div class="sect2">
<h3 id="_any_other_way_is_ugly">2.2. Any Other Way Is Ugly</h3>
<div class="paragraph"><p>In C4 you can position objects by their <tt>origin</tt> or their <tt>center</tt> points. This isn&#8217;t usual in Objective-C programming, because you can only set the <tt>center</tt> of a UIView. We&#8217;ve added the option of setting the <tt>origin</tt> to all objects in C4. That said, this means that an object has 2 coordinate points to choose from.</p></div>
<div class="paragraph"><p>If I was to create properties for x and y then we&#8217;d have something like:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">obj</span><span class="p">.</span><span class="n">originX</span> <span class="o">=</span> <span class="p">...;</span>
<span class="n">obj</span><span class="p">.</span><span class="n">originY</span> <span class="o">=</span> <span class="p">...;</span>
<span class="n">obj</span><span class="p">.</span><span class="n">centerX</span> <span class="o">=</span> <span class="p">...;</span>
<span class="n">obj</span><span class="p">.</span><span class="n">centerY</span> <span class="o">=</span> <span class="p">...;</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;But, I found the following just a touch more elegant:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">obj</span><span class="p">.</span><span class="n">origin</span> <span class="o">=</span> <span class="p">...;</span>
<span class="n">obj</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="p">...;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_sealing_the_deal">2.3. Sealing the Deal</h3>
<div class="paragraph"><p>At the very core of C4&#8217;s animation framework, various kinds of properties are "animatable", with the <tt>center</tt> point of views being one of these things. When you change the center point a view moves automatically. It was important to keep consistent with the way that Core Animation worked, so keeping to CGPoints etc. was the way to go.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_coordinates">3. Coordinates</h2>
<div class="sectionbody">
<div class="paragraph"><p>The coordinate system in C4 uses points <em>not pixels</em>. One of the reasons for this is the always changing screen resolutions of different devices. It&#8217;s good to start thinking in terms of points because they will always be in the same place. If it helps, you can imagine that points are pixels but really what&#8217;s going on is that on some displays as single point can be 2, 4 or more actual pixels.</p></div>
<div class="sect2">
<h3 id="_where_8217_s_my_0_0">3.1. Where&#8217;s My {0,0}?</h3>
<div class="paragraph"><p>The zero-point for the canvas is in the <strong>top-left</strong> position. This zero-point is the same for all shapes, images, and any other visual object (more on this below). So, when you&#8217;re positioning elements on the canvas you can always count x from the left hand side, and y from the top <em>no matter which orientation the device is in</em>.</p></div>
<div class="paragraph"><p>Here&#8217;s how I would position the center of a circle at the zero point:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
        <span class="n">C4Shape</span> <span class="o">*</span><span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">40</span><span class="p">,</span><span class="mi">40</span><span class="p">)];</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointZero</span><span class="p">;</span>
        <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">circle</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryCircleCenteredZero.png" alt="A Circle Centered at the Canvas Zero Point" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">See how I used a <tt>CGRectMake</tt> to create the circle in the code above? This is a common way for creating views and objects in Objective-C.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_where_8217_s_my_cen_ter">3.2. Where&#8217;s My {CEN,TER}?</h3>
<div class="paragraph"><p>All visual objects have a center point (even the canvas!). You can set and access them in the following way:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">obj</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="p">...;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryCenterSquare.png" alt="A Centered Square" />
</div>
</div>
<div class="paragraph"><p>Here&#8217;s how I would position the center of a square at the center of the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
        <span class="n">C4Shape</span> <span class="o">*</span><span class="n">square</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">rect</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">192</span><span class="p">,</span><span class="mi">192</span><span class="p">)];</span>
        <span class="n">square</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
        <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">square</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_where_8217_s_my_poi_nts">3.3. Where&#8217;s My {POI,NTS}?</h3>
<div class="paragraph"><p>The two basic points of an object are easy to grab, you just access the <tt>center</tt> and <tt>origin</tt> properties. So, if you wanted to highlight these two points on a square you could do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">c1</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">square</span><span class="p">.</span><span class="n">origin</span><span class="p">;</span>
<span class="n">c2</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">square</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryCenterSquarePoints.png" alt="A Centered Square with Highlighted Points" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">check out this <a href="https://gist.github.com/C4Tutorials/5350254">gist</a></td>
</tr></table>
</div>
<div class="paragraph"><p>It&#8217;s pretty easy to see those two points, but what about a circle? Circles don&#8217;t have top-left corners! This is what it looks like if we replace the square with a circle:</p></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryCenterCirclePoints.png" alt="A Centered Circle with Highlighted Points" />
</div>
</div>
<div class="paragraph"><p>The "origin" of the circle seems to be floating up in the top-left above the shape. What&#8217;s going on here?!</p></div>
<div class="paragraph"><p>Well, the thing is is that all objects (shapes, movies, text, etc.) are simply visual contents that sit inside of "hidden" views. In the following image I&#8217;m going to highlight the "frame" of the view so that the previous image makes sense.</p></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryCenterCircleFrame.png" alt="A Centered Circle with Highlighted Frame" />
</div>
</div>
<div class="paragraph"><p>The <strong>origin</strong> of any object is actually the top-left corner of the object&#8217;s frame. The <strong>center</strong> of any object is the mid-x and mid-y coordinate of the object&#8217;s frame.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">If you look closely you&#8217;ll see that the stroke of the circle actually ends up slightly outside the frame. This happens because the stroke of a shape is drawn precisely on the outline of the shape with 1/2 of the <tt>lineWidth</tt> on the outside of the shape and 1/2 of the <tt>lineWidth</tt> on the inside of the shape.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_points_v_pixels">3.4. Points v. Pixels</h3>
<div class="paragraph"><p>Thinking about <em>points</em> instead of pixels makes sense when you consider different devices, different hardware, different screen resolutions. On a screen of 320x480, 640x960, 1024x768, or 1136-by-640, the point {10,10} will always be the same distance from the top-left corner of the screen. The distance in <em>points</em> will always be the same even though one screen might be 163ppi or 326ppi (i.e. meaning MORE pixels).</p></div>
</div>
<div class="sect2">
<h3 id="_anchoring_things_down">3.5. Anchoring Things Down</h3>
<div class="paragraph"><p>Every visual object has an <tt>anchorPoint</tt> property, which is the point around which all geometric manipulations to the object occur. For example, applying a rotation to an object with the default anchor point causes the object to rotate around its center. Changing the anchor point to a different location would cause the layer to rotate around that new point.</p></div>
<div class="paragraph"><p>First, the <tt>anchorPoint</tt> is measured relative to the frame of the object with the <tt>width</tt> and <tt>height</tt> each being 1.0f. This means that the <tt>center</tt> of the the object is {0.5f,0.5f}.</p></div>
<div class="paragraph"><p>After setting the <tt>anchorPoint</tt> to a new value, and then setting the center, you&#8217;ll see that any changes to an object offset its visual content.</p></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryAnchorPoint.png" alt="A Centered Circle with Displaced Anchor Point" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">See how the origin changes with the shape, but the center stays with the <tt>anchorPoint</tt>.</td>
</tr></table>
</div>
<div class="paragraph"><p>Here&#8217;s an example showing two images, one with an offset <tt>anchorPoint</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">img2</span><span class="p">.</span><span class="n">anchorPoint</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="o">-</span><span class="mf">1.0f</span><span class="p">,</span><span class="mf">0.5f</span><span class="p">);</span>

<span class="n">img1</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
<span class="n">img2</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryAnchorPointImage.png" alt="Two Images, One With An Offset anchorPoint" />
</div>
</div>
<div class="paragraph"><p>&#8230;and rotating these two images shows how transformations happen <em>around</em> the <tt>anchorPoint</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">img1</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="n">QUARTER_PI</span><span class="p">;</span>
<span class="n">img2</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="n">QUARTER_PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryAnchorPointRotated.png" alt="Two Images, Rotated" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">check out this <a href="https://gist.github.com/C4Tutorials/5356198">gist</a></td>
</tr></table>
</div>
<div class="paragraph"><p>&#8230;Here&#8217;s a fancy version of working with <tt>anchorPoint</tt> properties.</p></div>
<div class="imageblock">
<div class="content">
<img src="geometry/geometryAnchorPointHelix.png" alt="Fancy" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">Check out this <a href="https://gist.github.com/C4Tutorials/5356352">gist</a></td>
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
