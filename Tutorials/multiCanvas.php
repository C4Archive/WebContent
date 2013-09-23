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

<h2>MultiCanvas</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://github.com/C4Tutorials/MultiCanvas" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64684611" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>Okay, now we&#8217;re getting into some cool territory. This tutorial will show you how to build an application that uses multiple canvases and a navigation bar that lets you switch between them.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_from_the_ground_up">1. From the Ground UP</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to work backwards through building this application, first focusing on subclassing <tt>C4CanvasController</tt> to create multiple workspaces. After we have all the workspaces built, we&#8217;ll then get into how to navigate between them. We have to do it this way because our main <tt>setup</tt> method won&#8217;t make sense until the end.</p></div>
<div class="sect2">
<h3 id="_steps">1.1. Steps</h3>
<div class="paragraph"><p>We&#8217;re going to take the following steps to make this happen:</p></div>
<div class="olist arabic"><ol class="arabic">
<li>
<p>
Build a <tt>WorkSpaceA</tt> object whose canvas has a shape and a label.
</p>
</li>
<li>
<p>
Build a <tt>WorkSpaceB</tt> object whose canvas has a shape and a label.
</p>
</li>
<li>
<p>
Build a <tt>WorkSpaceC</tt> object whose canvas contains <strong>copies</strong> of <tt>WorkSpaceA</tt> and <tt>WorkSpaceB</tt>.
</p>
</li>
<li>
<p>
Build a <tt>NavigationBar</tt> and populate it with buttons
</p>
</li>
<li>
<p>
Connect those buttons to methods that switch between canvases
</p>
</li>
<li>
<p>
Add the first canvas (i.e. <tt>WorkSpaceA</tt>) to our main canvas.
</p>
</li>
</ol></div>
<div class="paragraph"><p>That&#8217;s it&#8230; Let&#8217;s get going.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_workspace_a">2. WorkSpace A</h2>
<div class="sectionbody">
<div class="paragraph"><p>To work with multiple canvases, we have to first create a subclass of <tt>C4CanvasController</tt> that we can use as a workspace for our new canvas. Subclassing is fairly straightforward, if you don&#8217;t know how to do it yet you should check out the <a href="/tutorials/subclassing.php">subclassing</a> tutorial. Either way, you should be able to follow along with this tutorial.</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/workspaceA.png" alt="WorkSpace A" />
</div>
</div>
<div class="sect2">
<h3 id="_create_a_subclass">2.1. Create a Subclass</h3>
<div class="paragraph"><p>To create a subclass of <tt>C4CanvasController</tt>, you can click on <tt>File &gt; New &gt; New File</tt>, like so:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/multiCanvasMenu.png" alt="Create a New File" />
</div>
</div>
<div class="paragraph"><p>You&#8217;ll get a popup window that lets you choose the kind of file you want to create. From the left-hand column select <strong>Cocoa Touch</strong>, and from the options that appear on the right select <strong>Objective-C Class</strong>, like so:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/objcClass.png" alt="Create a New Objective-C Class" />
</div>
</div>
<div class="paragraph"><p>Next, you&#8217;ll be prompted for the name of the file you want to create, and its kind. First change the <strong>subclass of</strong> option to <tt>C4CanvasController</tt> and then overwrite the <strong>class</strong> option with <tt>WorkSpaceA</tt>. Finally, select <strong>both</strong> the "Targeted for iPad" and "With XIB for user interface" options. You should see the following:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/options.png" alt="Options for the New Class" />
</div>
</div>
<div class="olist lowerroman"><ol class="lowerroman">
<li>
<p>
hit <strong>Next</strong> and Xcode will build you a set of 3 files.
</p>
</li>
</ol></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/threeFilenames.png" alt="Three Files" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_lather_rinse_repeat">2.2. Lather, Rinse, Repeat</h3>
<div class="paragraph"><p>Do the previous steps 2 more times, creating <tt>WorkSpaceB</tt> and <tt>WorkSpaceC</tt> subclasses. Your project should now have the following list of files:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/nineFilenames.png" alt="Nine Files" />
</div>
</div>
</div>
<div class="sect2">
<h3 id="_changing_the_views">2.3. Changing The Views</h3>
<div class="paragraph"><p>So, you asked Xcode to build you 3 XIB files. These are <em>Interface Builder</em> files that you could potentially use to do some drag and drop design for your application&#8217;s interface. We need to make a simple change to each one of these files. You can do the following steps with <em>each</em> file you created.</p></div>
<div class="paragraph"><p>First, click on the <tt>WorkSpaceA.xib</tt> file, and you&#8217;ll see your code window replaced by a view of the Interface Builder.</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/interfaceBuilder.png" alt="Interface Builder" />
</div>
</div>
<div class="paragraph"><p>Reveal the <strong>Utilities</strong> panel. Click on the interface view (it should have a little blue border around it now) and then in the utilities panel select the <strong>Identity Inspector</strong>. You should now see this:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/UIView.png" alt="Identity Inspector UIView" />
</div>
</div>
<div class="paragraph"><p>At the top of the identity inspector is a section called <strong>Custom Class</strong> which has a little text window with a title called <strong>Class</strong>. The default class for the view is <tt>UIView</tt>.</p></div>
<div class="paragraph"><p>Change the class to read <tt>C4View</tt>, like so:</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/C4View.png" alt="Identity Inspector C4View" />
</div>
</div>
<div class="paragraph"><p>Now, <em>do this for the other 2 workspaces</em>.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">The identity inspector icon is the 3rd from the left at the top of the utilities panel.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_code">2.4. Code</h3>
<div class="paragraph"><p>We&#8217;re going to add a label a shape and a simple touch interaction to <tt>WorkSpaceA</tt>. You can start by <em>deleting</em> the following from the implementation:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@interface</span> <span class="n">testViewController</span> <span class="p">()</span>

