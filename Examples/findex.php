<?php
// Include WordPress
define('WP_USE_THEMES', false);
+$root = realpath($_SERVER["DOCUMENT_ROOT"]);
+include "$root/wp-load.php";get_header();
?>

<link rel="stylesheet" href="/css/c4tutorial.css" type="text/css" />
<link rel="stylesheet" href="/css/pygments.css" type="text/css" />


<div class="row">
	<div class="span12 intro">
		<h2>Examples</h2>
		<div class="span9">
			<div id="preamble">
				<div class="paragraph">
					<p>Here's a pretty huge list of examples for you (right now we're at 215) to dive into and learn about C4. If you are new to this site, and haven't worked with our examples very much, we suggest you pop over and have a look at the <a href="http://www.cocoaforartists.org/tutorials/readingExamples">Reading Examples</a> tutorial.
					</p>
				</div>
			</div>
		</div> 
	</div>
</div>

<div class="row">
	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Shapes</h3>
		<ul>
			<li><a href="rectanglesEllipses">Rectangles & Ellipses</a></li>
			<li><a href="linesPolygons">Lines, Triangles & Polygons</a></li>
			<li><a href="bezierQuadCurves">Bezier & Quadratic Curves</a></li>
			<li><a href="arcsWedges">Arcs & Wedges</a></li>
			<li><a href="textShapes">Text Shapes</a></li>
			<li><a href="fillStroke">Fill & Stroke</a></li>
			<li><a href="fillRule">Fill Rule</a></li>
			<li><a href="lineWidth">Line Width</a></li>
			<li><a href="lineEndPoints">Line End Points</a></li>
			<li><a href="lineCap">Line Cap</a></li>
			<li><a href="lineJoin">Line Join</a></li>
		</ul>
	</div>

	<div class="span2">		
		<h3 style="margin-bottom: 12px;">...</h3>
		<ul>
			<li><a href="strokeStartEndAnimated">Stroke Start/End</a></li>
			<li><a href="lineDashPattern">Dash Pattern</a></li>
			<li><a href="lineDashPhase">Dash Phase</a></li>
			<li><a href="lineDashPhase1.php">Dash Phase Animated <em>(1)</em></a></li>
			<li><a href="lineDashPhase2.php">Dash Phase Animated <em>(2)</em></a></li>
			<li><a href="lineDashPhase3.php">Dash Phase Animated <em>(3)</em></a></li>
			<li><a href="shapeMorphing.php">Morphing Between Shapes</a></li>
			<li><a href="lineWidthAdvanced">Advanced Line Width</a></li>
			<li><a href="strokeStartEndAdvanced">Advanced Stroke Start/End</a></li>
			<li><a href="textShapesAdvanced">Advanced Text Shapes</a></li>
			<li><a href="bezierQuadCurvesInteractive.php">Interactive Curves</a></li>
		</ul>
	</div>

	<div class="span2">
		<h3 style="margin-bottom: 12px;">Images</h3>
		<ul>
			<li><a href="imageNamed">Create an Image</a></li>
			<li><a href="imageWidth">Image Width</a></li>
			<li><a href="imageHeight">Image Height</a></li>
			<li><a href="imageFrame">An Image's Frame</a></li>
			<li><a href="imageProperty.php">The Image Property</a></li>
			<li><a href="imageWithImage">Copying an Image</a></li>
			<li><a href="imageRawData">From Scratch</a></li>
			<li><a href="imageRawPattern">Create a Pattern</a></li>
		</ul> 
	</div>	
	
	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Movies</h3>
		<ul>
			<li><a href="movieNamed.php">Creating a Movie</a></li>
			<li><a href="movieVolume.php">Volume</a></li>
			<li><a href="movieWidth.php">Width</a></li>
			<li><a href="movieHeight.php">Height</a></li>
			<li><a href="movieFrame.php">Frame</a></li>
			<li><a href="moviePlayPause.php">Play & Pause</a></li>
			<li><a href="movieShouldAutoplay.php">Autoplay</a></li>
			<li><a href="movieLoops.php">Loops</a></li>
			<li><a href="movieRate.php">Rate</a></li>
			<li><a href="movieCurrentTimeDuration.php">Current Time & Duration</a></li>
			<li><a href="movieReachedEnd.php">Reached End</a></li>
			<li><a href="movieSeekToTime.php">Seeking</a></li>
			<li><a href="movieSeekByAddingTime.php">Seek By Adding</a></li>
		</ul>
	</div>
	
	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Labels</h3>
		<ul>
			<li><a href="labelWithText">Create w/ Text</a></li>
			<li><a href="labelTextFont">Create w/ Text, Font</a></li>
			<li><a href="labelTextFontFrame">Create w/ Text, Font, Frame</a></li>
			<li><a href="labelText">A Label's Text'</a></li>
			<li><a href="labelSizeToFit">[sizeToFit]</a></li>
			<li><a href="labelFont">Font</a></li>
			<li><a href="labelWidth">Width</a></li>
			<li><a href="labelHeight">Height</a></li>
			<li><a href="labelTextColor">Text Color</a></li>
			<li><a href="labelTextShadow">Text Shadow</a></li>
			<li><a href="labelBackgroundColor">Background Color</a></li>
		</ul> 
	</div>

	<div class="span2">		
		<h3 style="margin-bottom: 12px;">...</h3>
		<ul>
			<li><a href="labelHighlightColor.php">Highlight Color</a></li>
			<li><a href="labelTextHighlighting">Highlighting Text</a></li>
			<li><a href="labelTextAlignment">Text Alignment</a></li>
			<li><a href="labelNumberLines">Number of Lines</a></li>
			<li><a href="labelParagraphs">Paragraphs</a></li>
			<li><a href="labelTruncation">Truncation</a></li>
			<li><a href="labelAdjustsFontSize">Adjusts Font Size</a></li>
			<li><a href="labelCharacterClip">Clipping Chars</a></li>
			<li><a href="labelCharacterWrap">Wrapping Chars</a></li>
			<li><a href="labelBaselineAdjustment">Baseline Adjustment</a></li>
			<li><a href="labelLayerShadow">Layer Shadow</a></li>
		</ul> 
	</div>
