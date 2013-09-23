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

<h2>Composite Objects</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5375529" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684452" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>I&#8217;m going to show you how to put shapes in a shape and mask the shape with another shape.</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjects.png" alt="A Composite Shape" />
</div>
</div>
<div class="paragraph"><p>Allons-y.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_principal">1. Principal</h2>
<div class="sectionbody">
<div class="paragraph"><p>The main thing you should take away from this tutorial is this:</p></div>
<div class="sidebarblock">
<div class="content">
<div class="paragraph"><p><em>You can put objects inside one another, and work with them like they were on the canvas.</em></p></div>
</div></div>
<div class="paragraph"><p>I&#8217;ll show take you <a href="/compositeObjects/stepByStep.png">step-by-step</a> through making an example, and then show some others at the end of the tutorial.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_the_main_object">2. The Main Object</h2>
<div class="sectionbody">
<div class="paragraph"><p>I wanted to start out by creating an intricate shape, like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjects.png" alt="A Composite Shape" />
</div>
</div>
<div class="paragraph"><p>And thought to myself, "How would I do this in C4?" I came up with a couple of ways:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Build a bunch of arc shapes and position/rotate them into place
</p>
</li>
<li>
<p>
Build a bunch of curves in place, and use their control points to shape the shape
</p>
</li>
<li>
<p>
Build a bunch of circles and adjust their start / end points
</p>
</li>
<li>
<p>
Build a bunch of circles and mask them
</p>
</li>
</ol></div>
<div class="paragraph"><p>After toying around for a bit I settled on the last option for the following reasons:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
too much math
</p>
</li>
<li>
<p>
too much math
</p>
</li>
<li>
<p>
too much math
</p>
</li>
<li>
<p>
pretty easy
</p>
</li>
</ol></div>
<div class="sect2">
<h3 id="_how_to_mask_12_objects">2.1. How to Mask 12 objects</h3>
<div class="paragraph"><p>The easiest way to mask 12 objects is to position them <strong>inside</strong> a main object and then mask the main object. That&#8217;s right, I said <em>inside</em>.</p></div>
<div class="paragraph"><p>When you mask an object, it and <em>all</em> of its subviews will clip to the mask. The parts that become "invisible" are those that are directly underneath any alpha channels in the mask. For instance, if you use a circle as a mask then everything outside that circle will be invisible and anything inside the area of the circle will be visible.</p></div>
<div class="paragraph"><p>If the circle is semi-transparent, then the things inside the circle will also become semi-transparent.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Apple&#8217;s doc on  <a href="https://developer.apple.com/library/ios/#documentation/graphicsimaging/conceptual/drawingwithquartz2d/dq_images/dq_images.html">bitmap images and image masks</a>.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_build_a_shape">2.2. Build a Shape</h3>
<div class="paragraph"><p>We&#8217;re going to start by building our main object, a shape. It doesn&#8217;t really matter what shape we&#8217;re going to use, only that the frame is big enough for our animation.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">mainShape</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">mainShape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">368</span><span class="p">,</span> <span class="mi">368</span><span class="p">)];</span>
    <span class="n">mainShape</span><span class="py">.strokColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
    <span class="n">mainShape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
        <span class="n">mainShape</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:mainShape</span><span class="p">];</span>

        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsMainShape.png" alt="The Main Shape" />
