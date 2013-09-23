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

<h2>C4: Media &amp; Interactivity for iOS</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Thanks for joining the newest workshop on C4 at <a href="http://www.vivomediaarts.com">VIVO</a>! I&#8217;m pretty excited to be hosting this workshop because it&#8217;s the first one in Vancouver, and the first one at VIVO too.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_intro">1. Intro</h2>
<div class="sectionbody">
<div class="paragraph"><p>I&#8217;ve designed this workshop to be a solid foundation for getting started with C4 as an API for building creative and expressive applications. There is really <em>too much</em> to teach in a single session or a single workshop but, because of its design, there are fundamentals common to almost everything in C4 that are important to understand right at the very beginning.</p></div>
<div class="paragraph"><p>This workshop is meant to teach just that.</p></div>
<div class="paragraph"><p>In doing so, I&#8217;ve split up the workshop into 4 main sections. 3 of these tackle an important concept and highlight examples of code that will get you rolling along. The fourth session will be for open questions and the chance to work on anything that might interest you.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_gestalt">2. Gestalt</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first session will focus on working with shapes to create gestalt imagery. The shapes and images we&#8217;ll be creating will be fairly simple, but they&#8217;ll also be designed in a way that highlights some of the important characteristics of working with objects in C4.</p></div>
<div class="imageblock">
<div class="content">
<img src="gestalt/gestaltHeader.png" alt="Four Gestalt Images" />
</div>
</div>
<div class="sect2">
<h3 id="_date">2.1. Date</h3>
<div class="paragraph"><p>Monday May 6th, 7-10pm</p></div>
</div>
<div class="sect2">
<h3 id="_content">2.2. Content</h3>
<div class="paragraph"><p>Here&#8217;s a link to the <a href="gestalt.php">GESTALT</a> workshop tutorial.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_media_mashup">3. Media Mashup</h2>
<div class="sectionbody">
<div class="paragraph"><p>In this session participants will be challenged to integrate images, audio and video into the gestalt works they created and build upon what they learned in the previous session. At the end of the session participants will be asked to think about a directed project they want to create.</p></div>
<div class="sect2">
<h3 id="_date_2">3.1. Date</h3>
<div class="paragraph"><p>Wednesday May 9th, 7-10pm.</p></div>
</div>
<div class="sect2">
<h3 id="_content_2">3.2. Content</h3>
<div class="paragraph"><p>n/a</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_interactivity">4. Interactivity</h2>
<div class="sectionbody">
<div class="paragraph"><p>The focus for this session will be on integrating touch and gestural interaction with the objects and media that were introduced in Sessions 1 &amp; 2. participants will choose and begin working their own directed projects.</p></div>
<div class="sect2">
<h3 id="_date_3">4.1. Date</h3>
<div class="paragraph"><p>Monday May 13th, 7-10pm</p></div>
</div>
<div class="sect2">
<h3 id="_content_3">4.2. Content</h3>
<div class="paragraph"><p>Here&#8217;s a link to the <a href="interaction.php">INTERACTION</a> workshop tutorial</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_directed_projects">5. Directed Projects</h2>
<div class="sectionbody">
<div class="paragraph"><p>In this session participants will continue working on their directed projects. New concepts and specific techniques that arise through their experimentation will be discussed.</p></div>
<div class="paragraph"><p>Participants will also be introduced to some pretty fresh new content for the C4 site. We just finished working on <strong>125</strong> new examples and <strong>20</strong> new tutorials.</p></div>
<div class="sect2">
<h3 id="_date_4">5.1. Date</h3>
<div class="paragraph"><p>Wednesday May 16th, 7-10pm</p></div>
</div>
<div class="sect2">
<h3 id="_content_4">5.2. Content</h3>
<div class="paragraph"><p>n/a</p></div>
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
