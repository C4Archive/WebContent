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
<div id="header" class="span8">

<h2>Exporting Media</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5399635" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>And so, like a bad interrogator, you ask "Oh C4 how do I get stuff out of you?" Well, in this tutorial I&#8217;ll show you two or three little tricks for exporting images and pdfs from C4. It&#8217;s going to take a bit of raw code, but I have no doubts you&#8217;ll be able to keep up.</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/exporting.png" alt="Left to Right: Low, High, PDF" />
</div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_first_the_files">1. First, The Files</h2>
<div class="sectionbody">
<div class="paragraph"><p>First things first. To show you what we&#8217;re going to be producing, you can click on the following links:</p></div>
<div class="paragraph"><p><a href="exportingMedia/exportedImageFromC4.png">A Normal 768 × 1024 @ 72ppi Image</a></p></div>
<div class="paragraph"><p><a href="exportingMedia/exportedHighResImageFromC4.png">A High Res 3840 × 5120 @ 360ppi Image</a></p></div>
<div class="paragraph"><p><a href="exportingMedia/exportedPDFFromC4.png">A Scalable 768 x 1024 PDF</a></p></div>
</div>
</div>
<div class="sect1">
<h2 id="_things_in_contextref">2. Things in ContextRef</h2>
<div class="sectionbody">
<div class="paragraph"><p>The first bit, as we usually do, is to start with creating the class variables that you&#8217;re going to use in the tutorial. This time you&#8217;ll probably <em>not</em> has seen the kind of variable we&#8217;re going to use.</p></div>
<div class="sect2">
<h3 id="_cgcontextref">2.1. CGContextRef</h3>
<div class="paragraph"><p>When you&#8217;re going to draw things into images or pdfs that are ready for saving or rendering on screen, there&#8217;s this in-between space called a <em>drawing context</em> that you fill up with what you&#8217;re drawing. When you&#8217;ve finished adding things to the drawing context, then you can either render that context into something you want to see on screen, or you can save it to a file.</p></div>
<div class="paragraph"><p>This context is part of the <em>Core Graphics</em> framework, and so it has a <tt>CG</tt> prefix. Add the following to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="nc">CGContextRef</span> <span class="n">graphicsContext</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This is the only class variable we&#8217;re going to need for this tutorial. Apple describes this as:</p></div>
<div class="sidebarblock">
<div class="content">
<div class="paragraph"><p>A graphics context contains drawing parameters and all device-specific information needed to render the paint on a page to the destination, whether the destination is a window in an application, a bitmap image, a PDF document, or a printer.</p></div>
</div></div>
<div class="paragraph"><p>The rest of this tutorial is going to be laid out like this:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
create an object to go on the canvas
</p>
</li>
<li>
<p>
create a graphics context to draw into
</p>
</li>
<li>
<p>
draw the canvas and all its subviews into the graphics context
</p>
</li>
<li>
<p>
save the graphics context to two different places
</p>
</li>
</ol></div>
<div class="paragraph"><p>Andale.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_the_canvas">3. The Canvas</h2>
<div class="sectionbody">
<div class="paragraph"><p>In writing this tutorial I actually learned a lot about rendering to graphics contexts. One of the great things I found out for myself was that if you set things up properly you can choose to render <em>only one object</em> and all of its subviews will render as well.</p></div>
<div class="paragraph"><p>I thought this was brilliant because I had originally tried to do a bunch of nonsense like context translating, pushing and popping, rendering things upside down and so on just to get everything looking in the image like it did on the canvas&#8230; And, though this more raw approach was working, it was really really heavy on Core Graphics tricks&#8230;</p></div>
<div class="paragraph"><p>We&#8217;re actually just going to draw the canvas into our context. So, we can start by setting it up and adding an object to be drawn.</p></div>
<div class="sect2">
<h3 id="_setup">3.1. Setup</h3>
<div class="paragraph"><p>Create your setup method to have a shape made from a string centered on the canvas. Add the following to your code:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;AvenirNextCondensed-Heavy&quot;</span> <span class="n">size:</span><span class="mi">144</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">s</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">shapeFromString:</span><span class="s">@&quot;EXPORTING&quot;</span> <span class="n">withFont:font</span><span class="p">];</span>
    <span class="n">s</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:s</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>All we&#8217;ve done is create a big fat font, created a shape from the work <tt>EXPORTING</tt> and added that to the center of the canvas.</p></div>
