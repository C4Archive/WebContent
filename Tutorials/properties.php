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

<h2>Properties</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>All objects have properties, and using them is probably <em>the most important</em> thing you need to learn when it comes to working with C4. This tutorial will give an overview of what properties are, how to set them and how to use them to make animations happen in your applications.</p></div>
<div class="paragraph"><p>Properties are important. <em>All objects have properties you can set.</em></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_what_art_thou_property">1. What Art Thou, Property?</h2>
<div class="sectionbody">
<div class="paragraph"><p>Apple documentation describes properties as:</p></div>
<div class="quoteblock">
<div class="content">
<div class="paragraph"><p>A declared property provides a syntactical shorthand for declaring a class’s accessor methods and, optionally, implementing them. You can declare a property anywhere in the method declaration list, which is in the interface of a class, or in the declaration of a protocol or category.</p></div>
</div>
<div class="attribution">
&#8212; Apple
</div></div>
<div class="paragraph"><p>Ugh&#8230;</p></div>
<div class="sect2">
<h3 id="_things_you_can_change_simply">1.1. Things You Can Change, Simply</h3>
<div class="paragraph"><p>For all intents and purposes, <em>properties</em> represent things you can change about an object by giving you a <em>simple</em> way of doing so. Properties are things like <tt>fillColor</tt>, <tt>width</tt>, and <tt>rate</tt>, all of which change the look or behaviour or state of an object. Examples of how to use these properties might look like the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4BLUE</span><span class="p">;</span>
<span class="n">movie</span><span class="p">.</span><span class="n">origin</span> <span class="o">=</span> <span class="n">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">);</span>
<span class="n">sample</span><span class="p">.</span><span class="n">rate</span> <span class="o">=</span> <span class="mf">0.5f</span><span class="p">;</span>
<span class="n">slider</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_simply">1.2. Simply.</h3>
<div class="paragraph"><p>The simple part about properties is that they are <em>always</em> set with an <tt>=</tt> sign.</p></div>
<div class="paragraph"><p>So, when you see&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">someObject</span><span class="p">.</span><span class="n">property</span> <span class="o">=</span> <span class="n">someNewValue</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;you can tell that there is a <em>property</em> of <em>some object</em> that you&#8217;re setting, and that you&#8217;re setting it to <em>some new value</em>.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_styles">2. Styles</h2>
<div class="sectionbody">
<div class="paragraph"><p>Style properties are those that change the <strong>look</strong> of an object.</p></div>
<div class="sect2">
<h3 id="_shapes">2.1. Shapes</h3>
<div class="paragraph"><p>The C4Shape object has the most amount of style properties because you can change all kinds of things from stroke / fill colors, to line widths, dash patterns, line cap styles, and more. Examples of these are:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">circle</span><span class="p">.</span><span class="n">fillColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
<span class="n">circle</span><span class="p">.</span><span class="n">lineWidth</span> <span class="o">=</span> <span class="mf">50.0f</span><span class="p">;</span>
<span class="n">circle</span><span class="p">.</span><span class="n">lineDashPattern</span> <span class="o">=</span> <span class="err">@</span><span class="p">[</span><span class="err">@</span><span class="p">(</span><span class="mi">30</span><span class="p">),</span><span class="err">@</span><span class="p">(</span><span class="mi">25</span><span class="p">)];</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesShapes.png" alt="Shape Properties" />
</div>
</div>
<div class="paragraph"><p>&#8230;, and so on.</p></div>
<div class="paragraph"><p>The <a href="/docs/Classes/C4Shape.html">C4Shape documentation</a> has a list of properties you can change for shapes. <em>These properties apply to all shapes.</em></p></div>
</div>
<div class="sect2">
<h3 id="_all_visual_objects">2.2. All Visual Objects</h3>
<div class="paragraph"><p>There are a basic set of properties common to <em>all</em> visual objects. Common style properties are:</p></div>
<div class="ulist"><ul>
<li>
<p>
opacity
</p>
</li>
<li>
<p>
background/border color
</p>
</li>
<li>
<p>
border width
</p>
</li>
<li>
<p>
mask
</p>
</li>
</ul></div>
<div class="paragraph"><p>To change the opacity of <em>any</em> visual object you simply do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">visualObject</span><span class="p">.</span><span class="n">alpha</span> <span class="o">=</span> <span class="mf">0.5f</span><span class="p">;</span> <span class="c1">//makes the object 50% translucent</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesAlpha.png" alt="Alpha Property" />
</div>
</div>
<div class="paragraph"><p>To change the background color of any object to <em>red</em> you simply do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">backgroundColor</span> <span class="o">=</span> <span class="n">C4RED</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">cornerRadius</span> <span class="o">=</span> <span class="mf">20.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesBackground.png" alt="Background Properties" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_shadows">3. Shadows</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 5 different shadow properties that allow you to change the look / characteristic of a visual object&#8217;s shadow. They are:</p></div>
<div class="ulist"><ul>
<li>
<p>
shadowRadius
</p>
</li>
<li>
<p>
shadowOpacity
</p>
</li>
<li>
<p>
shadowColor
</p>
</li>
<li>
<p>
shadowOffset
</p>
</li>
<li>
<p>
shadowPath
</p>
</li>
</ul></div>
<div class="paragraph"><p>There are <strong>2</strong> essential things you need to do in order to create a shadow for an object: set its <em>opacity</em> (to something visible), and set its <em>offset</em>. The following will create a shadow for an object:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">shadowOpacity</span> <span class="o">=</span> <span class="mf">0.8f</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">shadowRadius</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">shadowOffset</span> <span class="o">=</span> <span class="n">CGSizeMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">);</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesShadow.png" alt="Shadow Properties" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_geometries">4. Geometries</h2>
<div class="sectionbody">
<div class="paragraph"><p>All visual objects have geometry properties that you use to change their location, size (sometimes), and rotation.</p></div>
<div class="sect2">
<h3 id="_locations">4.1. Locations</h3>
<div class="paragraph"><p>Changing the location of a visual object on the canvas is a cinch.  All you do is specify a new point for either the <tt>origin</tt> or the <tt>center</tt> properties of the object and it will move accordingly.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">image</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
<span class="n">image</span><span class="p">.</span><span class="n">origin</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesLocations.png" alt="Location Properties" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_sizes">4.2. Sizes</h3>
<div class="paragraph"><p>I say <em>sometimes</em> for size because objects like images and movies allow you to change their width or heights by setting their properties&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">image</span><span class="p">.</span><span class="n">width</span> <span class="o">=</span> <span class="mi">768</span><span class="p">;</span>
<span class="n">image</span><span class="p">.</span><span class="n">height</span> <span class="o">*=</span> <span class="mi">2</span><span class="p">;</span>
<span class="n">image</span><span class="p">.</span><span class="n">frame</span> <span class="o">=</span> <span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">320</span><span class="p">,</span><span class="mi">320</span><span class="p">);</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesSizesImages.png" alt="Image Size Properties" />
</div>
</div>
<div class="paragraph"><p>&#8230;but some objects don&#8217;t let you do this&#8230;</p></div>
<div class="paragraph"><p>If you&#8217;re resizing a shape you have to "rebuild" it like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="nc">C4WorkSpace</span> <span class="p">{</span>
        <span class="n">C4Shape</span> <span class="o">*</span><span class="n">circle</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
        <span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n">C4Shape</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">)];</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
        <span class="p">[</span><span class="n">self</span><span class="p">.</span><span class="n">canvas</span> <span class="n">addShape</span><span class="o">:</span><span class="n">circle</span><span class="p">];</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
        <span class="p">[</span><span class="n">circle</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">368</span><span class="p">,</span><span class="mi">128</span><span class="p">)];</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesSizesShapes.png" alt="Changing a Shape's Size" />
</div>
</div>
<div class="paragraph"><p>&#8230;for the previous code, when you touch the canvas the circle will turn into an ellipse.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">We have to reset the center of the circle to keep it in the middle of the canvas after the shape changes size.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_rotation">4.3. Rotation</h3>
<div class="paragraph"><p>It&#8217;s pretty easy to rotate shapes in C4 because all visual objects have rotation properties. You can rotate in all three axes with Z being the default rotation property.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="p">.</span><span class="n">rotation</span> <span class="o">=</span> <span class="n">QUARTER_PI</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">rotationX</span> <span class="o">=</span> <span class="n">QUARTER_PI</span><span class="p">;</span>
<span class="n">shape</span><span class="p">.</span><span class="n">rotationY</span> <span class="o">=</span> <span class="n">QUARTER_PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesRotation.png" alt="Rotation Properties" />
</div>
</div>
<div class="paragraph"><p>&#8230;and you can do this kind of thing for <strong>all</strong> visual objects&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesRotationOther.png" alt="Rotating Other Kinds of Objects" />
</div>
</div>
<div class="paragraph"><p><strong>WHAAAAAAAAAAAAT!?!?!?!?!</strong></p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_animation">5. Animation</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are two unique properties for animations: <tt>animationDuration</tt> and <tt>animationOptions</tt>. We will only show you the former for now, leaving the latter for a full tutorial on animations.</p></div>
<div class="paragraph"><p>If we take the code from above, where we change the shape of a circle, and add just <strong>one line of code</strong> before the ellipse changes, then the result will be an animation.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="nf">touchesBegan</span> <span class="p">{</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">animationDuration</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
        <span class="p">[</span><span class="n">circle</span> <span class="n">ellipse</span><span class="o">:</span><span class="n">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">368</span><span class="p">,</span><span class="mi">512</span><span class="p">)];</span>
        <span class="n">circle</span><span class="p">.</span><span class="n">center</span> <span class="o">=</span> <span class="n">self</span><span class="p">.</span><span class="n">canvas</span><span class="p">.</span><span class="n">center</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="imageblock">
<div class="content">
<img src="properties/propertiesAnimation.png" alt="Animation Properties" />
</div>
</div>
<div class="paragraph"><p>&#8230;this code makes the circle stretch and grow for a duration of <strong>2.0</strong> seconds.</p></div>
<div class="sect2">
<h3 id="_values">5.1. Values</h3>
<div class="paragraph"><p>As you come across different objects in C4, especially non-visual ones, you&#8217;ll run into a lot of different properties that are simply values.</p></div>
<div class="paragraph"><p>For example, the playback rate of an audio file can be adjusted by writing:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">sample</span><span class="p">.</span><span class="n">rate</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>You can change the values of a vector like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">vector</span><span class="p">.</span><span class="n">x</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>
<span class="n">vector</span><span class="p">.</span><span class="n">z</span> <span class="o">=</span> <span class="n">PI</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Adjusting the bounding values of a slider is easy:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">slider</span><span class="p">.</span><span class="n">minimumValue</span> <span class="o">=</span> <span class="mf">5.0f</span><span class="p">;</span>
<span class="n">slider</span><span class="p">.</span><span class="n">maximumValue</span> <span class="o">=</span> <span class="mf">50.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;and so is reading the same slider:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">CGFloat</span> <span class="n">f</span> <span class="o">=</span> <span class="n">slider</span><span class="p">.</span><span class="n">value</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">6. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>So, the main thing to take away from this tutorial is that:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
<em>all objects use properties</em> and
</p>
</li>
<li>
<p>
<em>working with properties is *dead-easy*</em>.
</p>
</li>
</ol></div>
<div class="paragraph"><p>There are <em>way</em> too many properties for me to list in a single tutorial, but hopefully what I&#8217;ve outlined here gives you a sense of how properties work. Once you get a grip on how to adjust things, animate them, and so on, you&#8217;ll start to see how <em>everything begins to work *the same way*</em>.</p></div>
<div class="paragraph"><p>Properties are important.</p></div>
<div class="paragraph"><p>Ciao.</p></div>
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
