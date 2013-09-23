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

<h2>Advanced Swipe Gestures</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5423123" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64685013" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Swipe gestures are flexible little things. You can grab a bunch of different information from them. In this tutorial we&#8217;re going to build an interactive app that read the number, direction and location of swipe gestures. It&#8217;s going to then draw from the origin point of the swipe an arrows for each touch in the gesture. Finally, the direction and color of the arrows will be determined by the gesture&#8217;s direction.</p></div>
<div class="imageblock">
<div class="content">
<img src="interactionSwipesAdvanced/interactionSwipesAdvanced.png" alt="Advanced Swipe Gestures" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_problem">1. The Problem</h2>
<div class="sectionbody">
<div class="paragraph"><p>We want to build an application that reads gestures and draws arrows, seems straightforward but there&#8217;s a couple of things to this tutorial that are tricky.</p></div>
<div class="ulist"><ul>
<li>
<p>
create an arrow
</p>
</li>
<li>
<p>
create a dynamic set of arrows (depending on the gesture count)
</p>
</li>
<li>
<p>
rotate and color the arrows depending on swipe direction
</p>
</li>
<li>
<p>
fade them out
</p>
</li>
</ul></div>
</div>
</div>
<div class="sect1">
<h2 id="_create_an_arrow">2. Create an Arrow</h2>
<div class="sectionbody">
<div class="paragraph"><p>This is probably the easiest step (aside from the fading out) in the tutorial. We&#8217;re going to build an arrow shape using <tt>CGPath</tt> and then save that shape as a variable we can come back to and copy when we need it. Add the following variable to your implementation:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">arrow</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, add to your project the following method that will create an arrow shape:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">createArrow</span> <span class="p">{</span>
    <span class="nc">CGMutablePathRef</span> <span class="n">arrowPath</span> <span class="o">=</span> <span class="nc">CGPathCreateMutable</span><span class="p">();</span>
    <span class="nc">CGPathMoveToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">10</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">10</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">187</span><span class="p">,</span> <span class="mi">0</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">222</span><span class="p">,</span> <span class="mi">15</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">187</span><span class="p">,</span> <span class="mi">30</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">20</span><span class="p">);</span>
    <span class="nc">CGPathAddLineToPoint</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">20</span><span class="p">);</span>
    <span class="nc">CGPathCloseSubpath</span><span class="p">(</span><span class="n">arrowPath</span><span class="p">);</span>

    <span class="n">arrow</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">1</span><span class="p">,</span> <span class="mi">1</span><span class="p">)];</span>
    <span class="n">arrow</span><span class="py">.path</span> <span class="o">=</span> <span class="n">arrowPath</span><span class="p">;</span>
    <span class="n">arrow</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method creates a mutable path and then, point by point, draws an arrow shape. It then creates the arrow object as a basic rect, then swaps its path for the arrow we just created. That&#8217;s it.</p></div>
<div class="paragraph"><p>Now, add the following to your setup:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">createArrow</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="sect2">
<h3 id="_check_it_out">2.1. Check it Out</h3>
<div class="paragraph"><p>The arrow you&#8217;re creating looks like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="interactionSwipesAdvanced/arrow.png" alt="The Arrow Shape" />
</div>
</div>
<div class="paragraph"><p>But, if you&#8217;d like to see for yourself you can try adding the following to your canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:arrow</span><span class="p">];</span>
<span class="n">arrow</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_create_an_arrow_set">3. Create an Arrow Set</h2>
<div class="sectionbody">
<div class="paragraph"><p>The next step is to figure out how to create a dynamic set of arrows that is going to represent the direction and number of touches for each swipe. This step is going to need 3 things:</p></div>
<div class="ulist"><ul>
<li>
<p>
the number of touches
</p>
</li>
<li>
<p>
the direction of touches
</p>
</li>
<li>
<p>
a color
</p>
</li>
</ul></div>
<div class="paragraph"><p>Create and add to your project a method that takes all three of these things:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">createArrowSet:</span><span class="p">(</span><span class="nc">NSInteger</span><span class="p">)</span><span class="n">touchCount</span>
                  <span class="n">rotation:</span><span class="p">(</span><span class="nc">CGFloat</span><span class="p">)</span><span class="n">rotation</span>
                     <span class="n">color:</span><span class="p">(</span><span class="nc">UIColor</span> <span class="o">*</span><span class="p">)</span><span class="n">arrowColor</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method will take all three arguments we need for constructing our set of arrows.</p></div>