<span class="k">@end</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;also delete the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span> <span class="p">(</span><span class="kt">id</span><span class="p">)</span><span class="n">initWithNibName:</span><span class="p">(</span><span class="nc">NSString</span> <span class="o">*</span><span class="p">)</span><span class="n">nibNameOrNil</span> <span class="n">bundle:</span><span class="p">(</span><span class="nc">NSBundle</span> <span class="o">*</span><span class="p">)</span><span class="n">nibBundleOrNil</span>
<span class="p">{</span>
    <span class="k">self</span> <span class="o">=</span> <span class="p">[</span><span class="n">super</span> <span class="n">initWithNibName:nibNameOrNil</span> <span class="n">bundle:nibBundleOrNil</span><span class="p">];</span>
    <span class="k">if</span> <span class="p">(</span><span class="k">self</span><span class="p">)</span> <span class="p">{</span>
        <span class="c1">// Custom initialization</span>
    <span class="p">}</span>
    <span class="k">return</span> <span class="k">self</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span> <span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">viewDidLoad</span>
<span class="p">{</span>
    <span class="p">[</span><span class="n">super</span> <span class="n">viewDidLoad</span><span class="p">];</span>
    <span class="c1">// Do any additional setup after loading the view from its nib.</span>
<span class="p">}</span>

<span class="o">-</span> <span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">didReceiveMemoryWarning</span>
<span class="p">{</span>
    <span class="p">[</span><span class="n">super</span> <span class="n">didReceiveMemoryWarning</span><span class="p">];</span>
    <span class="c1">// Dispose of any resources that can be recreated.</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Now, you can add the following class variables to the implementation:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n">WorkSpaceA</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">label</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">circle</span><span class="p">;</span>
    <span class="kt">BOOL</span> <span class="n">animating</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Next, we&#8217;re going to setup our shape and our label for this class. Add a <tt>setup</tt> method that has the following code:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">circle</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">ellipse:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">368</span><span class="p">,</span> <span class="mi">368</span><span class="p">)];</span>
    <span class="n">circle</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">50.0f</span><span class="p">;</span>
    <span class="n">circle</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:circle</span><span class="p">];</span>

    <span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;Avenir&quot;</span> <span class="n">size:</span><span class="mi">92</span><span class="p">];</span>
    <span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;WorkSpace A&quot;</span> <span class="n">font:font</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">whiteColor</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="n">label</span><span class="py">.zPosition</span> <span class="o">=</span> <span class="mi">2</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:label</span><span class="p">];</span>

    <span class="k">self</span><span class="py">.canvas.borderColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4BLUE</span><span class="p">;</span>
    <span class="k">self</span><span class="py">.canvas.borderWidth</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method creates a large circle with a thick border. It adds the name of the workspace to a label over top of the shape, and then gives a little border to the canvas.</p></div>
