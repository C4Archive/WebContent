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

<h2>Labels: Paragraphs</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3239817" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Creating a label with paragraphs is easy.</p></div>
<div class="imageblock">
<div class="content">
<img src="labelParagraphs/labelParagraphs.png" alt="Label Paragraphs" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_a_lonnnng_text">1. A Lonnnng Text</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first thing to do is create a long text. In this text, you should add <em>newline</em> characters so that the label will be able to create paragraphs.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">NSString</span> <span class="o">*</span><span class="n">text</span> <span class="o">=</span> <span class="s">@&quot;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed et sapien quam, fermentum pharetra quam. In fermentum massa eget nisl mollis vitae elementum est accumsan. Aenean sit amet libero elit. Nunc vel sodales risus. In et commodo eros. Nulla rhoncus pretium varius. Aliquam pellentesque porttitor erat, porta mattis arcu elementum ac. Ut turpis quam, interdum eu adipiscing nec, cursus vel urna. Mauris ac scelerisque nisl. Donec eu nunc eu sem bibendum laoreet. Nulla id nunc consectetur urna mollis porta. Donec auctor ultricies metus.</span><span class="se">\n\n</span><span class="s">Nullam neque massa, placerat ut tristique at, suscipit id augue. Donec id tortor quis lorem rhoncus lacinia. Quisque at velit magna, ac gravida dolor. Nullam ut risus quis felis feugiat accumsan. Sed sed metus vitae elit mollis mattis. Sed dapibus pellentesque sodales. Nam ac odio nulla. Sed sollicitudin orci vitae odio placerat ac tempor nisl tempus. Suspendisse dictum porta risus id dapibus. Praesent quam diam, iaculis sit amet condimentum ut, tincidunt vitae lectus. Cras dolor enim, luctus non laoreet non, bibendum vel lacus. In non massa lorem, at tempus nisi. Donec tortor nisl, ultrices quis varius at, suscipit nec eros. Cras consectetur egestas risus eget cursus.</span><span class="se">\n\n</span><span class="s">Mauris elementum bibendum mi at elementum. Donec id tempus magna. Curabitur quis lacus neque. Donec ac lectus vel tellus hendrerit ornare. Mauris tempus varius imperdiet. Pellentesque dictum metus sed orci porta nec facilisis risus varius. Cras non odio eget orci volutpat porttitor egestas a urna. Donec massa dui, aliquam quis venenatis nec, vehicula eu erat. Suspendisse lorem sapien, vehicula nec pulvinar a, pharetra a turpis. Mauris faucibus turpis urna.</span><span class="se">\n\n</span><span class="s">Ut suscipit pharetra ullamcorper. Aliquam rhoncus mollis tellus sit amet porttitor. Maecenas gravida magna eget nunc mattis gravida. Nullam purus sapien, viverra vel hendrerit nec, varius in justo. Proin in metus sem. Nullam nec adipiscing orci. Fusce at turpis orci, at mattis massa. Curabitur quis odio interdum enim hendrerit convallis a dapibus turpis. Sed sed ligula non ante iaculis consectetur.</span><span class="se">\n\n</span><span class="s">Proin at viverra tellus. Nam enim eros, tincidunt sit amet lacinia vitae, accumsan in tellus. Cras semper suscipit ligula vitae aliquet. Proin eget ligula risus, vel lobortis velit. Integer rutrum purus ligula. Nulla bibendum porttitor ornare. Aenean vitae neque vitae ligula gravida rutrum et eget mi. Mauris purus ligula, lacinia quis ultrices tincidunt, venenatis eget massa. Suspendisse sapien eros, pharetra non rhoncus ac, dapibus vitae nisi. Integer nec sem nulla, eget vulputate nibh.&quot;</span><span class="p">;</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">There are a few \n\n characters hiding in the text.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_create_a_label">2. Create a Label</h2>
<div class="sectionbody">
<div class="paragraph"><p>Next, create a label.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:text</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_resize_the_label">3. Resize the Label</h2>
<div class="sectionbody">
<div class="paragraph"><p>When the label has been created, change its frame so that it&#8217;s tall and skinny.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">label</span><span class="py">.frame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">300</span><span class="p">,</span><span class="mi">600</span><span class="p">);</span>
</pre></div></div></div>
</div>
</div>
<div class="sect1">
<h2 id="_adjust_the_number_of_lines">4. Adjust the Number Of Lines</h2>
<div class="sectionbody">
<div class="paragraph"><p>Make sure to change the label&#8217;s <em>numberOfLines</em> property so that it can handle the long text.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">label</span><span class="py">.numberOfLines</span> <span class="o">=</span> <span class="mi">100</span><span class="p">;</span>
</pre></div></div></div>
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
