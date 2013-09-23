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

<h2>Taps and Touches</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5421700" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64685162" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial I&#8217;ll show you how to build a little application that receives <tt>TAP</tt> gestures and then updates its interface to show you how many touches and how many taps were recognized.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_the_problem">1. The Problem</h2>
<div class="sectionbody">
<div class="paragraph"><p>I want to build an application that will allow me to register what kind of <tt>TAP</tt> gesture has just been registered. Seems simple enough&#8230; But, there are a couple of hurdles. First, we need to make sure that <em>only one</em> gesture is ever being recognized. Second, we need to create gestures for all possible combinations of taps and touches. Third, we need to update a dynamic label and then reset it shortly thereafter.</p></div>
<div class="sect2">
<h3 id="_the_vars">1.1. The Vars</h3>
<div class="paragraph"><p>As usual, let&#8217;s start by outlining the variables for this project. Add the following to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">tapsAndTouches</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Timer</span> <span class="o">*</span><span class="n">resetTimer</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We need a persistent reference to our label, but also to our timer. So, we define them here as class variables.</p></div>
</div>
<div class="sect2">
<h3 id="_the_label">1.2. The Label</h3>
<div class="paragraph"><p>The next thing we can do is to create our label with a default text. Add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">createLabel</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;AvenirNextCondensed-Heavy&quot;</span> <span class="n">size:</span><span class="mi">96</span><span class="p">];</span>
    <span class="n">tapsAndTouches</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;TAPS</span><span class="se">\n</span><span class="s">&amp;</span><span class="se">\n</span><span class="s">TOUCHES&quot;</span> <span class="n">font:font</span><span class="p">];</span>
    <span class="n">tapsAndTouches</span><span class="py">.numberOfLines</span> <span class="o">=</span> <span class="mi">3</span><span class="p">;</span>
    <span class="p">[</span><span class="n">tapsAndTouches</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">tapsAndTouches</span><span class="py">.textAlignment</span> <span class="o">=</span> <span class="n">ALIGNTEXTCENTER</span><span class="p">;</span>
    <span class="n">tapsAndTouches</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="n">tapsAndTouches</span><span class="py">.userInteractionEnabled</span> <span class="o">=</span> <span class="nb">NO</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:tapsAndTouches</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Pretty straightforward. We create a font, we create a string that says <tt>"TAPS &amp; TOUCHES"</tt> on 3 different lines. We size the label, center it and add it to the canvas. We also make sure to disable its interaction so it doesn&#8217;t interfere with the canvas.</p></div>
<div class="paragraph"><p>Oh yeah, call this method from the <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">createLabel</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_label_updating">2. Label Updating</h2>
<div class="sectionbody">
<div class="paragraph"><p>The principle interaction of this app we&#8217;re working on is to register a tap gesture. Then update a label that displays the number of touches and the number of taps for the gesture. To do this add a method to your project that takes 2 string variables, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">updateLabelTaps:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">tapString</span> <span class="n">touches:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">touchString</span> <span class="p">{</span>
    <span class="n">tapsAndTouches</span><span class="py">.text</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;%@</span><span class="se">\n</span><span class="s">&amp;</span><span class="se">\n</span><span class="s">%@&quot;</span><span class="p">,</span><span class="n">tapString</span><span class="p">,</span><span class="n">touchString</span><span class="p">];</span>
    <span class="p">[</span><span class="n">tapsAndTouches</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">tapsAndTouches</span><span class="py">.width</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.width</span><span class="p">;</span>
    <span class="n">tapsAndTouches</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>

    <span class="p">[</span><span class="n">resetTimer</span> <span class="n">invalidate</span><span class="p">];</span>
    <span class="n">resetTimer</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Timer</span> <span class="n">automaticTimerWithInterval:</span><span class="mf">2.0f</span> <span class="n">target:</span><span class="k">self</span> <span class="n">method:</span><span class="s">@&quot;resetLabel&quot;</span> <span class="n">repeats:</span><span class="nb">NO</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method accepts 2 strings and updates the label. It also starts a timer that will wait 2 seconds before triggering a method that will reset the label.</p></div>
<div class="sect2">
<h3 id="_reset">2.1. Reset</h3>
<div class="paragraph"><p>The reset method is pretty simple, add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">resetLabel</span> <span class="p">{</span>
    <span class="n">tapsAndTouches</span><span class="py">.text</span> <span class="o">=</span> <span class="s">@&quot;TAPS</span><span class="se">\n</span><span class="s">&amp;</span><span class="se">\n</span><span class="s">TOUCHES&quot;</span><span class="p">;</span>
    <span class="p">[</span><span class="n">tapsAndTouches</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">tapsAndTouches</span><span class="py">.width</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.width</span><span class="p">;</span>
    <span class="n">tapsAndTouches</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_tap">3. The TAP</h2>
<div class="sectionbody">
<div class="paragraph"><p>Before creating all the tap gestures and dealing with receiving only the one we want, let&#8217;s work through creating a method that will pull apart the gesture and trigger the <tt>updateLabelTaps:Touches:</tt> method. Add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">tap:</span><span class="p">(</span><span class="nc">UITapGestureRecognizer</span> <span class="o">*</span><span class="p">)</span><span class="n">tapGesture</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This is the method to which we&#8217;re going to attach all our gestures.</p></div>
<div class="sect2">
<h3 id="_tapstring">3.1. tapString</h3>
<div class="paragraph"><p>We want to pass a string representation of our taps to update our label. We&#8217;re going to do this by using a switch statement. We use a switch to give us the opportunity to flip between different states for 1, 2, 3,&#8230; taps.</p></div>
<div class="paragraph"><p>Add the following to your <tt>tap:</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSString</span> <span class="o">*</span><span class="n">tapString</span><span class="p">;</span>
<span class="k">switch</span> <span class="p">(</span><span class="n">tapGesture</span><span class="py">.numberOfTapsRequired</span><span class="p">)</span> <span class="p">{</span>
    <span class="k">case</span> <span class="mi">1</span><span class="o">:</span>
        <span class="n">tapString</span> <span class="o">=</span> <span class="s">@&quot;SINGLE TAP&quot;</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">case</span> <span class="mi">2</span><span class="o">:</span>
        <span class="n">tapString</span> <span class="o">=</span> <span class="s">@&quot;DOUBLE TAP&quot;</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">case</span> <span class="mi">3</span><span class="o">:</span>
        <span class="n">tapString</span> <span class="o">=</span> <span class="s">@&quot;TRIPLE TAP&quot;</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">default</span><span class="o">:</span>
        <span class="n">tapString</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;%d TAPS&quot;</span><span class="p">,</span><span class="n">tapGesture</span><span class="py">.numberOfTapsRequired</span><span class="p">];</span>
        <span class="k">break</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This looks into the gesture and grabs the <tt>numberOfTapsRequired</tt> for the gesture to recognize. Then, we create different strings for each state using the words <tt>SINGLE</tt>, <tt>DOUBLE</tt>, <tt>TRIPLE</tt> or a number representation for the string.</p></div>
</div>
<div class="sect2">
<h3 id="_touchstring">3.2. touchString</h3>
<div class="paragraph"><p>Next, we&#8217;re going to use the same trick for creating a string to represent the number of touches. Add the following to the <tt>tap:</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSString</span> <span class="o">*</span><span class="n">touchString</span><span class="p">;</span>
<span class="k">switch</span> <span class="p">(</span><span class="n">tapGesture</span><span class="py">.numberOfTouches</span><span class="p">)</span> <span class="p">{</span>
    <span class="k">case</span> <span class="mi">1</span><span class="o">:</span>
        <span class="n">touchString</span> <span class="o">=</span> <span class="s">@&quot;1 TOUCH&quot;</span><span class="p">;</span>
        <span class="k">break</span><span class="p">;</span>
    <span class="k">default</span><span class="o">:</span>
        <span class="n">touchString</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSString</span> <span class="n">stringWithFormat:</span><span class="s">@&quot;%d TOUCHES&quot;</span><span class="p">,</span><span class="n">tapGesture</span><span class="py">.numberOfTouches</span><span class="p">];</span>
        <span class="k">break</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This simply distinguishes the text for 1 or more touches.</p></div>
</div>
<div class="sect2">
<h3 id="_updating">3.3. Updating</h3>
<div class="paragraph"><p>Now that we have 2 strings necessary for updating we can add the following to our <tt>tap:</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">updateLabelTaps:tapString</span> <span class="n">touches:touchString</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>When this gets called right at the end of the <tt>tap:</tt> method our label will change.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_a_8217_settin_8217_up">4. A&#8217;Settin&#8217;Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now, we&#8217;re going to get back to our <tt>setup</tt> method. Here we have to do 2 main things:</p></div>
<div class="ulist"><ul>
<li>
<p>
Create all our gestures
</p>
</li>
<li>
<p>
Limit recognition to 1 gesture at a time
</p>
</li>
</ul></div>
<div class="sect2">
<h3 id="_create_8217_em">4.1. Create&#8217;em</h3>
<div class="paragraph"><p>Let&#8217;s create gestures for up to 5 fingers and 5 taps. This means that we want to register:</p></div>
<div class="ulist"><ul>
<li>
<p>
1-touch 1-tap
</p>
</li>
<li>
<p>
1-touch 2-taps
</p>
</li>
<li>
<p>
&#8230;
</p>
</li>
<li>
<p>
5-touches 5-taps
</p>
</li>
</ul></div>
<div class="paragraph"><p>To do this we have to create unique gestures for each one of the cases listed above (25 in total). We can do this by adding a <tt>for</tt> loop. Add the following to your <tt>setup</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSInteger</span> <span class="n">tapCount</span> <span class="o">=</span> <span class="mi">5</span><span class="p">;</span>
<span class="nc">NSInteger</span> <span class="n">touchCount</span> <span class="o">=</span> <span class="mi">5</span><span class="p">;</span>
<span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="n">tapCount</span> <span class="o">+</span> <span class="mi">1</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">j</span> <span class="o">=</span> <span class="mi">1</span><span class="p">;</span> <span class="n">j</span> <span class="o">&lt;</span> <span class="n">touchCount</span> <span class="o">+</span> <span class="mi">1</span><span class="p">;</span> <span class="n">j</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="nc">NSString</span> <span class="o">*</span><span class="n">tapName</span> <span class="o">=</span> <span class="p">[</span><span class="s">@&quot;tap&quot;</span> <span class="n">stringByAppendingFormat:</span><span class="s">@&quot;%d_%d&quot;</span><span class="p">,</span><span class="n">i</span><span class="p">,</span><span class="n">j</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span> <span class="n">addGesture:TAP</span> <span class="n">name:tapName</span> <span class="n">action:</span><span class="s">@&quot;tap:&quot;</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTapsRequired:i</span> <span class="n">forGesture:tapName</span><span class="p">];</span>
        <span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:j</span> <span class="n">forGesture:tapName</span><span class="p">];</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This loop runs through all cases creating gestures whose names are defined like: <tt>tap1_1</tt>, <tt>tap1_2</tt>, &#8230; , <tt>tap5_5</tt>. It also sets the number of taps and touches required for each gesture.</p></div>
</div>
<div class="sect2">
<h3 id="_let_8217_em_fail">4.2. Let&#8217;em Fail</h3>
<div class="paragraph"><p>By default <em>all</em> gestures will trigger. For instance, if you make a triple-tap gesture both the single and double-tap gestures will trigger before you get to the triple-tap. The way you get around this is by allowing gestures to <em>fail</em>.</p></div>
<div class="paragraph"><p>For a triple tap failing works like this:</p></div>
<div class="ulist"><ul>
<li>
<p>
single tap gets registered (triggers if both double- and triple-taps fail)
</p>
</li>
<li>
<p>
double tap gets registered (triggers if triple-tap fails)
</p>
</li>
</ul></div>
<div class="paragraph"><p>We can set this logic up by adding the following <tt>for</tt> loop to our <tt>setup</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSDictionary</span> <span class="o">*</span><span class="n">allGestures</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">allGestures</span><span class="p">];</span>
<span class="nc">NSArray</span> <span class="o">*</span><span class="n">gestNames</span> <span class="o">=</span> <span class="p">[</span><span class="n">allGestures</span> <span class="n">allKeys</span><span class="p">];</span>
<span class="n">gestNames</span> <span class="o">=</span> <span class="p">[</span><span class="n">gestNames</span> <span class="n">sortedArrayUsingFunction:strSort</span> <span class="n">context:</span><span class="nb">NULL</span><span class="p">];</span>
<span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="p">[</span><span class="n">gestNames</span> <span class="n">count</span><span class="p">];</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">UIGestureRecognizer</span> <span class="o">*</span><span class="n">g</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">gestureForName:gestNames</span><span class="p">[</span><span class="n">i</span><span class="p">]];</span>
    <span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">j</span> <span class="o">=</span> <span class="n">i</span><span class="o">+</span><span class="mi">1</span><span class="p">;</span> <span class="n">j</span> <span class="o">&lt;</span> <span class="p">[</span><span class="n">gestNames</span> <span class="n">count</span><span class="p">];</span> <span class="n">j</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
        <span class="p">[</span><span class="n">g</span> <span class="n">requireGestureRecognizerToFail:</span><span class="p">[</span><span class="k">self</span> <span class="n">gestureForName:gestNames</span><span class="p">[</span><span class="n">j</span><span class="p">]]];</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>First, grab all the gestures attached to the canvas. Then grab the names of all the gestures, ordering them based on their names. Finally run a double-loop that requires all gestures to fail for anything past the current gesture.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">The <tt>requireGestureRecognizerToFail</tt> will actually wait for a tenth of a second or so to make sure any other gesture isn&#8217;t triggered. The gesture won&#8217;t recognize immediately but it doesn&#8217;t take too long&#8230; This is normal.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_past">4.3. Past?!</h3>
<div class="paragraph"><p>I mentioned that we want to trigger only if <em>any other gesture</em> past the current one fails. For instance, we&#8217;ll trigger single-tap only if double-tap fails.</p></div>
<div class="paragraph"><p>We don&#8217;t need to say double-tap triggers if single-tap fails.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">5. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;ve created a fairly simple application that allows the user to tap the screen up to 5 times with up to 5 fingers. A label in the center of the screen will change its text to reflect the number of touches and taps that are registered. Though the app looks and acts simple there were actually a couple of tricky nested loops to create and modify the gestures so that we only react to the last one that was triggered.</p></div>
<div class="paragraph"><p>Oh, the old familiar places.</p></div>
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