<div class="paragraph"><p>Finally, add the following <tt>touchesBegan</tt> method to the workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
    <span class="k">if</span><span class="p">(</span><span class="n">animating</span> <span class="o">==</span> <span class="nb">NO</span><span class="p">)</span> <span class="p">{</span>
        <span class="n">circle</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">4.0f</span><span class="p">;</span>
        <span class="n">circle</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span> <span class="o">|</span> <span class="n">AUTOREVERSE</span><span class="p">;</span>
        <span class="n">circle</span><span class="py">.strokeStart</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
        <span class="n">animating</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>The first time you tap the canvas this code will trigger an animation on the shape&#8217;s <tt>strokeStart</tt> property.</p></div>
</div>
<div class="sect2">
<h3 id="_done_with_a">2.5. Done With A</h3>
<div class="paragraph"><p>We&#8217;re done with <tt>WorkSpaceA</tt> for now.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_workspaceb">3. WorkSpaceB</h2>
<div class="sectionbody">
<div class="paragraph"><p>We&#8217;re going to set up <tt>WorkSpaceB</tt> in much the same way as we did the previous workspace. The slight differences between the two are that:</p></div>
<div class="ulist"><ul>
<li>
<p>
the shape is a square
</p>
</li>
<li>
<p>
the shape&#8217;s <tt>strokeColor</tt> is red
</p>
</li>
<li>
<p>
the animation rotates the shape
</p>
</li>
</ul></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/workspaceB.png" alt="WorkSpace B" />
</div>
</div>
<div class="sect2">
<h3 id="_code_2">3.1. Code</h3>
<div class="paragraph"><p>The code for <tt>WorkSpaceB</tt> is like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="cp">#import &quot;WorkSpaceB.h&quot;</span>

<span class="k">@implementation</span> <span class="n">WorkSpaceB</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Label</span> <span class="o">*</span><span class="n">label</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Font</span> <span class="o">*</span><span class="n">font</span><span class="p">;</span>
    <span class="n-ProjectClass">C4Shape</span> <span class="o">*</span><span class="n">square</span><span class="p">;</span>
    <span class="kt">BOOL</span> <span class="n">animating</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">square</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Shape</span> <span class="n">rect:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">368</span><span class="p">,</span> <span class="mi">368</span><span class="p">)];</span>
    <span class="n">square</span><span class="py">.lineWidth</span> <span class="o">=</span> <span class="mf">50.0f</span><span class="p">;</span>
    <span class="n">square</span><span class="py">.strokeColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
    <span class="n">square</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addShape:square</span><span class="p">];</span>

    <span class="n">font</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Font</span> <span class="n">fontWithName:</span><span class="s">@&quot;Avenir&quot;</span> <span class="n">size:</span><span class="mi">92</span><span class="p">];</span>
    <span class="n">label</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Label</span> <span class="n">labelWithText:</span><span class="s">@&quot;WorkSpace B&quot;</span> <span class="n">font:font</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.backgroundColor</span> <span class="o">=</span> <span class="p">[</span><span class="nc">UIColor</span> <span class="n">whiteColor</span><span class="p">];</span>
    <span class="n">label</span><span class="py">.center</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.center</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addLabel:label</span><span class="p">];</span>

    <span class="k">self</span><span class="py">.canvas.borderColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4RED</span><span class="p">;</span>
    <span class="k">self</span><span class="py">.canvas.borderWidth</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
<span class="p">}</span>