<div class="paragraph"><p>We&#8217;re done here.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_three_methods">4. Three Methods</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to export a snapshot of our app in 3 different ways: as a 1:1 image, as a high-res image and as a PDF. In order to do this, we&#8217;ll need to write 3 different methods for creating appropriate <em>drawing contexts</em>.</p></div>
<div class="sect2">
<h3 id="_a_normal_context">4.1. A Normal Context</h3>
<div class="paragraph"><p>The first method will give us a run-of-the-mill image context with no special options. Add the following method outside your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="nc">CGContextRef</span><span class="p">)</span><span class="n">createImageContext</span> <span class="p">{</span>
    <span class="nc">UIGraphicsBeginImageContext</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.frame.size</span><span class="p">);</span>
    <span class="k">return</span> <span class="nc">UIGraphicsGetCurrentContext</span><span class="p">();</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates a basic image context the same size as our canvas and gives it back to us. With this method we&#8217;ll be able to produce images that are <tt>768 x 1024</tt> at <tt>72ppi</tt>. Opening the file and checking its image size in Photoshop gives us the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/lowResDetails.png" alt="Low Res Details in Photoshop" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_a_high_res_context">4.2. A High Res Context</h3>
<div class="paragraph"><p>The second method you can create is the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="nc">CGContextRef</span><span class="p">)</span><span class="n">createHighResImageContext</span> <span class="p">{</span>
    <span class="nc">UIGraphicsBeginImageContextWithOptions</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.frame.size</span><span class="p">,</span> <span class="nb">YES</span><span class="p">,</span> <span class="mf">5.0f</span><span class="p">);</span>
    <span class="k">return</span> <span class="nc">UIGraphicsGetCurrentContext</span><span class="p">();</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This creates a drawing context from which we&#8217;ll be able to grab an image that is 5 times as wide and tall, and 5 times as dense in terms of points. Yes, yes, you read that properly&#8230; We&#8217;re going to get a <tt>3840 x 5120</tt> at <tt>360ppi</tt> image. Opening the file and checking its image size in Photoshop gives us the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/highResDetails.png" alt="High Res Details in Photoshop" />
</div>
</div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">The <tt>YES</tt> part of that method basically means that our image will NOT have any background alpha transparency.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_a_pdf_context">4.3. A PDF Context</h3>
<div class="paragraph"><p>If you want to preserver scalability of paths and shapes and such, you&#8217;ll want to draw to a PDF. The third method you can create is:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="nc">CGContextRef</span><span class="p">)</span><span class="n">createPDFContext</span> <span class="p">{</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">fileName</span> <span class="o">=</span> <span class="s">@&quot;exportedPDFFromC4.pdf&quot;</span><span class="p">;</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">outputPath</span> <span class="o">=</span> <span class="p">[[</span><span class="k">self</span> <span class="n">documentsDirectory</span><span class="p">]</span> <span class="n">stringByAppendingPathComponent:fileName</span><span class="p">];</span>

    <span class="nc">UIGraphicsBeginPDFContextToFile</span><span class="p">(</span><span class="n">outputPath</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.frame</span><span class="p">,</span> <span class="nb">nil</span><span class="p">);</span>
    <span class="nc">UIGraphicsBeginPDFPage</span><span class="p">();</span>
    <span class="k">return</span> <span class="nc">UIGraphicsGetCurrentContext</span><span class="p">();</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>As you can see, building a PDF context is a little different. You have to first create a file for the rendering, including a file name and a location. The location is going to be in our app&#8217;s Documents directory. After you have the output file path, then you create the context and begin a page for the pdf.</p></div>
