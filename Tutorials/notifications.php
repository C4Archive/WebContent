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

<h2>Notifications</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>On iOS there is a really great system for communicating between objects. This system lets you broadcast and listen for messages, and then react to those messages in any way you want. The really nice thing about using notifications to communicate between objects is that you can create a ton of responsiveness in your applications without too much mess.</p></div>
<div class="imageblock">
<div class="content">
<img src="notifications/notifications.png" alt="Notifications" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_communicating">1. Communicating</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are two steps to setting up the communication between two objects. Even though you can have many objects react to a notification from another single object, the communication is really 1 to 1. That is, in any communication there is always two steps to set up: 1) a <em>listener</em> and 2) a <em>broadcaster</em>.</p></div>
<div class="sect2">
<h3 id="_speak_a_k_a_posting">1.1. Speak! (a.k.a. posting)</h3>
<div class="paragraph"><p>The first step in setting up the communication between objects is to have one of them <strong>POST</strong> a notification. The basic syntax is this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">postNotification</span><span class="o">:</span><span class="s">@&quot;aMessage&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>All objects in C4 can post notifications, and notifications can be inserted into <em>any</em> method in your object&#8217;s implementation.</p></div>
</div>
<div class="sect2">
<h3 id="_listen">1.2. Listen!</h3>
<div class="paragraph"><p>The second step in setting up the communication between objects is to have one of them <strong>LISTEN</strong> for a notification. The basic syntax is this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;aMessage&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;aMethod&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Notice how there is a second part to this call? When you want to <em>listen</em> for a notification what you really want is to <em>react</em> to the notification. Reacting means running some code.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_what_should_my_message_be">2. What should my message be?</h2>
<div class="sectionbody">
<div class="paragraph"><p>Naming your message is really easy: <em>it can be anything</em>. In the example above we post <tt>@"aMessage"</tt> which is just a string (i.e. a bunch of characters). This message could be:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="s">@&quot;aMessage&quot;</span>
<span class="s">@&quot;a message&quot;</span>
<span class="s">@&quot;a massage&quot;</span>
<span class="s">@&quot;something completely different&quot;</span>
</pre></div></div></div>
<div class="paragraph"><p>When you specify a message you have to make sure that you listen <em>exactly</em> for that message. So, the listen calls for the above examples would be:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;aMessage&quot;</span> <span class="p">...];</span>
<span class="p">[</span><span class="n">obj</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;a message&quot;</span> <span class="p">...];</span>
<span class="p">[</span><span class="n">obj</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;a massage&quot;</span> <span class="p">...];</span>
<span class="p">[</span><span class="n">obj</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;something completely different&quot;</span> <span class="p">...];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_what_should_i_run">3. What should I run?</h2>
<div class="sectionbody">
<div class="paragraph"><p>The intention of posting and listening is for one object to <em>react</em> to another object. What this means is that you want an object to run some specific code when something happens elsewhere in your application. We call this kind of programming <em>event-based</em>.</p></div>
<div class="sect2">
<h3 id="_running_code">3.1. Running Code</h3>
<div class="paragraph"><p>I&#8217;ve mentioned it a couple times, but I really want to drive this home. When you <em>listen</em> for a notification, you want to <em>run</em> some code. The way you do this is to have a method with the code you want to run, and then use that method name like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;aMessage&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;aMethod&quot;</span><span class="p">]</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">aMethod</span> <span class="p">{</span>
    <span class="c1">//some code to run</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_ok_but_why">3.2. Ok. But, WHY?</h3>
<div class="paragraph"><p>Event-based architecture is really great for mobile devices because you don&#8217;t have to be constantly checking the state of things. It reduces the amount of observing and calculating you have to do on your own.</p></div>
<div class="paragraph"><p>Try to think about another language how you might use for creative coding. How would you set up a system for checking when an object is touched? What if there were dozens of objects on the screen? How would you distinguish one object in particular? Then, how would you react to that?</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">The underlying architecture of iOS applications and devices uses this event-based approach because it&#8217;s lighter on the system. C4 taps into this architecture to take advantage of this extremely useful technique.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_ok_but_how">3.3. Ok. But, HOW?</h3>
<div class="paragraph"><p>There are a few ways of practically setting up listening. The easiest is to have one object listen to itself! You can also have one or many objects listen for a message. Also, instead of listening for <em>all</em> messages from <em>all</em> objects, one can actually listen for notifications from <em>specific</em> objects.</p></div>
<div class="paragraph"><p>Let&#8217;s have a look at how to implement each of these tricks.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_listen_to_thy_self">4. Listen To Thy Self</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can have an object listen to itself. The easiest way to show how you can do this is to do this in the canvas.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;react&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">react</span> <span class="p">{</span>
    <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">colorWithWhite</span><span class="o">:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span> <span class="n">alpha</span><span class="o">:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">Check out this <a href="https://gist.github.com/C4Tutorials/5339853">gist</a>.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_wait_i_didn_8217_t_post_anything">4.1. Wait. I didn&#8217;t post anything!!!</h3>
