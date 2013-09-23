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

<h2>Media Objects</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>If you&#8217;re new to C4, you should read this tutorial first because it explains the concept of media objects.</p></div>
<div class="imageblock">
<div class="content">
<img src="mediaObjects/mediaObjects.png" alt="The Object of my Media" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_media_objects">1. Media Objects</h2>
<div class="sectionbody">
<div class="paragraph"><p>Everything, yes, <em>everything</em> in C4 is a media object. All the things you see on screen – e.g. images, shapes and movies – are these little self-contained objects that you can manipulate, change and interact with. You can adjust the visual properties of objects, you can swap one object for another (using iOS transitions if you want), and you can even interact with touch and multitouch on &#8230; wait for it&#8230; ANY object.</p></div>
<div class="paragraph"><p>We&#8217;ve tried to keep the process of working with media objects as similar as possible. This means that working with a movie is as easy as working with a shape, and same for images, sliders, buttons, and so on&#8230; When you put something on screen you write something like the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:image</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:shape</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addMovie:movie</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Similarly, you can position visual objects like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">image</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">aPoint</span><span class="p">;</span>
<span class="n">shape</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">aPoint</span><span class="p">;</span>
<span class="n">movie</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">aPoint</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;There are even some common characteristics between visual and non-visual objects:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">image</span> <span class="n">listenFor:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">runMethod:</span><span class="s">@&quot;test&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="n">audioSample</span> <span class="n">listenFor:</span><span class="s">@&quot;touchesBegan&quot;</span> <span class="n">runMethod:</span><span class="s">@&quot;play&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Wait, woah. Non-Visual?&#8230;</p></div>
</div>
</div>
<div class="sect1">
<h2 id="anchor-visnonvis">2. Visual vs. Non-Visual</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 2 primary types of objects in C4: Visual and Non-visual. Pretty easy right?</p></div>
<div class="paragraph"><p>Visual objects are all the things you can put on screen:</p></div>
<div class="ulist"><ul>
<li>
<p>
shapes
</p>
</li>
<li>
<p>
movies
</p>
</li>
<li>
<p>
images
</p>
</li>
<li>
<p>
buttons
</p>
</li>
<li>
<p>
sliders
</p>
</li>
<li>
<p>
cameras
</p>
</li>
<li>
<p>
scroll views
</p>
</li>
<li>
<p>
text labels
</p>
</li>
<li>
<p>
openGL
</p>
</li>
</ul></div>
<div class="paragraph"><p>Non-visual objects are all the things that you can work with but can&#8217;t see:</p></div>
<div class="ulist"><ul>
<li>
<p>
fonts
</p>
</li>
<li>
<p>
math
</p>
</li>
<li>
<p>
timers
</p>
</li>
<li>
<p>
vectors
</p>
</li>
<li>
<p>
audio samples
</p>
</li>
<li>
<p>
view controllers
</p>
</li>
</ul></div>
<div class="sect2">
<h3 id="_c4object_c4control">2.1. C4Object / C4Control</h3>
<div class="paragraph"><p>We separated the concept of visual and non-visual objects into 2 distinct categories: <em>objects</em> and <em>controls</em>. <strong>Any</strong> object will share all the tricks and code that are in C4Object. All visual objects share some common functionality from C4Control, which provides methods for setting basic styles, position, sizes, and some animations as well.</p></div>
</div>
<div class="sect2">
<h3 id="anchor-speak">2.2. Speak!</h3>
<div class="paragraph"><p>All objects can speak to one another. The actual term for this is "posting a notification", but it essentially means that throughout your code you can insert little statements like:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">postNotification:</span><span class="s">@&quot;iHaveSpoken&quot;</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="anchor-listen">2.3. Listen!</h3>
<div class="paragraph"><p>All objects can listen to one another as well. So, if you want an object to react to something said by another object you can do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">listenFor:</span><span class="s">@&quot;iHaveSpoken&quot;</span> <span class="n">andRunMethod:</span><span class="s">@&quot;aMethod&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;which will trigger <tt>aMethod</tt> whenever the object hears the <tt>@"iHaveSpoken"</tt> message posted by any object (even itself!). Objects can also listen to specific objects:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">listenFor:</span><span class="s">@&quot;iHaveSpoken&quot;</span> <span class="n">fromObject:aSpecificObject</span> <span class="n">andRunMethod:</span><span class="s">@&quot;aMethod&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;which will only trigger when <tt>aSpecificObject</tt> has posted a notification.</p></div>
</div>
<div class="sect2">
<h3 id="anchor-run">2.4. Run!</h3>
<div class="paragraph"><p>Another neat feature is that objects can command themselves to do something after a certain amount of time (i.e. delay). It&#8217;s pretty easy:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">runMethod:</span><span class="s">@&quot;aMethod&quot;</span> <span class="n">afterDelay:</span><span class="mf">1.0f</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;which will trigger <tt>aMethod</tt> after a 1 second wait time.</p></div>
</div>
<div class="sect2">
<h3 id="anchor-styles">2.5. Styles!</h3>
<div class="paragraph"><p>Aside from being "seen", visual objects have one major difference from non-visual objects: <strong>styles</strong>. You can style a visual object by setting its properties, many of which are common across <strong>all</strong> visual objects. Here&#8217;s a short list of common properties:</p></div>
<div class="ulist"><ul>
<li>
<p>
rotation
</p>
</li>
<li>
<p>
borderWidth
</p>
</li>
<li>
<p>
borderColor
</p>
</li>
<li>
<p>
animationDuration
</p>
</li>
<li>
<p>
animationDelay
</p>
</li>
<li>
<p>
shadowRadius
</p>
</li>
<li>
<p>
shadowColor
</p>
</li>
<li>
<p>
shadowOpacity
</p>
</li>
<li>
<p>
shadowOffset
</p>
</li>
<li>
<p>
shadowPath
</p>
</li>
<li>
<p>
mask
</p>
</li>
<li>
<p>
origin
</p>
</li>
<li>
<p>
center
</p>
</li>
<li>
<p>
width
</p>
</li>
<li>
<p>
height
</p>
</li>
<li>
<p>
zPosition
</p>
</li>
<li>
<p>
&#8230;
</p>
</li>
</ul></div>
<div class="paragraph"><p>&#8230;I could go on here, but its better if you have a look at the C4Control documentation.</p></div>
</div>
<div class="sect2">
<h3 id="_but_where_do_things_differ">2.6. But Where Do Things Differ?</h3>
<div class="paragraph"><p>Right. There are a lot of <em>common</em> things between objects, but where they really differ is in the small details for each <em>kind</em> of object. For instance, shapes have a lineWidth property, movies have a play method, labels have a text property, and so on&#8230; The real differences between objects come into play when you have a look at the <em>kind</em> of object and its properties. There&#8217;s too many to list here, but hopefully you&#8217;ll dig through C4 learning the different kinds of things you can do!</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_integration">3. Integration</h2>
<div class="sectionbody">
<div class="paragraph"><p>Another absolutely essential thing that&#8217;s cooked into C4 is the integration of various objects&#8230; You can combine and combine and combine and combine in tons of different ways! The <a href="#anchor-speak">speak</a>, <a href="#anchor-listen">listen</a>, and <a href="#anchor-run">run</a> tricks mentioned above are great examples of how you can integrate the actions of different objects with one another. You can also use objects to visually influence each other as well, through masking and by adding them to one another.</p></div>
<div class="sect2">
<h3 id="anchor-masking">3.1. Masking</h3>
<div class="paragraph"><p>A simple example is masking. The two easiest ways to create masks are to use transparent images, or shapes. You set an object&#8217;s mask like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">image</span><span class="py">.mask</span> <span class="o">=</span> <span class="n">shape</span><span class="p">;</span>
<span class="n">movie</span><span class="py">.mask</span> <span class="o">=</span> <span class="n">image</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="mediaObjects/masking.png" alt="Image and Shape Masking" />
</div>
</div>
<div class="paragraph"><p>Basically, the invisible parts of a mask become transparent and the visual parts stay opaque.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">You can have a look at the code for <a href="https://gist.github.com/C4Tutorials/5304944">masking</a>.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="anchor-combining">3.2. Combining</h3>
<div class="paragraph"><p>You can combine objects by adding them to one another. For example, if you wanted to makes something like a face you could create the shape of the head and then add all the facial features to that object. Here&#8217;s a short example of how you can add a bunch of shapes to another one, and then move the entire group by moving the main shape (e.g. face).</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">face</span> <span class="n">addShape:hair</span><span class="p">];</span>
<span class="p">[</span><span class="n">face</span> <span class="n">addShape:leftEye</span><span class="p">];</span>
<span class="p">[</span><span class="n">face</span> <span class="n">addShape:rightEye</span><span class="p">];</span>
<span class="p">[</span><span class="n">face</span> <span class="n">addShape:nose</span><span class="p">];</span>
<span class="p">[</span><span class="n">face</span> <span class="n">addShape:mouth</span><span class="p">];</span>
<span class="p">[</span><span class="n">face</span> <span class="n">addShape:mouthLine</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:face</span><span class="p">];</span>
<span class="n">face</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="mediaObjects/face.png" alt="A Face" />
</div>
</div>
<div class="paragraph"><p>The key part of this example is the <tt>face.center = ...;</tt> because when you move the face, all the other objects move as well. They have been added to the face to create a combined shape.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">You can have a look at the code for <a href="https://gist.github.com/C4Tutorials/5304505">how to make a face</a>.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_interaction">4. Interaction</h2>
<div class="sectionbody">
<div class="paragraph"><p>Every visual object in C4 can recognize touches and gestures.</p></div>
<div class="paragraph"><p>Oh, yes! Let me repeat that&#8230;</p></div>
<div class="paragraph"><p>EVERY VISUAL OBJECT IN C4 CAN RECOGNIZE <em>TOUCHES</em> AND <em>GESTURES</em>!!!!</p></div>
<div class="paragraph"><p>The easiest way to add gesture to an object so you can drag it around the screen is like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">addGesture:PAN</span> <span class="n">name:</span><span class="s">@&quot;pan&quot;</span> <span class="n">action:</span><span class="s">@&quot;move:&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Working with gestures and touches is complicated, so we&#8217;ll leave the details for another tutorial. In the mean time, here are a few important points:</p></div>
<div class="sect2">
<h3 id="anchor-touches">4.1. Touches</h3>
<div class="paragraph"><p>The canvas, and every visual object, has a set of methods that get called when the object registers a touch. Here&#8217;s the list:</p></div>
<div class="ulist"><ul>
<li>
<p>
touchesBegan
</p>
</li>
<li>
<p>
touchesBegan:withEvent:
</p>
</li>
<li>
<p>
touchesMoved
</p>
</li>
<li>
<p>
touchesMoved:withEvent:
</p>
</li>
<li>
<p>
touchesEnded
</p>
</li>
<li>
<p>
touchesEnded:WithEvent:
</p>
</li>
<li>
<p>
pressedLong
</p>
</li>
</ul></div>
<div class="paragraph"><p>You can use these to add basic functionality to your objects. A simple example would be to change the color of a shape when it is touched:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
        <span class="k">self</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="py">...</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="anchor-gestures">4.2. Gestures</h3>