</div>  

<div class="row">
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Colors</h3>
		<ul>
			<li><a href="colorAllTypes">All Color Types</a></li>
			<li><a href="C4Colors">C4 Colors</a></li>
			<li><a href="colorPredefined">Predefined Colors</a></li>
			<li><a href="colorRGBA">RGB(A) Colors</a></li>
			<li><a href="colorHSBA">HSB(A) Colors</a></li>
			<li><a href="colorSystem">System Colors</a></li>
			<li><a href="colorWithAlpha">Transparent Colors</a></li>
			<li><a href="colorPatternImage">Pattern Images as Colors</a></li>
			<li><a href="colorGrabValues">Grabbing Color Values</a></li>
		</ul> 
	</div>	

	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Fonts</h3>
		<ul>
			<li><a href="fontWithSize">Creating a Font</a></li>
			<li><a href="fontSystem">System Fonts</a></li>
			<li><a href="fontProperties">Font Properties</a></li>
			<li><a href="fontAndFamilyConsole">Font Families <em>Console</em></a></li>
			<li><a href="fontFamilyLabels">Font Families <em>Labels</em></a></li>
			<li><a href="fontFamilyConsole">Fonts/Font Families <em>Console</em></a></li>
			<li><a href="fontAndFamilyLabels">Fonts/Font Families <em>Labels</em></a></li>
		</ul> 
	</div>

	<div class="span2">		
		<h3 style="margin-bottom: 12px;">OpenGL</h3>
		<ul>
			<li><a href="glBasic">Basic GL</a></li>
			<li><a href="glAnimation.php">Animation</a></li>
			<li><a href="glDrawOnce.php">Draw Once</a></li>
			<li><a href="glFrameInterval">Frame Interval</a></li>
		</ul> 
	</div>

	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Timers</h3>
		<ul>
			<li><a href="timerAutomatic.php">Automatic Timers</a></li>
			<li><a href="timerStartStop.php">Start & Stop</a></li>
			<li><a href="timerMultiple.php">Multiple Timers</a></li>
		</ul>
	</div>
	
	<div class="span2">		
		<h3 style="margin-bottom: 12px;">Misc.</h3>
		<ul>
			<li><a href="helloC4">Hello C4</a></li>
		</ul>
	</div>
