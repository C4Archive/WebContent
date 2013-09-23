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

<h2>GitHub</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial, we&#8217;ll orient you to the GitHub website and how to use it for finding and sharing code. But, first and foremost, <em>GitHub is the best way for you to share code with the C4 Community</em>.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/github.png" alt="GitHub" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_github_site">1. The GitHub Site</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can get to the GitHub site by clicking the following link:</p></div>
<div class="paragraph"><p><a href="https://github.com">GitHub Main Page</a></p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_mainpage.png" alt="GitHub Main Page" height="400" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/warning.png" alt="Warning" />
</td>
<td class="content">If you haven&#8217;t ever set up GIT on your computer, or if you don&#8217;t know, then you should stop right here and follow along the <a href="https://help.github.com/articles/set-up-git">Setting Up Git</a> tutorial from GitHub.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_about_github">1.1. About GitHub</h3>
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
<h3 id="_why_github">1.2. Why Github?</h3>
<div class="paragraph"><p>We&#8217;ve chosen to use GitHub to host our projects, code and snippets for a lot of reasons. Foremost, we use it in our daily workflow for adding features and refining C4, because it allows us to easily version and save the project. Furthermore, because the project is open-source GitHub also allows <em>you</em> to download the most up to date version of C4 at any time.</p></div>
<div class="paragraph"><p>GitHub also has some really interesting components to it, like the ability to store whole projects, or to store <em>snippets</em> of code as <strong>Gists</strong>. When a project is public and placed on GitHub <em>anyone</em> can download it, which means that if you <a href="stackoverflow.php">Ask A Question</a> and link to your current project on GitHub we can download and toy around with it for you before answering.</p></div>
<div class="paragraph"><p>There are more reasons, but the main GitHub page says it all&#8230; At the time of writing this tutorial, there are <em>over 2 million people</em> currently sharing <em>over 3.6 million projects</em> online.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_sign_up">2. Sign Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>First things first, sign up for GitHub by clicking on the <em>Signup and Pricing</em> button at the top of the main page.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_plans.png" alt="GitHub Plans" height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_the_free_plan">2.1. The Free Plan</h3>
<div class="paragraph"><p>GitHub has a nice concept&#8230; Private accounts cost money, whereas free accounts require code to be open-source.</p></div>
<div class="paragraph"><p>Sign yourself up for a <strong>free</strong> account.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_freeaccount.png" alt="GitHub Plans" height="400" />
</div>
</div>
<div class="paragraph"><p>&#8230;Then log in.</p></div>
</div>
<div class="sect2">
<h3 id="_your_account">2.2. Your Account</h3>
<div class="paragraph"><p>When you&#8217;ve logged in you&#8217;ll be taken to your main account page, if you don&#8217;t see this page then simply click on your account name at the top of the page you&#8217;re currently on.</p></div>
</div>
<div class="sect2">
<h3 id="_home_page">2.3. Home Page</h3>
<div class="paragraph"><p>Your GitHub home page has a few things for you to check out, including a space for notifications, a short list of your repositories, and an long list of notifications. You can also navigate a to several places including actions, pull requests, issues and so on&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_home.png" alt="Your GitHub Home Page" height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_bootcamp">3. BootCamp</h2>
<div class="sectionbody">
<div class="paragraph"><p>From your home page you can see a big area called Bootcamp. This is a great place to learn how to set up git on your computer, create a repository, fork a repo, and how to use their social network to watch projects and follow people.</p></div>
<div class="paragraph"><p>So, if you&#8217;re new to Git we suggest you walk through the bootcamp to get situated.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">GIT can get confusing, and so can GitHub, so from time to time I revisit these walkthroughs&#8230; They&#8217;re great for covering the basics again and again.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_c4_on_github">4. C4 On GitHub</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 4 different accounts for C4 on GitHub, but you don&#8217;t need to remember all of them. In fact, most of the time you&#8217;ll just click a button on our website which will take you straight to a repository without you needing to know with which account it is associated.</p></div>
<div class="paragraph"><p>But, just to let you know, the 4 accounts are the following:</p></div>
<div class="ulist"><ul>
<li>
<p>
C4Framework
</p>
</li>
<li>
<p>
C4Examples
</p>
</li>
<li>
<p>
C4Tutorials
</p>
</li>
<li>
<p>
C4Code
</p>
</li>
</ul></div>
<div class="sect2">
<h3 id="_c4framework">4.1. C4Framework</h3>
<div class="paragraph"><p>This account is where the core API is hosted, so if you want access to the latest builds you can find them here. A lot of the code that on this repo will be experimental but will eventually be incorporated into the files included in the installer.</p></div>
<div class="paragraph"><p>This account also has a repo for the structure of the installer.</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://github.com/C4Framework">C4Framework</a>
</p>
</li>
</ul></div>
</div>
<div class="sect2">
<h3 id="_c4examples">4.2. C4Examples</h3>
<div class="paragraph"><p>This account hosts all of the code, both projects and gists, that you can access from any one of the <strong>example</strong> pages on our website.</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://github.com/C4Examples">C4Examples</a>
</p>
</li>
</ul></div>
</div>
<div class="sect2">
<h3 id="_c4tutorials">4.3. C4Tutorials</h3>
<div class="paragraph"><p>This account hosts all of the code, both projects and gists, that you can access from any one of the <strong>tutorial</strong> pages on our website.</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://github.com/C4Tutorials">C4Tutorials</a>
</p>
</li>
</ul></div>
</div>
<div class="sect2">
<h3 id="_c4code">4.4. C4Code</h3>
<div class="paragraph"><p>This account hosts any code that isn&#8217;t associated with the framework, or the examples / tutorials. It is a place where we can upload code, test, answers to people&#8217;s problems and so on. The code hosted here will work when it is uploaded, but <em>will not be updated</em> for new releases and so on&#8230;</p></div>
<div class="ulist"><ul>
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
<h2 id="_gists">5. Gists</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 2 ways to host code on GitHub. The typical way is to have projects up online, where the entire structure of the project is held. For instance, the C4Framework repo for the core API (<a href="https://github.com/C4Framework/C4iOS">C4iOS</a>) contains all the files necessary for a complete Xcode project.</p></div>
<div class="paragraph"><p>Sometimes this is <em>overkill</em>.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gist.png" alt="Gist" height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_gist_github_com">5.1. gist.github.com</h3>
<div class="paragraph"><p>When we want to share short <em>snippets</em> of code that can be run in a typical C4 project, we will upload our examples as <strong>Gists</strong>.</p></div>
<div class="paragraph"><p>Gists exist at <a href="https://gist.github.com">gist.github.com</a>, and for each different account you add the appropriate extension.</p></div>
</div>
<div class="sect2">
<h3 id="_c4examples_on_gist">5.2. C4Examples on Gist</h3>
<div class="paragraph"><p>Most examples on our website will be hosted as gists and can be found at:</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://gist.github.com/C4Examples">C4Examples on Gist</a>
</p>
</li>
</ul></div>
<div class="paragraph"><p>Currently there are approximately <strong>120</strong> examples hosted on gist (not all of which have their own page on our website, yet).</p></div>
<div class="imageblock">
<div class="content">
<img src="github/c4examples.png" alt="List of Examples on Gist" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_what_a_gist_looks_like">5.3. What a Gist Looks Like</h3>
<div class="paragraph"><p>When you go to a gist, you basically see a window with a title, options for editing, downloading or starring the file, and at least one frame that contains code.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/singlegist.png" alt="Single Gist" height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_grab_github_code">6. Grab GitHub Code</h2>
<div class="sectionbody">
<div class="paragraph"><p>Okay, so this is a long tutorial, but we&#8217;ve finally made it to the meat. The point is to show you how to get code from GitHub. There are several ways, but we&#8217;ll show you the easiest here.</p></div>
<div class="paragraph"><p>Every Git repo has the following bar at the top of its main page:</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_getcode.png" alt="Grab GitHub Code" />
</div>
</div>
<div class="sect2">
<h3 id="_the_zip">6.1. The Zip</h3>
<div class="paragraph"><p>Every Git repo has an option to download the entire project as a .zip file. Simply click on the ZIP button and your browser will download a copy of the project as a .zip file.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_zip.png" alt="Zip Code" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_the_read_only">6.2. The Read-only</h3>
<div class="paragraph"><p>Every Git repo has an option to download the a read-only copy of the project. You can use this option (via your Terminal app) to grab code if you&#8217;re having firewall trouble.</p></div>
<div class="quoteblock">
<div class="content">
<div class="paragraph"><p>Many firewalls will block the git:// and ssh URLs from working.</p></div>
</div>
<div class="attribution">
</div></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_readonly.png" alt="Read Only" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">you can read the following for more information, but it&#8217;s a bit technical, <a href="https://help.github.com/articles/which-remote-url-should-i-use">Remote URLs</a></td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_the_clone">6.3. The Clone</h3>
<div class="paragraph"><p>The last option we&#8217;ll describe here is the clone button, which actually puts a copied version of a project on your computer without forking. This would allow you to contribute to the project, by pushing, or to update your own copy using a <em>rebase</em> command from time to time.</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gh_clone.png" alt="Clone" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_grab_gist_code">7. Grab Gist Code</h2>
<div class="sectionbody">
<div class="paragraph"><p>Grabbing code from Gist is pretty easy as well, there are 3 main options that we&#8217;ll cover.</p></div>
<div class="paragraph"><p>Every Gist repo has the following bar at the top of its page:</p></div>
<div class="imageblock">
<div class="content">
<img src="github/gist_getcode.png" alt="Grab Gist Code" />
</div>
</div>
<div class="sect2">
<h3 id="_the_easiest">7.1. The Easiest</h3>
<div class="paragraph"><p>Most Gists that we&#8217;ll be putting up online will only be a few lines of code.</p></div>
<div class="paragraph"><p><em>COPY AND PASTE</em> the contents of the gist directly into your <tt>C4WorkSpace.m</tt> file.</p></div>
</div>
<div class="sect2">
<h3 id="_the_zip_2">7.2. The Zip</h3>
<div class="paragraph"><p>For Gists that have more than one file, you can copy and paste if they&#8217;re small enough, or you can click the <strong>download</strong> button and drag the files from the zip folder into your project.</p></div>
</div>
<div class="sect2">
<h3 id="_the_clone_2">7.3. The Clone</h3>
<div class="paragraph"><p>If you want, you can also clone the repo like a GitHub project and have a local copy on your computer. You do this by copying and pasting the clone address into your Terminal app.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_finally">8. Finally</h2>
<div class="sectionbody">
<div class="paragraph"><p>This is not an exhaustive description of GitHub. If you want to learn more, you can go to any of the following links:</p></div>
<div class="ulist"><ul>
<li>
<p>
<a href="https://help.github.com">GitHub Help</a>
</p>
</li>
<li>
<p>
<a href="https://help.github.com/articles/set-up-git">Set Up Git</a>
</p>
</li>
<li>
<p>
<a href="https://help.github.com/articles/create-a-repo">Create A Repo</a>
</p>
</li>
<li>
<p>
<a href="https://help.github.com/articles/fork-a-repo">Fork A Repo</a>
</p>
</li>
<li>
<p>
<a href="https://help.github.com/articles/be-social">Be Social</a>
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