<div class="paragraph"><p>C4 integrates with the iOS gesture recognizer system, which makes adding gestures to you objects a cinch. There are a basic set of gestures you can add:</p></div>
<div class="ulist"><ul>
<li>
<p>
TAP
</p>
</li>
<li>
<p>
SWIPERIGHT
</p>
</li>
<li>
<p>
SWIPELEFT
</p>
</li>
<li>
<p>
SWIPEUP
</p>
</li>
<li>
<p>
SWIPEDOWN
</p>
</li>
<li>
<p>
PAN
</p>
</li>
<li>
<p>
LONGPRESS
</p>
</li>
</ul></div>
<div class="paragraph"><p>&#8230;and the great thing about these gestures is that you can also easily customize them!</p></div>
</div>
<div class="sect2">
<h3 id="_customizing_gestures">4.3. Customizing Gestures</h3>
<div class="paragraph"><p>C4 makes customizing gestures quite a bit easier than doing it in pure objective-c. For instance, you can specify the number of taps for a gesture to recognize:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">addGesture:TAP</span> <span class="n">name:</span><span class="s">@&quot;myTapGesture&quot;</span> <span class="n">action:</span><span class="s">@&quot;aMethod&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="n">obj</span> <span class="n">numberOfTapsRequired:</span><span class="mi">2</span> <span class="n">forGesture:</span><span class="s">@&quot;myTapGesture&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Other methods that you can use to customize gestures are:</p></div>
<div class="ulist"><ul>
<li>
<p>
numberOfTapsRequired:forGesture
</p>
</li>
<li>
<p>
numberOfTouchesRequired:forGesture
</p>
</li>
<li>
<p>
minimumNumberOfTouches:forGesture
</p>
</li>
<li>
<p>
maximumNumberOfTouches:forGesture
</p>
</li>
<li>
<p>
swipeDirection:forGesture
</p>
</li>
<li>
<p>
minimumPressDuration:forGesture
</p>
</li>
</ul></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_animation">5. Animation</h2>
<div class="sectionbody">
<div class="paragraph"><p>From the outset, one of the major things I wanted to do with C4 when was to simplify the way of working with animations. I researched a lot, looking at different frameworks, various programming languages, and a ton of techniques for creating animation systems that work with media. Along the way I found a badass framework called <strong>Core Animation</strong> which is sophisticated, mature, and really really powerful.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">This guy does a better jobs of explaining <a href="http://www.youtube.com/watch?v=pyd8O-2mkgk">Core Animation</a>. It was introduced in 2007, and has come a really really really long way since then.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="anchor-animations">5.1. Automatic Animations</h3>
<div class="paragraph"><p>One of the really nice things about C4 is that you can tell your objects to change (position, color, size, etc.) and sit back and watch them animate. Here&#8217;s an example that will change the <tt>lineWidth</tt> of a shape over the course of 2 seconds:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
<span class="n">shape</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">30.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Here&#8217;s another example of changing the width of a movie:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">movie</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
<span class="n">movie</span><span class="py">.width</span> <span class="o">=</span> <span class="mf">100.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Let&#8217;s say you create a little OpenGL object and you want rotate it 360 degrees, and then repeat forever:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">gl</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
<span class="n">gl</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span><span class="p">;</span>
<span class="n">gl</span><span class="py">.rotation</span> <span class="o">+=</span> <span class="n">TWO_PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>There are tons of properties you can animate for almost all visual objects. This is just a taste of what you can do.</p></div>
<div class="paragraph"><p>The basic principle, though, of working with animations in C4 is this:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Simply set your object&#8217;s <tt>animationDuration</tt> and <tt>animationOptions</tt>
</p>
</li>
<li>
<p>
Change all the animatable values you want to see animated.
</p>
</li>
</ol></div>
<div class="paragraph"><p>That&#8217;s it.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_end_of_the_beginning">6. The End of the Beginning</h2>
<div class="sectionbody">
<div class="paragraph"><p>This tutorial was written just to give a very brief overview what what you&#8217;re going to be working with in C4. It can be a very different way of thinking, but it gets pretty easy after a little while. Once you&#8217;ve made a few experiments in C4 you&#8217;ll be cruising, building more and more wicked and gorgeous apps.</p></div>
<div class="paragraph"><p>So, here are a few things to remember:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
there are <a href="#anchor-visnonvis">visual and non-visual</a> objects (and they all share similar things)
</p>
</li>
<li>
<p>
objects can <a href="#anchor-speak">speak</a>, <a href="#anchor-listen">listen</a>, and <a href="#anchor-run">run</a>
</p>
</li>
<li>
<p>
visual objects have <a href="#anchor-styles">styles</a>
</p>
</li>
<li>
<p>
<em>everything</em> in C4 has interaction, through <a href="#anchor-touches">touches</a> and <a href="#anchor-gestures">gestures</a>.
</p>
</li>
<li>
<p>
<em>everything</em> (well, almost everything) in C4 can be <a href="#anchor-animations">animated</a>
</p>
</li>
</ol></div>
<div class="paragraph"><p>Be sure to check out other <a href="/tutorials/">Tutorials</a> and all the <a href="/examples/">Examples</a> which have specific code and more step-by-step instructions.</p></div>
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
