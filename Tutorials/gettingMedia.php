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

<h2>Getting Media</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5424012" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684937" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Two things: 1) Get Images from the Web 2) Get Movies from the Web. That&#8217;s what we&#8217;re doing here.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_the_url_of_media">1. The URL of Media</h2>
<div class="sectionbody">
<div class="paragraph"><p>It&#8217;s just as easy to create visual objects from downloadable web media as it is to create them from files in your application. In this tutorial I&#8217;ll show you how to grab data from the internet and turn that into an image. I&#8217;ll also show you how to grab movies from the internet in 2 ways: 1) from your own source 2) from Vimeo.</p></div>
<div class="paragraph"><p>The main thing we&#8217;re going to do in each case is create these visual objects using URLs.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/warning.png" alt="Warning" />
</td>
<td class="content">You need to be connected to the internet for this tutorial (either on your device, or via your comp for the simulator).</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_implement_amp_setup">2. Implement &amp; Setup</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;ve done this implementation and setup a lot, so let&#8217;s blitz through it. Add the following to your application:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">robotsImage</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Movie</span> <span class="o">*</span><span class="n">robotsMovie</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="k">self</span><span class="py">.canvas.backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">blackColor</span><span class="p">];</span>

    <span class="n">robotsImage</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createRobotsImage</span><span class="p">];</span>
    <span class="k">if</span><span class="p">(</span><span class="n">robotsImage</span> <span class="o">!=</span> <span class="nb">nil</span><span class="p">)</span> <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:robotsImage</span><span class="p">];</span>

    <span class="n">robotsMovie</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createRobotsMovie</span><span class="p">];</span>
    <span class="k">if</span><span class="p">(</span><span class="n">robotsMovie</span> <span class="o">!=</span> <span class="nb">nil</span><span class="p">)</span> <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addMovie:robotsMovie</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>In a nutshell, what we&#8217;re doing is creating 2 objects: an image and a movie. If the objects are created (i.e. they aren&#8217;t <tt>nil</tt>) add them to the canvas. Easy.</p></div>
<div class="sect2">
<h3 id="_what_the_tt_nil_tt">2.1. What the <tt>nil</tt>?</h3>
<div class="paragraph"><p>When you create objects from URLs they might return <tt>nil</tt> for a couple of reasons:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Your device might not be connected to the internet
</p>
</li>
<li>
<p>
The URL might be wrong
</p>
</li>
</ol></div>
<div class="paragraph"><p>If either of these cases happens, then the initializer methods return <tt>nil</tt> instead of giving you an object.</p></div>
<div class="paragraph"><p>You need to check to make sure there&#8217;s an actual object to add to the canvas before adding it, otherwise the app will crash.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Try editing the image and the movie urls to see what happens.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_grab_an_image">3. Grab An Image</h2>
<div class="sectionbody">
<div class="paragraph"><p>To create a <tt>C4Image</tt> from a file on the web, we&#8217;re going to pass in a web url as a string. Add the following method to your application:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="p">)</span><span class="n">createRobotsImage</span> <span class="p">{</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">robotsImageUrl</span> <span class="o">=</span> <span class="s">@&quot;http://www.c4ios.com/tutorials/gettingMedia/robots.png&quot;</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">imageFromURL</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageWithURL:robotsImageUrl</span><span class="p">];</span>
    <span class="n">imageFromURL</span><span class="py">.width</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.width</span><span class="p">;</span>
    <span class="n">imageFromURL</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>

    <span class="k">return</span> <span class="n">imageFromURL</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We try to create an image, we set its width and center properties. Then we return it. If it is <tt>nil</tt> we return it anyways.</p></div>
<div class="paragraph"><p>The image we&#8217;re downloading is actually <tt>1600x1200</tt> but still a pretty small file, so with a fast internet connection your device should be able to grab this data lickety-split.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">You can actually send messages to <tt>nil</tt> objects&#8230; It&#8217;s a bit strange to think that if there&#8217;s no movie that you can actually set its width and center&#8230; Nothing will happen though. Be carful of this, it slips me up sometimes when I&#8217;m looking for a bug and its seems like my code is normal but nothing is happening.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_grab_a_movie">4. Grab A Movie</h2>
<div class="sectionbody">
<div class="paragraph"><p>To create a <tt>C4Movie</tt> from a file on the web, we&#8217;re going to pass in a web url as a string. Add the following method to your application:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="n-ProjectClass">C4Movie</span> <span class="o">*</span><span class="p">)</span><span class="n">createRobotsMovie</span> <span class="p">{</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">robotsURL</span> <span class="o">=</span> <span class="s">@&quot;http://c4ios.com/tutorials/gettingMedia/robots.mp4&quot;</span><span class="p">;</span>

    <span class="n-ProjectClass">C4Movie</span> <span class="o">*</span><span class="n">movieFromURL</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Movie</span> <span class="n">movieWithURL:robotsURL</span><span class="p">];</span>
    <span class="n">movieFromURL</span><span class="py">.zPosition</span> <span class="o">=</span> <span class="mi">200</span><span class="p">;</span>
    <span class="n">movieFromURL</span><span class="py">.perspectiveDistance</span> <span class="o">=</span> <span class="mf">1000.0f</span><span class="p">;</span>
    <span class="n">movieFromURL</span><span class="py">.alpha</span> <span class="o">=</span> <span class="mf">0.0f</span><span class="p">;</span>
    <span class="n">movieFromURL</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.center.x</span><span class="p">,</span><span class="k">self</span><span class="py">.canvas.center.y</span><span class="o">-</span><span class="mi">10</span><span class="p">);</span>
    <span class="n">movieFromURL</span><span class="py">.loops</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
    <span class="n">movieFromURL</span><span class="py">.shouldAutoplay</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">runMethod:</span><span class="s">@&quot;animate&quot;</span> <span class="n">afterDelay:</span><span class="mf">0.5f</span><span class="p">];</span>

    <span class="k">return</span> <span class="n">movieFromURL</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Once we create the movie we position it so that it&#8217;s above the image by some <tt>zPosition</tt> distance. We then give it perspective, set its alpha to zero, position it and tell it to repeat, tell it to play automatically, and then wait a half second before animating the movie</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">You can actually play movies from Vimeo as well, so long as you links to actual video files and not just players.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_put_an_animation_on_it">4.1. Put An Animation On It</h3>
<div class="paragraph"><p>Add a method that slowly rotates the movie:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">animate</span> <span class="p">{</span>
    <span class="n">robotsMovie</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
    <span class="n">robotsMovie</span><span class="py">.alpha</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>

    <span class="n">robotsMovie</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span> <span class="o">|</span> <span class="n">LINEAR</span><span class="p">;</span>
    <span class="n">robotsMovie</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="n">robotsMovie</span><span class="py">.duration</span><span class="p">;</span>
    <span class="n">robotsMovie</span><span class="py">.rotationY</span> <span class="o">+=</span> <span class="n">TWO_PI</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method animates the movie&#8217;s alpha from 0 to 1 for 1 second, and starts rotating it so that it does a full 360 through the entire length of the movie. Pretty straightforward.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">5. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>We created 2 different visual objects from media we downloaded from the web. We grabbed one video from a Vimeo link, and one from a private link.</p></div>
<div class="paragraph"><p>Tuesday&#8217;s coming, did you bring your coat?</p></div>
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