<div class="paragraph"><p>So, those are the 3 methods you need to create drawing contexts. Now, I&#8217;m going to step aside to describe the Documents directory and how to get images and pdfs and other files you  might create <strong>out of the iPad or iPhone</strong> you&#8217;re working on.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_an_app_8217_s_documents">5. An App&#8217;s Documents</h2>
<div class="sectionbody">
<div class="paragraph"><p>Every app has a Documents directory. Since we&#8217;re going to be saving images to this directory, and not just PDFs, we actually need to create a method that will return us the directory path. Add the following to your application:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">documentsDirectory</span> <span class="p">{</span>
    <span class="nc">NSArray</span> <span class="o">*</span><span class="n">paths</span> <span class="o">=</span> <span class="nc">NSSearchPathForDirectoriesInDomains</span><span class="p">(</span><span class="nc">NSDocumentDirectory</span><span class="p">,</span> <span class="nc">NSUserDomainMask</span><span class="p">,</span> <span class="nb">YES</span><span class="p">);</span>
    <span class="k">return</span> <span class="n">paths</span><span class="p">[</span><span class="mi">0</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method gives the path to your app&#8217;s Documents directory.</p></div>
<div class="sect2">
<h3 id="_sharing_documents">5.1. Sharing Documents</h3>
<div class="paragraph"><p>If you haven&#8217;t done this before, then pay close attention.</p></div>
<div class="paragraph"><p><strong>Every app you build can share its documents via iTunes.</strong></p></div>
<div class="paragraph"><p>This means that if you want to get the media that you&#8217;re producing in C4 out of the device and onto your computer, you&#8217;re going to have to set your app up for sharing.</p></div>
</div>
<div class="sect2">
<h3 id="_xxxx_info_plist">5.2. XXXX-Info.plist</h3>
<div class="paragraph"><p>In the Project Navigator on the left hand side of the Xcode window, navigate to a file called <tt>XXXX-Info.plist</tt> where the XXXX is the name of your current project. For me, I&#8217;m working in the main C4iOS development project, so the file is called <tt>C4iOS-Info.plist</tt>.</p></div>
<div class="paragraph"><p>When you click on this file a list of variables pops up in the main window. Somewhere along the list is an entry called <strong>Application Supports iTunes File Sharing</strong>, its type is a <tt>BOOL</tt>.</p></div>
<div class="paragraph"><p>What you want to do is set this value to <tt>YES</tt></p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/itunesFileSharingYES.png" alt="Select File Sharing" />
</div>
</div>
<div class="paragraph"><p>If you don&#8217;t have this entry, simply add a row either by right-clicking on a file and choosing <tt>Add Row</tt>, or clicking on the + symbol of any of the other rows. Then, for the Key part of the new row insert <tt>Application Supports iTunes File Sharing</tt> and then set the value to <tt>YES</tt>.</p></div>
</div>
<div class="sect2">
<h3 id="_getting_documents">5.3. Getting Documents</h3>
<div class="paragraph"><p>The next time you compile the application it will have a little flag that grants iTunes access to the app&#8217;s Documents directory.</p></div>
<div class="paragraph"><p>Open iTunes. (with your device plugged in)</p></div>
<div class="paragraph"><p>Click on the device in the top-right corner of the iTunes window.</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/iTunesSelectDevice.png" alt="Select the Device" />
</div>
</div>
<div class="paragraph"><p>Select the Apps tab.</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/iTunesDeviceAppsTab.png" alt="Device Apps Tab" />
</div>
</div>
<div class="paragraph"><p>Scroll down to the <strong>File Sharing</strong> section and click on your App.</p></div>
<div class="imageblock">
<div class="content">
<img src="exportingMedia/iTunesDeviceAppsSection.png" alt="Device Apps Section" />
</div>
</div>
<div class="paragraph"><p>On the right, in a little window titled <tt>XXXX Documents</tt> (mine&#8217;s C4iOS Documents) you&#8217;ll be able to see a list of files that <em>exist inside your app&#8217;s Documents directory.</em></p></div>
<div class="paragraph"><p>It&#8217;s from here that you&#8217;ll be able to grab the images and PDFs that we&#8217;re going to create.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_saving_to_places">6. Saving To Places</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to save the three different types of images and PDFs to two different places. Before we move on to the actual exporting, add the following three methods to your application:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">saveImageToLibrary</span> <span class="p">{</span>
    <span class="nc">UIImage</span> <span class="o">*</span><span class="n">image</span> <span class="o">=</span> <span class="nc">UIGraphicsGetImageFromCurrentImageContext</span><span class="p">();</span>
    <span class="nc">UIImageWriteToSavedPhotosAlbum</span><span class="p">(</span><span class="n">image</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="nb">nil</span><span class="p">,</span> <span class="nb">nil</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;This is the simplest way of saving a file to your device&#8217;s Photos Album.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">saveImage:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">fileName</span> <span class="p">{</span>
    <span class="nc">UIImage</span> <span class="o">*</span><span class="n">image</span> <span class="o">=</span> <span class="nc">UIGraphicsGetImageFromCurrentImageContext</span><span class="p">();</span>
    <span class="nc">NSData</span> <span class="o">*</span><span class="n">imageData</span> <span class="o">=</span> <span class="nc">UIImagePNGRepresentation</span><span class="p">(</span><span class="n">image</span><span class="p">);</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">savePath</span> <span class="o">=</span> <span class="p">[[</span><span class="k">self</span> <span class="n">documentsDirectory</span><span class="p">]</span> <span class="n">stringByAppendingPathComponent:fileName</span><span class="p">];</span>
    <span class="p">[</span><span class="n">imageData</span> <span class="n">writeToFile:savePath</span> <span class="n">atomically:</span><span class="nb">YES</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;This is how you save an image to your app&#8217;s Documents directory.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">savePDF</span> <span class="p">{</span>
    <span class="nc">UIGraphicsEndPDFContext</span><span class="p">();</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;The easiest of the three, this is how you save the PDF that you already created.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Remember, we already specified a file path for the PDF when we first created it.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_exporting">7. Exporting</h2>
<div class="sectionbody">
<div class="paragraph"><p>Ok, now we&#8217;re able to write a couple of methods for exporting images and media.</p></div>
<div class="sect2">
<h3 id="_renderincontext">7.1. renderInContext</h3>
<div class="paragraph"><p>The following few methods use the same trick to get the contents of the canvas into the drawing contexts that we&#8217;ll use for exporting. The line of code that&#8217;s important (and shared) in all those methods is:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">renderInContext:graphicsContext</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>That&#8217;s it. That&#8217;s how you render all the contents of the canvas (or any other visual object) into a drawing context.</p></div>
</div>
<div class="sect2">
<h3 id="_normal_images">7.2. Normal Images</h3>
<div class="paragraph"><p>To export normal sized images to both the Photo Album and to the app&#8217;s Documents directory, add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">exportImage</span> <span class="p">{</span>
    <span class="n">graphicsContext</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createImageContext</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">renderInContext:graphicsContext</span><span class="p">];</span>
    <span class="nc">NSString</span> <span class="o">*</span><span class="n">fileName</span> <span class="o">=</span> <span class="s">@&quot;exportedImageFromC4.png&quot;</span><span class="p">;</span>

    <span class="p">[</span><span class="k">self</span> <span class="n">saveImage:fileName</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">saveImageToLibrary</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Here we&#8217;ve created the normal context, rendered the canvas and set up a file name for exporting. Then we save the image to both places.</p></div>
</div>
<div class="sect2">
<h3 id="_high_res_images">7.3. High Res Images</h3>
<div class="paragraph"><p>To export high-res images, add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">exportHighResImage</span> <span class="p">{</span>
    <span class="n">graphicsContext</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createHighResImageContext</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">renderInContext:graphicsContext</span><span class="p">];</span>

    <span class="nc">NSString</span> <span class="o">*</span><span class="n">fileName</span> <span class="o">=</span> <span class="s">@&quot;exportedHighResImageFromC4.png&quot;</span><span class="p">;</span>

    <span class="p">[</span><span class="k">self</span> <span class="n">saveImage:fileName</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">saveImageToLibrary</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;The big differences between this and the last are calling the <tt>[self createHighResImageContext]</tt> and the name we use for exporting.</p></div>
</div>
<div class="sect2">
<h3 id="_pdf_images">7.4. PDF Images</h3>
<div class="paragraph"><p>To export PDF images, add the following method to your project:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">exportPDF</span> <span class="p">{</span>
    <span class="n">graphicsContext</span> <span class="o">=</span> <span class="p">[</span><span class="k">self</span> <span class="n">createPDFContext</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">renderInContext:graphicsContext</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span> <span class="n">savePDF</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Pff. Simple.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_doing_it">8. Doing It</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now, to actually get our app to do all this, add the following 3 lines of code to the end of your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">exportImage</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">exportHighResImage</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">exportPDF</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Your <tt>setup</tt> should look like:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;AvenirNextCondensed-Heavy&quot;</span> <span class="n">size:</span><span class="mi">144</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">s</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">shapeFromString:</span><span class="s">@&quot;EXPORTING&quot;</span> <span class="n">withFont:font</span><span class="p">];</span>
    <span class="n">s</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:s</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span> <span class="n">exportImage</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">exportHighResImage</span><span class="p">];</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">exportPDF</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, check out your Photos Album for the new images you&#8217;ve made and then via iTunes grab the files that are in the Documents directory of your app and drag them to your Desktop. You can open them in Preview.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_to_not_cg">9. To Not CG</h2>
<div class="sectionbody">
<div class="paragraph"><p>Originally I was doing all this in almost pure Core Graphics. Eariler I mentioned that I learned something while doing this tutorial, and that was to NOT do it in Core Graphics. The <tt>UIKit</tt> functions I found were gold:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIGraphicsGetImageFromCurrentImageContext</span><span class="p">();</span>
<span class="nc">UIGraphicsBeginImageContext</span><span class="p">();</span>
<span class="nc">UIGraphicsBeginImageContextWithOptions</span><span class="p">();</span>
<span class="nc">UIGraphicsBeginPDFContextToFile</span><span class="p">();</span>
<span class="nc">UIGraphicsBeginPDFPage</span><span class="p">();</span>
<span class="nc">UIGraphicsEndPDFContext</span><span class="p">();</span>
<span class="nc">UIImageWriteToSavedPhotosAlbum</span><span class="p">();</span>
<span class="nc">UIGraphicsGetCurrentContext</span><span class="p">();</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;Those functions save a TON of code, and a ton of figuring out the quirks with doing everything by hand.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/github.png" alt="Github" />
</td>
<td class="content">If you&#8217;re interested to see how I did things before finding these <tt>UIKit</tt> methods, check out this <a href="https://gist.github.com/C4Tutorials/5416238">gist</a></td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">10. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;ve gone through a few steps for creating images and PDFs and saving those to different places on your device. You can now go ahead and grab these things off your iPhone or iPad and work some photoshop magic with them if you want. Or, not. Your choice&#8230; But, I hope you make pretty things now.</p></div>
<div class="paragraph"><p>Arriba.</p></div>
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
