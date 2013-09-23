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

<h2>Reading Documentation</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>In this tutorial we will show you how to understand our documentation (as well as Apple&#8217;s, because we&#8217;ve copied their style) and translate what&#8217;s written into code.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/classDocument.png" alt="The C4Control Class Document" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">I had trouble understanding Apple&#8217;s documentation style when I first started working with Objective-C in 2009. It took me a while to get used to the way they presented their reference and API. After a while I began to fully understand how search and navigate the tremendous amount of resources that are available for developing iOS apps.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_main_doc_types">1. Main Doc Types</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are 2 main types of documents that you&#8217;ll run into when searching through the organizer. These are:</p></div>
<div class="ulist"><ul>
<li>
<p>
API reference document
</p>
</li>
<li>
<p>
Conceptual document (Programming Guides)
</p>
</li>
</ul></div>
<div class="sect2">
<h3 id="_class_reference_docs">1.1. Class Reference Docs</h3>
<div class="paragraph"><p>A class reference document presents you with a thorough description of a Class. You&#8217;ll probably find yourself reading more through this kind of document than any other when you need to know more about the methods, properties and hierarchies of an individual object.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/classReference.png" alt="A Class Reference Document" />
</div>
</div>
<div class="paragraph"><p>Because these kinds of documents are highly detailed, you&#8217;ll find yourself coming back to them more often than any other type of document.</p></div>
</div>
<div class="sect2">
<h3 id="_programming_guides">1.2. Programming Guides</h3>
<div class="paragraph"><p>These documents provide an in-depth look at the techniques you&#8217;ll need for developing various concepts. At the moment, we don&#8217;t offer programming guides for C4 (instead, we provide tutorials and examples on our site), but you will often run into these documents when searching for iOS-specific concepts.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/programmingGuide.png" alt="A Programming Guide" />
</div>
</div>
<div class="paragraph"><p>These documents should be read when you <em>need to read them</em>, or if you&#8217;re generally bored and interested in reading something equally dry. The subjects for each programming guide are on a general topic and will give you a better understanding of how to approach a problem or better understand a subject.</p></div>
<div class="paragraph"><p>For instance, if you want to learn more about <em>views</em> and what they are, you can search for the <em>iOS View Programming Guide</em>.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_anatomy_of_a_reference_doc">2. Anatomy of a Reference Doc</h2>
<div class="sectionbody">
<div class="paragraph"><p>The style of reference docs can be strange at first. The rest of this tutorial will focus on the structure of reference docs and how to turn them into code. A reference document is broken up into the following sections:</p></div>
<div class="ulist"><ul>
<li>
<p>
Title &amp; Details
</p>
</li>
<li>
<p>
Overview
</p>
</li>
<li>
<p>
Tasks
</p>
</li>
<li>
<p>
Properties
</p>
</li>
<li>
<p>
Instance Methods
</p>
</li>
</ul></div>
<div class="sect2">
<h3 id="_title_amp_details">2.1. Title &amp; Details</h3>
<div class="paragraph"><p>This section outlines the "placement" of the current class in relation to the overall hierarchy of other objects, documentation, current api versions and so on&#8230;</p></div>
<div class="paragraph"><p>This section usually has a list of the following details:</p></div>
<div class="ulist"><ul>
<li>
<p>
<strong>Inherits From</strong> The ancestors of the current class, you can look to these for more methods that the current class can use
</p>
</li>
<li>
<p>
<strong>Conforms To</strong> A list of "protocols" to which the current class has to conform, meaning that its api has to have certain methods defined
</p>
</li>
<li>
<p>
<strong>Framework</strong> For Apple apis this link will point you to the framework that needs to be imported into your project for this class to be used
</p>
</li>
<li>
<p>
<strong>Availability</strong> The earliest possible version of iOS you need in order to use this class, e.g. a class with availability of iOS 6.0 cannot be used if you&#8217;re running 5.1
</p>
</li>
<li>
<p>
<strong>Declared In</strong> The header and implementation files that define the methods and properties available to this class
</p>
</li>
<li>
<p>
<strong>Related Sample Code</strong> For many classes you can click on these links to find sample projects and code that use the current class
</p>
</li>
</ul></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/titleDetails.png" alt="Class Reference Title & Details" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">But They&#8217;re Not All There&#8230;</div>A class does not have to present all of the details listed above. For simple classes there might not be many details to list out. However, more complicated classes may have a lot of detail to fully explain how they are constructed and work.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_overview">2.2. Overview</h3>
<div class="paragraph"><p>This section usually provides an in-depth discussion about the current class. The overview for the C4Control class is very long because it is the base class for all visible objects in C4. The C4Control overview outlines all the details and characteristics that are available in every other class (e.g. C4Shape, C4Movie, etc.) that inherits <em>from</em> it.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/overview.png" alt="Class Reference Overview" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">It&#8217;s always a good idea to at least skim the overview for a class because it will tell you how it works.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_tasks">2.3. Tasks</h3>
<div class="paragraph"><p>This section is a complete list of methods and properties available for the current class. This means that for <em>any</em> object you create you will be able to call these methods and access or change the properties in this list.</p></div>
<div class="paragraph"><p>The tasks section is generally broken down into groups of methods and properties that are related to one another. In the C4Control class, the <em>Convenience Methods</em> section contains 4 methods that help you deal with general situations, whereas the <em>Setting a Control&#8217;s Origin Point</em> section only has a single property.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/tasks.png" alt="Class Reference Tasks" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_properties">2.4. Properties</h3>
<div class="paragraph"><p>This section is a listing of all properties, sorted alphabetically, that apply to the current class.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/properties.png" alt="Class Reference Properties" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_class_methods">2.5. Class Methods</h3>
<div class="paragraph"><p>This section is a listing of all class methods, sorted alphabetically, that apply to the current class.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/classMethods.png" alt="Class Reference Class Methods" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Class methods are those that start with a <tt>+</tt> symbol in the documentation, e.g. <tt>+(void)doSomething</tt>. This means that you can <em>only</em> call them like this: <tt>[C4Shape ...];</tt> and not on individual objects themselves.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_instance_methods">2.6. Instance Methods</h3>
<div class="paragraph"><p>This section is a listing of all instance methods, sorted alphabetically, that apply to the current class.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/instanceMethods.png" alt="Class Reference Instance Methods" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Instance methods are those that start with a <tt>-</tt> symbol in the documentation, e.g. <tt>-(void)doSomething</tt>. This means that you can <em>only</em> call them like this: <tt>[objectName ...];</tt> and not on classes.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_reading_a_property">3. Reading A Property</h2>
<div class="sectionbody">
<div class="paragraph"><p>The best way to think about properties is that they are <em>characteristics</em> of an object, like the <tt>fillColor</tt> property of a C4Shape. In many cases properties represent style but they can also represent the current state of an object, such as its location or the current time of a movie.</p></div>
<div class="paragraph"><p>There are 4 things you should be aware of when reading a property&#8217;s documentation:</p></div>
<div class="ulist"><ul>
<li>
<p>
Name
</p>
</li>
<li>
<p>
Attributes
</p>
</li>
<li>
<p>
Type
</p>
</li>
<li>
<p>
Animatable
</p>
</li>
<li>
<p>
Discussion
</p>
</li>
</ul></div>
<div class="paragraph"><p>The following image shows a typical documentation of a property and highlights the 4 main components you should know about&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/property.png" alt="A Property" />
</div>
</div>
<div class="sect2">
<h3 id="_name">3.1. Name</h3>
<div class="paragraph"><p>This is the name of the property, it is the "word" that you use when programming to access this property.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">shape</span><span class="py">.anchorPoint</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_attributes">3.2. Attributes</h3>
<div class="paragraph"><p>The attributes of a property tell you a few important things, some of which you&#8217;ll only have to worry about when you start programming your own classes with properties. For the most part you&#8217;ll want to pay attention to the <em>readability</em> of the property and it&#8217;s <em>accessor names</em>.</p></div>
<div class="ulist"><ul>
<li>
<p>
<strong>readonly</strong>: you can only get the value for this property, you cannot change it. An example of this is a C4Movie&#8217;s <tt>currentTime</tt> property, you can get the time but you <strong>cannot</strong> set it&#8230;
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">theTime</span> <span class="o">=</span> <span class="n">movie</span><span class="py">.currentTime</span><span class="p">;</span> <span class="c1">//gets the current time</span>
<span class="n">movie</span><span class="py">.currentTime</span> <span class="o">=</span> <span class="mf">10.0f</span><span class="p">;</span>           <span class="c1">//NOT POSSIBLE</span>
</pre></div></div></div>
<div class="ulist"><ul>
<li>
<p>
<strong>readwrite</strong>: you can both get and set the value for this property. An example of this is a visible object&#8217;s <tt>anchorPoint</tt> property.
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGPoint</span> <span class="n">theAnchor</span> <span class="o">=</span> <span class="n">shape</span><span class="py">.anchorPoint</span><span class="p">;</span>  <span class="c1">//gets the anchor point</span>
<span class="n">movie</span><span class="py">.anchorPoint</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="mi">10</span><span class="p">,</span><span class="mi">10</span><span class="p">);</span> <span class="c1">//sets the anchor point</span>
</pre></div></div></div>
<div class="ulist"><ul>
<li>
<p>
<strong>getter=</strong>: for semantic reasons the name of the property can be changed so that it reads better. For a documented property
</p>
</li>
</ul></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@property</span> <span class="p">(</span><span class="n">readonly</span><span class="p">,</span> <span class="n">getter</span><span class="o">=</span><span class="n">isPlaying</span><span class="p">)</span> <span class="kt">BOOL</span> <span class="n">playing</span><span class="p">;</span>
</pre></div></div></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">(</span><span class="n">theMovie</span><span class="py">.isPlaying</span> <span class="o">==</span> <span class="nb">YES</span><span class="p">)</span> <span class="p">{};</span> <span class="c1">//reads nicer than...</span>
<span class="k">if</span><span class="p">(</span><span class="n">theMovie</span><span class="py">.playing</span> <span class="o">==</span> <span class="nb">YES</span><span class="p">)</span> <span class="p">();</span>   <span class="c1">//not as nice...</span>
</pre></div></div></div>
<div class="ulist"><ul>
<li>
<p>
<strong>setter=</strong>: I run into this attribute rarely, but it pops up from time to time. The reasoning is semantic, the same as above.
</p>
</li>
</ul></div>
</div>
<div class="sect2">
<h3 id="_type">3.3. Type</h3>
<div class="paragraph"><p>This states the <em>class</em> or <em>data type</em> of the property. The type of a visible object&#8217;s <tt>anchorPoint</tt> is <tt>CGPoint</tt>, whereas its frame will be <tt>CGRect</tt>, and if it&#8217;s a C4Shape its <tt>fillColor</tt> will be a <tt>UIColor</tt> object.</p></div>
</div>
<div class="sect2">
<h3 id="_animatable">3.4. Animatable</h3>
<div class="paragraph"><p>This part of the description will tell you whether or not a property is animatable. If it is, then you can change it&#8217;s value over time.</p></div>
</div>
<div class="sect2">
<h3 id="_discussion">3.5. Discussion</h3>
<div class="paragraph"><p>The discussion describes in detail the important things to know about a property.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">Officially</div>For more on properties, have a look at the <a href="https://developer.apple.com/library/ios/#documentation/Cocoa/Conceptual/ObjectiveC/Chapters/ocProperties.html">Declared Properties</a> documentation.</td>
</tr></table>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_translating_a_property_to_code">4. Translating A Property to Code</h2>
<div class="sectionbody">
<div class="paragraph"><p>The following diagram shows you where and how the documentation of a property appears in code:</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/propertyTranslate.png" alt="A Property Translated to Code" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_reading_a_method">5. Reading a Method</h2>
<div class="sectionbody">
<div class="paragraph"><p>Reading a method is similar to reading a property, in that there are 4 things you should be aware of when reading a property&#8217;s documentation:</p></div>
<div class="ulist"><ul>
<li>
<p>
Name
</p>
</li>
<li>
<p>
Return Type
</p>
</li>
<li>
<p>
Class / Instance Type
</p>
</li>
<li>
<p>
Parameters
</p>
</li>
</ul></div>
<div class="paragraph"><p>The following image shows a typical documentation of a property and highlights the 4 main components you should know about&#8230;</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/method1.png" alt="A Method" />
</div>
</div>
<div class="sect2">
<h3 id="_name_2">5.1. Name</h3>
<div class="paragraph"><p>This is the name of the method, without parameters. When you call this method in code it will be filled in with parameters.</p></div>
</div>
<div class="sect2">
<h3 id="_return_type">5.2. Return Type</h3>
<div class="paragraph"><p>This is the type of object or data that you will get back from the method. Most often you will see <tt>void</tt> which means that the method does something but doesn&#8217;t give you anything back when it&#8217;s finished. Otherwise, you&#8217;ll either get an object (e.g. C4Shape) or a data structure (e.g. CGPoint).</p></div>
</div>
<div class="sect2">
<h3 id="_class_instance_type">5.3. Class / Instance Type</h3>
<div class="paragraph"><p>This is a mark at the beginning of the method name, which is either a <tt>-</tt> or a <tt>+</tt>, denoting whether the method can be called from a class or instance object.</p></div>
<div class="paragraph"><p>Calling a <strong>class method</strong> looks like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span><span class="mi">0</span><span class="p">,</span><span class="mi">100</span><span class="p">,</span><span class="mi">100</span><span class="p">)];</span>
</pre></div></div></div>
<div class="paragraph"><p>Calling an <strong>instance method</strong> looks like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">pattern</span><span class="p">[</span><span class="mi">5</span><span class="p">]</span> <span class="o">=</span> <span class="p">{</span><span class="mi">1</span><span class="p">,</span><span class="mi">2</span><span class="p">,</span><span class="mi">3</span><span class="p">,</span><span class="mi">4</span><span class="p">,</span><span class="mi">5</span><span class="p">};</span>
<span class="p">[</span><span class="n">shape</span> <span class="n">setDashPattern:pattern</span> <span class="n">pointCount:</span><span class="mi">5</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>You cannot call an instance method from a class. The following will not work:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">setDashPattern:pattern</span> <span class="n">pointCount:</span><span class="mi">5</span><span class="p">];</span> <span class="c1">//will NOT work</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">See the Difference?</div>The difference between class and instance methods is that you can call a class method without first having to define and construct an object. Instance methods only work with objects that have previously been created.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_parameters">5.4. Parameters</h3>
<div class="paragraph"><p>Parameters are the things a method needs in order to run. Some methods require no parameters, others require many, and some can take a variable number of parameters. When reading a method pay attention to the <em>type</em> of the parameter which is listed right beside the parameter name.</p></div>
<div class="paragraph"><p>The following source code shows a definition for the <tt>addShape:</tt> method of a <tt>C4Control</tt> object. The method takes <em>one</em> parameter whose type is <tt>C4Shape</tt>.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span> <span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">addShape:</span><span class="p">(</span><span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="p">)</span><span class="n">aShape</span><span class="p">;</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">Every parameter has a brief discussion about what it is and what its value "should be"&#8230; For instance, the <em>startAngle</em> property for the <em>arc:</em> method states "The starting angle of the arc, in radians in the range of (0 .. 2*PI)" meaning that the value of <em>startAngle</em> that <strong>you</strong> pass to the method will be interpreted be between 0 .. 2*PI.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_longer_methods">5.5. Longer Methods</h3>
<div class="paragraph"><p>Reading a method with more parameters is the same as reading one with fewer. The following diagram shows you longer method that has the same 4 things to be aware of.</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/method2.png" alt="A Longer Method" />
</div>
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_translating_a_method_to_code">6. Translating a Method to Code</h2>
<div class="sectionbody">
<div class="paragraph"><p>The following diagram shows you a translation of the <tt>addShape:</tt> method for a <tt>C4Shape</tt> object:</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/method1Translate.png" alt="The addShape Method" />
</div>
</div>
<div class="paragraph"><p>The following diagram shows you a translation of the <tt>arc:</tt> method for a <tt>C4Shape</tt> object:</p></div>
<div class="imageblock">
<div class="content">
<img src="readingDocumentation/method2Translate.png" alt="A arc Method" />
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
