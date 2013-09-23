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

<h2>Parallax Scrolling</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5400120" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684698" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial, I&#8217;ll show you how you can offset the positions of layered scrollviews to create a parallax effect.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_intro">1. Intro</h2>
<div class="sectionbody">
<div class="paragraph"><p>Scrolling content to create the effect of movement across landscapes has been used for a very long time, especially in animation. Some early examples of its use in games are <a href="http://en.wikipedia.org/wiki/Moon_Patrol">Moon Patrol</a>, <a href="http://en.wikipedia.org/wiki/Star_Force">Star Force</a> and <a href="http://en.wikipedia.org/wiki/Final_Fight">Final Fight</a>. The point is to create an illusion of depth in the background of a moving scene. Today, there is lots of interest in using this technique for <a href="http://webdesignledger.com/inspiration/21-examples-of-parallax-scrolling-in-web-design">websites</a>.</p></div>
<div class="sect2">
<h3 id="_layer_trick">1.1. Layer Trick</h3>
<div class="paragraph"><p>We&#8217;re going to set up a project that uses 5 scrollviews as layers on top of one another. The following image is a good example of how layers can be separated, then scrolled,  to create the effect.</p></div>
<div class="imageblock">
<div class="content">
<img src="parallaxScrolling/twwParallax.jpg" alt="The Whispered World" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_scrollviews">1.2. ScrollViews</h3>
<div class="paragraph"><p>There&#8217;s a `C4ScrollView object that we&#8217;re going to use, but this tutorial won&#8217;t go through the basics of creating them. For that, you can check out the <a href="/examples/scrollViewImage.php">scrollViewImage</a> and <a href="/examples/scrollViewLabel.php">scrollViewLabel</a> examples.</p></div>
<div class="imageblock">
<div class="content">
<img src="parallaxScrolling/scrollviews.png" alt="Scrollview Examples" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_strategy">1.3. Strategy</h3>
<div class="paragraph"><p>What we&#8217;re going to do is the following:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
set up 5 scrollviews
</p>
</li>
<li>
<p>
add a bunch of labels to the scrollviews
</p>
</li>
<li>
<p>
add interaction to the top layer scrollview
</p>
</li>
<li>
<p>
adjust all the other scrollviews when the top-layer is scrolled
</p>
</li>
</ol></div>
<div class="paragraph"><p>Seems pretty straightforward, but there are a few tricks.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_5_scrollviews">2. 5 ScrollViews</h2>
<div class="sectionbody">
<div class="paragraph"><p>To create 5 scrollviews we&#8217;re going to use two <tt>for</tt> loops, one nested inside the other. The outer loop will create the 5 views, the inner loop will add the labels to each individual view.</p></div>
<div class="sect2">
<h3 id="_font_and_label">2.1. Font and Label</h3>
<div class="paragraph"><p>We plan to reuse a font and a label over and over, so we&#8217;re going to create ones to reference outside the first loop. We also want to keep individual references to each scrollview, so we need some variables for that. Start your workspace off like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4ScrollView</span> <span class="o">*</span><span class="n">sv1</span><span class="p">,</span> <span class="o">*</span><span class="n">sv2</span><span class="p">,</span> <span class="o">*</span><span class="n">sv3</span><span class="p">,</span> <span class="o">*</span><span class="n">sv4</span><span class="p">,</span> <span class="o">*</span><span class="n">sv5</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;AvenirNext-Heavy&quot;</span> <span class="n">size:</span><span class="mi">24</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;5&quot;</span> <span class="n">font:font</span><span class="p">];</span>
        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This gives us 5 references that we&#8217;ll use later. It also gives us 2 objects, the font and the label, that we&#8217;ll be able to modify throughout the loops.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_outer_loop">3. Outer Loop</h2>
