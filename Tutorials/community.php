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
<img src="community/community.png" alt="The C4 Community" />
</div>
</div>
<div class="paragraph"><p>The C4 community exists in a few different places, and for good reason. We could host our own code, our own forums, and have email bulletins, but instead we tried to go a bit of a different route.</p></div>
<div class="paragraph"><p>We&#8217;ve chosen 4 places to keep you up to date with new releases, code samples / examples / tutorials / &#8230;, news and pictures.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_stack_overflow">1. Stack Overflow</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="community/stackoverflow.png" alt="Stack Overflow" />
</div>
</div>
<div class="paragraph"><p>Instead of a wiki or forum that we&#8217;ll be updating and maintaining, we&#8217;ve decided to use <a href="http://stackoverflow.com">Stack Overflow</a> as the place to ask questions and have discussions about programming.</p></div>
<div class="sect2">
<h3 id="_about_s_o">1.1. About S.O.</h3>
<div class="paragraph"><p>Stack Overflow is a free Q &amp; A site that you can use to ask questions about C4. The site describes itself as:</p></div>
<div class="quoteblock">
<div class="content">
<div class="paragraph"><p>Stack Overflow is a programming Q &amp; A site that’s free. Free to ask questions, free to answer questions, free to read, free to index, built with plain old HTML, no fake rot13 text on the home page, no scammy google-cloaking tactics, no salespeople, no JavaScript windows dropping down in front of the answer asking for $12.95 to go away&#8230;</p></div>
</div>
<div class="attribution">
&#8212; S.O. Team
</div></div>
<div class="paragraph"><p>For more, check out their <a href="http://stackoverflow.com/about">About</a> page</p></div>
</div>
<div class="sect2">
<h3 id="_the_c4_tag">1.2. The C4 Tag</h3>
<div class="paragraph"><p>Every question asked on S.O. has at least one, but usually several tags associated with it. The C4 Framework has its own tag, which you can check out here: <a href="http://stackoverflow.com/questions/tagged/c4">C4 on S.O.</a></p></div>
<div class="paragraph"><p>You can read all the other posts about C4, upvote them, contribute to answers and so on&#8230;</p></div>
<div class="paragraph"><p>When you ask a question about C4 you can tag it and the rest of the C4 community will get a little notification. We&#8217;re pretty eager to answer questions that pop up, so you should get a response fairly quickly.</p></div>
</div>
<div class="sect2">
<h3 id="_s_o_tutorial">1.3. S.O. Tutorial</h3>
<div class="paragraph"><p>We&#8217;ve written a tutorial for getting started with Stack Overflow. There are a few steps to getting up and running with the site, though nothing to daunting.</p></div>
<div class="paragraph"><p>We also describe a few things like setting up a logo (using Gravatar if you want), and the etiquette for asking questions and posting answers.</p></div>
<div class="paragraph"><p><a href="stackoverflow.php">Stack Overflow Tutorial</a></p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_github">2. GitHub</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="community/github.png" alt="GitHub" />
</div>
</div>
<div class="paragraph"><p>All of the code you&#8217;ll run into on our site is hosted on <a href="https://github.com">GitHub</a> where we have a few different accounts. For example, the core API is hosted on our <a href="https://github.com/C4Framework">C4Framework</a> account, whereas all the examples we provide are hosted on our <a href="https://github.com/C4Examples">C4Examples</a> account.</p></div>
<div class="sect2">
<h3 id="_about_github">2.1. About GitHub</h3>
<div class="paragraph"><p>GitHub is a free, open-source site for hosting and sharing code, and working remotely / together with people on projects.</p></div>
<div class="quoteblock">
<div class="content">
<div class="paragraph"><p>GitHub is the best place to share code with friends, co-workers, classmates, and complete strangers. Over a million people use GitHub to build amazing things together.</p></div>
</div>
<div class="attribution">
&#8212; GitHub Team
</div></div>
<div class="paragraph"><p>For more, check out their <a href="https://github.com/about">About</a> page</p></div>
</div>
<div class="sect2">
<h3 id="_github_tutorial">2.2. GitHub Tutorial</h3>
<div class="paragraph"><p>GitHub can be a bit confusing at times, but there are only a few basics that you&#8217;ll need to know. So, we&#8217;ve written a tutorial for getting you up to speed on grabbing projects, finding examples and contributing your comments / issues.</p></div>
<div class="paragraph"><p><a href="github.php">GitHub Tutorial</a></p></div>
</div>
<div class="sect2">
<h3 id="_c4_accounts">2.3. C4 Accounts</h3>
<div class="paragraph"><p>The following is a list of accounts we host on GitHub:</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://github.com/C4Framework">C4Framework</a>
</p>
</li>
<li>
<p>
<a href="https://github.com/C4Examples">C4Examples</a>
</p>
</li>
<li>
<p>
<a href="https://gist.github.com/C4Examples">C4Examples on Gist</a>
</p>
</li>
<li>
<p>
<a href="https://github.com/C4Tutorials">C4Tutorials</a>
</p>
</li>
<li>
<p>
<a href="https://github.com/C4Code">C4Code</a>
</p>
</li>
</ul></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_twitter">3. Twitter</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="community/twitter.png" alt="Twitter" />
</div>
</div>
<div class="paragraph"><p>We use twitter for posting news and notifications about C4. You can check out our feed at:</p></div>
<div class="paragraph"><p><a href="http://twitter.com/cocoafor">@CocoaFor</a></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_instagram">4. Instagram</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="community/instagram.png" alt="Instagram" />
</div>
</div>
<div class="paragraph"><p>We use Instagram to host any pics we&#8217;re taking of new projects, workshops, or anything else that&#8217;s going on with C4. You can check out our feed using the following profile:</p></div>
<div class="paragraph"><p><strong>cocoafor</strong></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_vimeo">5. Vimeo</h2>
<div class="sectionbody">
<div class="imageblock">
<div class="content">
<img src="community/vimeo.png" alt="Vimeo" />
</div>
</div>
<div class="paragraph"><p>All of the videos you seen on this site are hosted by Vimeo. You can check out the C4 feed at:</p></div>
<div class="paragraph"><p><a href="https://vimeo.com/user7392530">C4 on Vimeo</a></p></div>
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