</div>
</div>
<div class="paragraph" id="anchor-shape"><p>We&#8217;re going to put all of our other shapes inside this one and then mask it. Since we don&#8217;t want any color in the background of our mask we make the <tt>strokeColor</tt> and <tt>fillColor</tt> both clear.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_create_the_shapes">3. Create The Shapes</h2>
<div class="sectionbody">
<div class="paragraph"><p>The next thing we need to do is create our shapes and add them to the <tt>mainShape</tt> object. To get the effect of what we&#8217;re looking for, this sort of snowflakey concentric-ring thing, we want to have many circles rotated about a common point.</p></div>
<div class="sect2">
<h3 id="_building_the_circles">3.1. Building The Circles</h3>
<div class="paragraph"><p>For simplicity, we&#8217;re just going to say that the shape of our circles will be the same size as our <tt>mainShape</tt>. We also want to position the shapes based on the <tt>center</tt> of our <tt>mainShape</tt> rather than that of the canvas, so we create a point.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">mainShape</span><span class="py">.width</span> <span class="o">/</span> <span class="mi">2</span><span class="p">,</span> <span class="n">mainShape</span><span class="py">.height</span> <span class="o">/</span> <span class="mi">2</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>To create 12 identical shapes rotated around a point we use a <tt>for</tt> loop. To get the shapes rotating from their bottom-most point we set their <tt>anchorPoint</tt> to {0.5,1.0} and then its <tt>rotation</tt> property to be <tt>1/12th</tt> of a full rotation. Finally,</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">12</span><span class="p">;</span> <span class="n">i</span> <span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">shape</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:mainShape</span><span class="py">.frame</span><span class="p">];</span>
    <span class="n">shape</span><span class="py">.anchorPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mf">0.5</span><span class="p">,</span><span class="mf">1.0f</span><span class="p">);</span>
    <span class="n">shape</span><span class="py">.center</span> <span class="o">=</span> <span class="n">center</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">TWO_PI</span> <span class="o">/</span> <span class="mf">12.0f</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
    <span class="p">[</span><span class="n">mainShape</span> <span class="n">addShape:shape</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This gives us a nice distribution of circles. All the objects appear to be rotating around the center of the canvas.</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsCircles.png" alt="12 Circles in a Main Shape" />
</div>
</div>
<div class="paragraph"><p>But, this is a bit of a deception. What&#8217;s really going on is that these circles are actually at the {192,192} position of the <tt>mainShape</tt>, the center of which is at the center of the canvas. If we hadn&#8217;t already set the <a href="#anchor-shape">stroke and fill</a> colors to clear our shape would look like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsMainShapeRevealed.png" alt="12 Circles Main Shape Revealed" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_put_a_mask_on_it">4. Put A Mask On It</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now it&#8217;s time to add a mask to our <tt>mainShape</tt>. This is a pretty straightforward step, all we do is create the shape, center it to the <tt>mainShape</tt> and then add it as a mask.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">mask</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:mainShape</span><span class="py">.frame</span><span class="p">];</span>
<span class="n">mask</span><span class="py">.center</span> <span class="o">=</span> <span class="n">center</span><span class="p">;</span>
<span class="n">mainShape</span><span class="py">.mask</span> <span class="o">=</span> <span class="n">mask</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsMaskShape.png" alt="The Mask's Shape" />
</div>
</div>
<div class="paragraph"><p>&#8230;That&#8217;s what the mask shape looks like over top of the circles.</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsMasked.png" alt="Composite Shape Masked" />
</div>
</div>
<div class="paragraph"><p>&#8230;And, this is what the circles look like when the <tt>mainShape</tt> is masked.</p></div>
<div class="paragraph"><p>Easy peasy.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_a_touch_of_animation">5. A Touch of Animation</h2>
<div class="sectionbody">
<div class="paragraph"><p>So, right now we have a nice looking shape that doesn&#8217;t do anything other than just sit still in the middle of the screen. I didn&#8217;t like this so I wanted to add some animation to the shape.</p></div>
<div class="sect2">
<h3 id="_rotating_12_shapes">5.1. Rotating 12 Shapes</h3>
<div class="paragraph"><p>The first thing I wanted to do was rotate the shapes, but how can you get all 12 shapes to rotate around the mid-point at the same time, you ask? Well, the answer is <strong>you don&#8217;t</strong>!</p></div>
<div class="paragraph"><p>All our shapes are <em>inside</em> the <tt>mainShape</tt>, so to get them all rotating the only thing we have to do is rotate the <tt>mainShape</tt>. You can put the following code right at the end of the <tt>setup</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">mainShape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
<span class="n">mainShape</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span> <span class="o">|</span> <span class="n">LINEAR</span><span class="p">;</span>
<span class="n">mainShape</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">TWO_PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsRotating.png" alt="Rotating Shape" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_borrrrring">5.2. Borrrrring</h3>
<div class="paragraph"><p>Now, I thought to myself, for a tutorial just rotating the shape is a bit flat and boring. How do I spice up the animations&#8230;? I wanted to animate the <tt>strokeColor</tt> and the <tt>rotation</tt> of each circle in the <tt>mainShape</tt>. I also wanted the animations for each of the circles to be offset by a little bit of time.</p></div>
<div class="paragraph"><p>The easiest way to do this was to add animations to the shapes <strong>in</strong> the <tt>for</tt> loop that we ran earlier. To get the offset animations I could use the <tt>runMethod:withObject:afterDelay</tt> method on each circle. This approach required me to first create a new method called <tt>animateShape:</tt> into which I would pass the current shape. Inside this method would be all the required animation stuff for the current circle.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animateShape:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">shape</span> <span class="p">{</span>
    <span class="n">shape</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span> <span class="o">|</span> <span class="n">AUTOREVERSE</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">PI</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>What this method does is take any shape that&#8217;s passed to it and run some animation stuff on it. To trigger this method I had to put the following line of code inside the <tt>for</tt> loop right at the end of all the other code:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">12</span><span class="p">;</span> <span class="n">i</span> <span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="c1">//...other stuff</span>
        <span class="c1">//[mainShape addShape:shape];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">runMethod:</span><span class="s">@&quot;animateShape:&quot;</span> <span class="n">withObject:shape</span> <span class="n">afterDelay:</span><span class="p">(</span><span class="n">i</span><span class="o">+</span><span class="mi">1</span><span class="p">)</span><span class="o">*</span><span class="mf">0.5f</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>The important part of this step is the <tt>(i+1)*0.5f</tt> which offsets the starting of each circle&#8217;s animation by a half second. And we&#8217;re already getting a nice little animation:</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsAnimation.png" alt="Composite Shape Animated" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_just_a_touch_more">5.3. Just A Touch More</h3>
<div class="paragraph"><p>I wanted to add one final touch to the animation, which again takes the same kind of animation delay approach as above. Inside the <tt>animateShape:</tt> method I snuck in a delayed call to a second animation method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animateShape:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">shape</span> <span class="p">{</span>
        <span class="c1">//...other stuff</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">runMethod:</span><span class="s">@&quot;animateStrokeEnd:&quot;</span> <span class="n">withObject:shape</span> <span class="n">afterDelay:</span><span class="mf">0.25f</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animateStrokeEnd:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">shape</span> <span class="p">{</span>
    <span class="n">shape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">15.0f</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.strokeEnd</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsFinal.png" alt="Final Look" />
</div>
</div>
<div class="paragraph"><p><a href="compositeObjects/veryNice.jpg">Very Niiiice.</a></p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_composite_movies">6. Composite Movies</h2>
<div class="sectionbody">
<div class="paragraph"><p>Here&#8217;s another example of composite objects using movies and masks. I put a shape in a shape to make a donut, then the donut in a movie to make a mask. I copy the donut and turn it white, then I put the this in a movie to make it look like a cut out. Then I put the masked movie in the other movie and spin the whole thing. Then, I spin the masked movie the other direction to make it look like there&#8217;s a movie in a movie.</p></div>
<div class="imageblock">
<div class="content">
<img src="compositeObjects/compositeObjectsMovie.png" alt="A Composite Movie" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">Grab this <a href="https://gist.github.com/C4Tutorials/5384383">gist</a></td>
</tr></table>
</div>
<div class="paragraph"><p>Right. I put a movie in a movie so you can watch while you watch.</p></div>
<div class="paragraph"><p>Carry on.</p></div>
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