<div class="paragraph"><p>There are a few methods in C4, common to all visual objects, that automatically post notifications for you. These methods are:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesBegan</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesEnded</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesMoved</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">longPress</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">swipedRight</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">swipedLeft</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">swipedUp</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">swipedDown</span><span class="p">;</span>
<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">tapped</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>It&#8217;s possible for you <tt>listenFor</tt> these method names, and not have to set up the <tt>postNotification</tt> yourself.</p></div>
<div class="paragraph"><p>There are a couple of other messages you can <tt>listenFor</tt> from specific objects like the <tt>@"endedNormally"</tt> from an audio sample, the <tt>@"reachedEnd"</tt> from a movie, or the <tt>@"imageWasCaptured"</tt> from a camera.</p></div>
</div>
<div class="sect2">
<h3 id="_can_8217_t_i_just_run_the_method">4.2. Can&#8217;t I just run the method?</h3>
<div class="paragraph"><p>Yes. You could easily just do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesBegan</span> <span class="p">{</span>
        <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">colorWithWhite</span><span class="o">:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span> <span class="n">alpha</span><span class="o">:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_then_why_would_i_do_this">4.3. Then WHY would I do this?</h3>
<div class="paragraph"><p>This simple example might be a little <em>too</em> simple and actually overcomplicate reacting to a touch. However, it does show how you can have one object listen to itself.</p></div>
<div class="paragraph"><p>I&#8217;ve found for myself that I want to listen for methods in a single class when I want my setup to be simple. For example, if I have a complicated class with lots of methods and lines of code I could create a <tt>setup</tt> method like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
        <span class="c1">//do stuff</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;methodAWasRun&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;methodB&quot;</span><span class="p">];</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;methodBWasRun&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;methodC&quot;</span><span class="p">];</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">methodA</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">methodA</span> <span class="p">{</span>
        <span class="c1">//do stuff that might take a while</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">postNotification</span><span class="o">:</span><span class="s">@&quot;methodAWasRun&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">methodB</span> <span class="p">{</span>
        <span class="c1">//do more stuff that might take a while</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">postNotification</span><span class="o">:</span><span class="s">@&quot;methodBWasRun&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">methodC</span> <span class="p">{</span>
        <span class="c1">//do even more stuff</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This might seem a little banal but, when you&#8217;re looking at a class with hundreds of lines of code, being able to read how a class links and triggers methods in its setup makes it really easy to understand what&#8217;s going on.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_listen_to_thy_neighbours">5. Listen to Thy Neighbours</h2>
<div class="sectionbody">
<div class="paragraph"><p>The most common thing you&#8217;re going to do is listen for notifications from other objects. Here&#8217;s a really simple example that shows you how to react to objects being touched.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">s</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">192</span><span class="p">)];</span>
    <span class="n">s</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">s</span><span class="p">];</span>

        <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;react&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">react</span> <span class="p">{</span>
    <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">colorWithWhite</span><span class="o">:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span> <span class="n">alpha</span><span class="o">:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This example shows how you can listen for the <tt>touchesBegan</tt> from an object.</p></div>
<div class="paragraph"><p>If you run this example you&#8217;ll see that touching the canvas also changes its color. The reason is that BOTH the canvas and the shape are posting the <tt>touchesBegan</tt> notification.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">check out this <a href="https://gist.github.com/C4Tutorials/5339875">gist</a></td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_listen_to_thy_specific_neighbour_s">6. Listen to Thy Specific Neighbour(s)</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can <tt>listenFor</tt> notifications from specific objects. Modifying the previous example, we can make the canvas change color when only the shape is touched:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">fromObject</span><span class="o">:</span><span class="n">s</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;react&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">check out this <a href="https://gist.github.com/C4Tutorials/5339967">gist</a></td>
</tr></table>
</div>
<div class="paragraph"><p>This is a pretty easy trick. But, it becomes a lot of code if you want to listen to the same message from <em>many</em> objects. A simple way of listening to many objects is to do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">fromObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">s1</span><span class="p">,</span><span class="n">s2</span><span class="p">,</span><span class="n">s3</span><span class="p">]</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;react&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>If you modify our example to use 3 shapes and use the line of code above, then the canvas will change color when any object other than the canvas is touched.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">check out this <a href="https://gist.github.com/C4Tutorials/5340007">gist</a></td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_who_said_what">7. Who Said What?</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now we&#8217;re starting to get advanced! You can actually target an object that posts a notification so that you can work with it directly. You need to do two things:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
The method you run has to receive an <tt>NSNotification</tt>
</p>
</li>
<li>
<p>
The method name you choose in your <tt>listenFor</tt> has to have an : at the end of its name
</p>
</li>
</ol></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
        <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;aMessage&quot;</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;aMethod:&quot;</span><span class="p">];</span> <span class="c1">//note the :</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">aMethod:</span><span class="p">(</span><span class="n">NSNotification</span> <span class="o">*</span><span class="p">)</span><span class="nv">notification</span> <span class="p">{</span>
        <span class="c1">// do stuff</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This technique passes the notification that was posted, from which you can grab the object. Let&#8217;s say you know that for a particular message the object posting the notification is a shape. You can do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Shape</span> <span class="o">*</span><span class="n">theNotifyingObject</span> <span class="o">=</span> <span class="p">(</span><span class="n">C4Shape</span> <span class="o">*</span><span class="p">)[</span><span class="n">notification</span> <span class="n">object</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, this is how you would change the color of a shape that was touched:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">s1</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">192</span><span class="p">,</span> <span class="mi">192</span><span class="p">)];</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">s2</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">s1</span><span class="p">.</span><span class="n">frame</span><span class="p">];</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">s3</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">s1</span><span class="p">.</span><span class="n">frame</span><span class="p">];</span>

    <span class="n">s1</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">x</span><span class="p">,</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">height</span> <span class="o">*</span> <span class="mf">0.25f</span><span class="p">);</span>
    <span class="n">s2</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
    <span class="n">s3</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">x</span><span class="p">,</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">height</span> <span class="o">*</span> <span class="mf">0.75f</span><span class="p">);</span>

    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">s1</span><span class="p">,</span><span class="n">s2</span><span class="p">,</span><span class="n">s3</span><span class="p">]];</span>

    <span class="p">[</span><span class="n">self</span> <span class="n">listenFor</span><span class="o">:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">fromObjects</span><span class="o">:</span><span class="err">@</span><span class="p">[</span><span class="n">s1</span><span class="p">,</span><span class="n">s2</span><span class="p">,</span><span class="n">s3</span><span class="p">]</span> <span class="n">andRunMethod</span><span class="o">:</span><span class="s">@&quot;randomColor:&quot;</span><span class="p">];</span>
<span class="p">}</span>

