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

<h2>Color: All Types</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3177100" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>There are a <strong>LOT</strong> of different ways to create colors in C4. You can use the <a href="C4Colors.php">C4 colors</a> (from the logo), you can use the <a href="colorPredefined.php">predefined</a> UIColors, you can create <a href="colorRGBA.php">RGB</a> or <a href="colorHSBA">HSB</a> colors, you can use <a href="colorPatternImage.php">pattern images</a> as colors&#8230;</p></div>
<div class="paragraph"><p>So, there&#8217;s a lot to choose from&#8230;</p></div>
<div class="paragraph"><p>This example shows basic use of all the different types of colors.</p></div>
<div class="imageblock">
<div class="content">
<img src="colorAllTypes/colorAllTypes.png" alt="Using all types of colors" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_default_colors">1. Default Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>The default colors for any shape that you create are <strong>C4BLUE</strong> for the <em>fillColor</em> and <strong>C4RED</strong> for the <em>strokeColor</em>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_c4_colors">2. C4 Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 3 C4 Colors, which match those from the logo. They are:
. <strong>C4RED</strong>
. <strong>C4Blue</strong>
. <strong>C4GREY</strong>.</p></div>
<div class="paragraph"><p>You can use them like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_predefined_colors">3. Predefined Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can use any of the <a href="colorPredefined.php">predefined</a> UIColors, like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">orangeColor</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">darkGrayColor</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_rgb_a_colors">4. RGB(A) Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can create <a href="colorRGBA.php">RGB colors</a> by using the UIColor <tt>colorWithRed:green:blue:alpha:</tt> method.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithRed:</span><span class="mf">0.50</span> <span class="n">green:</span><span class="mf">1.0</span> <span class="n">blue:</span><span class="mf">0.0</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithRed:</span><span class="mf">0.50</span> <span class="n">green:</span><span class="mf">0.0</span> <span class="n">blue:</span><span class="mf">0.0</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">]</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">What&#8217;s that 1.0f?</div>Remember that all the color values in C4 are actually mapped from 0 to 1, rather than 0 to 255 like in some other APIs. In C4,  an RGB value of 255 = 1.0f, 128 = 0.5f, &#8230; If you like to work with RGB values, you can use the RGBtoFloat() method to convert for you.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_hsb_a_colors">5. HSB(A) Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can create <a href="colorHSBA.php">HSB colors</a> by using the UIColor <tt>colorWithHue:saturation:brightness:alpha:</tt> method.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithHue:</span><span class="mf">0.5</span> <span class="n">saturation:</span><span class="mf">1.0</span> <span class="n">brightness:</span><span class="mf">1.0</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithHue:</span><span class="mf">0.25</span> <span class="n">saturation:</span><span class="mf">0.75</span> <span class="n">brightness:</span><span class="mf">0.5</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_white_colors">6. White Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>You easily specify greyscale colors with the UIColor <tt>colorWithWhite:alpha:</tt> method (with 1.0f being white, and 0.0f being black).</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithWhite:</span><span class="mf">0.5</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithWhite:</span><span class="mf">0.33</span> <span class="n">alpha:</span><span class="mf">1.0</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_system_colors">7. System Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are several <a href="colorSystem.php">system colors</a> that OSX uses, that you can use as well.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">scrollViewTexturedBackgroundColor</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">darkTextColor</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_alpha_colors">8. Alpha Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>All colors, no matter what type they are, can be used to create copies of themselves with different <a href="colorWithAlpha.php">alpha values</a>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rect</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">rect</span><span class="py">.fillColor</span> <span class="n">colorWithAlphaComponent:</span><span class="mf">0.5</span><span class="p">];</span>
<span class="n">rect</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="n">rect</span><span class="py">.strokeColor</span> <span class="n">colorWithAlphaComponent:</span><span class="mf">0.5</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_pattern_image_colors">9. Pattern Image Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>It is actually possible to create colors using <a href="colorPatternImage.php">patterns and images</a>. The UIColor <tt>colorWithPatternImage:</tt> method allows you to easily apply an image as a fill, or stroke to a shape or any other object that has a color property.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">fillPattern</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;pyramid.png&quot;</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.fillColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:fillPattern</span><span class="py">.UIImage</span><span class="p">];</span>

<span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">strokePattern</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Image</span> <span class="n">imageNamed:</span><span class="s">@&quot;pattern.png&quot;</span><span class="p">];</span>
<span class="n">shape</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">colorWithPatternImage:strokePattern</span><span class="py">.UIImage</span><span class="p">];</span>
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