<span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
    <span class="k">if</span><span class="p">(</span><span class="n">animating</span> <span class="o">==</span> <span class="nb">NO</span><span class="p">)</span> <span class="p">{</span>
        <span class="n">square</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">4.0f</span><span class="p">;</span>
        <span class="n">square</span><span class="py">.animationOptions</span> <span class="o">=</span> <span class="n">REPEAT</span> <span class="o">|</span> <span class="n">AUTOREVERSE</span><span class="p">;</span>
        <span class="n">square</span><span class="py">.rotation</span> <span class="o">=</span> <span class="n">TWO_PI</span><span class="p">;</span>
        <span class="n">animating</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>

<span class="k">@end</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_done_with_b">3.2. Done With B</h3>
<div class="paragraph"><p>That&#8217;s it. We do the same steps for this workspace as we did the first, and we&#8217;re done.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_workspace_c">4. WorkSpace C</h2>
<div class="sectionbody">
<div class="paragraph"><p>I originally made this example with 2 canvases, but afterwards thought it would be good to show how you can make <em>copies</em> of those canvases and host them in another completely different canvas.</p></div>
<div class="paragraph"><p>That&#8217;s what we&#8217;re going to do now.</p></div>
<div class="imageblock">
<div class="content">
<img src="multiCanvas/workspaceC.png" alt="WorkSpace C" />
</div>
</div>
<div class="sect2">
<h3 id="_import">4.1. Import</h3>
<div class="paragraph"><p>First things first. Since we&#8217;re going to be working with other workspace classes, we&#8217;re going to have to <tt>#import</tt> them into our <tt>WorkSpaceC</tt>. It&#8217;s pretty easy. At the top of your file you should write the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="cp">#import &quot;WorkSpaceC.h&quot;</span>
<span class="cp">#import &quot;WorkSpaceA.h&quot;</span>
<span class="cp">#import &quot;WorkSpaceB.h&quot;</span>
</pre></div></div></div>
<div class="paragraph"><p>The <tt>"WorkSpace.C.h"</tt> part will already be in your file, you just have to add the <tt>A</tt> and <tt>B</tt> parts.</p></div>
</div>
<div class="sect2">
<h3 id="_variables">4.2. Variables</h3>
<div class="paragraph"><p>Now, for the variables part of the <tt>WorkSpaceC</tt> implementation, make one for each <tt>A</tt> and <tt>B</tt>, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n">WorkSpaceC</span> <span class="p">{</span>
    <span class="n">WorkSpaceA</span> <span class="o">*</span><span class="n">workspaceA</span><span class="p">;</span>
    <span class="n">WorkSpaceB</span> <span class="o">*</span><span class="n">workspaceB</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_setup">4.3. Setup</h3>
<div class="paragraph"><p>Just like you did with the first two classes, delete all the nonsense in the implementation file and replace them with a <tt>setup</tt> method. Then, we&#8217;re going to <em>initialize</em> the two workspace objects that we defined in our variables.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">workspaceA</span> <span class="o">=</span> <span class="p">[[</span><span class="n">WorkSpaceA</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithNibName:</span><span class="s">@&quot;WorkSpaceA&quot;</span> <span class="n">bundle:</span><span class="p">[</span><span class="nc">NSBundle</span> <span class="n">mainBundle</span><span class="p">]];</span>
    <span class="n">workspaceB</span> <span class="o">=</span> <span class="p">[[</span><span class="n">WorkSpaceB</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithNibName:</span><span class="s">@&quot;WorkSpaceB&quot;</span> <span class="n">bundle:</span><span class="p">[</span><span class="nc">NSBundle</span> <span class="n">mainBundle</span><span class="p">]];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>We use the <tt>initWithNibName</tt> method which looks inside the "bundle" of our application and finds the correct <tt>xib</tt> file you specify. This builds the canvases for us.</p></div>
<div class="paragraph"><p>We can now start positioning the two other canvases in this canvas. Add the following code to do so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">CGFloat</span> <span class="n">offset</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">*</span> <span class="mf">0.01f</span><span class="p">;</span>

<span class="n">workspaceA</span><span class="py">.canvas.frame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="n">offset</span><span class="p">,</span><span class="n">offset</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">-</span> <span class="mi">2</span> <span class="o">*</span> <span class="n">offset</span><span class="p">,(</span><span class="k">self</span><span class="py">.canvas.height</span> <span class="o">-</span> <span class="n">offset</span> <span class="o">*</span> <span class="mi">3</span><span class="p">)</span><span class="o">/</span><span class="mf">2.0f</span><span class="p">);</span>
<span class="n">workspaceB</span><span class="py">.canvas.frame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="n">offset</span><span class="p">,</span><span class="n">offset</span> <span class="o">*</span> <span class="mi">2</span> <span class="o">+</span> <span class="n">workspaceA</span><span class="py">.canvas.height</span><span class="p">,</span> <span class="n">workspaceA</span><span class="py">.canvas.width</span><span class="p">,</span> <span class="n">workspaceA</span><span class="py">.canvas.height</span><span class="p">);</span>
<span class="n">workspaceB</span><span class="py">.canvas.clipsToBounds</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>The <tt>offset</tt> helps us make the canvases of <tt>WorkSpaceA</tt> and <tt>WorkSpaceB</tt> <em>slightly</em> smaller in width than that of our <tt>WorkSpaceC</tt> canvas. The rest of the code simply sets the frames of each canvas, and then tells `WorkSpaceB`s canvas to clip its contents. We set the clipping because when the square starts rotating it would otherwise have its corners visible outside the frame of the smaller canvas.</p></div>
<div class="paragraph"><p>Finally, set up the two canvases and then add them to our `WorkSpaceC`s canvas, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">workspaceA</span> <span class="n">setup</span><span class="p">];</span>
<span class="p">[</span><span class="n">workspaceB</span> <span class="n">setup</span><span class="p">];</span>

<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addObjects:</span><span class="err">@</span><span class="p">[</span><span class="n">workspaceA</span><span class="py">.canvas</span><span class="p">,</span> <span class="n">workspaceB</span><span class="py">.canvas</span><span class="p">]];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_c4workspace">5. C4WorkSpace</h2>
<div class="sectionbody">
<div class="paragraph"><p>Now we&#8217;re getting somewhere. We have our other 3 workspaces all ready to go. All we have to do now is create the main canvas and set up the navigation to handle switching between the 3 canvases for those workspaces.</p></div>
<div class="sect2">
<h3 id="_import_2">5.1. Import</h3>
<div class="paragraph"><p>Just like we did before, we have to import references to our 3 workspaces. Add the following to your <tt>C4WorkSpace</tt> implementation:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="cp">#import &quot;C4WorkSpace.h&quot;</span>
<span class="cp">#import &quot;WorkSpaceA.h&quot;</span>
<span class="cp">#import &quot;WorkSpaceB.h&quot;</span>
<span class="cp">#import &quot;WorkSpaceC.h&quot;</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_variables_2">5.2. Variables</h3>
<div class="paragraph"><p>Next, we&#8217;re going to want to set up variables so we can reference the various workspaces as well as the <tt>currentView</tt> which we need for switching (I&#8217;ll explain this in a bit).</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">@implementation</span> <span class="n-ProjectClass">C4WorkSpace</span> <span class="p">{</span>
    <span class="n">WorkSpaceA</span> <span class="o">*</span><span class="n">workspaceA</span><span class="p">;</span>
    <span class="n">WorkSpaceB</span> <span class="o">*</span><span class="n">workspaceB</span><span class="p">;</span>
    <span class="n">WorkSpaceC</span> <span class="o">*</span><span class="n">workspaceC</span><span class="p">;</span>
    <span class="n-ProjectClass">C4View</span> <span class="o">*</span><span class="n">currentView</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_create_workspaces">5.3. Create WorkSpaces</h3>
