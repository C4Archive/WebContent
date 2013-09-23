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

<h2>Font: Font and Family Names</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3230838" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Every font in a family has a specific name. For instance, an italic font will have a different name than a bold font. This example builds off of the <a href="fontFamilyLabels.php">previous font example</a>.</p></div>
<div class="imageblock">
<div class="content">
<img src="fontAndFamilyLabels/fontAndFamilyLabels.png" alt="Font and Family Names" height="500" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">iosfonts.com</div><a href="www.iosfonts.com">iOS Fonts</a> has a complete list of fonts for iOS, and shows their availability (i.e. iOS 4.3, 5.0, 6.0, etc&#8230;)</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_family_name_array">1. Family Name Array</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first step to printing out the font family names available in the current version of iOS that you&#8217;re working with, is to grab an array of all font families.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSArray</span> <span class="o">*</span><span class="n">familyNames</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">familyNames</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_create_labels">2. Create Labels</h2>
<div class="sectionbody">
<div class="paragraph"><p>All the names in the array are actually <strong>NSString</strong> objects, which means we can easily use them to create labels. A simple <strong>for</strong> loop will help us here.</p></div>
<div class="sect2">
<h3 id="_a_shifting_origin">2.1. A Shifting Origin</h3>
<div class="paragraph"><p>First, we create a <strong>CGPoint</strong> which we will use to set the origin for every label&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">point</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span> <span class="mi">10</span><span class="p">);</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_a_tricky_loop">2.2. A Tricky Loop</h3>
<div class="paragraph"><p>We create an outer loop that cycles through all the font families and then each one of their font names&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="n">familyName</span> <span class="k">in</span> <span class="n">familyNamesArray</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">NSArray</span> <span class="o">*</span><span class="n">fontNames</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontNamesForFamilyName:familyName</span><span class="p">];</span>
    <span class="c1">//The nested loop goes here</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_the_nested_loop">2.3. The Nested Loop</h3>
<div class="paragraph"><p>The nested loop does most of the work, just like the <a href="fontFamilyLabels.php">previous example</a> this one creates a label for each font name in a family and puts it up on the screen.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="n">fontName</span> <span class="k">in</span> <span class="n">fontNames</span><span class="p">)</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">f</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:fontName</span> <span class="n">size:</span><span class="mf">14.0f</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">l</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:fontName</span> <span class="n">font:f</span><span class="p">];</span>
    <span class="n">l</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">point</span><span class="p">;</span>
    <span class="n">point</span><span class="py">.y</span> <span class="o">+=</span> <span class="n">l</span><span class="py">.height</span><span class="p">;</span>
    <span class="k">if</span> <span class="p">(</span><span class="n">point</span><span class="py">.y</span> <span class="o">&gt;</span> <span class="k">self</span><span class="py">.canvas.height</span><span class="p">)</span> <span class="p">{</span>
        <span class="n">point</span><span class="py">.x</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">/</span> <span class="mi">3</span><span class="p">;</span>
        <span class="n">point</span><span class="py">.y</span> <span class="o">=</span> <span class="mi">10</span><span class="p">;</span>
    <span class="p">}</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:l</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_putting_it_all_together">3. Putting It All Together</h2>
<div class="sectionbody">
<div class="paragraph"><p>The entire loop looks like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSArray</span> <span class="o">*</span><span class="n">familyNamesArray</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">familyNames</span><span class="p">];</span>

<span class="nc">CGPoint</span> <span class="n">point</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span> <span class="mi">10</span><span class="p">);</span>

<span class="k">for</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="n">familyName</span> <span class="k">in</span> <span class="n">familyNamesArray</span><span class="p">)</span> <span class="p">{</span>
    <span class="nc">NSArray</span> <span class="o">*</span><span class="n">fontNames</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontNamesForFamilyName:familyName</span><span class="p">];</span>
    <span class="k">for</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="n">fontName</span> <span class="k">in</span> <span class="n">fontNames</span><span class="p">)</span> <span class="p">{</span>
        <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">f</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:fontName</span> <span class="n">size:</span><span class="mf">14.0f</span><span class="p">];</span>
        <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">l</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:fontName</span> <span class="n">font:f</span><span class="p">];</span>
        <span class="n">l</span><span class="py">.origin</span> <span class="o">=</span> <span class="n">point</span><span class="p">;</span>
        <span class="n">point</span><span class="py">.y</span> <span class="o">+=</span> <span class="n">l</span><span class="py">.height</span><span class="p">;</span>
        <span class="k">if</span> <span class="p">(</span><span class="n">point</span><span class="py">.y</span> <span class="o">&gt;</span> <span class="k">self</span><span class="py">.canvas.height</span><span class="p">)</span> <span class="p">{</span>
            <span class="n">point</span><span class="py">.x</span> <span class="o">+=</span> <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">/</span> <span class="mi">3</span><span class="p">;</span>
            <span class="n">point</span><span class="py">.y</span> <span class="o">=</span> <span class="mi">10</span><span class="p">;</span>
        <span class="p">}</span>
        <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:l</span><span class="p">];</span>
    <span class="p">}</span>
<span class="p">}</span>
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
