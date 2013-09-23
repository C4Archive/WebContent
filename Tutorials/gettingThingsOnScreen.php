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

<h2>Getting Things On Screen</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5373415" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>This tutorial will show you how to put 11 different kinds of visual C4 objects onto the screen.</p></div>
<div class="imageblock">
<div class="content">
<img src="gettingThingsOnScreen/gettingThingsOnScreen.png" alt="Getting Things On Screen" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_addit">1. addIt</h2>
<div class="sectionbody">
<div class="paragraph"><p>The concept of using things in C4 is a little different than most other creative-coding frameworks you may have run across in the past. One of the key ideas behind C4 is to reduce the amount of drawing and system resources to have lighter, longer-lasting applications. This is really important in the case of mobile devices because they have limited resources.</p></div>
<div class="paragraph"><p>So, what we have in C4 are <em>visual objects</em> that can be <strong>added</strong> to the canvas.</p></div>
<div class="sect2">
<h3 id="_what_does_this_mean">1.1. What does this mean?</h3>
<div class="paragraph"><p>This means that <em>everything</em> you see on the screen in C4 is it&#8217;s own object, something self-contained that handles its own drawing in a really efficient way.</p></div>
<div class="paragraph"><p>This also means that you can add an object to the screen and just leave it there <em>without having to redraw it constantly</em>. Once an object has been added to the screen it just stays there.</p></div>
</div>
<div class="sect2">
<h3 id="_how_do_i_do_this">1.2. How do I do this?</h3>
<div class="paragraph"><p>The canvas is the main place where you will add objects, and to do so you use one of the following calls:</p></div>
<div class="ulist"><ul>
<li>
<p>
addCamera:
</p>
</li>
<li>
<p>
addGL:
</p>
</li>
<li>
<p>
addImage:
</p>
</li>
<li>
<p>
addLabel:
</p>
</li>
<li>
<p>
addMovie:
</p>
</li>
<li>
<p>
addShape:
</p>
</li>
<li>
<p>
addUIElement:
</p>
</li>
<li>
<p>
addSubview:
</p>
</li>
</ul></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">You can actually add visual objects to one another as well, to make complex objects&#8230; But, we&#8217;ll leave that for link:/tutorials/compositeObjects.php</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_in_steps">1.3. In Steps</h3>
<div class="paragraph"><p>We&#8217;re going to walk through the code that builds the image show above. To put everything together properly we&#8217;ll work in steps.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_defining_objects">2. Defining Objects</h2>
<div class="sectionbody">
<div class="paragraph"><p>We want to build 11 objects and align them vertically across the screen. To do this we&#8217;ll create each object, then resize them, then position them, then add them to the screen.</p></div>
<div class="sect2">
<h3 id="_implementation">2.1. @implementation</h3>
<div class="paragraph"><p>To start, we want to define the objects we&#8217;re going to use in our implementation, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="nc">C4WorkSpace</span> <span class="p">{</span>
    <span class="n">C4Shape</span> <span class="o">*</span><span class="n">shape</span><span class="p">;</span>
    <span class="n">C4Movie</span> <span class="o">*</span><span class="n">movie</span><span class="p">;</span>
    <span class="n">C4Image</span> <span class="o">*</span><span class="n">image</span><span class="p">;</span>
    <span class="n">C4GL</span> <span class="o">*</span><span class="n">gl</span><span class="p">;</span>
<span class="c1">//    C4Camera *camera;</span>
    <span class="n">C4Label</span> <span class="o">*</span><span class="n">label</span><span class="p">;</span>
    <span class="n">C4Button</span> <span class="o">*</span><span class="n">button</span><span class="p">;</span>
    <span class="n">C4Slider</span> <span class="o">*</span><span class="n">slider</span><span class="p">;</span>
    <span class="n">C4Stepper</span> <span class="o">*</span><span class="n">stepper</span><span class="p">;</span>
    <span class="n">C4Switch</span> <span class="o">*</span><span class="n">onOffSwitch</span><span class="p">;</span>
    <span class="n">C4ScrollView</span> <span class="o">*</span><span class="n">scrollView</span><span class="p">;</span>

    <span class="n">NSArray</span> <span class="o">*</span><span class="n">allObjects</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We list all our objects here because we need references to them later&#8230; We&#8217;re going to call them from different methods, so we need them to be declared as class variables.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">We&#8217;ve created an extra <tt>allObjects</tt> array which we&#8217;ll use in the <tt>shortAdd</tt> method. I&#8217;ve commented out the <tt>C4Camera</tt> object because it doesn&#8217;t run on the simulator, but if you can run this example on an iPad you should uncomment all the lines for the camera object.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_objects">3. Creating Objects</h2>