</div>    
<div class="row">
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Math</h3>
		<ul>
			<li><a href="abs.php">abs</a></li>
			<li><a href="acos.php">acos</a></li>
			<li><a href="asin.php">asin</a></li>
			<li><a href="atan.php">atan</a></li>
			<li><a href="arctangent.php">atan2</a></li>
			<li><a href="ceil.php">ceil</a></li>
			<li><a href="constrain.php">constrain</a></li>
			<li><a href="cos.php">cos</a></li>
			<li><a href="floor.php">floor</a></li>
			<li><a href="map.php">map</a></li>
			<li><a href="max.php">max</a></li>
			<li><a href="min.php">min</a></li>
			<li><a href="round.php">round</a></li>
			<li><a href="sin.php">sin</a></li>
			<li><a href="tan.php">tan</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Audio Samples</h3>
		<ul>
			<li><a href="play.php">play</a></li>
			<li><a href="pause.php">pause</a></li>
			<li><a href="isPlaying.php">isPlaying</a></li>
			<li><a href="endedNormally.php">endedNormally</a></li>
			<li><a href="duration.php">duration</a></li>
			<li><a href="rate.php">rate</a></li>
			<li><a href="loops.php">loops</a></li>
			<li><a href="numberOfLoops.php">numberOfLoops</a></li>
			<li><a href="volume.php">volume</a></li>
			<li><a href="pan.php">pan</a></li>
			<li><a href="playAtTime.php">playAtTime</a></li>
			<li><a href="currentTimeSimple.php">currentTime</a> <i>(1)</i></li>
			<li><a href="currentTimeBoth.php">currentTime</a> <i>(2)</i></li>
			<li><a href="metering.php">metering</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">C4Control</h3>
		<ul>
			<li><a href="alpha.php">alpha</a></li>
			<li><a href="backgroundColor.php">backgroundColor</a></li>
			<li><a href="borderColor.php">borderColor</a></li>
			<li><a href="borderWidth.php">borderWidth</a></li>
			<li><a href="center.php">center</a></li>
			<li><a href="cornerRadius.php">cornerRadius</a></li>
			<li><a href="frame.php">frame</a></li>
			<li><a href="imageConstrainsProportions.php">constrainsProportions</a></li>
			<li><a href="imageHeightWidth.php">height / width</a> <i>(images)</i></li>
			<li><a href="masksToBounds.php">masksToBounds</a></li>
			<li><a href="move.php">move</a></li>
			<li><a href="origin.php">origin</a></li>
			<li><a href="perspectiveDistance.php">perspectiveDistance</a></li>
			<li><a href="remove.php">remove</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">...</h3>
		<ul>
			<li><a href="rotation.php">rotation</a></li>
			<li><a href="transformRotation.php">rotation</a> <i>(transform)</i></li>
			<li><a href="shadowColor.php">shadowColor</a></li>
			<li><a href="shadowOffset.php">shadowOffset</a></li>
			<li><a href="shadowOpacity.php">shadowOpacity</a></li>
			<li><a href="shadowPath.php">shadowPath</a></li>
			<li><a href="shadowRadius.php">shadowRadius</a></li>
			<li><a href="transformScale.php">scale</a> <i>(transform)</i></li>
			<li><a href="transformTranslate.php">translate (transform)</a></li>
			<li><a href="zPosition.php">zPosition</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Image Filters</h3>
		<ul>
			<li><a href="additionComposite.php">additionComposite</a></li>
			<li><a href="colorBlend.php">colorBlend</a></li>
			<li><a href="colorBurn.php">colorBurn</a></li>
			<li><a href="colorControlSaturation.php">colorControlSaturation</a></li>
			<li><a href="colorInvert.php">colorInvert</a></li>
			<li><a href="colorMatrix.php">colorMatrix</a></li>
			<li><a href="darkenBlend.php">darkenBlend</a></li>
			<li><a href="differenceBlend.php">differenceBlend</a></li>
			<li><a href="exclusionBlend.php">exclusionBlend</a></li>
			<li><a href="exposureAdjust.php">exposureAdjust</a></li>
			<li><a href="gammaAdjustment.php">gammaAdjustment</a></li>
			<li><a href="hardLightBlend.php">hardLightBlend</a></li>
			<li><a href="highlightShadowAdjust.php">highlightShadowAdjust</a></li>
			<li><a href="hueAdjust.php">hueAdjust</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">...</h3>
		<ul>
			<li><a href="hueBlend.php">hueBlend</a></li>
			<li><a href="lightenBlend.php">lightenBlend</a></li>
			<li><a href="luminosityBlend.php">luminosityBlend</a></li>
			<li><a href="maximumComposite.php">maximumComposite</a></li>
			<li><a href="minimumComposite.php">minimumComposite</a></li>
			<li><a href="multiplyBlend.php">multiplyBlend</a></li>
			<li><a href="multiplyComposite.php">multiplyComposite</a></li>
			<li><a href="overlayBlend.php">overlayBlend</a></li>
			<li><a href="saturationBlend.php">saturationBlend</a></li>
			<li><a href="screenBlend.php">screenBlend</a></li>
			<li><a href="sepiaTone.php">sepiaTone</a></li>
			<li><a href="softLightBlend.php">softLightBlend</a></li>
			<li><a href="straighten.php">straighten</a></li>
			<li><a href="temperatureAndTint.php">temperatureAndTint</a></li>
		</ul>
	</div>
