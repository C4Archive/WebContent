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

<h2>Pan Gestures</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5423205" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684697" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial I&#8217;ll show you how to use <tt>PAN</tt> gestures to move objects and change states. We&#8217;re going to build a dynamic label that moves and changes its text based on how many touches there are.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_intro">1. Intro</h2>
<div class="sectionbody">
<div class="paragraph"><p>Using the <tt>move:</tt> method for visual objects is pretty easy, all you do is the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">addGesture:</span><span class="s">@&quot;PAN&quot;</span> <span class="n">name:</span><span class="s">@&quot;pan&quot;</span> <span class="n">action:</span><span class="s">@&quot;move:&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>But, did you know that it&#8217;s pretty easy to override this and add some fancy customization? We&#8217;re going to do that for the canvas to trigger the motion and changes for our label.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_create_the_label">2. Create the Label</h2>
<div class="sectionbody">
<div class="paragraph"><p>First things first, let&#8217;s build a label and put it on the canvas. We need a class reference for the label, so add this to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">label</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Then, add the following method to set it up:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setupLabel</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;AvenirNextCondensed-Heavy&quot;</span> <span class="n">size:</span><span class="mi">96</span><span class="p">];</span>
    <span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;I&#39;m A Drag&quot;</span> <span class="n">font:font</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="n">label</span><span class="py">.userInteractionEnabled</span> <span class="o">=</span> <span class="nb">NO</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:label</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Pretty simple setup actually. It just constructs the label with a big font, centers it and adds it to the canvas.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_setup">3. Setup</h2>
<div class="sectionbody">
<div class="paragraph"><p>As usual, we setup our workspace to do the things we want.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">setupLabel</span><span class="p">];</span>

    <span class="k">self</span><span class="py">.canvas.multipleTouchEnabled</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:PAN</span> <span class="n">name:</span><span class="s">@&quot;pan&quot;</span> <span class="n">action:</span><span class="s">@&quot;move:&quot;</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Because we&#8217;re working with multiple touches we need to tell the canvas to listen for them, so we set <tt>multipleTouchEnabled</tt> to <tt>YES</tt> and we&#8217;re all good. If we didn&#8217;t, we&#8217;d only ever register one touch at a time.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">This is true for all visual objects!</td>
</tr></table>
</div>
<div class="paragraph"><p>Now, all we do is add the gesture to the canvas and we&#8217;re done setting things up.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_maxium_override">4. Maxium Override</h2>
<div class="sectionbody">
<div class="paragraph"><p>How many of you are old enough to get this joke? Anyways, I am.</p></div>
<div class="paragraph"><p>If you try to do anything with the canvas now nothing will happen! The reason is that we need to override the <tt>move:</tt> method for the canvas. Add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">move:</span><span class="p">(</span><span class="nc">UIPanGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="n">recognizer</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">label</span> <span class="n">move:recognizer</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>What happens now is that we pass the gesture to our label and trigger our label&#8217;s <tt>move:</tt> method! So even though we&#8217;re interacting with the canvas we can actually simulate interacting with the label.</p></div>
<div class="sect2">
<h3 id="_the_beginning">4.1. The Beginning</h3>
<div class="paragraph"><p>I wanted to add a little flair to the beginning of the gesture so that the label picks up the number of touches being registered and changes its text content accordingly.</p></div>
<div class="paragraph"><p>You can do this by accessing the gesture&#8217;s <tt>numberOfTouches</tt> property. Add the following to the <tt>move:</tt> method.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">(</span><span class="n">recognizer</span><span class="py">.state</span> <span class="o">==</span> <span class="nc">UIGestureRecognizerStateBegan</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">NSInteger</span> <span class="n">touchCount</span> <span class="o">=</span> <span class="n">recognizer</span><span class="py">.numberOfTouches</span><span class="p">;</span>
    <span class="n">label</span><span class="py">.text</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;%d Touch Pan&quot;</span><span class="p">,</span> <span class="n">touchCount</span><span class="p">];</span>
    <span class="p">[</span><span class="n">label</span> <span class="n">sizeToFit</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Pretty straightforward! We grab the number of touches for the gesture when it starts and create a string. We then use the string to change the content of the label and update its text.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Remember, the position of the label is constantly being updated by its <tt>move:</tt> method.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_all_good_things">4.2. All Good Things</h3>
<div class="paragraph"><p>Our gesture must come to an end point. When it does we want it to move back to the center of the canvas, so we add a little bit of animation. Add the following <em>after</em> the previous <tt>if</tt> statement you wrote:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre> <span class="k">else</span> <span class="nf">if</span> <span class="p">(</span><span class="n">recognizer</span><span class="py">.state</span> <span class="o">==</span> <span class="nc">UIGestureRecognizerStateEnded</span><span class="p">)</span> <span class="p"> </span>
    <span class="n">label</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">0.25f</span><span class="p">;</span>
    <span class="n">label</span><span class="py">.text</span> <span class="o">=</span> <span class="s">@&quot;I&#39;m a Drag&quot;</span><span class="p">;</span>
    <span class="p">[</span><span class="n">label</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This basically checks to see if the gesture ended and if so it creates an animation for the label to move back to the center of the canvas. It also resets its text.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">5. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This is a pretty short tutorial on how to override the <tt>move:</tt> method which is cooked into all visual objects. This is pretty handy because it means you can add customization depending on the state of the gesture recognizer.</p></div>
<div class="paragraph"><p>Finito.</p></div>
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