<div class="sectionbody">
<div class="paragraph"><p>One nice thing about object-oriented coding is that you can separate jobs into little bundles. This makes it easier to understand what&#8217;s happening, and to isolate problems. I&#8217;ve isolated the creation of objects by creating a method called <tt>createObjects</tt>. Like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">createObjects</span> <span class="p">{</span>
    <span class="n">shape</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">128</span><span class="p">,</span> <span class="mi">128</span><span class="p">)];</span>
    <span class="n">movie</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Movie</span> <span class="n">movieNamed</span><span class="o">:</span><span class="s">@&quot;inception.mov&quot;</span><span class="p">];</span>
    <span class="n">image</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Image</span> <span class="n">imageNamed</span><span class="o">:</span><span class="s">@&quot;C4Sky&quot;</span><span class="p">];</span>
    <span class="n">gl</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4GL</span> <span class="n">glWithFrame</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">600</span><span class="p">,</span> <span class="mi">400</span><span class="p">)];</span>
<span class="c1">//    camera = [C4Camera cameraWithFrame:shape.frame];</span>
    <span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Label</span> <span class="n">labelWithText</span><span class="o">:</span><span class="s">@&quot;A Label&quot;</span><span class="p">];</span>
    <span class="n">button</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Button</span> <span class="n">buttonWithType</span><span class="o">:</span><span class="n">ROUNDEDRECT</span><span class="p">];</span>
    <span class="n">slider</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Slider</span> <span class="n">slider</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">128</span><span class="p">,</span> <span class="mi">44</span><span class="p">)];</span>
    <span class="n">stepper</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Stepper</span> <span class="n">stepper</span><span class="p">];</span>
    <span class="n">onOffSwitch</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Switch</span> <span class="k">switch</span><span class="p">];</span>
    <span class="n">scrollView</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4ScrollView</span> <span class="n">scrollView</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">128</span><span class="p">,</span> <span class="mi">96</span><span class="p">)];</span>

    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>So, 10 objects are created with dimensions&#8230; But, for the moment some of the dimensions are a bit arbitrary. Why? Because we&#8217;re going to have a second method that tailors all the objects.</p></div>