<div class="sectionbody">
<div class="paragraph"><p>The outer loop is where we&#8217;re going to construct individual scrollviews. We do this by setting up the details for the labels we&#8217;ll add, then construct and style the view, and add it to the canvas. Create the outer loop like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="mi">6</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="sect2">
<h3 id="_label_styles">3.1. Label Styles</h3>
<div class="paragraph"><p>The first thing we are going to do is style the labels for each scroll view. Add the following to the top of the outer loop:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">font</span><span class="py">.pointSize</span> <span class="o">=</span> <span class="mi">36</span> <span class="o">*</span> <span class="n">i</span><span class="p">;</span>
<span class="n">label</span><span class="py">.text</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;%d&quot;</span><span class="p">,</span><span class="mi">6</span><span class="o">-</span><span class="n">i</span><span class="p">];</span>
<span class="n">label</span><span class="py">.font</span> <span class="o">=</span> <span class="n">font</span><span class="p">;</span>
<span class="n">label</span><span class="py">.backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:</span><span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;lines.png&quot;</span><span class="p">]</span><span class="py">.UIImage</span><span class="p">];</span>
<span class="p">[</span><span class="n">label</span> <span class="n">sizeToFit</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;What this does is create a label whose font size is a factor of 36pts, and sets its text accordingly. For <tt>i = 1</tt> the label will be 5 and the point size 36, for <tt>i = 2</tt> the label will be 4 and point size 72, and so on&#8230; We do this so that the scrollviews get added on top of one another with 5 being added first (i.e. in the background) and 1 last (i.e. on top). The top layer will have the largest labels.</p></div>
<div class="paragraph"><p>We then set the background color to be a transparent image, which just gives us a little bit of context to see the label.</p></div>
</div>
<div class="sect2">
<h3 id="_constructing_a_scrollview">3.2. Constructing A Scrollview</h3>
<div class="paragraph"><p>Right after styling the label we&#8217;re going to construct the scrollview for the given layer. We do it in this order so that we can build the layer to fit the size of the label.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4ScrollView</span> <span class="o">*</span><span class="n">currentView</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4ScrollView</span> <span class="n">scrollView:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.width</span><span class="p">,</span> <span class="n">label</span><span class="py">.height</span><span class="p">)];</span>
<span class="n">currentView</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="n">currentView</span><span class="py">.contentSize</span> <span class="o">=</span> <span class="nc">CGSizeMake</span><span class="p">(</span><span class="n">currentView</span><span class="py">.width</span> <span class="o">*</span> <span class="mi">17</span><span class="p">,</span> <span class="n">currentView</span><span class="py">.height</span><span class="p">);</span>
<span class="n">currentView</span><span class="py">.backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithWhite:</span><span class="mf">1.0f</span> <span class="n">alpha:</span><span class="mf">0.2f</span><span class="p">];</span>
<span class="n">currentView</span><span class="py">.borderColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>
<span class="n">currentView</span><span class="py">.borderWidth</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>We create each scrollview so that it&#8217;s the width of the canvas and the exact height of the labels that we&#8217;re putting into it, then we put it in the center of the canvas. Then, we set the <tt>contentSize</tt> of the current view to be <em>very</em> wide (i.e. 17 time wider than the frame of the view). Finally, the <tt>backgroundColor</tt> is set to white to give a nice little fade effect.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">My good friend Stefan calls this the fog effect, or something like that. He&#8217;s Austrian and he was once trying to explain to me how visualizing fog could help us with creating depth in a 2D/3D rendering of text landscapes that we were working on&#8230; That talk was about the <a href="http://www.aec.at/zeitraum/index_en.html">Zeitraum Project</a> by the <a href="http://www.aec.at/futurelab/en">Futurelab</a>.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_setting_the_var">3.3. Setting the Var</h3>
<div class="paragraph"><p>Finally, before we add the scrollview to the canvas we run the following code:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">(</span><span class="mi">6</span> <span class="o">-</span> <span class="n">i</span> <span class="o">==</span> <span class="mi">1</span><span class="p">)</span> <span class="n">sv1</span> <span class="o">=</span> <span class="n">currentView</span><span class="p">;</span>
<span class="k">else</span> <span class="k">if</span><span class="p">(</span><span class="mi">6</span> <span class="o">-</span> <span class="n">i</span> <span class="o">==</span> <span class="mi">2</span><span class="p">)</span> <span class="n">sv2</span> <span class="o">=</span> <span class="n">currentView</span><span class="p">;</span>
<span class="k">else</span> <span class="k">if</span><span class="p">(</span><span class="mi">6</span> <span class="o">-</span> <span class="n">i</span> <span class="o">==</span> <span class="mi">3</span><span class="p">)</span> <span class="n">sv3</span> <span class="o">=</span> <span class="n">currentView</span><span class="p">;</span>
<span class="k">else</span> <span class="k">if</span><span class="p">(</span><span class="mi">6</span> <span class="o">-</span> <span class="n">i</span> <span class="o">==</span> <span class="mi">4</span><span class="p">)</span> <span class="n">sv4</span> <span class="o">=</span> <span class="n">currentView</span><span class="p">;</span>
<span class="k">else</span> <span class="k">if</span><span class="p">(</span><span class="mi">6</span> <span class="o">-</span> <span class="n">i</span> <span class="o">==</span> <span class="mi">5</span><span class="p">)</span> <span class="n">sv5</span> <span class="o">=</span> <span class="n">currentView</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>It&#8217;s not very dynamic, but it basically sets each <tt>sv</tt> variable appropriately. Since we&#8217;re creating the level-5 scrollview first we need to invert <tt>i</tt> so that we point to the right view (hence, the <tt>6-i</tt>).</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_inner_loop">4. Inner Loop</h2>
<div class="sectionbody">
<div class="paragraph"><p>Right after creating the current scrollview, we&#8217;re going to build a second <tt>for</tt> loop that will populate the scrollview with labels. For simplicity, the labels will all be the same.</p></div>
<div class="paragraph"><p>After toying around with different widths, I picked <tt>17</tt> as the number of labels I wanted to have in my scrollviews. I did this because at the end of 17 elements all the numbers line up like they do when the application first starts.</p></div>
<div class="paragraph"><p>To add all the labels to the scrollview, we do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">j</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">j</span> <span class="o">&lt;</span> <span class="mi">17</span><span class="p">;</span> <span class="n">j</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">currentLabel</span> <span class="o">=</span> <span class="p">[</span><span class="n">label</span> <span class="n">copy</span><span class="p">];</span>
    <span class="n">currentLabel</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">currentView</span><span class="py">.width</span><span class="o">/</span><span class="mf">2.0f</span> <span class="o">+</span> <span class="n">currentView</span><span class="py">.width</span> <span class="o">*</span> <span class="n">j</span><span class="p">,</span>
                                      <span class="n">currentView</span><span class="py">.height</span> <span class="o">/</span> <span class="mf">2.0f</span><span class="p">);</span>
    <span class="p">[</span><span class="n">currentView</span> <span class="n">addLabel:currentLabel</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This makes a 17 copies of the label we created in the outer loop and adds each one a regular distance apart. As we scroll through each view we&#8217;ll be able to see the labels.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_observe">5. Observe!</h2>
<div class="sectionbody">
<div class="paragraph"><p>This is a really important part. There&#8217;s this thing in programming called Key-Value Observing (KVO) for short. It&#8217;s a really sophisticated way of making sure that things change when other things change. We&#8217;re going to take advantage of this to link up all the movements of the scrollviews.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">It&#8217;s taken me a long time to actually get a grip on <tt>KVO</tt> having to explain it for this tutorial really had me learn the essentials of creating observable properties. I am probably going to integrate <tt>KVO</tt> into more <tt>C4UIElement</tt> classes in the future.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_add_an_observer">5.1. Add an Observer</h3>
<div class="paragraph"><p>The first step to this is to add an observer. Just after the outer <tt>for</tt> loop, we can write the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">sv1</span> <span class="n">addObserver:</span><span class="k">self</span> <span class="n">forKeyPath:</span><span class="s">@&quot;contentOffset&quot;</span> <span class="n">options:</span><span class="nc">NSKeyValueObservingOptionNew</span> <span class="n">context:</span><span class="nb">nil</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This says that <tt>self</tt> (in this case the canvas) is going to start observing <tt>sv1</tt> (the top-most scrollview layer). It&#8217;s going to observe the <tt>contentOffset</tt> and run every time there is a change in that property. At the moment, the <tt>NSKeyValueObservingOptionNew</tt> and <tt>nil</tt> aren&#8217;t really important but you gotta have them in there for now.</p></div>
</div>
<div class="sect2">
<h3 id="_the_observe_method">5.2. The Observe Method</h3>
<div class="paragraph"><p>We now have to build an observe method that will run every time the <tt>contentOffset</tt> property of <tt>sv1</tt> is changed. In this method we&#8217;ll handle the displacement of all the scrollviews.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">observeValueForKeyPath:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">keyPath</span>
                     <span class="n">ofObject:</span><span class="p">(</span><span class="kt">id</span><span class="p">)</span><span class="n">object</span>
                       <span class="n">change:</span><span class="p">(</span><span class="nc">NSDictionary</span> <span class="o">*</span><span class="p">)</span><span class="n">change</span>
                      <span class="n">context:</span><span class="p">(</span><span class="kt">void</span> <span class="o">*</span><span class="p">)</span><span class="n">context</span> <span class="p">{</span>
    <span class="c1">//observe here</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Although it has a gross name, it works like a charm. It&#8217;s time to put in the code that does all the updating:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">((</span><span class="n-ProjectClass">C4ScrollView</span> <span class="o">*</span><span class="p">)</span><span class="n">object</span> <span class="o">==</span> <span class="n">sv1</span><span class="p">)</span> <span class="p">{</span>
    <span class="n">sv2</span><span class="py">.contentOffset</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">sv1</span><span class="py">.contentOffset.x</span><span class="o">/</span><span class="mi">2</span><span class="p">,</span><span class="mi">0</span><span class="p">);</span>
    <span class="n">sv3</span><span class="py">.contentOffset</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">sv2</span><span class="py">.contentOffset.x</span><span class="o">/</span><span class="mi">2</span><span class="p">,</span><span class="mi">0</span><span class="p">);</span>
    <span class="n">sv4</span><span class="py">.contentOffset</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">sv3</span><span class="py">.contentOffset.x</span><span class="o">/</span><span class="mi">2</span><span class="p">,</span><span class="mi">0</span><span class="p">);</span>
    <span class="n">sv5</span><span class="py">.contentOffset</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="n">sv4</span><span class="py">.contentOffset.x</span><span class="o">/</span><span class="mi">2</span><span class="p">,</span><span class="mi">0</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We check to make sure that the object coming is in actually <tt>sv1</tt> and then we set the displacement of <tt>sv2</tt> to be <em>half</em> of that for <tt>sv1</tt>. We continue to daisy-chain all the displacement values for each layer until we get to the very last one. In this order each layer moves at half the speed of the one above it.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">6. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>There weren&#8217;t too many pictures in this tutorial, actually none at all. But, that&#8217;s mainly because the scrollviews were all invisible&#8230; So there wasn&#8217;t really anything to see. We added 5 scrollviews to the screen in a dynamic way and then added some interaction so that they all scroll at the same time, but at different speeds. This is a pretty rudimentary way of doing parallax scroll tricks, but it works really well.</p></div>
<div class="paragraph"><p>All in all, you learned how to dynamically generate scrollviews with content, overlay them and use KVO to link them all up.</p></div>
<div class="paragraph"><p>In a nutshell, we can now scroll scrollviews while scrolling a scrollview</p></div>
<div class="paragraph"><p><a href="parallaxScrolling/bizarro.jpg">Bizarro</a>!</p></div>
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
