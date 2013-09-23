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

<h2>Taking Snapshots</h2>
<span id="author">Written by: <a href="mailto:tutorials@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/C4Tutorials/5399635" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/64685220" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>It&#8217;s time for a photoshoot. In this tutorial I&#8217;ll show you how to access the cameras on your devices, switch between them and capture images so that you can play around with them.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_getting_a_camera">1. Getting A Camera</h2>
<div class="sectionbody">
<div class="paragraph"><p>There are two cameras on the devices you&#8217;ll be building for, the front (which is on the same side of the screen) and the back. When you create a <tt>C4Camera</tt> it defaults to the front. Let&#8217;s get one up and running.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">setup</span> <span class="p">{</span>
    <span class="n">cam</span> <span class="o">=</span> <span class="p">[</span><span class="n-ProjectClass">C4Camera</span> <span class="n">cameraWithFrame:</span><span class="nc">CGRectMake</span><span class="p">(</span><span class="mi">0</span><span class="p">,</span> <span class="mi">0</span><span class="p">,</span> <span class="mi">240</span><span class="p">,</span> <span class="mi">320</span><span class="p">)];</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addCamera:cam</span><span class="p">];</span>
    <span class="p">[</span><span class="n">cam</span> <span class="n">initCapture</span><span class="p">];</span>
    <span class="c1">//...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>That&#8217;s it. 3 lines of code in your <tt>setup</tt> will place a camera on the screen. You&#8217;ll have a little frame up in the top-left of the canvas that will be capturing the view from the camera.</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">You can make the frame of the anything you want, but the standard size is <tt>4:3</tt>, or <tt>3:4</tt> in portrait mode. When you have any other ratio for your frame, the camera will fill to the width of the frame and then clip anything outside of that.</td>
</tr></table>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_capture_an_image">2. Capture An Image</h2>
<div class="sectionbody">
<div class="paragraph"><p>Capturing an image is quite easy, all you have to do is call the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="n">cam</span> <span class="n">captureImage</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>Simple.</p></div>
<div class="sect2">
<h3 id="_where_8217_s_the_image">2.1. Where&#8217;s the Image?</h3>
<div class="paragraph"><p>Doing something with the image is a little trickier actually. The reason for this is <strong>time</strong>. Let&#8217;s say we set up some code that looks like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">touchesBegan</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">cam</span> <span class="n">captureImage</span><span class="p">];</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">img</span> <span class="o">=</span> <span class="n">cam</span><span class="py">.capturedImage</span><span class="p">;</span>
    <span class="c1">//do something with img...</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>NOTHING WILL HAPPEN!!!</p></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/tip.png" alt="Tip" />