<div class="paragraph"><p>The reason for the second method is that some objects need extra methods run on them to be useful, or set up; objects like the camera (if you&#8217;re compiling for a device), the scrollview, and so on. Separating the setup from the instantiation is the kind of "bundling" I was mentioning before.</p></div>
<div class="sect2">
<h3 id="_the_allobjects">3.1. The allObjects</h3>
<div class="paragraph"><p>Before we move on to tailoring the objects, let&#8217;s do one last little setup. We want to build an array that holds all the objects so that we can later use this array to arrange objects on the canvas. We&#8217;ll also use it in the <tt>shortAdd</tt> method.</p></div>
<div class="paragraph"><p>At the end of the <tt>createObjects</tt> method add the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre>    <span class="n">allObjects</span> <span class="o">=</span> <span class="err">@</span><span class="p">[</span><span class="n">shape</span><span class="p">,</span>
                   <span class="n">movie</span><span class="p">,</span>
                   <span class="n">image</span><span class="p">,</span>
                   <span class="n">gl</span><span class="p">,</span>
<span class="c1">//                   camera,</span>
                   <span class="n">label</span><span class="p">,</span>
                   <span class="n">button</span><span class="p">,</span>
                   <span class="n">slider</span><span class="p">,</span>
                   <span class="n">stepper</span><span class="p">,</span>
                   <span class="n">onOffSwitch</span><span class="p">,</span>
                   <span class="n">scrollView</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates an array that holds references to all our objects. There&#8217;s a nice <tt>@[]</tt> shorthand that was introduced into Objective-C last year, which I use to create the array.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_tailoring_objects">4. Tailoring Objects</h2>
<div class="sectionbody">
<div class="paragraph"><p>For lack of creativity, I named the next method <tt>setupObjects</tt>. You can do the same and <a href="http://www.bonzasheila.com/art/archives/nov05/images/15.%20Klingstedt,Carl%20Gustav%20-%20A%20Monk%20Chastising%20A%20Nun.jpg">chastise</a> me later for being lazy.</p></div>
<div class="paragraph"><p>Create a method called <tt>setupObjects</tt> and add the following to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setupObjects</span> <span class="p">{</span>
    <span class="n">movie</span><span class="p">.</span><span class="n">height</span> <span class="o">=</span> <span class="n">shape</span><span class="p">.</span><span class="n">height</span><span class="p">;</span>
    <span class="n">image</span><span class="p">.</span><span class="n">height</span> <span class="o">=</span> <span class="n">movie</span><span class="p">.</span><span class="n">height</span><span class="p">;</span>
    <span class="n">gl</span><span class="p">.</span><span class="n">frame</span> <span class="o">=</span> <span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">150</span><span class="p">,</span> <span class="mi">100</span><span class="p">);</span>
<span class="c1">//    camera.frame = shape.frame;</span>
<span class="c1">//    [camera initCapture];</span>
    <span class="p">[</span><span class="n">label</span> <span class="n">sizeToFit</span><span class="p">];</span>
    <span class="n">C4Image</span> <span class="o">*</span><span class="n">table</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Image</span> <span class="n">imageNamed</span><span class="o">:</span><span class="s">@&quot;C4Table&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="n">scrollView</span> <span class="n">addImage</span><span class="o">:</span><span class="n">table</span><span class="p">];</span>
    <span class="n">scrollView</span><span class="p">.</span><span class="n">contentSize</span> <span class="o">=</span> <span class="n">table</span><span class="p">.</span><span class="n">bounds</span><span class="p">.</span><span class="n">size</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>What we&#8217;re doing here is:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
setting the height of the movie to match the shape
</p>
</li>
<li>
<p>
setting the height of the image to match the height of the movie
</p>
</li>
<li>
<p>
creating a new frame for the gl object
</p>
</li>
<li>
<p>
</p>
</li>
<li>
<p>
resizing the label to fit its text
</p>
</li>
<li>
<p>
adding a new image called <tt>table</tt> to the scrollview
</p>
</li>
<li>
<p>
setting the scroll view&#8217;s content size to match the size of <tt>table</tt>
</p>
</li>
</ol></div>
<div class="sect2">
<h3 id="_why">4.1. Why?</h3>
<div class="paragraph"><p>We could have done this step in conjunction with the <tt>createObjects</tt> method, but I wanted to be specific about a couple of things.</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
I wanted the <tt>createObjects</tt> method to focus only on the 11 objects we&#8217;re concerned about in this tutorial. That is, I <strong>didn&#8217;t</strong> want the creation of <tt>table</tt> in that method.
</p>
</li>
<li>
<p>
I wanted a place where I could isolate the resizing of objects outside of their instantiation
</p>
</li>
<li>
<p>
The concept of <tt>[camera initCapture]</tt> and <tt>[scrollview addImage:]</tt> don&#8217;t conceptually fit in the "idea" of creating objects, so I wanted these separate as well.
</p>
</li>
<li>
<p>
Since I was going to separate the setup of at least 2 objects, I figured I&#8217;d separate all their setups.
</p>
</li>
</ol></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_positioning_objects">5. Positioning Objects</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now that everything has its proper size, you can write a method that positions all the objects. This is our first use of the <tt>allObjects</tt> array. We&#8217;re going to iterate through all the objects and perform a basic displacement&#8230; Since we know that the steps are going to be the same for all objects, we will write a more "generic" bit of code to handle this displacement.</p></div>
<div class="paragraph"><p>Add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">positionObjects</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="sect2">
<h3 id="_generic_code">5.1. Generic Code</h3>
<div class="paragraph"><p>We know two things that are important: 1) that everything in the <tt>allObjects</tt> array is a visual object and 2) that because they&#8217;re visual , they&#8217;re <em>all</em> descendants from <tt>C4Object</tt>.</p></div>
<div class="paragraph"><p>First, we set up a <tt>currentCenter</tt> point that we&#8217;re going to use to set the positions of all the objects.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGPoint</span> <span class="n">currentCenter</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">.</span><span class="n">x</span><span class="p">,</span> <span class="mi">20</span><span class="p">);</span>
</pre></div></div></div>
<div class="paragraph"><p>Next, we&#8217;re going to set up a <tt>for</tt> loop that will iterate over all the objects:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span> <span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="p">[</span><span class="n">allObjects</span> <span class="n">count</span><span class="p">];</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Then, we&#8217;re going to start by grabbing each object and <em>casting</em> it as a <tt>C4Object</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">C4Control</span> <span class="o">*</span><span class="n">obj</span> <span class="o">=</span> <span class="p">(</span><span class="n">C4Control</span> <span class="o">*</span><span class="p">)</span><span class="n">allObjects</span><span class="p">[</span><span class="n">i</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Now that we have an object, we&#8217;re going to position it using the following steps:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
shift the <tt>centerPoint</tt> by half the object&#8217;s height
</p>
</li>
<li>
<p>
position the object
</p>
</li>
<li>
<p>
shift the <tt>centerPoint</tt> by half the object&#8217;s height again, <strong>plus</strong> a 10.0f pt gap.
</p>
</li>
</ol></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">currentCenter</span><span class="p">.</span><span class="n">y</span> <span class="o">+=</span> <span class="n">obj</span><span class="p">.</span><span class="n">height</span> <span class="o">/</span> <span class="mf">2.0f</span><span class="p">;</span>
<span class="n">obj</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">currentCenter</span><span class="p">;</span>
<span class="n">currentCenter</span><span class="p">.</span><span class="n">y</span> <span class="o">+=</span> <span class="n">obj</span><span class="p">.</span><span class="n">height</span><span class="o">/</span><span class="mf">2.0</span> <span class="o">+</span> <span class="mi">10</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;and that&#8217;s that.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_adding">6. Adding</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 2 ways of adding things to the screen: <em>individually</em> and <em>as a group</em>. I&#8217;ll show you how to add in both ways, starting with individually.</p></div>
<div class="sect2">
<h3 id="_individually">6.1. Individually</h3>
<div class="paragraph"><p>When you&#8217;re working with single objects, this is the best way to go about getting them on screen. Add the following method to your code:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">individualAdd</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">shape</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addMovie</span><span class="o">:</span><span class="n">movie</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addImage</span><span class="o">:</span><span class="n">image</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addGL</span><span class="o">:</span><span class="n">gl</span><span class="p">];</span>
    <span class="c1">//    [self.canvas addCamera:camera];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addLabel</span><span class="o">:</span><span class="n">label</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addUIElement</span><span class="o">:</span><span class="n">button</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addUIElement</span><span class="o">:</span><span class="n">slider</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addUIElement</span><span class="o">:</span><span class="n">stepper</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addUIElement</span><span class="o">:</span><span class="n">onOffSwitch</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addSubview</span><span class="o">:</span><span class="n">scrollView</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method takes the reference for each object and calls a specific <tt>add</tt> method on that object. You should always try to adhere to the practice of adding things properly (actually, xcode will throw a warning if you&#8217;re not doing it properly).</p></div>