</div>
<div class="row">
	<div class="span2">
		<h3 style="margin-bottom: 12px;">User Interface</h3>
		<ul>
			<li><a href="buttonAction.php">buttonAction</a></li>
			<li><a href="buttonSetup.php">buttonSetup</a></li>
			<li><a href="scrollViewImage.php">scrollViewImage</a></li>
			<li><a href="scrollViewLabel.php">scrollViewLabel</a></li>
			<li><a href="sliderActionTouch.php">sliderActionTouch</a></li>
			<li><a href="sliderActionValue.php">sliderActionValue</a></li>
			<li><a href="sliderSetup.php">sliderSetup</a></li>
			<li><a href="stepperAction.php">stepperAction</a></li>
			<li><a href="stepperSetup.php">stepperSetup</a></li>
			<li><a href="switchAction.php">switchAction</a></li>
			<li><a href="switchSetup.php">switchSetup</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Interaction</h3>
		<ul>
			<li><a href="listenFor.php">listenFor</a></li>
			<li><a href="listenForFromObject.php">listenForFromObject</a></li>
			<li><a href="longpress.php">longPress</a></li>
			<li><a href="longpressAdvanced.php">longPress</a> <i>(Advanced)</i></li>
			<li><a href="maskingImages.php">masking</a> <i>(with images)</i></li>
			<li><a href="maskingShapes.php">masking</a> <i>(with shapes)</i></li>
			<li><a href="maskingImagesAnimated.php">masking</a> <i>(animated images)</i></li>
			<li><a href="maskingShapesAnimated.php">masking</a> <i>(animated shapes)</i></li>
			<li><a href="maskingShapesSubview.php">masking</a> <i>(subviews)</i></li>
			<li><a href="maskingWithAnimatedImages.php">animatedImage masking</a></li>
			<li><a href="maximumNumberOfTouches.php">maximumNumberOfTouches</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">...</h3>
		<ul>
			<li><a href="minimumNumberOfTouches.php">minimumNumberOfTouches</a></li>
			<li><a href="minimumPressDuration.php">minimumPressDuration</a></li>
			<li><a href="moveAndPan.php">moveAndPan</a></li>
			<li><a href="numberOfTapsRequired.php">numberOfTapsRequired</a></li>
			<li><a href="numberOfTouchesRequired.php">numberOfTouchesRequired</a></li>
			<li><a href="runMethod.php">runMethod</a></li>
			<li><a href="runMethodWithObject.php">runMethodWithObject</a></li>
			<li><a href="swipe.php">swipe</a></li>
			<li><a href="tap.php">tap</a></li>
			<li><a href="touchesBegan.php">touchesBegan</a></li>
			<li><a href="touchesBeganWithEvent.php">touchesBeganWithEvent</a></li>
			</ul>
		</div>
		<div class="span2">
			<h3 style="margin-bottom: 12px;">...</h3>
			<ul>
			<li><a href="touchesEnded.php">touchesEnded</a></li>
			<li><a href="touchesEndedWithEvent.php">touchesEndedWithEvent</a></li>
			<li><a href="touchesMoved.php">touchesMoved</a></li>
			<li><a href="touchesMovedWithEvent.php">touchesMovedWithEvent</a></li>
		</ul>
	</div>
	<div class="span2">
		<h3 style="margin-bottom: 12px;">Advanced</h3>
		<ul>
			<li><a href="arcs.php">arc diagrams</a></li>
			<li><a href="lineTouchPoint.php">line - touch - point</a></li>
			<li><a href="phasing.php">phasing</a></li>
			<li><a href="probability.php">probability</a></li>
			<li><a href="randomMovementSimple.php">randomMovement</a> <i>(1)</i></li>
			<li><a href="randomMovementCircle.php">randomMovement</a> <i>(2)</i></li>
			<li><a href="randomMovementMask.php">randomMovement</a> <i>(3)</i></li>
			<li><a href="randomMovementSubview.php">randomMovement</a> <i>(4)</i></li>
		</ul>
	</div>
</div>
<?php get_footer() ?>