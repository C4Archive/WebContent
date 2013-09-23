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
<div id="header" class="span12">

<h2>Reading Examples</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial we will show you how to understand a bit about our <a href="/examples">Examples</a>.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingExamples/readingExamples.png" alt="Examples of Examples" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_title_amp_preamble_amp_toc">1. Title &amp; Preamble &amp; TOC</h2>
<div class="sectionbody">
<div class="paragraph"><p>The title and preamble are fairly straightforward. The title describes the document, whereas the preamble gives you a sense of what the example entails.</p></div>
<div class="paragraph"><p>The table of contents gives you links to the different sections of the page.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingExamples/titlePreambleTOC.png" alt="Title & Preamble & TOC" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_visuals">2. Visuals</h2>
<div class="sectionbody">
<div class="paragraph"><p>This section gives you a visual snapshot of what the current example. Most often this section is an image of an iPad running  an application that shows what the example is all about. When the example shows something moving or some kind of interaction, this section will be a video linked to the <a href="http://www.vimeo.com/c4ios">C4 Vimeo</a> account. In some rare cases, this section is an image of the Xcode window itself.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_writeup">3. Writeup</h2>
<div class="sectionbody">
<div class="paragraph"><p>The writeup is broken down into 2 components: <em>descriptions</em> and <em>code blocks</em>.</p></div>
<div class="sect2">
<h3 id="_descriptions">3.1. Descriptions</h3>
<div class="paragraph"><p>Descriptions are sections of the writeup that pertain to a <em>single</em> concept, sometimes broken down into sub-concepts. These may also contain NOTES, WARNINGS, CAUTIONS, etc&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="readingExamples/descriptions.png" alt="Descriptions" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_code_blocks">3.2. Code Blocks</h3>
<div class="paragraph"><p>Code blocks are used to <em>explain</em> the concept of a description, and though they could be incorporated into one of your projects, they are not designed to be standalone. Moreover, they are designed to communicate the idea of what is being discussed.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingExamples/codeBlock.png" alt="Code Block" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_code_links">4. Code Links</h2>
<div class="sectionbody">
<div class="paragraph"><p>At the top-right of every page there is a space where a link to a <em>GitHub</em> or a <em>Gist</em> respository exists. The image that you see in the example can be built and compiled from this code.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingExamples/codeLink.png" alt="Code Link" />
</div>
</div>
<div class="sect2">
<h3 id="_steps_to_using_code">4.1. Steps to Using Code</h3>
<div class="paragraph"><p>There are only a couple of easy steps to use the code from an example.</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
click on the link
</p>
</li>
<li>
<p>
if it is a gist, copy the entire contents of the gist into an open C4 project in Xcode
</p>
</li>
<li>
<p>
if it is a GitHub repo, download and open the project
</p>
</li>
<li>
<p>
from Xcode, click the run button
</p>
</li>
</ol></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_what_examples_are_not">5. What Examples Are Not</h2>
<div class="sectionbody">
<div class="paragraph"><p><em>Examples are not tutorials!</em> Examples are written to show you only the concept of a topic. The reason for this is because some examples take a lot of extra setup code to get running nicely, the details of which aren&#8217;t really that interesting&#8230; So, for examples we strip the code blocks down only to the lines that matter.</p></div>
<div class="paragraph"><p><em>Examples are not complete code!</em> Although each code block should run on its own if copied into a project, there might be some extra setup code that needs to be inserted into a C4 project before the example can run. <strong>You should grab the linked code at the top of the page to see the example run.</strong></p></div>
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
