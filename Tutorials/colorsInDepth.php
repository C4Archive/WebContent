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

<h2>Colors In-depth</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial I&#8217;ll introduce you to the <tt>UIColor</tt> class, which you&#8217;ll use to create colors in C4. This tutorial will be a bit high-level, leaving the details of implementation to various examples. You&#8217;ll find links to examples peppered throughout the tutorial.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_oh_color">1. Oh Color</h2>
<div class="sectionbody">
<div class="paragraph"><p>You&#8217;ll use colors a lot when working in C4. They can be applied to the <tt>strokeColor</tt> and <tt>fillColor</tt> of shapes, to the <tt>borderColor</tt>, <tt>backgroundColor</tt> and <tt>shadowColor</tt> of visual objects, to the various states of <tt>C4UIElements</tt> like buttons and sliders and so on.</p></div>
<div class="paragraph"><p>You&#8217;ll use them everywhere, and because the C4 framework uses <tt>UIColor</tt> (i.e. native Objective-C) it&#8217;s a good idea to create a thorough introduction to the class.</p></div>
<div class="sect2">
<h3 id="_why_the_ui">1.1. Why the UI?</h3>
<div class="paragraph"><p>A great question is "why isn&#8217;t there a <tt>C4Color</tt>?"&#8230;</p></div>
<div class="paragraph"><p>This was a big decision for me when I was creating C4. After a long time deciding whether to do what&#8217;s been done with other objects (i.e. wrap them in <tt>C4Object</tt> or <tt>C4Control</tt>). There were a two main reasons that sold me on this technique.</p></div>
</div>
<div class="sect2">
<h3 id="_you_8217_re_learning_ios">1.2. You&#8217;re Learning iOS</h3>
<div class="paragraph"><p>I wanted C4 to be the kind of API that would springboard people into programming native iOS applications. <tt>UIColor</tt> is one of those classes that just doesn&#8217;t need to be subclassed, and because of this it just made sense to have it be one of the ties to get you into native programming.</p></div>
</div>
<div class="sect2">
<h3 id="_because_they_say_so">1.3. Because They Say So</h3>
<div class="paragraph"><p>Seriously. <tt>UIColor</tt> has been engineered so that you&#8217;ll only ever have to subclass it in very rare circumstances. A quick scan of the <a href="http://developer.apple.com/library/ios/#documentation/UIKit/Reference/UIColor_Class/">official documentation</a> reads ``Most developers should have no need to subclass UIColor. The only time doing so might be necessary is if you require support for additional colorspaces or color models.``</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_c4_colors">2. C4 Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 3 default colors you can use in C4.</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>C4RED</tt>
</p>
</li>
<li>
<p>
<tt>C4BLUE</tt>
</p>
</li>
<li>
<p>
<tt>C4GREY</tt>
</p>
</li>
</ul></div>
<div class="paragraph"><p>These are the colors of the logo and are cooked into the API. So, any time you want to use a default color you can write something like:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
<span class="n">label</span><span class="py">.textColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>
<span class="n">movie</span><span class="py">.shadowColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="http://c4ios.com/examples/C4Colors">C4 Colors</a> example.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_preset_colors">3. Preset Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are a bunch of preset colors that you can access through the <tt>UIColor</tt> class. You can create a preset color by writing something like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">blueColor</span><span class="p">];</span>
</pre></div></div></div>
<div class="sect2">
<h3 id="_basic_presets">3.1. Basic Presets</h3>
<div class="paragraph"><p>There are 15 preset colors that you can use. To do so, you might replace the <tt>blueColor</tt> (from the code above) with any of the following list:</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>blackColor</tt>
</p>
</li>
<li>
<p>
<tt>blueColor</tt>
</p>
</li>
<li>
<p>
<tt>brownColor</tt>
</p>
</li>
<li>
<p>
<tt>cyanColor</tt>
</p>
</li>
<li>
<p>
<tt>darkGrayColor</tt>
</p>
</li>
<li>
<p>
<tt>grayColor</tt>
</p>
</li>
<li>
<p>
<tt>greenColor</tt>
</p>
</li>
<li>
<p>
<tt>lightGrayColor</tt>
</p>
</li>
<li>
<p>
<tt>magentaColor</tt>
</p>
</li>
<li>
<p>
<tt>orangeColor</tt>
</p>
</li>
<li>
<p>
<tt>purpleColor</tt>
</p>
</li>
<li>
<p>
<tt>redColor</tt>
</p>
</li>
<li>
<p>
<tt>whiteColor</tt>
</p>
</li>
<li>
<p>
<tt>yellowColor</tt>
</p>
</li>
<li>
<p>
<tt>clearColor</tt>
</p>
</li>
</ul></div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthPreset1.png" alt="Presets" />
</div>
</div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthPreset2.png" alt="Presets" />
</div>
</div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthPreset3.png" alt="Presets" />
</div>
</div>
<div class="paragraph"><p>That <tt>clearColor</tt> is a nice object that essentially fills with no color whatsoever. You can use <tt>clearColor</tt> in instances where you don&#8217;t want to see the line or the fill of a shape.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="http://c4ios.com/examples/colorPredefined">Predefined Colors</a> example.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_system_presets">3.2. System Presets</h3>
<div class="paragraph"><p>There are 5 preset colors that your operating system uses, and you can access them just like the basic presets. Here&#8217;s the list:</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>lightTextColor</tt>
</p>
</li>
<li>
<p>
<tt>darkTextColor</tt>
</p>
</li>
<li>
<p>
<tt>viewFlipsideBackgroundColor</tt>
</p>
</li>
<li>
<p>
<tt>scrollViewTexturedBackgroundColor</tt>
</p>
</li>
<li>
<p>
<tt>underPageBackgroundColor</tt>
</p>
</li>
</ul></div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthSystemPresets.png" alt="Presets" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="http://c4ios.com/examples/colorSystem">System Colors</a> example.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_custom_colors">4. Custom Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>For the most part, you&#8217;ll be wanting to create your own custom colors. There are a bunch of different ways to do so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithRed:</span><span class="mf">1.0f</span> <span class="n">green:</span><span class="mf">0.0f</span> <span class="n">blue:</span><span class="mf">0.0f</span> <span class="n">alpha:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithWhite:</span><span class="mf">1.0f</span> <span class="n">alpha:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:aUImage</span><span class="p">];</span>
<span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithHue:</span><span class="mf">1.0f</span> <span class="n">saturation:</span><span class="mf">0.0f</span> <span class="n">brightness:</span><span class="mf">0.0f</span> <span class="n">alpha:</span><span class="mf">1.0f</span><span class="p">];</span>
<span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithCGColor:aCGColor</span><span class="p">];</span>
<span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithCIColor:aCIColor</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>The method&#8217;s listed above are the main ways you&#8217;re going to be creating colors, and I&#8217;ve listed them in descending order of how often I use them. The last 3 you&#8217;ll probably not be using very often, but from time to time it&#8217;s good to know that they&#8217;re there. The <tt>colorWithPatternImage:</tt> is a pretty interesting method.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="http://c4ios.com/examples/colorRGBA">RGB</a> and <a href="http://c4ios.com/examples/colorHSBA">HSB</a> examples.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_alpha_8217_d_colors">4.1. Alpha&#8217;d Colors</h3>
<div class="paragraph"><p>One of the really nice components of the <tt>UIColor</tt> class is that you can create new colors with different alpha values from a single object. You can do this with <em>all</em> colors even <tt>C4RED</tt>, <tt>C4BLUE</tt> and <tt>C4GREY</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIColor</span> <span class="o">*</span><span class="n">newColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">oldColor</span> <span class="n">colorWithAlphaComponent:</span><span class="mf">0.5f</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthTransparent.png" alt="Transparent Colors" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="/examples/colorWithAlpha.php">Transparent Colors</a> example</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_pattern_images">4.2. Pattern Images</h3>
<div class="paragraph"><p>Fancy. Fancy. You can use <em>images</em> as the basis for generating a color!</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIColor</span> <span class="o">*</span><span class="n">c</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:aC4Image</span><span class="py">.UIImage</span><span class="p">];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="colorsInDepth/colorsInDepthPatternImage.png" alt="Pattern Image Colors" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Have a look at the <a href="http://c4ios.com/examples/colorPatternImage.php">Pattern Image</a> example</td>
</tr></table>
</div>
<div class="paragraph"><p>Please remember, most of our other <a href="/tutorials/">tutorials</a> are more fun.</p></div>
<div class="paragraph"><p>Thank you for being patient.</p></div>
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
