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

<h2>Swipe Gestures</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5421765" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64685113" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>There are 4 basic swipe methods that come cooked into all visual objects in C4. In this tutorial I&#8217;ll show you how to use them.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_here_we_go">1. Here We Go</h2>
<div class="sectionbody">
<div class="paragraph"><p>Cooked into all visual objects are 4 methods you can use for interaction:</p></div>
<div class="ulist"><ul>
<li>
<p>
swipedUp
</p>
</li>
<li>
<p>
swipedDown
</p>
</li>
<li>
<p>
swipedLeft
</p>
</li>
<li>
<p>
swipedRight
</p>
</li>
</ul></div>
<div class="paragraph"><p>You can use these methods without needing to be declared, just like <tt>touchesBegan</tt>, <tt>touchesMoved</tt> and <tt>touchesEnded</tt>.</p></div>
<div class="sect2">
<h3 id="_the_variables">1.1. The Variables</h3>
<div class="paragraph"><p>For this example we&#8217;re going to create a label whose text changes depending on the direction of a swipe. After a couple of seconds the label is going to return to its original text. To do this we&#8217;re going to add the following variables to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">swipeLabel</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Timer</span> <span class="o">*</span><span class="n">resetTimer</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>The reset timer will trigger the change back to the original text.</p></div>
</div>
<div class="sect2">
<h3 id="_label_setup">1.2. Label Setup</h3>
<div class="paragraph"><p>To create the label, add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setupLabel</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;Helvetica&quot;</span> <span class="n">size:</span><span class="mi">96</span><span class="p">];</span>

    <span class="n">swipeLabel</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;Swipe Me&quot;</span> <span class="n">font:font</span><span class="p">];</span>
    <span class="p">[</span><span class="n">swipeLabel</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">swipeLabel</span><span class="py">.textAlignment</span> <span class="o">=</span> <span class="n">ALIGNTEXTCENTER</span><span class="p">;</span>
    <span class="n">swipeLabel</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="n">swipeLabel</span><span class="py">.userInteractionEnabled</span> <span class="o">=</span> <span class="nb">NO</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:swipeLabel</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This simply creates a font and a label which it then adds to the screen. We center the text, and make sure that it&#8217;s interaction is turned off so that it doesn&#8217;t interfere with any swipes we perform.</p></div>
<div class="paragraph"><p>Oh yeah, make sure to call this from your <tt>setup</tt> like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">setupLabel</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_label_updating">1.3. Label Updating</h3>
<div class="paragraph"><p>To update a label we&#8217;re going to give it some new text and recenter it based on the length of that text. Then, we&#8217;re going to trigger a timer to wait for 2 seconds before resetting the text to its original message. Add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">updateLabelWithText:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">newLabelText</span> <span class="p">{</span>
    <span class="n">swipeLabel</span><span class="py">.text</span> <span class="o">=</span> <span class="n">newLabelText</span><span class="p">;</span>
    <span class="p">[</span><span class="n">swipeLabel</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">swipeLabel</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>

    <span class="p">[</span><span class="n">resetTimer</span> <span class="n">invalidate</span><span class="p">];</span>
    <span class="n">resetTimer</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Timer</span> <span class="n">automaticTimerWithInterval:</span><span class="mf">2.0f</span> <span class="n">target:</span><span class="k">self</span> <span class="n">method:</span><span class="s">@&quot;resetLabel&quot;</span> <span class="n">repeats:</span><span class="nb">NO</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Pretty straightforward. The only thing to do now it to make sure we have a method that resets the label (it&#8217;s going to get called by the timer). Add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">resetLabel</span> <span class="p">{</span>
    <span class="n">swipeLabel</span><span class="py">.text</span> <span class="o">=</span> <span class="s">@&quot;Swipe Me&quot;</span><span class="p">;</span>
    <span class="p">[</span><span class="n">swipeLabel</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">swipeLabel</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">The <tt>NO</tt> part of the message for the timer means that it&#8217;s only going to trigger one time. The <tt>[restTimer invalidate]</tt> makes sure to stop the timer if it already is counting down, this lets us reset it to 2 seconds.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_swipes">2. The Swipes</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now we want to add the swipe methods to our project. Remember, <em>all</em> visual objects have these methods cooked into them. Add the following methods to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">swipedDown</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">updateLabelWithText:</span><span class="s">@&quot;DOWN&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">swipedLeft</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">updateLabelWithText:</span><span class="s">@&quot;LEFT&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">swipedRight</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">updateLabelWithText:</span><span class="s">@&quot;RIGHT&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">swipedUp</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">updateLabelWithText:</span><span class="s">@&quot;UP&quot;</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We&#8217;re just overriding each of these so that they trigger the <tt>updateLabelWithText:</tt> with the appropriate message.</p></div>
<div class="sect2">
<h3 id="_add_8217_em">2.1. Add&#8217;em</h3>
<div class="paragraph"><p>Now it&#8217;s time to add our listening code. This is the same procedure as listening for <tt>touchesBegan</tt> or the like. Add the following lines to your <tt>setup</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPEDOWN</span> <span class="n">name:</span><span class="s">@&quot;down&quot;</span> <span class="n">action:</span><span class="s">@&quot;swipedDown&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPELEFT</span> <span class="n">name:</span><span class="s">@&quot;left&quot;</span> <span class="n">action:</span><span class="s">@&quot;swipedLeft&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPERIGHT</span> <span class="n">name:</span><span class="s">@&quot;right&quot;</span> <span class="n">action:</span><span class="s">@&quot;swipedRight&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">addGesture:SWIPEUP</span> <span class="n">name:</span><span class="s">@&quot;up&quot;</span> <span class="n">action:</span><span class="s">@&quot;swipedUp&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, when you swipe the canvas in any direction you&#8217;re going to see the label change! (and reset 2 seconds after the last swipe was made).</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_listen">3. Listen</h2>
<div class="sectionbody">
<div class="paragraph"><p>For a last huzzah, and to show you that these methods work the same way as <tt>touchesBegan</tt> etc., add the following methods to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setupListeners</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;swipedUp&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;checkMessage:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;swipedDown&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;checkMessage:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;swipedLeft&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;checkMessage:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;swipedRight&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;checkMessage:&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">checkMessage:</span><span class="p">(</span><span class="nc">NSNotification</span> <span class="o">*</span><span class="p">)</span><span class="n">notification</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Log</span><span class="p">([</span><span class="n">notification</span> <span class="n">name</span><span class="p">]);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This listens for when a gesture is triggered and runs a method that simply prints out the name of the gesture&#8217;s notification. We could have added a call to <tt>checkMessage:</tt> in each of the swipe methods, but there&#8217;s a good reason to do it this way.</p></div>
<div class="paragraph"><p>When a <tt>SWIPE</tt> gesture is added to an object and its action is one of <tt>swipedDown</tt>, <tt>swipedUp</tt>, <tt>swipedLeft</tt>, <tt>swipedRight</tt> it will post a notification using the action&#8217;s name. This is the same for when you <tt>listenFor:</tt> any of <tt>touchesBegan</tt>, <tt>touchesMoved</tt>, <tt>touchesEnded</tt> or <tt>touchesCanceled</tt>. The main difference is that gestures for swipes need to be added where touches come already cooked into each by default.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">4. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>Short and sweet, this tutorial showed you how to use the default swipe gesture methods so that you can call your own methods when each of them happens. It also showed you how to set up a timer to reset everything after a few seconds. Finally, you were also introduced to the idea of being able to <tt>listenFor</tt> gestures.</p></div>
<div class="paragraph"><p>I&#8217;m outta here, high five.</p></div>
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