<div class="sect2">
<h3 id="_the_arrowset_shape">3.1. The arrowSet Shape</h3>
<div class="paragraph"><p>We can create a shape that will hold all the arrows we need for the gesture we&#8217;re receiving. But, to do so, we&#8217;ll need to know how many arrows to fit in the shape. We&#8217;re going to do this by first calculating a frame that adapts to the height of our arrow shape times the number of arrows, with a gap between each one.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">gap</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
<span class="nc">CGRect</span> <span class="n">arrowSetFrame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="n">arrow</span><span class="py">.width</span><span class="p">,</span> <span class="n">arrow</span><span class="py">.height</span> <span class="o">*</span> <span class="n">touchCount</span> <span class="o">+</span> <span class="n">gap</span> <span class="o">*</span> <span class="p">(</span><span class="n">touchCount</span> <span class="o">-</span> <span class="mi">1</span><span class="p">));</span>
<span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">arrowSet</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:arrowSetFrame</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates a shape whose frame will enclose all the arrows we&#8217;re going to add to the canvas. Next, let&#8217;s just style the shape a little bit:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">arrowSet</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
<span class="n">arrowSet</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">clearColor</span><span class="p">];</span>
<span class="n">arrowSet</span><span class="py">.userInteractionEnabled</span> <span class="o">=</span> <span class="nb">NO</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>We make sure that the main shape is invisible and that it (and its future subviews) have their interaction disabled.</p></div>
</div>
<div class="sect2">
<h3 id="_add_arrows">3.2. Add Arrows</h3>
<div class="paragraph"><p>Now that we have a shape we can add arrows to it&#8230; We grab the number of touches and add that many arrows. Add the following <tt>for</tt> loop to the <tt>createArrowSet</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="n">touchCount</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newArrow</span> <span class="o">=</span> <span class="p">[</span><span class="n">arrow</span> <span class="n">copy</span><span class="p">];</span>
    <span class="n">newArrow</span><span class="py">.origin</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="n">arrow</span><span class="py">.height</span><span class="o">*</span><span class="n">i</span> <span class="o">+</span> <span class="n">gap</span> <span class="o">*</span> <span class="n">i</span><span class="p">);</span>
    <span class="n">newArrow</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n">arrowColor</span><span class="p">;</span>
    <span class="p">[</span><span class="n">arrowSet</span> <span class="n">addShape:newArrow</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We create a copy of our original arrow, adjust its origin point to match its current number and then fill it with the current color for the gesture. We then add the shape to the arrow set.</p></div>
</div>
<div class="sect2">
<h3 id="_rotate_the_set">3.3. Rotate The Set</h3>
<div class="paragraph"><p>The <tt>anchorPoint</tt> is going to allow us to rotate easily around the center of the set of arrows. However, since we want to start the arrows at the start of the gesture, we set the <tt>x</tt> value of the anchor point to the <tt>0</tt>.</p></div>
<div class="paragraph"><p>Add the following lines of code <em>after</em> the <tt>for</tt> loop:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">arrowSet</span><span class="py">.anchorPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mf">0.5f</span><span class="p">);</span>
<span class="n">arrowSet</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">rotation</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>When we update the <tt>center</tt> of the arrow set it will position itself with the arrows starting at the original touch point. This is also the point around which the arrows will rotate.</p></div>
</div>
<div class="sect2">
<h3 id="_return_it">3.4. Return It</h3>
<div class="paragraph"><p>Finally, we need to return the set from our method. Finish off the method by adding:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">return</span> <span class="n">arrowSet</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_swipe">4. The Swipe</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now comes the nitty gritty. We&#8217;re going to read the swipe gesture and build everything off of it&#8230; Create a method that receives a <tt>UISwipeGestureRecognizer</tt> like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">swipe:</span><span class="p">(</span><span class="nc">UISwipeGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="n">swipeGesture</span> <span class="p">{</span>
   <span class="c1">//fancy swipe stuff goes heres</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>What we&#8217;re to do in this method is grab the 3 things we need for the <tt>createArrowSet</tt> method.</p></div>