<div class="paragraph"><p>This step is pretty straightforward, actually it&#8217;s really similar to what we did in <tt>WorkSpaceC</tt>. Create the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">createWorkSpaces</span> <span class="p">{</span>
    <span class="n">workspaceA</span> <span class="o">=</span> <span class="p">[[</span><span class="n">WorkSpaceA</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithNibName:</span><span class="s">@&quot;WorkSpaceA&quot;</span> <span class="n">bundle:</span><span class="p">[</span><span class="nc">NSBundle</span> <span class="n">mainBundle</span><span class="p">]];</span>
    <span class="n">workspaceB</span> <span class="o">=</span> <span class="p">[[</span><span class="n">WorkSpaceB</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithNibName:</span><span class="s">@&quot;WorkSpaceB&quot;</span> <span class="n">bundle:</span><span class="p">[</span><span class="nc">NSBundle</span> <span class="n">mainBundle</span><span class="p">]];</span>
    <span class="n">workspaceC</span> <span class="o">=</span> <span class="p">[[</span><span class="n">WorkSpaceC</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithNibName:</span><span class="s">@&quot;WorkSpaceC&quot;</span> <span class="n">bundle:</span><span class="p">[</span><span class="nc">NSBundle</span> <span class="n">mainBundle</span><span class="p">]];</span>

    <span class="nc">CGFloat</span> <span class="n">offSet</span> <span class="o">=</span> <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">*</span> <span class="mf">0.05f</span><span class="p">;</span>
    <span class="n">workspaceA</span><span class="py">.canvas.frame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="n">offSet</span><span class="p">,</span>
                                         <span class="n">offSet</span><span class="p">,</span>
                                         <span class="k">self</span><span class="py">.canvas.width</span> <span class="o">-</span> <span class="mi">2</span> <span class="o">*</span> <span class="n">offSet</span><span class="p">,</span>
                                         <span class="k">self</span><span class="py">.canvas.height</span> <span class="o">-</span> <span class="mi">44</span> <span class="o">-</span> <span class="mi">2</span> <span class="o">*</span> <span class="n">offSet</span><span class="p">);</span>
    <span class="n">workspaceB</span><span class="py">.canvas.frame</span> <span class="o">=</span> <span class="n">workspaceA</span><span class="py">.canvas.frame</span><span class="p">;</span>
    <span class="n">workspaceC</span><span class="py">.canvas.frame</span> <span class="o">=</span> <span class="n">workspaceB</span><span class="py">.canvas.frame</span><span class="p">;</span>

    <span class="p">[</span><span class="n">workspaceA</span> <span class="n">setup</span><span class="p">];</span>
    <span class="p">[</span><span class="n">workspaceB</span> <span class="n">setup</span><span class="p">];</span>
    <span class="p">[</span><span class="n">workspaceC</span> <span class="n">setup</span><span class="p">];</span>

    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addSubview:workspaceA</span><span class="py">.canvas</span><span class="p">];</span>
    <span class="n">currentView</span> <span class="o">=</span> <span class="p">(</span><span class="n-ProjectClass">C4View</span> <span class="o">*</span><span class="p">)</span><span class="n">workspaceA</span><span class="py">.canvas</span><span class="p">;</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method initializes the 3 workspaces, creates a bit of an offset and then sets their frames. It then runs <tt>setup</tt> on all three and then finishes off by adding only the <strong>canvas</strong> of <tt>WorkSpaceA</tt> to our main canvas. We don&#8217;t need to add the other ones because we&#8217;re going to use some fancy <tt>UIView</tt> switching a little bit later.</p></div>
<div class="paragraph"><p>Finally, add the following line to the main workspace&#8217;s <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">createWorkSpaces</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_create_toolbar">5.4. Create ToolBar</h3>
<div class="paragraph"><p>The next thing we&#8217;re going to do is create a <tt>UIToolBar</tt> object that will have 3 buttons. Each Button will link to a method that will do some switching between the current view and one of the other workspaces.</p></div>
<div class="paragraph"><p>Create a method called that will set up a tool bar for us, like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">createToolBar</span> <span class="p">{</span>
    <span class="nc">CGRect</span> <span class="n">toolBarFrame</span> <span class="o">=</span> <span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.height</span> <span class="o">-</span> <span class="mi">44</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.width</span><span class="p">,</span> <span class="mi">44</span><span class="p">);</span>
    <span class="nc">UIToolbar</span> <span class="o">*</span><span class="n">toolBar</span> <span class="o">=</span> <span class="p">[[</span><span class="nc">UIToolbar</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithFrame:toolBarFrame</span><span class="p">];</span>
    <span class="n">toolBar</span><span class="py">.barStyle</span> <span class="o">=</span> <span class="nc">UIBarStyleBlackTranslucent</span><span class="p">;</span>
    <span class="c1">//....</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This step creates a frame for the tool bar that is 44 points high, the width of the canvas, and positioned at the bottom of the canvas. It also gives the tool bar a translucent style.</p></div>
<div class="paragraph"><p>Next, we&#8217;re going to add a bunch of button items to the toolbar. Specifically, we&#8217;re going to add 3 buttons sandwiched between 2 invisible flexible items. This sandwiching will center the 3 buttons for us.</p></div>
<div class="paragraph"><p>Add the following code to the <tt>createToolBar</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="nc">UIBarButtonItem</span> <span class="o">*</span><span class="n">flexible</span> <span class="o">=</span> <span class="p">[[</span><span class="nc">UIBarButtonItem</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithBarButtonSystemItem:</span><span class="nc">UIBarButtonSystemItemFlexibleSpace</span>
                                                                          <span class="n">target:</span><span class="nb">nil</span>
                                                                          <span class="n">action:</span><span class="nb">nil</span><span class="p">];</span>

<span class="nc">UIBarButtonItem</span> <span class="o">*</span><span class="n">b1</span> <span class="o">=</span> <span class="p">[[</span><span class="nc">UIBarButtonItem</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithTitle:</span><span class="s">@&quot;WorkSpace A&quot;</span>
                                                       <span class="n">style:</span><span class="nc">UIBarButtonItemStyleBordered</span>
                                                      <span class="n">target:</span><span class="k">self</span>
                                                      <span class="n">action:</span><span class="k">@selector</span><span class="p">(</span><span class="n">switchToA</span><span class="p">)];</span>
<span class="nc">UIBarButtonItem</span> <span class="o">*</span><span class="n">b2</span> <span class="o">=</span> <span class="p">[[</span><span class="nc">UIBarButtonItem</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithTitle:</span><span class="s">@&quot;WorkSpace B&quot;</span>
                                                       <span class="n">style:</span><span class="nc">UIBarButtonItemStyleBordered</span>
                                                      <span class="n">target:</span><span class="k">self</span>
                                                      <span class="n">action:</span><span class="k">@selector</span><span class="p">(</span><span class="n">switchToB</span><span class="p">)];</span>
<span class="nc">UIBarButtonItem</span> <span class="o">*</span><span class="n">b3</span> <span class="o">=</span> <span class="p">[[</span><span class="nc">UIBarButtonItem</span> <span class="n">alloc</span><span class="p">]</span> <span class="n">initWithTitle:</span><span class="s">@&quot;WorkSpace C&quot;</span>
                                                       <span class="n">style:</span><span class="nc">UIBarButtonItemStyleBordered</span>
                                                      <span class="n">target:</span><span class="k">self</span>
                                                      <span class="n">action:</span><span class="k">@selector</span><span class="p">(</span><span class="n">switchToC</span><span class="p">)];</span>
</pre></div></div></div>
<div class="paragraph"><p>So, we&#8217;ve created a flexible invisible item and 3 buttosn. Each button is named for one of the workspaces and is given an action that will trigger switching.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">See how the action is specified as <tt>@selector(...)</tt>? This is the standard way of dynamically passing a method as code. Here is a good link for learning about  <a href="https://developer.apple.com/library/ios/#documentation/General/Conceptual/DevPedia-CocoaCore/Selector.html"><tt>@selector</tt> and <tt>SEL</tt></a></td>
</tr></table>
</div>
<div class="paragraph"><p>Next, we&#8217;re going to add all these elements to the toolbar. Even though we have created only 1 <tt>flexible</tt> object, we can use it twice. Add all the elements to the toolbar, then add the toolbar to the canvas like so:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">toolBar</span> <span class="n">setItems:</span><span class="err">@</span><span class="p">[</span><span class="n">flexible</span><span class="p">,</span> <span class="n">b1</span><span class="p">,</span> <span class="n">b2</span><span class="p">,</span> <span class="n">b3</span><span class="p">,</span> <span class="n">flexible</span><span class="p">]];</span>

<span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addSubview:toolBar</span><span class="p">];</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_switching_views">6. Switching Views</h2>
<div class="sectionbody">
<div class="paragraph"><p>Even though this is still part of the <tt>C4WorkSpace</tt>, I&#8217;ve made this part of the tutorial its own section because we&#8217;re doing some interesting things that need more explanation. Now that we&#8217;ve created out buttons and assigned some actions, it&#8217;s time to actually build the methods that we&#8217;re referring to.</p></div>
<div class="paragraph"><p>We&#8217;re going to construct a single <tt>switchToView:transitionOptions:</tt> method that handles the actual switching of views. Then, we&#8217;ll create 3 methods that use this method in different ways. The 3 methods will be those that are triggered by the buttons in our toolbar.</p></div>
<div class="sect2">
<h3 id="_the_actual_switch">6.1. The ACTUAL Switch</h3>
<div class="paragraph"><p>Let&#8217;s start by constructing the actual switch. Add the following method to your class:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">switchToView:</span><span class="p">(</span><span class="n-ProjectClass">C4View</span> <span class="o">*</span><span class="p">)</span><span class="n">view</span> <span class="n">transitionOptions:</span><span class="p">(</span><span class="nc">UIViewAnimationOptions</span><span class="p">)</span><span class="n">options</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method is going to take a single <tt>C4View</tt> and a set of <tt>UIViewAnimationOptions</tt> as arguments. We&#8217;re using this strange-looking options type to specify <em>how we want our views to switch</em>. This is the raw way of specifying how we want our animations to look.</p></div>
<div class="paragraph"><p>Let&#8217;s keep moving&#8230;</p></div>
<div class="paragraph"><p>We want to make sure that we&#8217;re not switching between the same view. That means that we don&#8217;t want to trigger animations or run any code in the following cases:</p></div>
<div class="ulist"><ul>
<li>
<p>
<tt>WorkSpaceA</tt> is visible and we hit the <tt>WorkSpaceA</tt> button
</p>
</li>
<li>
<p>
<tt>WorkSpaceB</tt> is visible and we hit the <tt>WorkSpaceB</tt> button
</p>
</li>
<li>
<p>
<tt>WorkSpaceC</tt> is visible and we hit the <tt>WorkSpaceC</tt> button
</p>
</li>
</ul></div>
<div class="paragraph"><p>Pretty straightforward right? Add the following <tt>if</tt> statement to your method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="p">[</span><span class="n">currentView</span> <span class="n">isEqual:view</span><span class="p">])</span> <span class="p">{</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This statement will prevent anything from happening if the current view is visible and we touch the current view&#8217;s button.</p></div>
<div class="paragraph"><p>Now we&#8217;re going to get our hands dirty with <tt>UIView</tt> animations and blocks. Add the following to the inside of that <tt>if</tt> statement:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="nc">UIView</span> <span class="n">transitionFromView:currentView</span>
                    <span class="n">toView:view</span>
                  <span class="n">duration:</span><span class="mf">0.75f</span>
                   <span class="n">options:options</span>
                <span class="n">completion:</span><span class="o">^</span><span class="p">(</span><span class="kt">BOOL</span> <span class="n">finished</span><span class="p">)</span> <span class="p">{</span>
    <span class="n">currentView</span> <span class="o">=</span> <span class="n">view</span><span class="p">;</span>
    <span class="n">finished</span> <span class="o">=</span> <span class="nb">YES</span><span class="p">;</span>
<span class="p">}];</span>
</pre></div></div></div>
<div class="paragraph"><p>There is this neat <tt>transitionFromView:toView:</tt> method in the <tt>UIView</tt> class. We use it and pass it 5 things:</p></div>
<div class="ulist"><ul>
<li>
<p>
A view to switch <strong>from</strong>
</p>
</li>
<li>
<p>
A view to switch <strong>to</strong>
</p>
</li>
<li>
<p>
A duration for the animation
</p>
</li>
<li>
<p>
A set of animation options
</p>
</li>
<li>
<p>
A <strong>block</strong> of code to execute for the animation
</p>
</li>
</ul></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">The <tt>block</tt> part of this might seem a bit strange to you so I&#8217;ll try to briefly explain it&#8230; Blocks are methods that can be passed around like objects and variables. In the same way as you use a * to mark an object (e.g. C4View *v;) you use a ^ to mark a block. In a <tt>UIView</tt> animation, all the code that is inside the <tt>block</tt> will be executed in the animation that you&#8217;re creating.</td>
</tr></table>
</div>
</div>
<div class="sect2">
<h3 id="_the_switch_methods">6.2. The Switch Methods</h3>
<div class="paragraph"><p>There are 3 switch methods that create animation options and then apply them to the transition from the current view to that of whichever button was pressed.</p></div>
<div class="paragraph"><p>For switching to <tt>WorkSpaceA</tt>, add the following method to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">switchToA</span> <span class="p">{</span>
    <span class="nc">UIViewAnimationOptions</span> <span class="n">options</span> <span class="o">=</span> <span class="nc">UIViewAnimationOptionTransitionFlipFromLeft</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">switchToView:</span><span class="p">(</span><span class="n-ProjectClass">C4View</span><span class="o">*</span><span class="p">)</span><span class="n">workspaceA</span><span class="py">.canvas</span> <span class="n">transitionOptions:options</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This specifies that when we switch to <tt>WorkSpaceA</tt> we&#8217;re going to see the view <strong>flip in from the left</strong>.</p></div>
<div class="paragraph"><p>For switching to <tt>WorkSpaceB</tt>, add the following method to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">switchToB</span> <span class="p">{</span>
    <span class="nc">UIViewAnimationOptions</span> <span class="n">options</span> <span class="o">=</span> <span class="nc">UIViewAnimationOptionTransitionCurlDown</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">switchToView:</span><span class="p">(</span><span class="n-ProjectClass">C4View</span><span class="o">*</span><span class="p">)</span><span class="n">workspaceB</span><span class="py">.canvas</span> <span class="n">transitionOptions:options</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This specifies that when we switch to <tt>WorkSpaceB</tt> we&#8217;re going to see the view <strong>curl down from the top</strong>.</p></div>
<div class="paragraph"><p>For switching to <tt>WorkSpaceC</tt>, add the following method to your workspace:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">switchToC</span> <span class="p">{</span>
    <span class="nc">UIViewAnimationOptions</span> <span class="n">options</span> <span class="o">=</span> <span class="nc">UIViewAnimationOptionTransitionCrossDissolve</span><span class="p">;</span>
    <span class="p">[</span><span class="k">self</span> <span class="n">switchToView:</span><span class="p">(</span><span class="n-ProjectClass">C4View</span><span class="o">*</span><span class="p">)</span><span class="n">workspaceC</span><span class="py">.canvas</span> <span class="n">transitionOptions:options</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This specifies that when we switch to <tt>WorkSpaceB</tt> we&#8217;re going to see the view <strong>cross dissolve</strong>.</p></div>
<div class="paragraph"><p>Now run it and go and click all the buttons and canvases.</p></div>
<div class="paragraph"><p>C&#8217;est Tout.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">7. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>We just worked through an advanced tutorial that uses subclasses of <tt>C4CanvasController</tt> to create an application that has multiple workspaces and canvases. We added 3 workspaces to our app and switched between them using <tt>UIView</tt> transition animations. The control of the application used buttons nested (and centered) in a <tt>UIToolBar</tt> object. Finally, we had 2 canvases with interactive elements on them, and a third canvas that had smaller versions of the first two inside of it.</p></div>
<div class="paragraph"><p>I wanted to say something witty about too many canvases, but when I searched for images to use in my wit-making, I only found these: <a href="multiCanvas/tooManyCanvases1.jpg">image</a>, <a href="multiCanvas/tooManyCanvases2.jpg">image</a>, and <a href="multiCanvas/tooManyCanvases3.jpeg">image</a>&#8230;</p></div>
<div class="paragraph"><p>I&#8217;m speechless.</p></div>
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
