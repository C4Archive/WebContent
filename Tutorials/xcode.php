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

<h2>Xcode, In a Nutshell</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="xcode/xcode.png" alt="Xcode" />
</div>
</div>
<div class="paragraph"><p>In this tutorial, we&#8217;ll orient you to Xcode, Apple&#8217;s Integrated Development Environment (IDE)&#8230; This will be only a brief introduction to the application with a focus on the more important parts that you&#8217;ll use when working with C4.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_xcode_app">1. Xcode App</h2>
<div class="sectionbody">
<div class="paragraph"><p>The <a href="https://developer.apple.com/xcode/">Xcode</a> app is the program you need to use in order to develop for iOS and Mac. It is called an IDE because it packages a lot of useful components, softwares and resources in a single application. There is an extraordinary amount of things that Xcode can do, as described on its main page:</p></div>
<div class="quoteblock">
<div class="content">Xcode is Apple&#8217;s powerful integrated development environment for creating great apps for Mac, iPhone, and iPad. Xcode includes the Instruments analysis tool, iOS Simulator, and the latest Mac OS X and iOS SDKs.</div>
<div class="attribution">
<em>developer.apple.com/xcode/</em><br />
&#8212; Apple
</div></div>
<div class="paragraph"><p>A full orientation to Xcode takes more than just reading, it takes practice. But, you can get up and running easily enough without having to worry about all the details of the application.</p></div>
<div class="paragraph"><p>Here, we&#8217;ll show you 5 things you will need to know about Xcode and why you need to know them when you develop with C4.</p></div>
<div class="ulist"><ul>
<li>
<p>
The 4 Areas of Xcode
</p>
</li>
<li>
<p>
How to Build &amp; Run
</p>
</li>
<li>
<p>
Debugging
</p>
</li>
<li>
<p>
Documentation
</p>
</li>
<li>
<p>
Setup Tricks
</p>
</li>
</ul></div>
</div>
</div>
<div class="sect1">
<h2 id="_the_4_areas_of_xcode">2. The 4 Areas of Xcode</h2>
<div class="sectionbody">
<div class="paragraph"><p>The Xcode app is broken up into 4 main areas.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/xcode4Areas.png" alt="4 Main Areas of Xcode" />
</div>
</div>
<div class="sect2">
<h3 id="_editor_area">2.1. Editor Area</h3>
<div class="paragraph"><p>This is the space where you will <em>write code</em>.</p></div>
<div class="paragraph"><p>If you can&#8217;t see the editor then you can click on any <strong>.m</strong> or <strong>.h</strong> file in the navigator.</p></div>
</div>
<div class="sect2">
<h3 id="_navigator_area">2.2. Navigator Area</h3>
<div class="paragraph"><p>This is where you can look and navigate through your entire project&#8217;s structure. Here, you can add, delete, rename and preview files.</p></div>
<div class="paragraph"><p>If you can&#8217;t see the navigator then you can click on the leftmost view button at the top-left of Xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/viewButtonNavigator.png" alt="The Navigator View Button" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_debug_area">2.3. Debug Area</h3>
<div class="paragraph"><p>You&#8217;ll most likely be using the console in this area&#8230; The console is the place where your application will write notifications, crashes, and other things that you might slip into your code using the <tt>C4Log()</tt> function.</p></div>
<div class="paragraph"><p>If you can&#8217;t see the console then you can click on the middle view button at the top-left of Xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/viewButtonConsole.png" alt="The Console View Button" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_utility_area">2.4. Utility Area</h3>
<div class="paragraph"><p>The panel on the left side of the Xcode window is where you&#8217;ll find a lot of helpful links, advice and even some drag / drop options. The purpose of this panel it to provide you with useful help for developing applications.</p></div>
<div class="paragraph"><p>If you can&#8217;t see the utilities then you can click on the rightmost view button at the top-left of Xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/viewButtonUtility.png" alt="The Utility View Button" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_how_to_build_amp_run">3. How To Build &amp; Run</h2>
<div class="sectionbody">
<div class="paragraph"><p>The main thing you&#8217;ll be doing is compiling your code and running it, either on a device or in the iOS Simulator.</p></div>
<div class="sect2">
<h3 id="_the_run_button">3.1. The Run Button</h3>
<div class="paragraph"><p>This is the easiest way to build an application and launch it&#8230; So, when you&#8217;ve got some code that you want to test, all you have to do is hit the run button at the top-left of the xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/run.png" alt="The Run Button" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_schemes">3.2. Schemes</h3>
<div class="paragraph"><p>When Xcode compiles it uses something called a Scheme to determine <em>how it will compile</em> your application. In general, you can think of a scheme as <strong>a configuration to use when building</strong>. Rarely will you need to change or update schemes, which is nice because they can get complicated.</p></div>
<div class="paragraph"><p>The scheme area of Xcode looks like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/schemeButton.png" alt="The Schemes Button" />
</div>
</div>
<div class="paragraph"><p>For the most part you&#8217;ll only ever need to switch between <strong>iOS Simulator</strong> and <strong>iOS Device</strong>. If you have an iOS device plugged into your computer, you can change the current scheme to build and deploy straight to the device.</p></div>
<div class="paragraph"><p>You simply <em>click on the right side of the scheme button</em> and select the device.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/changeScheme.png" alt="Changing the Scheme" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_debugging">4. Debugging</h2>
<div class="sectionbody">
<div class="paragraph"><p>There is a lot to learn when it comes to debugging, but one of the most handy tricks to know is how to use the <strong>console</strong>. We won&#8217;t get into a full explanation of debugging, but we will show you where the console is and how to print out to it&#8230;</p></div>
<div class="sect2">
<h3 id="_console">4.1. Console</h3>
<div class="paragraph"><p>The debug console is a window that receives notifications and messages from the current running application and is a place where you can read what is going on inside the app.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/debugConsole.png" alt="The Debug Console" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_c4log">4.2. C4Log()</h3>
<div class="paragraph"><p>Get to know this simple function! <tt>C4Log()</tt> will allow you to print messages to the console at any time in your application. You can place calls to <tt>C4Log()</tt> inside methods and functions to help you figure out what is happening, when, and what the value of a variable might be at any given time.</p></div>
<div class="paragraph"><p>You&#8217;ll see some references to <tt>C4Log()</tt> throughout the tutorials and examples, so keep an eye out&#8230;</p></div>
<div class="paragraph"><p>For now, we used the following call to <tt>C4Log()</tt> to produce the message in the image above:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n-ProjectClass">C4Log</span><span class="p">(</span><span class="s">@&quot;This is the Debug Console&quot;</span><span class="p">);</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_documentation">5. Documentation</h2>
<div class="sectionbody">
<div class="paragraph"><p>Xcode has a really nice integration with all sorts of documentation, including Objective-C, Carbon, C, and much much more. We have also figured out how to integrate C4 documentation into Xcode, it gets installed when you run the C4 installer.</p></div>
<div class="sect2">
<h3 id="_the_organizer">5.1. The Organizer</h3>
<div class="paragraph"><p>You can use the documentation organizer to locate and use documentation resources that come with Xcode, and to access C4 documentation. The documentation organizer is a full-featured viewer that manages documentation sets (also known as doc sets), to provide integrated searching and viewing of developer documentation. The organizer comprises a navigator area and a content area.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/organizer.png" alt="The Documentation Organizer" height="400" />
</div>
</div>
<div class="paragraph"><p>You can launch the Organizer by pressing on the button at the top-right of the Xcode window.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/organizerButton.png" alt="The Organizer Button" />
</div>
</div>
<div class="paragraph"><p>Two of the most useful ways of navigating with documentation are the <em>browse</em> and <em>search</em> modes, both of which can be accessed on the left-hand side of the organizer window.</p></div>
<div class="paragraph"><p>When you click on the little eye icon, a list of available documentation sets pops up. In the image below we&#8217;ve clicked on the C4 docset and scrolled down the <em>classes</em> section to see a list of available classes.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/organizerBrowse.png" alt="Organizer Browse Mode" height="400" />
</div>
</div>
<div class="paragraph"><p>The second, and probably most useful way to navigate code is to use the search function. In the image below we&#8217;ve clicked on the little search icon and entered <strong>NSObject</strong> as the search term. As you can see, a list of various kinds of documentation resources appears, including: reference, guides and sample code.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/organizerSearch.png" alt="Organizer Search Mode" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_option_click">5.2. Option-Click</h3>
<div class="paragraph"><p>Instead of always going to the organizer, you can get a quick view of the documentation for a given method, class or object. Selecting the object and then <tt><strong>option-clicking</strong></tt>, or <tt><strong>right-clicking</strong></tt>, causes a small popover window to appear with documentation specific for that particular method, class or object.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/optionClick.png" alt="Quick View of Documentation" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_setup_tricks">6. Setup Tricks</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are two tricks for setting up Xcode that can be quite useful. One is to learn how to change the color settings, and the other is to set the console to appear when a project is run.</p></div>
<div class="sect2">
<h3 id="_color_settings">6.1. Color Settings</h3>
<div class="paragraph"><p>You can change the color settings of Xcode by opening up its <strong>preferences</strong> pane and selecting the <strong>Fonts &amp; Colors</strong> tab. You&#8217;ll find that there are a few preinstalled color schemes.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/colorSettings.png" alt="Setting the Color Theme" height="400" />
</div>
</div>
<div class="paragraph"><p>The one being used in the image above is called Solarized. You can download the theme from:</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="http://ethanschoonover.com/solarized">Solarized, by Ethan Schoonover</a>
</p>
</li>
</ul></div>
<div class="paragraph"><p>&#8230;and then copy the <tt>.dvtcolortheme</tt> files into the following address on your computer:</p></div>
<div class="paragraph"><p><tt><strong>~/Library/Developer/Xcode/UserData/FontAndColorThemes</strong></tt></p></div>
</div>
<div class="sect2">
<h3 id="_automatic_console">6.2. Automatic Console</h3>
<div class="paragraph"><p>Its a good idea to have the console open up every time an application is run, because you always want to see the messages that might appear while your app is running. This saves you from having to open the console yourself each time you hit the run button&#8230;</p></div>
<div class="paragraph"><p>To set this option, navigate to the <strong>Behaviors</strong> tab in the preferences pane and then select the <strong>Build Starts</strong> option on the left-hand side of the window. Then, on the right-hand side, check the <strong>Show debugger with Console View</strong> option.</p></div>
<div class="imageblock">
<div class="content">
<img src="xcode/consoleSettings.png" alt="Setting the Console Options" height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_user_guide">7. User Guide</h2>
<div class="sectionbody">
<div class="paragraph"><p>The Xcode 4 User Guide is BORING! But, there&#8217;s all kinds of useful information in it&#8230;</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://developer.apple.com/library/ios/documentation/ToolsLanguages/Conceptual/Xcode4UserGuide/000-About_Xcode/about.html//apple_ref/doc/uid/TP40010215">Xcode User Guide</a>
</p>
</li>
</ul></div>
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
