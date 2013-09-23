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

<h2>C4Shape: Stroke Start / End</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3177399" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this example we create lines from the top to the bottom of the canvas. Then, we use the <em>strokeStart</em> and <em>strokeEnd</em> points to style them into a pyramid.</p></div>
<div class="imageblock">
<div class="content">
<img src="strokeStartEndAdvanced/strokeStartEndAdvanced.png" alt="Advanced Stroke Start & End" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_create_the_variables">1. Create the Variables</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are a couple steps to creating this example, first we create 3 variables:
1. An array of <strong>CGPoint</strong> structs to use as the end points for all the lines, a total line count based on each
2. A total line count, based on each line being 5.0 pts thick + 1.0 pt gap between lines (i.e. 6.0f)
3. A displacement value for each line to be used to calculate its stroke start / end points</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="nc">CGPointZero</span><span class="p">,</span><span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.width</span><span class="p">,</span><span class="mf">0.0f</span><span class="p">)};</span>
<span class="nc">CGFloat</span> <span class="n">totalLineCount</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.height</span> <span class="o">/</span> <span class="mf">6.0f</span><span class="p">;</span>
<span class="nc">CGFloat</span> <span class="n">strokeDisplacement</span> <span class="o">=</span> <span class="mf">0.5f</span> <span class="o">/</span> <span class="n">totalLineCount</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_create_the_lines">2. Create the Lines</h2>
<div class="sectionbody">
<div class="paragraph"><p>Next, we create the lines by running a loop that cycles through all the lines, styling and adding them to the canvas as it goes.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">for</span><span class="p">(</span><span class="kt">int</span> <span class="n">i</span> <span class="o">=</span> <span class="mi">0</span><span class="p">;</span> <span class="n">i</span> <span class="o">&lt;</span> <span class="n">totalLineCount</span><span class="p">;</span> <span class="n">i</span><span class="o">++</span><span class="p">)</span> <span class="p">{</span>
    <span class="c1">//set the end points for the current line</span>
    <span class="c1">//notice we only change the y value of the lines, moving them horizontally downwards</span>
    <span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span><span class="py">.y</span> <span class="o">=</span> <span class="n">i</span><span class="o">*</span><span class="mf">6.0f</span> <span class="o">+</span> <span class="mf">2.5f</span><span class="p">;</span>
    <span class="n">linePoints</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span><span class="py">.y</span> <span class="o">=</span> <span class="n">linePoints</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span><span class="py">.y</span><span class="p">;</span>

    <span class="c1">//create a new line</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">newLine</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">line:linePoints</span><span class="p">];</span>

    <span class="c1">//determine the current displacement of the ends of the line</span>
    <span class="nc">CGFloat</span> <span class="n">currentDisplacement</span> <span class="o">=</span> <span class="n">strokeDisplacement</span><span class="o">*</span><span class="p">(</span><span class="n">i</span><span class="o">+</span><span class="mi">1</span><span class="p">);</span>
    <span class="n">newLine</span><span class="py">.strokeStart</span> <span class="o">=</span> <span class="mf">0.5</span> <span class="o">-</span> <span class="n">currentDisplacement</span><span class="p">;</span>
    <span class="n">newLine</span><span class="py">.strokeEnd</span> <span class="o">=</span> <span class="mf">0.5f</span> <span class="o">+</span> <span class="n">currentDisplacement</span><span class="p">;</span>

    <span class="c1">//... and add it to the canvas</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:newLine</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Remember that you can animate all the <em>strokeStart</em> and <em>strokeEnd</em> points in this example&#8230;</td>
</tr></table>
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
