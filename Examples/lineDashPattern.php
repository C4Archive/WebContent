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

<h2>C4Shape: Line Dash Pattern</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3182808" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>You can set a dash pattern for the line of any C4Shape.</p></div>
<div class="imageblock">
<div class="content">
<img src="lineDashPattern/lineDashPattern.png" alt="Line Dash Pattern" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_pattern_concept">1. Pattern Concept</h2>
<div class="sectionbody">
<div class="paragraph"><p>You create a pattern by specifying a series of numbers which become the widths of the dashes and gaps in the pattern. These numbers are sequential and ordered such that the first number is a dash, the second is a gap, and so on&#8230; <em>dash</em> <em>gap</em> <em>dash</em> <em>gap</em>&#8230; Patterns will also repeat.</p></div>
<div class="paragraph"><p>For instance, the following will make a uniform pattern where all dashes are 5 points wide, and all gaps are 10 points wide.</p></div>
<div class="paragraph"><p><tt>5, 10</tt></p></div>
<div class="paragraph"><p>Turns into&#8230;</p></div>
<div class="paragraph"><p><tt>5, 10, 5, 10, 5, 10, 5, 10, 5, 10, 5, 10, 5, 10, &#8230;</tt> for the length of the line.</p></div>
<div class="paragraph"><p>An odd-numbered pattern like the following will also repeat, but the gaps and dashes will be slightly different than above</p></div>
<div class="paragraph"><p><tt>5, 10, 5</tt></p></div>
<div class="paragraph"><p>Turns into&#8230;</p></div>
<div class="paragraph"><p><tt>5, 10, 5, 5, 10, 5, 5, 10, 5, 5, 10, 5, 5, 10, 5, &#8230;</tt> for the length of the line.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_c_array_patterns">2. C-Array Patterns</h2>
<div class="sectionbody">
<div class="paragraph"><p>The easiest way to create a pattern for a line is to use a C-Array of <strong>CGFloat</strong> values.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">pattern</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="mi">5</span><span class="p">,</span><span class="mi">10</span><span class="p">};</span>
<span class="p">[</span><span class="n">line</span> <span class="n">setDashPattern:pattern</span> <span class="n">pointCount:</span><span class="mi">2</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">Familiar?</div>The technique for setting a dash pattern is similar to creating a polygon. You give the object your pattern and also have to specify the number of points in the pattern.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_nsarray_patterns">3. NSArray Patterns</h2>
<div class="sectionbody">
<div class="paragraph"><p>When you create a pattern using the <tt>setDashPattern:</tt> method, what happens under the hood is that the shape actually turns the C-Array into an NSArray of number objects.</p></div>
<div class="paragraph"><p>So, although the following technique is a bit longer it is actually the proper way of setting a dash pattern.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSArray</span> <span class="o">*</span><span class="n">patternArray</span> <span class="o">=</span> <span class="p">[</span><span class="nc">NSArray</span> <span class="n">arrayWithObjects:</span>
                         <span class="p">[</span><span class="nc">NSNumber</span> <span class="n">numberWithInt:</span><span class="mi">5</span><span class="p">],</span>
                         <span class="p">[</span><span class="nc">NSNumber</span> <span class="n">numberWithInt:</span><span class="mi">10</span><span class="p">],</span>
                         <span class="p">[</span><span class="nc">NSNumber</span> <span class="n">numberWithInt:</span><span class="mi">5</span><span class="p">],</span>
                         <span class="nb">nil</span><span class="p">];</span>
<span class="n">line</span><span class="py">.lineDashPattern</span> <span class="o">=</span> <span class="n">patternArray</span><span class="p">;</span>
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