<div class="paragraph"><p>We&#8217;ve tried to simplify working with these objects to the point where there&#8217;s lots of stuff going on behind the scenes when you create objects. Sometimes objects need delegates, sometimes they need other things tacked onto them just before they&#8217;re added to the screen. We bury this in the <tt>add</tt> methods so you don&#8217;t have to worry about it.</p></div>
<div class="paragraph"><p>Ok, but that was a lot of code for simply adding everything that I already created&#8230; So, let&#8217;s do a little bit of shorthand.</p></div>
</div>
<div class="sect2">
<h3 id="_adding_as_a_group">6.2. Adding as a Group</h3>
<div class="paragraph"><p>You can add objects as a group by first putting them in an array and then adding the array to the canvas. This is the second chance we have to use the <tt>allObjects</tt> array.</p></div>
<div class="paragraph"><p>Add this method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">groupAdd</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addObjects</span><span class="o">:</span><span class="n">allObjects</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>SIIIIIIIIIIMPLE.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_setup">7. The Setup</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now, with all the methods we&#8217;ve written our main application&#8217;s <tt>setup</tt> method is clear, simple and easy to read.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">createObjects</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">setupObjects</span><span class="p">];</span>
    <span class="p">[</span><span class="n">self</span> <span class="n">positionObjects</span><span class="p">];</span>

    <span class="p">[</span><span class="n">self</span> <span class="n">individualAdd</span><span class="p">];</span>
<span class="c1">//    [self groupAdd];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We create our objects, we set them up, we position them and then we either add them individually or as a group.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_removing">8. Removing</h2>
<div class="sectionbody">
<div class="paragraph"><p>And. Yes. You can <em>remove</em> objects just as easily:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">obj</span> <span class="n">removeFromSuperview</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This line of code will take the object off of the canvas. If it has a <em>strong</em> reference in your code then it will hang around, otherwise it might get swept up and destroyed if the canvas was the only thing hanging on to it.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">There&#8217;s no <tt>[self removeObjects:arrayOfObjects]</tt> method. Maybe in the next release if people ask for it.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">9. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This short tutorial goes over the concept of adding things to the screen. It&#8217;s different than other approaches used in creative coding frameworks because you&#8217;re not going to be <em>drawing</em> things to the screen. In fact, what you&#8217;re doing is taking on the concept of views and adding objects to the canvas.</p></div>
<div class="paragraph"><p>This addition of objects to the canvas may seem trivial but it is an important distinction in how objects are used in C4.</p></div>
<div class="paragraph"><p>Ciao.</p></div>
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
