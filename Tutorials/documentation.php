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

<h2>Documentation</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;ve designed the documentation for C4 to integrate into Xcode and be as similar as possible to Apple&#8217;s documentation. This tutorial will help you understand how to navigate both our and Apple&#8217;s documentation, show you where to and how look for things.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/documentation.png" alt="doc" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_orientation">1. Orientation</h2>
<div class="sectionbody">
<div class="paragraph"><p>First, there is a lot of documentation available form Apple on top of which we here at C4 have tried to add a lot of our own documentation.</p></div>
<div class="paragraph"><p>Second, you can access documentation directly through Xcode or by going to the <a href="/documentation/">C4 Documenation</a> or the <a href="https://developer.apple.com/library/ios/navigation/">iOS Developer Library</a>.</p></div>
<div class="paragraph"><p>Third, documentation and development resources come in all kinds of formats including docsets, html, pdf, project files, sample code, notes, videos.</p></div>
<div class="sect2">
<h3 id="_where_to_start">1.1. Where To Start</h3>
<div class="paragraph"><p>All the content you can find online – from both us and Apple - <em>can be accessed through Xcode</em>. So, we&#8217;re going to focus this tutorial on accessing content via the <em>Documentation Organizer</em>.</p></div>
</div>
<div class="sect2">
<h3 id="_what_to_look_for">1.2. What To Look For</h3>
<div class="paragraph"><p>The most reliable source for figuring out what various classes can do, which methods work, and so on will be the official documentation. Tutorials, examples and walkthroughs, and C4-specific material will be available here on the C4 site.</p></div>
</div>
<div class="sect2">
<h3 id="_what_to_do_next">1.3. What To Do Next</h3>
<div class="paragraph"><p>Just follow along with this tutorial. The main thing is for you get oriented to the basic components of the organizer. Afterwards, you can then start reading docs, finding projects and so on&#8230; After a while <em>things will get easier and more intuitive</em>.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_organizer">2. The Organizer</h2>
<div class="sectionbody">
<div class="paragraph"><p>Xcode has a viewer that provides you access that manages documentation and helps you find C4 and Apple documentation resources. There are 4 main components to the organizer that you will use most regularly, they&#8217;re highlighted in the image below.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/organizer.png" alt="The Organizer" />
</div>
</div>
<div class="sect2">
<h3 id="_open_it">2.1. Open It</h3>
<div class="paragraph"><p>Open the organizer from Xcode&#8217;s menu, <strong>Window &gt; Organizer</strong>&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/organizerMenu.png" alt="Menu access" />
</div>
</div>
<div class="paragraph"><p>&#8230;Or, by clicking on the button in the top-right part of the main Xcode window&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/organizerButton.png" alt="The Organizer Button" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_navigator_selector_bar">2.2. Navigator Selector Bar</h3>
<div class="paragraph"><p>You can switch between different types of navigation for finding content in the organizer. The two main options that you should use are the <em>browse</em> and <em>search</em> modes.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/browseAndSearch.png" alt="Browse And Search" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_navigator_area">2.3. Navigator Area</h3>
<div class="paragraph"><p>This is where the results of your searching and browsing appear. You use this space for either scrolling through documentation, organized hierarchically in the browse mode, or by selecting various documents and resources returned to you by your search.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/navigatorArea.png" alt="The Navigator Area" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_content_jump_bar">2.4. Content Jump Bar</h3>
<div class="paragraph"><p>The jump bar in the content area allows you to further explore the document and the library it’s in by clicking on the current document&#8217;s ancestors.</p></div>
</div>
<div class="sect2">
<h3 id="_content_area">2.5. Content Area</h3>
<div class="paragraph"><p>The current document you&#8217;ve chosen will appear here.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_browsing">3. Browsing</h2>
<div class="sectionbody">
<div class="paragraph"><p>Use the browse navigator in the documentation organizer to explore installed documentation sets and find documents relevant to your development needs.</p></div>
<div class="paragraph"><p>Documentation is provided in documentation sets (doc sets) and organized hierarchically into categories by technology. A category can contain any or all documentation resource types—guides, references, and sample code, for example. Each item in the navigation is generally classified into one of the following:</p></div>
<div class="ulist"><ul>
<li>
<p>
Library (doc set)
</p>
</li>
<li>
<p>
Category or conceptual document
</p>
</li>
<li>
<p>
API reference document
</p>
</li>
<li>
<p>
Sample code project
</p>
</li>
<li>
<p>
Document page or section
</p>
</li>
<li>
<p>
Help article
</p>
</li>
</ul></div>
<div class="paragraph"><p>You can choose an item at any level of the hierarchy (library, category, document, or section) to view the corresponding page in the content area. Choose a category, for example, to browse a topic page with details about each document in that category, including a content description, change summary, and publication date.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">To open the selected document in your browser, Control-click in the content area and choose Open Page in Browser.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_searching">4. Searching</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can search developer documentation to locate information specific to your immediate needs. Searching can be the fastest way to find the exact documentation you need.</p></div>
<div class="paragraph"><p>In the following example, one result for the search term <tt>beat</tt> is an item for the function <tt>MusicSequenceGetSecondsForBeats</tt>, identified by the <em>f</em> icon:</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/searchResult.png" alt="A Search Result" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_c4_documentation">5. C4 Documentation</h2>
<div class="sectionbody">
<div class="paragraph"><p>When you install C4 on your computer, one of the files that gets installed is the C4 docset. This file is placed in a directory that Xcode recognizes, and as such, it becomes integrated into the documentation organizer.</p></div>
<div class="sect2">
<h3 id="_browsing_c4_docs">5.1. Browsing C4 Docs</h3>
<div class="paragraph"><p>Browsing C4 documentation is the same process as for Apple docs. Open the navigator, click on the browse icon, then select the C4 Documentation button from the navigator area.</p></div>
<div class="paragraph"><p>A full list of classes, protocols and categories will appear in the Navigator area. As well, the main page of the docset will appear in the content area. From here, you can browse through the C4 API.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/C4Documentation.png" alt="C4 Documentation" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_searching_c4_docs">5.2. Searching C4 Docs</h3>
<div class="paragraph"><p>You search for C4 documentation in the normal way. Click the search icon, type in a word and results begin to appear. Simple.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_other_tricks">6. Other Tricks</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are a few other tricks to finding documentation that are available when you&#8217;re working directly in the Xcode window.</p></div>
<div class="sect2">
<h3 id="_quick_help">6.1. Quick Help</h3>
<div class="paragraph"><p>While you&#8217;re coding you might want to quickly check the documentation for a word. Instead of jumping to the organizer, typing something in, finding the document and then navigating to the part you want to read, there is a much easier option&#8230; To use <strong>Quick Help</strong>, do the following:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Place your cursor over the word you want to check, then&#8230;
</p>
</li>
<li>
<p>
option-click
</p>
</li>
</ol></div>
<div class="paragraph"><p>A popover will appear with the documentation specific for that word.</p></div>
<div class="paragraph"><p>You can do this for <strong>classes</strong>&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/quickHelpClass.png" alt="Quick Help for Classes" />
</div>
</div>
<div class="paragraph"><p>&#8230;for <strong>methods</strong>&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/quickHelpMethod.png" alt="Quick Help for Methods" />
</div>
</div>
<div class="paragraph"><p>&#8230;and for <strong>properties</strong>&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/quickHelpProperty.png" alt="Quick Help for Properties" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_jump_to_definition">6.2. Jump to Definition</h3>
<div class="paragraph"><p>For those of you who like to go straight to raw header documentation, you can jump to a word&#8217;s <strong>definition</strong> by doing the following:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Place your cursor over the word you want to check, then&#8230;
</p>
</li>
<li>
<p>
control-click
</p>
</li>
<li>
<p>
select <em>Jump to Definition</em>
</p>
</li>
</ol></div>
<div class="imageblock">
<div class="content">
<img src="documentation/jumpToDefinition.png" alt="Jump To Definition" />
</div>
</div>
<div class="paragraph"><p>&#8230;or simply</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
command-click
</p>
</li>
</ol></div>
<div class="paragraph"><p>&#8230;either way will bring you to the point in a header (<strong>.h</strong>) file where that word is defined.</p></div>
<div class="imageblock">
<div class="content">
<img src="documentation/C4ShapeDefinition.png" alt="C4Shape Definition" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_reading_documentation">7. Reading Documentation</h2>
<div class="sectionbody">
<div class="paragraph"><p>Check out the <a href="readingDocumentation.php">"Reading Documentation Tutorial"</a> to find out how to understand the structure of written docs and how they translate to code.</p></div>
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
