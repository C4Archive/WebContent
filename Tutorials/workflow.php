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

<h2>The C4 Community</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="workflow/workflow.png" alt="The C4 Community" />
</div>
</div>
<div class="paragraph"><p>This is how we work with C4. First, we write our apps using Xcode. Second, we host our projects on GitHub. Third, we ask questions and find answers to problems on StackOverflow.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_write_code">1. Write Code</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="workflow/xcode.png" alt="Xcode" />
</div>
</div>
<div class="paragraph"><p>You should write your apps in Xcode&#8230; Actually, we don&#8217;t think there&#8217;s any other reliable way of coding and directly building for iOS devices.</p></div>
<div class="paragraph"><p>Xcode makes coding easier in a lot of ways, but we won&#8217;t get into those for now. For more, see our <a href="xcode.php">Xcode Tutorial</a>.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_git_share_code">2. Git / Share Code</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="workflow/github.png" alt="GitHub" />
</div>
</div>
<div class="paragraph"><p>Git is a great technology for versioning / making backups of your current projects, you can use Git by itself but we suggest connecting it with a GitHub account.</p></div>
<div class="sect2">
<h3 id="_share">2.1. Share</h3>
<div class="paragraph"><p>If you host your projects on GitHub and you have a problem with them, you can always point us to the current state of your project and we can have a look at what&#8217;s going on. Well, we or anyone else who is helping you out with your project.</p></div>
</div>
<div class="sect2">
<h3 id="_get">2.2. Get</h3>
<div class="paragraph"><p>We host all our examples, tutorials, code, api and so on at GitHub. So, if you ever need a copy of some code you can find it online and download it to your computer. This can be anything from snippets of code to full Xcode projects.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_ask_find_answer">3. Ask / Find / Answer</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="workflow/stackoverflow.png" alt="Stack Overflow" />
</div>
</div>
<div class="paragraph"><p>Instead of a forum that we maintain, we&#8217;re trying to get C4 into the greater community. So, if you ever have a problem you&#8217;re facing in your project you can post it on <a href="stackoverflow.com">Stack Overflow</a>.</p></div>
<div class="sect2">
<h3 id="_ask">3.1. Ask</h3>
<div class="paragraph"><p>When you ask a question, make sure to add the C4 tag so that we get notified. Someone will answer your question as soon as possible.</p></div>
</div>
<div class="sect2">
<h3 id="_find">3.2. Find</h3>
<div class="paragraph"><p>If you&#8217;re having trouble with something, you can often find the answer to your problem on S.O. before posting a question of your own. The community is active all the time, and it&#8217;s quite common for someone who isn&#8217;t using C4 has run into the same issue that you&#8217;re having in your project&#8230; Not all problems you&#8217;ll face will be C4-specific.</p></div>
</div>
<div class="sect2">
<h3 id="_answer">3.3. Answer</h3>
<div class="paragraph"><p>When you&#8217;re comfortable enough with S.O. you can actually answer some questions yourself. This will build your reputation and help strengthen the community.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_summary">4. Summary</h2>
<div class="sectionbody">
<div class="paragraph"><p>We came to this combination of 3 parts for our workflow with C4 out of necessity.</p></div>
<div class="paragraph"><p>First, sharing project code is difficult in a web / online space (like <a href="http://www.processing.org">Processing</a> does so well) because apps don&#8217;t run in a browser. So, we needed a way to reliably share projects, snippets and such, without too much hassle. As well, the ability to version and save projects while keeping them public was a great opportunity and sold us on GitHub for hosting projects&#8230; It&#8217;s also free, so we can upload as many projects and examples as we want.</p></div>
<div class="paragraph"><p>Second, we opted against maintaing our own Forum / Wiki for a few reasons, none better than the fact that at <a href="http://www.stackoverflow.com">Stack Overflow</a> there are thousands of contributers many of whom will be able to answer the questions posed by people who use C4. Simply put, the community there is <em>very</em> strong. So, we thought it in our best interest to leverage that community and begin by <em>building in</em> the C4 community. We hope that people using C4 might become a newer part of S.O. by bringing their creative, aesthetic and expressive approaches.</p></div>
<div class="paragraph"><p>Finally, to build iOS apps you have to use Xcode, an IDE that can be quite confusing at first. But, the benefits of using Xcode far outweigh its often cluttered interface. Some of these benefits are:</p></div>
<div class="ulist"><ul>
<li>
<p>
Code completion
</p>
</li>
<li>
<p>
Integrated Documentation (even the C4 Docs are available with a right-click of your mouse)
</p>
</li>
<li>
<p>
Instruments (to help with debugging)
</p>
</li>
<li>
<p>
&#8230;
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