<div class="sect2">
<h3 id="_touchcount">4.1. touchCount</h3>
<div class="paragraph"><p>To get the number of touches used in the gesture, add the following to the <tt>swipe:</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSInteger</span> <span class="n">touchCount</span> <span class="o">=</span> <span class="n">swipeGesture</span><span class="py">.numberOfTouches</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_rotation_and_color">4.2. Rotation and Color</h3>
<div class="paragraph"><p>To set the rotation and color for the arrow set we&#8217;re going to need to dig into the swipe gesture, and we&#8217;re going to use a switch statement to do all this. Add the following to the <tt>swipe:</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">rotation</span><span class="p">;</span>
<span class="nc">UIColor</span> <span class="o">*</span><span class="n">arrowColor</span><span class="p">;</span>
<span class="k">switch</span> <span class="p">(</span><span class="n">swipeGesture</span><span class="py">.direction</span><span class="p">)</span> <span class="p">{</span>
    <span class="k">case</span> <span class="nc">UISwipeGestureRecognizerDirectionUp</span><span class="o">:</span>
        <span class="n">rotation</span> <span class="o">=</span> <span class="o">-</span><span class="n">HALF_PI</span><span class="p">;</span>
        <span class="n">arrowColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">case</span> <span class="nc">UISwipeGestureRecognizerDirectionLeft</span><span class="o">:</span>
        <span class="n">rotation</span> <span class="o">=</span> <span class="n">PI</span><span class="p">;</span>
        <span class="n">arrowColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">case</span> <span class="nc">UISwipeGestureRecognizerDirectionDown</span><span class="o">:</span>
        <span class="n">rotation</span> <span class="o">=</span> <span class="n">HALF_PI</span><span class="p">;</span>
        <span class="n">arrowColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:</span><span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;lines&quot;</span><span class="p">]</span><span class="py">.UIImage</span><span class="p">];</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">default</span><span class="o">:</span>
        <span class="n">rotation</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span>
        <span class="n">arrowColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates a <tt>rotation</tt> value and an <tt>arrowColor</tt> object. Then we grab the direction of the gesture by calling <tt>swipe.direction</tt>. With this direction we can use a <tt>switch</tt> statement to determine the values of the rotation and color.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Remember that the rotation of objects starts at 0 which is to the middle-right of the shape and increments in a clockwise fashion. So, when we want to rotate up we&#8217;re actually rotating counter-clockwise by a <tt>HALF_PI</tt> rotation. For more on rotations check out <a href="http://stackoverflow.com/a/9611173/1218605">this answer</a>.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_build_the_arrows">4.3. Build The Arrows</h3>
<div class="paragraph"><p>Now, with the 3 values we need we can add the following lines of code to our <tt>swipe:</tt> gesture so that the arrow set will get created, positioned and added to the canvas:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">arrowSet</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createArrowSet:touchCount</span> <span class="n">rotation:rotation</span> <span class="n">color:arrowColor</span><span class="p">];</span>

<span class="nc">CGPoint</span> <span class="n">touchPoint</span> <span class="o">=</span> <span class="p">[</span><span class="n">swipeGesture</span> <span class="n">locationInView:</span><span class="k">self</span><span class="py">.canvas</span><span class="p">];</span>
<span class="n">arrowSet</span><span class="py">.center</span> <span class="o">=</span> <span class="n">touchPoint</span><span class="p">;</span>

