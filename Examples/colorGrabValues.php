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

<h2>Color: Grab Values</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3218937" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Grabbing the values from a color is a little tricky, but actually a very convenient way of accessing and storing the each component so that you can use them in another part of your application.</p></div>
<div class="imageblock">
<div class="content">
<img src="colorGrabValues/colorGrabValues.png" alt="Grabbing Color Values" height="500" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_getting_colors">1. Getting Colors</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 3 convenient methods for getting values from a color. You can get the RGB, HSB and White color values.</p></div>
<div class="paragraph"><p>The basic trick to getting color values is to set up an array to hold them. This is quite a nice technique, because you then have all your values in one array that you can easily reuse.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">rgbValues</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="nc">CGFloat</span> <span class="n">hsbValues</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="nc">CGFloat</span> <span class="n">greyValues</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">4,4,2???</div>When grabbing RGB or HSB colors, you have to grab 4 values&#8230; The three primary color values <em>plus</em> one for the color&#8217;s <em>alpha</em> component. For greyscale colors you need to grab 2 values, the grey color <em>plus</em> the alpha.</td>
</tr></table>
</div>
<div class="sect2">
<h3 id="_grabbing_rgba">1.1. Grabbing RGBA</h3>
<div class="paragraph"><p>To grab the RGBA values from a color you do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">rgbValues</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="p">[</span><span class="n">color</span> <span class="n">getRed:</span><span class="o">&amp;</span><span class="n">rgbValues</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="n">green</span><span class="o">::&amp;</span><span class="n">rgbValues</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="n">blue:</span><span class="o">&amp;</span><span class="n">rgbValues</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="n">alpha:</span><span class="o">&amp;</span><span class="n">rgbValues</span><span class="p">[</span><span class="mi">3</span><span class="p">]];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_grabbing_hsba">1.2. Grabbing HSBA</h3>
<div class="paragraph"><p>To grab the HSBA values from a color you do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">hsbValues</span><span class="p">[</span><span class="mi">4</span><span class="p">];</span>
<span class="p">[</span><span class="n">color</span> <span class="n">getHue:</span><span class="o">&amp;</span><span class="n">hsbValues</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="n">saturation</span><span class="o">::&amp;</span><span class="n">hsbValues</span><span class="p">[</span><span class="mi">1</span><span class="p">]</span> <span class="n">brightness:</span><span class="o">&amp;</span><span class="n">hsbValues</span><span class="p">[</span><span class="mi">2</span><span class="p">]</span> <span class="n">alpha:</span><span class="o">&amp;</span><span class="n">hsbValues</span><span class="p">[</span><span class="mi">3</span><span class="p">]];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_grabbing_greyscale">1.3. Grabbing Greyscale</h3>
<div class="paragraph"><p>To grab the greyscale values from a color you do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">greyValues</span><span class="p">[</span><span class="mi">2</span><span class="p">];</span>
<span class="p">[</span><span class="n">color</span> <span class="n">getWhite:</span><span class="o">&amp;</span><span class="n">greyValues</span><span class="p">[</span><span class="mi">0</span><span class="p">]</span> <span class="n">alpha:</span><span class="o">&amp;</span><span class="n">greyValues</span><span class="p">[</span><span class="mi">1</span><span class="p">]];</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">What the &amp;?</div>In all of the examples above there is an &amp; symbol before the name of the array we&#8217;re using to grab colors. This <strong>&amp;</strong> is called a <em>unary operator</em> and actually passes the address of the variable to the function. So, when the function gets something like <strong>&amp;rgbValue[0]</strong> it actually has a direct reference to the first spot in the array. What it does is set the value of the array at the given address, rather than pass the variable back and forth through a method that might return something like a float value.</td>
</tr></table>
</div>
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
