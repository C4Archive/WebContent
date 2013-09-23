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

<h2>Image: Raw Data</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3232951" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>You can create images from scratch by using an array of raw data values.</p></div>
<div class="imageblock">
<div class="content">
<img src="imageRawData/imageRawData.png" alt="Image With Raw Data" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_image_with_raw_data">1. Image With Raw Data</h2>
<div class="sectionbody">
<div class="paragraph"><p>Creating an image from raw data (i.e. char values), takes a bit of setup. You need to first create an array to hold the color values you&#8217;ll use to make the image, which takes some calculating and mallocing and so on&#8230;</p></div>
<div class="sect2">
<h3 id="_create_a_raw_array">1.1. Create a Raw Array</h3>
<div class="paragraph"><p>The raw array you will use needs to be large enough to hold all the pixels for the image you&#8217;re creating. Typically, you use 4 different values for 1 color which means that you need 4 <strong>bytes</strong> for every pixel.</p></div>
<div class="paragraph"><p>We will create a 320x240 image, with 4 bytes per pixel (RGBA), so we need to first build an array big enough to hold all the data. Finally, because we&#8217;re doing this from scratch, we&#8217;ll have to <strong>malloc</strong> the array.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSInteger</span> <span class="n">width</span> <span class="o">=</span> <span class="mi">320</span><span class="p">;</span>
<span class="nc">NSInteger</span> <span class="n">height</span> <span class="o">=</span> <span class="mi">240</span><span class="p">;</span>
<span class="nc">NSInteger</span> <span class="n">bytesPerPixel</span> <span class="o">=</span> <span class="mi">4</span><span class="p">;</span>
<span class="nc">NSInteger</span> <span class="n">bytesPerRow</span> <span class="o">=</span> <span class="n">width</span> <span class="o">*</span> <span class="n">bytesPerPixel</span><span class="p">;</span>
<span class="kt">unsigned</span> <span class="kt">char</span> <span class="o">*</span><span class="n">rawData</span> <span class="o">=</span> <span class="n">malloc</span><span class="p">(</span><span class="n">height</span> <span class="o">*</span> <span class="n">bytesPerRow</span><span class="p">);</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_fill_the_array">1.2. Fill the Array</h3>
<div class="paragraph"><p>We fill the array by moving through each pixel, filling some with color, and leaving others empty. This example fills all the pixels in every other line in the image with the C4GREY color (i.e. 50,55,60,255).</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">y</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">y</span> <span class="o">&lt;</span> <span class="n">height</span><span class="p">;</span> <span class="n">y</span><span class="o">+=</span><span class="mi">2</span><span class="p">)</span> <span class="p">{</span>
    <span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">x</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">x</span> <span class="o">&lt;</span> <span class="n">width</span> <span class="o">*</span> <span class="n">bytesPerPixel</span><span class="p">;</span> <span class="n">x</span><span class="o">+=</span><span class="mi">4</span><span class="p">)</span> <span class="p">{</span>
        <span class="kt">int</span> <span class="n">currentPosition</span> <span class="o">=</span> <span class="n">x</span> <span class="o">+</span> <span class="n">y</span> <span class="o">*</span> <span class="n">bytesPerRow</span><span class="p">;</span>
        <span class="n">rawData</span><span class="p">[</span><span class="n">currentPosition</span><span class="p">]</span> <span class="o">=</span> <span class="mi">50</span><span class="p">;</span>
        <span class="n">rawData</span><span class="p">[</span><span class="n">currentPosition</span> <span class="o">+</span> <span class="mi">1</span><span class="p">]</span> <span class="o">=</span> <span class="mi">55</span><span class="p">;</span>
        <span class="n">rawData</span><span class="p">[</span><span class="n">currentPosition</span> <span class="o">+</span> <span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="mi">60</span><span class="p">;</span>
        <span class="n">rawData</span><span class="p">[</span><span class="n">currentPosition</span> <span class="o">+</span> <span class="mi">3</span><span class="p">]</span> <span class="o">=</span> <span class="mi">255</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_create_the_image">2. Create the Image</h2>
<div class="sectionbody">
<div class="paragraph"><p>We can now use the raw data array to create a C4Image.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">img</span> <span class="o">=</span> <span class="p">[[</span><span class="n-ProjectClass">C4Image</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithRawData:rawData</span> <span class="n">width:width</span> <span class="n">height:height</span><span class="p">];</span>
</pre></div></div></div>
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
