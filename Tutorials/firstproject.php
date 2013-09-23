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

<h2>Your First C4 Project</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial, we&#8217;ll take you through the steps of creating your first C4 project. In the end you&#8217;ll have built a simple - only a white screen - but full iOS app.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/firstproject.png" alt="A simple iOS app" height="400" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_creating_a_project">1. Creating a Project</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first step to creating a new C4 project is to open Xcode. If you&#8217;re running OSX 10.7, Xcode should be in your Applications folder.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/xcode.png" alt="Xcode.app in the Applications Folder" height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_welcome_window">1.1. Welcome Window</h3>
<div class="paragraph"><p>You&#8217;ll should be presented with a welcome window with some options for creating new projects, or opening old ones. Click on the <strong>Create a new Xcode project</strong> button to the left of the window.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/welcome.png" alt="Welcome to Xcode Window" height="400" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/warning.png" alt="Warning" />
</td>
<td class="content">If you don&#8217;t have Xcode, or you haven&#8217;t installed C4, you need to go through the <a href="gettingstarted.php">Getting Started</a> tutorial.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_select_the_c4_template">1.2. Select the C4 Template</h3>
<div class="paragraph"><p>The first step is to choose the kind of project you want to build. Xcode provides you with a lot of options, but you should choose the <strong>C4 Single View Application</strong>, under the iOS / Application section.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/c4template.png" alt="Choose the C4 Template" height="400" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/important.png" alt="Important" />
</td>
<td class="content">If you followed the <a href="gettingstarted.php">Getting Started</a> tutorial and you don&#8217;t see this option, then something went wrong with the installer. We suggest emailing us directly about the problem and we can help you figure out how to get running. Click on the author&#8217;s name at the top of this tutorial page, it should a <em>mailto</em> link that takes you to your email client.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_naming_your_project">1.3. Naming Your Project</h3>
<div class="paragraph"><p>Next, you&#8217;ll be presented with a panel that lets you choose options for your project. The first thing to do is to type in a project name, here we use the name <em>myProject</em>.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/name.png" alt="Naming your project" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_choose_a_device">1.4. Choose a device</h3>
<div class="paragraph"><p>You next have to choose the specific device you want to build an app for, and the choices are either <strong>iPad</strong> or <strong>iPhone</strong>. So, select one of those two options, and then hit <strong>Next</strong>.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/device.png" alt="Naming your project" height="400" />
</div>
</div>
<div class="paragraph"><p>WARNING:DO NOT choose the Universal option&#8230; As of Sept. 2012 this option is currently unavailable even though it looks like you can choose it&#8230; Making project templates in Xcode is quite complicated, but we&#8217;re working on getting a Universal option sometime later this year.</p></div>
</div>
<div class="sect2">
<h3 id="_class_prefix">1.5. Class Prefix</h3>
<div class="paragraph"><p>There&#8217;s an option to add a <strong>class prefix</strong> for your project. <strong>make sure this is blank</strong></p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/warning.png" alt="Warning" />
</td>
<td class="content">
<div class="title">CLASS PREFIX (17/09/2012)</div>MAKE SURE THE CLASS PREFIX FIELD REMAINS EMPTY. I will build a new version of the installer that can handle this better.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_choose_a_location">1.6. Choose a Location</h3>
<div class="paragraph"><p>Finally, you&#8217;re given the opportunity to save the project anywhere on your computer. Choose a location and then hit the <strong>Create</strong> button.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/create.png" alt="Create your project" height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_cleaning_up_xcode">2. Cleaning up Xcode</h2>
<div class="sectionbody">
<div class="paragraph"><p>When Xcode first opens the window is pretty noisy, there are a bunch of panels that we don&#8217;t need, and the current file selected is the project, which shows a bunch of information that&#8217;s not really important <em>at the moment</em>.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/noisy.png" alt="Noisy Xcode Window" height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_hide_the_utilities">2.1. Hide the Utilities</h3>
<div class="paragraph"><p>Hide the Utilities panel (on the right) by clicking the rightmost <em>view</em> button at the top-right of the Xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/hideutilities.png" alt="Hiding the Utilities Panel" />
</div>
</div>
<div class="paragraph"><p>When you&#8217;ve done that, you&#8217;ll be left with a view that shows only the project details.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/projectdetails.png" alt="Project Details" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_choose_c4workspace_m">2.2. Choose C4WorkSpace.m</h3>
<div class="paragraph"><p>From the file list on the left, choose the <tt><strong>C4Workspace.m</strong></tt> file, this will be the main place that you will be working with C4.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/workspace.png" alt="The Main WorkSpace" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_roll_up_folders">2.3. Roll Up Folders</h3>
<div class="paragraph"><p>Optionally, you can roll up all the folders on the left panel&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/rolledupfolders.png" alt="Rolled Up Folders" height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_build_and_launch">3. Build and Launch</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now it&#8217;s time to build and launch your first C4 app (which is actually a native iOS app). Simply click on the <strong>RUN</strong> button at the top-left of the Xcode window and your project should launch.</p></div>
<div class="imageblock">
<div class="content">
<img src="firstproject/run.png" alt="The Run Button" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">You should see your project open up in the iOS Simulator. If you have an iOS device plugged in, and you have an Apple Developer account, then you can change the Scheme (just to the right of the run button) from iOS Simulator to your device and your app should launch on your device.</td>
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