<span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">randomColor:</span><span class="p">(</span><span class="n">NSNotification</span> <span class="o">*</span><span class="p">)</span><span class="nv">notification</span> <span class="p">{</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">shape</span> <span class="o">=</span> <span class="p">(</span><span class="n">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">notification</span><span class="p">.</span><span class="n">object</span><span class="p">;</span>
    <span class="n">shape</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">UIColor</span> <span class="n">colorWithRed</span><span class="o">:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span>
                                      <span class="nl">green:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span>
                                       <span class="nl">blue:</span><span class="p">[</span><span class="n">C4Math</span> <span class="n">randomInt</span><span class="o">:</span><span class="mi">100</span><span class="p">]</span><span class="o">/</span><span class="mf">100.0f</span>
                                      <span class="nl">alpha:</span><span class="mf">1.0f</span><span class="p">];</span>

<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Here&#8217;s slightly more <a href="/examples/listenFor.php">advanced example</a> that listens for <tt>TAP</tt> gestures from objects.</p></div>
<div class="paragraph"><p>Balla.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">Check out this <a href="https://gist.github.com/C4Tutorials/5340194">gist</a></td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">8. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>I&#8217;ve shown some pretty basic examples of how you can communicate between objects. Some of the benefits of doing this are that you can have more flexible code, and you can easily respond to events happening in your application.</p></div>
<div class="paragraph"><p>This technique is really really powerful. Why? Because even though we have shown notifications only with shapes, you can do this with <strong>ALL OBJECTS IN C4</strong>. As you get used to working with notifications you&#8217;ll start to see relationships build between any kind of object.</p></div>
<div class="paragraph"><p>Soon, you&#8217;ll be building communications between shapes and images, images and movies, movies and sounds, sounds and opengl, cameras and just about anything and everything else!</p></div>
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