</td>
<td class="content">Don&#8217;t add the code above to your project.</td>
</tr></table>
</div>
<div class="paragraph"><p>The reason for this is that the second line of code (i.e. <tt>cam.capturedImage</tt>) will often happen too soon. The camera takes some time to register the image it&#8217;s capturing, convert it into the right format and create a <tt>C4Image</tt> that you can then access. It won&#8217;t take long (a few hundredths of a second) but it will definitely take longer than the time it takes to run the next line of code (which is like milliseconds).</p></div>
<div class="paragraph"><p>Instead, you have to <strong>listen</strong> for when the image is ready to be used.</p></div>
</div>
<div class="sect2">
<h3 id="_here_8217_s_how">2.2. Here&#8217;s How</h3>
<div class="paragraph"><p>Let&#8217;s use a tap gesture to get our camera taking snapshots, put the following after the <tt>[self styleCamera];</tt> in your setup:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">addGesture:TAP</span> <span class="n">name:</span><span class="s">@&quot;capture&quot;</span> <span class="n">action:</span><span class="s">@&quot;captureImage&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:</span><span class="mi">1</span> <span class="n">forGesture:</span><span class="s">@&quot;capture&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>We&#8217;ve set up a single tap gesture on the canvas that will trigger a method that will trigger <tt>[cam captureImage]</tt>. We make sure that the gesture will only take 1 tap to trigger, and no more because we&#8217;re going to use another <tt>TAP</tt> gesture later.</p></div>
<div class="paragraph"><p>Now, build the following method outside of <tt>setup</tt> so that the gesture will trigger it:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">captureImage</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">cam</span> <span class="n">captureImage</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Easy. If you run the app now, and tap the canvas you&#8217;ll hear the camera taking a snapshot.</p></div>
</div>
<div class="sect2">
<h3 id="_grabbing_the_image">2.3. Grabbing the Image</h3>
<div class="paragraph"><p>I mentioned earlier that you have to <strong>listen</strong> for when the image is ready and then go get it. Add the following to your project after the capture gesture.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">listenFor:</span><span class="s">@&quot;imageWasCaptured&quot;</span> <span class="n">fromObject:cam</span> <span class="n">andRunMethod:</span><span class="s">@&quot;putCapturedImageOnCanvas&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>What we do here, is listen for when the image is ready and then run a method that will do something with that image. Now, add the following method to your project outside of the <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">putCapturedImageOnCanvas</span> <span class="p">{</span>
    <span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="n">img</span> <span class="o">=</span> <span class="n">cam</span><span class="py">.capturedImage</span><span class="p">;</span>
    <span class="n">img</span><span class="py">.width</span> <span class="o">=</span> <span class="mf">240.0f</span><span class="p">;</span>
    <span class="n">img</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.width</span> <span class="o">*</span> <span class="mi">2</span> <span class="o">/</span> <span class="mi">3</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span><span class="p">);</span>
    <span class="p">[</span><span class="k">self</span><span class="py">.canvas</span> <span class="n">addImage:img</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method will grab the current captured image from the camera, resize it and add it to the canvas.</p></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_switching_cameras">3. Switching Cameras</h2>
<div class="sectionbody">
<div class="paragraph"><p>Switching from the front to the back camera is a cinch. To do so, we&#8217;re going to use the same trick as we did for capturing the image. That is, we&#8217;re going to set up a gesture that will trigger the camera to switch positions. Add the following to your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">addGesture:TAP</span> <span class="n">name:</span><span class="s">@&quot;frontBack&quot;</span> <span class="n">action:</span><span class="s">@&quot;switchFrontBack&quot;</span><span class="p">];</span>
<span class="p">[</span><span class="k">self</span> <span class="n">numberOfTouchesRequired:</span><span class="mi">2</span> <span class="n">forGesture:</span><span class="s">@&quot;frontBack&quot;</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>This is the reason why we specified the number of taps for our <tt>@"captureImage"</tt> gesture, we have a second <tt>TAP</tt> gesture that now takes 2 touches to trigger. When it does, it runs the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">switchFrontBack</span> <span class="p">{</span>
    <span class="k">if</span><span class="p">(</span><span class="n">cam</span><span class="py">.cameraPosition</span> <span class="o">==</span> <span class="n">CAMERAFRONT</span> <span class="o">||</span> <span class="n">cam</span><span class="py">.cameraPosition</span> <span class="o">==</span> <span class="n">CAMERAUNSPECIFIED</span><span class="p">)</span> <span class="p">{</span>
        <span class="n">cam</span><span class="py">.cameraPosition</span> <span class="o">=</span> <span class="n">CAMERABACK</span><span class="p">;</span>
    <span class="p">}</span> <span class="k">else</span> <span class="p">{</span>
        <span class="n">cam</span><span class="py">.cameraPosition</span> <span class="o">=</span> <span class="n">CAMERAFRONT</span><span class="p">;</span>
    <span class="p">}</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This method basically states that if the camera position is in the front or unspecified, the position will switch to the back camera. When its in the back it will switch to the front.</p></div>
<div class="paragraph"><p>Now, if you use a 2 finger tap on the canvas you&#8217;ll see that the camera switches (it takes about a quarter second).</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_dolling_things_up">4. Dolling Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>At this point, I was getting close to happy with this tutorial but a couple of things were bothering me&#8230; First, the camera looks really flat. Second, you can&#8217;t see any of the previous images because the new one always gets placed on top. Third, if I randomize the position of the images, the camera is actually placed <em>underneath</em> all of them and gets easily covered. Fourth, flipping the camera looks bad&#8230; It just switches.</p></div>
<div class="sect2">
<h3 id="_a_touch_of_style">4.1. A Touch of Style</h3>
<div class="paragraph"><p>To make the camera pop let&#8217;s give it a little style. We don&#8217;t <em>have</em> to do this, but we&#8217;re going to do it just to show that working with a <tt>C4Camera</tt> is the same as working with any other visual object in C4.</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">styleCamera</span> <span class="p">{</span>
    <span class="n">cam</span><span class="py">.center</span> <span class="o">=</span> <span class="nc">CGPointMake</span><span class="p">(</span><span class="k">self</span><span class="py">.canvas.width</span> <span class="o">/</span> <span class="mi">3</span><span class="p">,</span> <span class="k">self</span><span class="py">.canvas.center.y</span><span class="p">);</span>
    <span class="n">cam</span><span class="py">.borderColor</span> <span class="o">=</span> <span class="n-ProjectClass">C4GREY</span><span class="p">;</span>
    <span class="n">cam</span><span class="py">.borderWidth</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
    <span class="n">cam</span><span class="py">.shadowOpacity</span> <span class="o">=</span> <span class="mf">0.8f</span><span class="p">;</span>
    <span class="n">cam</span><span class="py">.shadowOffset</span> <span class="o">=</span> <span class="nc">CGSizeMake</span><span class="p">(</span><span class="mi">5</span><span class="p">,</span><span class="mi">5</span><span class="p">);</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>Oh, and don&#8217;t forget to add the following right after the <tt>[cam initCapture];</tt> in your <tt>setup</tt>:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">styleCamera</span><span class="p">];</span>
</pre></div></div></div>
</div>
<div class="sect2">
<h3 id="_add_interaction">4.2. Add Interaction</h3>
<div class="paragraph"><p>We&#8217;re going to add a little bit of interaction to the images by making them draggable, and giving us the ability to take them off the canvas if they&#8217;re not endearing. Add the following line of code to the <tt>putCapturedImageOnCanvas</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="p">[</span><span class="k">self</span> <span class="n">addInteraction:img</span><span class="p">];</span>
</pre></div></div></div>
<div class="paragraph"><p>&#8230;And, then create the following method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="o">-</span><span class="p">(</span><span class="kt">void</span><span class="p">)</span><span class="n">addInteraction:</span><span class="p">(</span><span class="n-ProjectClass">C4Image</span> <span class="o">*</span><span class="p">)</span><span class="n">img</span> <span class="p">{</span>
    <span class="p">[</span><span class="n">img</span> <span class="n">addGesture:PAN</span> <span class="n">name:</span><span class="s">@&quot;move&quot;</span> <span class="n">action:</span><span class="s">@&quot;move:&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="n">img</span> <span class="n">addGesture:TAP</span> <span class="n">name:</span><span class="s">@&quot;remove&quot;</span> <span class="n">action:</span><span class="s">@&quot;removeFromSuperview&quot;</span><span class="p">];</span>
    <span class="p">[</span><span class="n">img</span> <span class="n">numberOfTapsRequired:</span><span class="mi">2</span> <span class="n">forGesture:</span><span class="s">@&quot;remove&quot;</span><span class="p">];</span>
<span class="p">}</span>
</pre></div></div></div>
<div class="paragraph"><p>This makes the image movable, and whenever you do a double-tap (i.e. two consecutive single-taps) the image will disappear. And, because you run this from the <tt>putCapturedImageOnCanvas</tt> method, all the images you put on the canvas will have this functionality.</p></div>
</div>
<div class="sect2">
<h3 id="_oh_right_zpos">4.3. Oh, Right zPos</h3>
<div class="paragraph"><p>If you just tried moving any of the images you&#8217;ll have noticed that they cover the camera. Add the following to the <tt>styleCamera</tt> method:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">cam</span><span class="py">.zPosition</span> <span class="o">=</span> <span class="mi">5000</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>This makes sure that you&#8217;ll have to take at least 5000 images before one of them will be on top of the camera. Heavy-handed but effective.</p></div>
</div>
<div class="sect2">
<h3 id="_a_pretty_flip">4.4. A Pretty Flip</h3>
<div class="paragraph"><p>If you add the following code to your <tt>switchFrontBack</tt> method, and place it <strong>before</strong> the <tt>if</tt> statement, your camera will do a pretty flip as it switches:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">cam</span><span class="py">.animationDuration</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>
<span class="n">cam</span><span class="py">.perspectiveDistance</span> <span class="o">=</span> <span class="mf">500.0f</span><span class="p">;</span>
<span class="n">cam</span><span class="py">.rotationY</span> <span class="o">+=</span> <span class="n">TWO_PI</span><span class="p">;</span>
</pre></div></div></div>
</div>
</div>
</div>
<div class="sect1">
<h2 id="_wrapping_things_up">5. Wrapping Things Up</h2>
<div class="sectionbody">
<div class="paragraph"><p>This is a brief overview of how to work with <tt>C4Camera</tt>, for now it&#8217;s probably all you&#8217;ll need to get started. You might, later on, want to save some images to your Library or to your shared folder. But, this will be covered in another tutorial. For now, you know how to set up a camera, take a picture, switch camera devices and make everything look pretty and interactive.</p></div>
<div class="paragraph"><p>Enjoi.</p></div>
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