<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:arrowSet</span><span class="p">];</span>

<span class="p">[</span><span class="k">self</span> <span class="n">runMethod:</span><span class="s">@&quot;fadeOut:&quot;</span> <span class="n">withObject:arrowSet</span> <span class="n">afterDelay:</span><span class="mf">0.25f</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>The <tt>touchPoint</tt> is the origin of the first touch in a gesture. We use this value (located in our canvas) to position the center of our arrow set (anchored to {0,0.5}). After adding the arrows to the canvas we trigger a method to start fading them out.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_fade">5. The Fade</h2>
<div class="sectionbody">
<div class="paragraph"><p>To get the arrow set to fade out (and automatically remove itself from the canvas) add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">fadeOut:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">shape</span> <span class="p">{</span>
    <span class="n">shape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
    <span class="n">shape</span><span class="py">.alpha</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
    <span class="p">[</span><span class="n">shape</span> <span class="n">runMethod:</span><span class="s">@&quot;removeFromSuperview&quot;</span> <span class="n">afterDelay:shape</span><span class="py">.animationDuration</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Check out this <a href="interactionSwipesAdvanced/fade.jpg">fade</a></td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_gestures">6. The Gestures</h2>
<div class="sectionbody">
<div class="paragraph"><p>I&#8217;ve left this for last because it&#8217;s a bit tricky. Our concept is to draw a bunch of arrows depending on how many touches are in a particular swipe gesture. However, swipe gestures can only register for one particular count of touches. This means that if you want to have your swipe gestures register for 1, 2, or 3 touches, you&#8217;ll have to create gestures for each count.</p></div>
<div class="sect2">
<h3 id="_how_many">6.1. How Many?</h3>
<div class="paragraph"><p>We want to register swipes for 1, 2, or 3 touches in <em>any</em> direction. This means that we&#8217;re going to have to create 12 distinct gestures. And, because each gesture requires its own name, we&#8217;re going to have to set up a bit of a dynamic <tt>for</tt> loop for generating the gestures with unique names. Add the following to your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">4</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">name</span><span class="p">;</span>
    <span class="n">name</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;down%d&quot;</span><span class="p">,</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPEDOWN</span> <span class="n">name:name</span> <span class="n">action:</span><span class="s">@&quot;swipe:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:i</span> <span class="n">forGesture:name</span><span class="p">];</span>
    <span class="n">name</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;left%d&quot;</span><span class="p">,</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPELEFT</span> <span class="n">name:name</span> <span class="n">action:</span><span class="s">@&quot;swipe:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:i</span> <span class="n">forGesture:name</span><span class="p">];</span>
    <span class="n">name</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;up%d&quot;</span><span class="p">,</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPEUP</span> <span class="n">name:name</span> <span class="n">action:</span><span class="s">@&quot;swipe:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:i</span> <span class="n">forGesture:name</span><span class="p">];</span>
    <span class="n">name</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;right%d&quot;</span><span class="p">,</span><span class="n">i</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPERIGHT</span> <span class="n">name:name</span> <span class="n">action:</span><span class="s">@&quot;swipe:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:i</span> <span class="n">forGesture:name</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This will create gestures that are named like: <tt>down1</tt>, <tt>down2</tt>, <tt>down3</tt>. It also sets the number of touches for each one of those gestures.</p></div>
</div>
<div class="sect2">
<h3 id="_allow_multiple_touches">6.2. Allow Multiple Touches</h3>
<div class="paragraph"><p>Finally, you need to add the following to your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">self</span><span class="py">.canvas.multipleTouchEnabled</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">7. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>So, we really dug into the <tt>SWIPE</tt> gesture in this tutorial. We were able to grab a bunch of different components from the gesture and use those to dynamically create a set of arrows that are colored and rotated in the direction of the swipe. We also had to build 12 different gestures each with a unique name for each touch count and direction.</p></div>
<div class="paragraph"><p>Ahoy.</p></div>
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
