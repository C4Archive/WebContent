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

<h2>Interaction</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Interaction is <em>huge</em> in C4. Every kind of object can interact with one another via notifications and all visual object have some level of touch interaction. Tonight we&#8217;re going to step through most of the concepts surrounding interaction, starting with notifications.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_notifications">1. Notifications</h2>
<div class="sectionbody">
<div class="paragraph"><p>IMHO this is one of <em>the</em> defining features of C4. All objects can <em>communicate</em> with one another. A simple example of this happens when a square says "hey I was touched" and a circle who is listening to the square hears the message and changes its own color. Neither of these two objects has to have a reference to one another because they can interact via their app&#8217;s <em>notification center</em>.</p></div>
<div class="paragraph"><p>Let&#8217;s have a look&#8230;</p></div>
<div class="sect2">
<h3 id="_posting">1.1. Posting</h3>
<div class="paragraph"><p>An object can post notifications. This is like standing in the middle of a room and saying something. If there&#8217;s a crowd of people, some might listen to you, others might ignore you. In either case, the act of just saying something is like <em>posting a notification</em>.</p></div>
</div>
<div class="sect2">
<h3 id="_say_something">1.2. Say Something</h3>
<div class="paragraph"><p>To get an object to say something you can do this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">postNotification:</span><span class="s">@&quot;aMessage&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">postNotification:</span><span class="s">@&quot;nobodyListensToMe&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Basically, you can put anything as the message for the notification (but it has to be a string).</p></div>
</div>
<div class="sect2">
<h3 id="_where_does_it_go">1.3. Where Does It Go?</h3>
<div class="paragraph"><p>Nowhere, kinda. If you post something and nothing is listening for that message then it&#8217;s kinda like you didn&#8217;t do anything in the first place&#8230; Like a tree falling in a forest.</p></div>
</div>
<div class="sect2">
<h3 id="_listening">1.4. Listening</h3>
<div class="paragraph"><p>For this stuff to really work, there needs to be some object speaking and another listening. Furthermore, when an object listens for a message it should also react by running a method.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;aMessage&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;test&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;nobodyListensToMe&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;thatsBecauseYoureBoring&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Objects can listen to one another, or to themselves&#8230; An object listening to itself is a bit redundant, but illustrates the concept of notifications pretty well:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;nobodyListensToMe&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;thatsBecauseYoureBoring&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
        <span class="p">[</span><span class="k">self</span> <span class="n">postNotification:</span><span class="s">@&quot;nobodyListensToMe&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">thatsBecauseYoureBoring</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Log</span><span class="p">(</span><span class="s">@&quot;That&#39;s because you&#39;re boring&quot;</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cooked_in_notifications">2. Cooked-In Notifications</h2>
<div class="sectionbody">
<div class="paragraph"><p>A lot of objects already have notifications cooked into them. For instance, when <em>any</em> object is touched it posts a <tt>touchesBegan</tt> method. There are a bunch of messages that C4 objects broadcast out of the box so that you can more easily set up interactions.</p></div>
<div class="sect2">
<h3 id="_the_short_list">2.1. The Short List</h3>
<div class="paragraph"><p>Here&#8217;s a list of basic notifications you&#8217;ll use a lot in your development:</p></div>
<div class="listingblock">
<div class="title">all objects</div>
<div class="content"><div class="highlight"><pre><span class="s">@&quot;touchesBegan&quot;</span>
<span class="s">@&quot;touchesMoved&quot;</span>
<span class="s">@&quot;touchesEnded&quot;</span>
<span class="s">@&quot;swipedLeft&quot;</span>
<span class="s">@&quot;swipedRight&quot;</span>
<span class="s">@&quot;swipedUp&quot;</span>
<span class="s">@&quot;swipedDown&quot;</span>
<span class="s">@&quot;moved&quot;</span>
<span class="s">@&quot;tapped&quot;</span>
<span class="s">@&quot;pressedLong&quot;</span>
</pre></div></div></div>
<div class="listingblock">
<div class="title">buttons</div>
<div class="content"><div class="highlight"><pre><span class="s">@&quot;trackingBegan&quot;</span>
<span class="s">@&quot;trackingContinued&quot;</span>
<span class="s">@&quot;trackingEnded&quot;</span>
<span class="s">@&quot;trackingCancelled&quot;</span>
</pre></div></div></div>
<div class="listingblock">
<div class="title">random ones</div>
<div class="content"><div class="highlight"><pre><span class="s">@&quot;imageWasCaptured&quot;</span>             <span class="c1">//for the camera</span>
<span class="s">@&quot;pixelDataWasLoaded&quot;</span>           <span class="c1">//for images</span>
<span class="s">@&quot;movieIsReadyForPlayback&quot;</span>      <span class="c1">//for movies</span>
<span class="s">@&quot;movieReachedEnd&quot;</span>                      <span class="c1">//for movies</span>
<span class="s">@&quot;endedNormally&quot;</span>                        <span class="c1">//for audio samples</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_the_long_list">2.2. The Long List</h3>
<div class="paragraph"><p>All image filters post a notification when they&#8217;re complete. The message is the corresponding CoreImage filter name <em>plus</em> the word <tt>Complete</tt>. There&#8217;s something like 96 filters, so we wont go through them all. The following will give you a taste of how they&#8217;re written out:</p></div>
<div class="listingblock">
<div class="title">image filters</div>
<div class="content"><div class="highlight"><pre><span class="s">@&quot;CISepiaToneComplete&quot;</span>
<span class="s">@&quot;CIColorBurnBlendModeComplete&quot;</span>
<span class="s">@&quot;CIAreaHistogramComplete&quot;</span>
</pre></div></div></div>
<div class="paragraph"><p>etc&#8230;</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_masking">3. Masking</h2>
<div class="sectionbody">
<div class="paragraph"><p>In some strange way, I consider this a form of one object interacting with another because you need one to affect the other. You can use either shapes or images as masks for other objects.</p></div>
<div class="sect2">
<h3 id="_shapes">3.1. Shapes</h3>
<div class="paragraph"><p>Create a shape, then use it as a mask:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">object</span><span class="py">.mask</span> <span class="o">=</span> <span class="n">shape</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_images">3.2. Images</h3>
<div class="paragraph"><p>Create an image <em>with an alpha component</em> and use it as a mask:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">object</span><span class="py">.mask</span> <span class="o">=</span> <span class="n">image</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>GIST:Check out example that shows how to mask with <a href="http:/examples/maskingShapes.php">shapes</a>, and this one that uses <a href="/examples/maskingImages.php">images</a>.</p></div>
<div class="paragraph"><p>That&#8217;s it&#8230; in a nutshell. Now for the goods.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_touches">4. Touches</h2>
<div class="sectionbody">
<div class="paragraph"><p>Everything in C4 is touchable. There are 3 methods that give you really easy access to touches:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span><span class="p">{};</span>
<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesMoved</span><span class="p">{};</span>
<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesEnded</span><span class="p">{};</span>
</pre></div></div></div>
<div class="paragraph"><p>These three methods will most likely be your way into interaction.</p></div>
<div class="sect2">
<h3 id="_touchesbegan">4.1. touchesBegan</h3>
<div class="paragraph"><p>This method gets called from <em>any object</em> when that object registers a new touch. This might be triggered by one, two, three or more fingers touching an object at the same time. You can put other method calls in here and your objects will essentially start to work like buttons.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
        <span class="c1">//do stuff</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method gets called one time when any touch starts. Once it starts then the following method begins to get called&#8230;</p></div>
</div>
<div class="sect2">
<h3 id="_touchesmoved">4.2. touchesMoved</h3>
<div class="paragraph"><p>This method gets called from <em>any object</em> when that object has already registered a new touch and called <tt>touchesBegan</tt>. Afterwards, and for the life of the current touch, the <tt>touchesMoved</tt> method will get called for even the smallest movement of your finger.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesMoved</span> <span class="p">{</span>
        <span class="c1">//do stuff when a touch is dragged</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method stops being called when a finger is basically lifted from the screen or object. The following method is then called&#8230;</p></div>
</div>
<div class="sect2">
<h3 id="_touchesended">4.3. touchesEnded</h3>
<div class="paragraph"><p>This one is sort of the opposite of <tt>touchesBegan</tt> and happens only once at the end of the lifespan of a touch.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesEnded</span> <span class="p">{</span>
        <span class="c1">//now that the touch is done, do something to wrap up...</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_with_events">5. With Events</h2>
<div class="sectionbody">
<div class="paragraph"><p>If you really want to dig into touches you can override three advanced versions of the methods we just talked about:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan:</span><span class="p">(</span><span class="nc">NSSet</span><span class="o">*</span><span class="p">)</span><span class="n">touches</span> <span class="n">withEvent:</span><span class="p">(</span><span class="nc">UIEvent</span> <span class="o">*</span><span class="p">)</span><span class="n">event</span> <span class="p">{};</span>
<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesMoved:</span><span class="p">(</span><span class="nc">NSSet</span><span class="o">*</span><span class="p">)</span><span class="n">touches</span> <span class="n">withEvent:</span><span class="p">(</span><span class="nc">UIEvent</span> <span class="o">*</span><span class="p">)</span><span class="n">event</span> <span class="p">{};</span>
<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesEnded:</span><span class="p">(</span><span class="nc">NSSet</span><span class="o">*</span><span class="p">)</span><span class="n">touches</span> <span class="n">withEvent:</span><span class="p">(</span><span class="nc">UIEvent</span> <span class="o">*</span><span class="p">)</span><span class="n">event</span> <span class="p">{};</span>
</pre></div></div></div>
<div class="paragraph"><p>These methods work the same way as the simple versions, except they give you access to all the touches for a given moment. They all work the same way, so I&#8217;ll just explain the first example.</p></div>
<div class="sect2">
<h3 id="_where_8217_d_that_happen">5.1. Where&#8217;d That Happen</h3>
<div class="paragraph"><p>If you want to grab the point of a touch interaction you can do so by extracting a <tt>UITouch</tt> from the set of touches that was just registered. Then, you isolate the position of the touch like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesMoved:</span><span class="p">(</span><span class="nc">NSSet</span> <span class="o">*</span><span class="p">)</span><span class="n">touches</span> <span class="n">withEvent:</span><span class="p">(</span><span class="nc">UIEvent</span> <span class="o">*</span><span class="p">)</span><span class="n">event</span> <span class="p">{</span>
    <span class="nc">UITouch</span> <span class="o">*</span><span class="n">touchObject</span> <span class="o">=</span> <span class="p">[</span><span class="n">touches</span> <span class="n">anyObject</span><span class="p">];</span>
    <span class="nc">CGPoint</span> <span class="n">touchPoint</span> <span class="o">=</span> <span class="p">[</span><span class="n">touchObject</span> <span class="n">locationInView:</span><span class="k">self</span><span class="py">.canvas</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Log</span><span class="p">(</span><span class="s">@&quot;(%4.2f,%4.2f)&quot;</span><span class="p">,</span><span class="n">touchPoint</span><span class="py">.x</span><span class="p">,</span><span class="n">touchPoint</span><span class="py">.y</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This bit of code first extracts a touch from a given set of touches. Then, it isolates the location of the touch with respect to any view (here we just use the canvas). Finally, it takes the <tt>x</tt> and <tt>y</tt> positions of the <tt>touchPoint</tt> and prints them to the console.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_gestures">6. Gestures</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now we&#8217;re getting into it&#8230; But, from here we&#8217;re going to check out a couple of tutorials because these concepts are a bit deep.</p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/tutorials/interactionPanning.php">Interaction: Panning</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/tutorials/interactionPanningAdvanced">Interaction: Advanced Panning</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/tutorials/interactionSwipes">Interaction: Swipes</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/tutorials/interactionSwipesAdvanced">Interaction: Swipes Advanced</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/tutorials/interactionTapsAndTouches">Interaction: Taps &amp; Touches</a></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_ui_stuff">7. UI Stuff</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to do the same thing here, run through a bunch of examples that are online, because they&#8217;re split up in a good way and the entire topic is pretty broad.</p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/buttonSetup.php">UI: Button Setup</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/buttonAction.php">UI: Button Action</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/sliderSetup.php">UI: Slider Setup</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/sliderActionTouch.php">UI: Slider Action - Touch</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/sliderActionValue.php">UI: Slider Action - Value</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/stepperSetup.php">UI: Stepper Setup</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/stepperAction.php">UI: Stepper Action</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/switchSetup.php">UI: Switch Setup</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/switchAction.php">UI: Switch Action</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/scrollViewImage.php">UI: Scrollview Image</a></p></div>
<div class="paragraph"><p><a href="http://www.c4ios.com/examples/scrollViewLabel.php">UI: Scrollview Label</a></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">8. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This session went through a LOT of different techniques for getting objects to interact with one another, and also how to add touch interaction and UI elements to your projects. Hopefully we&#8217;ve gone through enough to show you that interaction is a HUGE part of C4, and more importantly that it&#8217;s completely integrated with visual objects.</p></div>
<div class="paragraph"><p>Merci Bien.</p></div>
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
