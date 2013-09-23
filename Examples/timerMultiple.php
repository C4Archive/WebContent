<?php
// Include WordPress
define('WP_USE_THEMES', false);
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/new/wp-load.php";
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

<h2>Timer: Multiple Timers</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3251112" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/48913862" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>This is an advanced example showing how you can give ID numbers to timers and actually have them pass themselves into a method.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_a_special_method">1. A Special Method</h2>
<div class="sectionbody">
<div class="paragraph"><p>Before we get started, we have to create a special method to accept a timer. What we&#8217;re going to do is have each timer <strong>pass itself into this method</strong>.</p></div>
<div class="paragraph"><p>To create a method that accepts a timer, do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">randomizeShape:</span><span class="p">(</span><span class="nc">NSTimer</span> <span class="o">*</span><span class="p">)</span><span class="n">currentTimer</span> <span class="p">{</span>
    <span class="c1">//do stuff with the currentTimer</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">Why an NSTimer here?</div>Even though we&#8217;re working with a C4Timer, it really is an NSTimer that does the job of firing on time and calling the methods we want. So, when the NSTimer fires it will pass <em>itself</em> and not the C4Timer in which its encapsulated.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_two_timers">2. Two Timers</h2>
<div class="sectionbody">
<div class="paragraph"><p>We can create two different timers, one that starts automatically and one that starts 1 second after the first one.</p></div>
<div class="sect2">
<h3 id="_userinfo">2.1. UserInfo</h3>
<div class="paragraph"><p>Every timer has a property called <tt>userInfo</tt> to which you can assign an object. If you want to assign an ID number as the info for a timer you can pass it an <tt>NSNumber</tt> object (not a CGFloat).</p></div>
</div>
<div class="sect2">
<h3 id="_the_first_timer">2.2. The First Timer</h3>
<div class="paragraph"><p>To create an automatic timer with an ID number you can do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSNumber</span> <span class="o">*</span><span class="n">timerID</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSNumber</span> <span class="n">numberWithInt:</span><span class="mi">0</span><span class="p">];</span>
<span class="n">firstTimer</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Timer</span> <span class="n">automaticTimerWithInterval:</span><span class="mf">2.0f</span>
                                          <span class="n">target:</span><span class="k">self</span>
                                          <span class="n">method:</span><span class="s">@&quot;randomizeShape:&quot;</span>
                                        <span class="n">userInfo:timerID</span>
                                         <span class="n">repeats:</span><span class="nb">YES</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">YES, your colon is important&#8230;</div>You may have noticed that the method name in the code above has a <tt>:</tt> at the end of it&#8230; This implies that the method will take an object. Since we know we&#8217;re calling this method with a C4Timer, we know that the object being passed will be an NSTimer.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_a_second_timer">2.3. A Second Timer</h3>
<div class="paragraph"><p>We will construct our second timer using a <tt>fireDate</tt>&#8230; Basically a number of seconds after which we want our timer to start firing. Also, because this is our second timer, we will update the timerID.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">timerID</span> <span class="o">=</span> <span class="nc">NSNumber</span> <span class="n">numberWithInt:</span><span class="mi">1</span><span class="p">];</span>
<span class="n">secondTimer</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Timer</span> <span class="n">timerWithFireDate:</span><span class="p">[</span><span class="nc">NSDate</span> <span class="n">dateWithTimeIntervalSinceNow:</span><span class="mf">1.0f</span><span class="p">]</span>
                                <span class="n">interval:</span><span class="mf">2.0f</span>
                                  <span class="n">target:</span><span class="k">self</span>
                                  <span class="n">method:</span><span class="s">@&quot;randomizeShape:&quot;</span>
                                <span class="n">userInfo:timerID</span>
                                 <span class="n">repeats:</span><span class="nb">YES</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_summary">3. Summary</h2>
<div class="sectionbody">
<div class="paragraph"><p>Ok, so what we did here in a nutshell is&#8230;
. We created a method that accepts a timer as its object
. We created an <tt>automatic</tt> timer with an ID of 0, which fires as soon as possible
. We created a timer with a delayed fire date of 1 second, with an ID of 1</p></div>
<div class="paragraph"><p>These steps set up a situation where the <tt>randomizeShape:</tt> method is actually being called every second <em>by two different offset timers</em>.</p></div>
<div class="sect2">
<h3 id="_what_happens_next">3.1. What happens next?</h3>
<div class="paragraph"><p>In order to take advantage of this situation we code into the <tt>randomizeShape</tt> some logic that differentiates the two timers and chooses to do different things for each timer.</p></div>
<div class="paragraph"><p>For the example in the video, we&#8217;ve separated an animation into 2 difference cases:</p></div>
<div class="ulist"><ul>
<li>
<p>
Timer 0 changes the <tt>fillColor</tt> of a shape
</p>
</li>
<li>
<p>
Timer 1 changes the <tt>strokeColor</tt> of a shape
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">randomizeShape:</span><span class="p">(</span><span class="nc">NSTimer</span> <span class="o">*</span><span class="p">)</span><span class="n">timer</span> <span class="p">{</span>
    <span class="c1">//figure out the current timer&#39;s id</span>
    <span class="kt">int</span> <span class="n">currentTimer</span> <span class="o">=</span> <span class="p">[(</span><span class="nc">NSNumber</span> <span class="o">*</span><span class="p">)</span><span class="n">timer</span><span class="py">.userInfo</span> <span class="n">intValue</span><span class="p">];</span>

    <span class="c1">//change fill or stroke accordingly</span>
    <span class="k">if</span><span class="p">(</span><span class="n">currentTimer</span> <span class="o">==</span> <span class="mi">0</span><span class="p">)</span> <span class="n">rect</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">randomColor</span><span class="p">];</span>
    <span class="k">else</span> <span class="n">rect</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">randomColor</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
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
