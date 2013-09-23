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

<h2>Stack Overflow</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial, we&#8217;ll orient you to the Stack Overflow (S.O.) website and how to use it for asking programming questions about C4.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/stackoverflow.png" alt="Stack Overflow" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_s_o_site">1. The S.O. Site</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can get to the S.O. site by clicking the following link:</p></div>
<div class="paragraph"><p><a href="http://stackoverflow.com">S.O. Main Page</a></p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_mainpage.png" alt="The S.O. Main Page" height="400" />
</div>
</div>
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
<div class="paragraph"><p>You can get to the About page by clicking the following link:</p></div>
<div class="paragraph"><p><a href="http://stackoverflow.com/about">S.O. About Page</a></p></div>
</div>
<div class="sect2">
<h3 id="_why_s_o">1.2. Why S.O.?</h3>
<div class="paragraph"><p>We&#8217;ve chosen to use Stack Overflow to moderate questions about C4 because it&#8217;s a new and popular community for programming questions. The entire site is open-source and its content is generated by its users, all that content is moderated by those users as well. It&#8217;s a really great community-driven site.</p></div>
<div class="paragraph"><p>SO, we&#8217;ve decided to go with S.O. because we believe that this is a better option than maintaining our own wiki / forum / messaging board. One reason is that <em>anyone</em> can answer questions about your project&#8230; If the question you ask is about iOS dev, or general programming topics, someone who isn&#8217;t part of the C4 community can help you out. This allows a LOT of people to contribute to helping with C4 questions, and gets us out into the larger development community.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_signing_up">2. Signing Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>Signing up for S.O. is pretty easy, and there are a lot of options to choose from. First, go to this link:</p></div>
<div class="paragraph"><p><a href="http://stackoverflow.com/users/login">S.O. Login / Sign-up</a></p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_login.png" alt="The S.O. Login Page" height="400" />
</div>
</div>
<div class="paragraph"><p>You can log in with Facebook, Google, or Yahoo&#8230; But, we think the best option is to create a new Stack Exchange account. So, if you haven&#8217;t got an account yet, then choose the <strong>click here to sign up</strong> option.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_signup.png" alt="The S.O. Signup Page" height="400" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_your_account">3. Your Account</h2>
<div class="sectionbody">
<div class="paragraph"><p>You can access your account information by clicking on your account name at the top of any page. When you scroll over your account name at the top you can see a small <em>activity</em> panel pop up to give you an overview of what you&#8217;ve been up to recently.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_activity.png" alt="Your Account Activity Rollover" height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_account_page">3.1. Account Page</h3>
<div class="paragraph"><p>Your account page gives you an overview of all the details of your account, from your personal details to everything that you&#8217;ve been up to on the S.O. site. The following image is from my account&#8230; <em>you can open the image in a new window to see it in larger detail</em>.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_account.png" alt="My Account" height="600" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_activity_details">3.2. Activity Details</h3>
<div class="paragraph"><p>At the bottom of your account page are several areas of "metrics", basically sections that show your entire contribution to S.O. broken down into:</p></div>
<div class="ulist"><ul>
<li>
<p>
Answers
</p>
</li>
<li>
<p>
Questions
</p>
</li>
<li>
<p>
Reputation
</p>
</li>
<li>
<p>
Tags
</p>
</li>
<li>
<p>
Badges
</p>
</li>
<li>
<p>
Accounts
</p>
</li>
<li>
<p>
Bounties
</p>
</li>
<li>
<p>
Votes
</p>
</li>
</ul></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_s_o_faq">4. S.O. FAQ</h2>
<div class="sectionbody">
<div class="paragraph"><p>The absolute best thing to do now is go to the S.O. FAQ and get a sense of what it&#8217;s all about:</p></div>
<div class="paragraph"><p><a href="http://stackoverflow.com/faq">S.O FAQ</a></p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_faq.png" alt="S.O. FAQ" height="400" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/important.png" alt="Important" />
</td>
<td class="content">DO THIS NOW! And, DO THIS BEFORE ASKING A QUESTION!</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_c4_tag_on_s_o">5. C4 Tag on S.O.</h2>
<div class="sectionbody">
<div class="paragraph"><p>You may have noticed that there are <em>tons</em> of questions about programming on stack overflow. For instance, at the time this tutorial was made there were <strong>3,615,310</strong> different questions asked on S.O.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_questions.png" alt="S.O. Questions" height="400" />
</div>
</div>
<div class="paragraph"><p>Fortunately, the site lets you filter through all its questions in several different ways, the easiest of which is by <strong>tags</strong>.</p></div>
<div class="paragraph"><p>There is a C4 tag that you can follow, which will give you direct access to all the questions ever asked about C4.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_c4questions.png" alt="C4 Questions on S.O." height="400" />
</div>
</div>
<div class="sect2">
<h3 id="_about_the_c4_tag">5.1. About the C4 Tag</h3>
<div class="paragraph"><p>Every tag has its own About page that you can get to by clicking on the tag itself. The C4 About page looks like this:</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_c4tag.png" alt="C4 Tag on S.O." height="400" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_before_you_ask_a_question">6. Before You Ask a Question</h2>
<div class="sectionbody">
<div class="paragraph"><p>Asking a question for the first time on S.O. can be a bit confusing, mainly because there is a particular style that you need to follow. This "style" is really a community-based effort to keep questions on topic and to keep the forums relevant and useful, without letting them fall into nonsense like you might find on other loosely moderated forums.</p></div>
<div class="sect2">
<h3 id="_read_the_faq">6.1. Read the FAQ</h3>
<div class="paragraph"><p>First, you should go through and skim the FAQ&#8230; There will be lots of information there to help you figure out how to write. And, you&#8217;ll get the <strong>Analytical</strong> badge for visiting all sections in the FAQ.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/analytical.png" alt="The Analytical Badge" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_how_to_ask">6.2. How to Ask</h3>
<div class="paragraph"><p>There is an in-depth page on how to ask questions on Stack Overflow.</p></div>
<div class="paragraph"><p><a href="http://stackoverflow.com/questions/how-to-ask">How To Ask a Question on S.O.</a></p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_howtoask.png" alt="The 'How To Ask a Question' Question" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_do_your_homework">6.3. Do Your Homework</h3>
<div class="paragraph"><p>The first thing to do <strong>before</strong> asking a question is to search for the answer to your question.</p></div>
<div class="paragraph"><p>So, just to a couple of searches for terms and full questions on the site to see if someone else has asked or answered your question already.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">More often than not your question has already been asked.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_ask_a_question">7. Ask a Question</h2>
<div class="sectionbody">
<div class="paragraph"><p>Ok, so you&#8217;ve done your homework and you&#8217;re ready to ask a question. Click on the <a href="http://stackoverflow.com/questions/ask">Ask Question</a> button at the top of any S.O. page.</p></div>
<div class="sect2">
<h3 id="_write_your_question">7.1. Write Your Question</h3>
<div class="paragraph"><p>Choose an appropriate and clear title, then write your question out.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/so_askquestion.png" alt="Ask Your Question" height="400" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_choose_some_tags">7.2. Choose Some Tags</h3>
<div class="paragraph"><p>You <strong>HAVE</strong> to choose at least 1 tag for your question, the first of which should be the <strong>C4</strong> tag. If you don&#8217;t tag your question with C4 then the C4 community won&#8217;t get notified and we won&#8217;t be able to answer.</p></div>
<div class="paragraph"><p>When you type C4 into the Tags text field, you&#8217;ll get a bunch of options. Click on the C4 option and it will add this tag to your question.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/tags.png" alt="Choose the C4 Tag" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_post_your_question">7.3. Post Your Question</h3>
<div class="paragraph"><p>When you&#8217;re done, hit the <strong>Post Your Question</strong> button at the bottom of the page.</p></div>
<div class="imageblock">
<div class="content">
<img src="stackoverflow/postquestion.png" alt="Post Your Question" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_don_8217_t_worry">8. DON&#8217;T WORRY!</h2>
<div class="sectionbody">
<div class="paragraph"><p>Don&#8217;t worry about the quality of your first post. You&#8217;ll pick it up quite quickly. More importantly, don&#8217;t worry about people being cranky about the way you ask questions on S.O., they&#8217;re really just trying to keep the forums in line.</p></div>
<div class="paragraph"><p>If needed, people will help you by suggesting clarifications which you can edit into your original post. People may even edit your post for you, without you knowing about it! There is a badge for helping keep questions clean and straightforward.</p></div>
<div class="paragraph"><p>So, <strong>POST</strong> and we&#8217;ll answer your question for you!</p></div>
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
