// Last updated November 2010 by Simon Sarris
// www.simonsarris.com
// sarris@acm.org
//
// Free to use and distribute at will
// So long as you are nice to people, etc

// This is a self-executing function that I added only to stop this
// new script from interfering with the old one. It's a good idea in general, but not
// something I wanted to go over during this tutorial
(function($) {
    $.fn.myCanvas=function(options) {
    	//On définit nos paramètres par défaut
    	var canvas, ctx, WIDTH, HEIGHT, ghostcanvas, gctx,  offsetx, offsety, mx, my, stylePaddingLeft, stylePaddingTop, styleBorderLeft, styleBorderTop;
        var defaults = {
        		boxes2: [], 
        		labels: [], 
        		selectionHandles: [],
        		
        		INTERVAL: 20,  // how often, in milliseconds, we check to see if a redraw is needed
        		isDrag: false,
        		isResizeDrag: false,
        		expectResize: -1, // New, will save the # of the selection handle if the mouse is over one.
        		canvasValid: false,
        		// The node (if any) being selected.
        		// If in the future we want to select multiple objects, this will get turned into an array
        		mySel: null,
        		// The selection color and width. Right now we have a red selection with a small width
        		mySelColor: '#CC0000',
        		mySelWidth: 2,
        		mySelBoxColor: 'white', // New for selection boxes
        		mySelBoxSize: 6
        };
        var settings = $.extend({}, defaults, options);
    	// Box object to hold data
    	function BoxCanvas() {
    	  this.x = 0;
    	  this.y = 0;
    	  this.w = 1; // default width and height?
    	  this.h = 1;
    	  this.label = null;
    	  this.fill = '#444444';
    	}

    	BoxCanvas.prototype = {
    		// New methods on the Box class
    		draw: function(context, optionalColor) {
    			var Paint = {
    			        RECTANGLE_STROKE_STYLE : 'black',
    			        RECTANGLE_LINE_WIDTH : 1,
    			        VALUE_FONT : '14px Arial',
    			        VALUE_FILL_STYLE : 'white'
    			  }
    		      if (context === gctx) {
    		        context.fillStyle = 'black'; // always want black for the ghost canvas
    		      } else {
    		        context.fillStyle = this.fill;
    		      }
    		      
    		      // We can skip the drawing of elements that have moved off the screen:
    		      if (this.x > WIDTH || this.y > HEIGHT) return; 
    		      if (this.x + this.w < 0 || this.y + this.h < 0) return;
    		      
    		      context.fillRect(this.x,this.y,this.w,this.h);
    		      context.textBaseline = "middle";
    		      context.font = Paint.VALUE_FONT;
    		      context.fillStyle = Paint.VALUE_FILL_STYLE;
    		      textX = this.x+this.w/2-context.measureText(this.label).width/2;
    		      textY = this.y+this.h/2;
    		      context.fillText(this.label, textX, textY);
    		      
    		    // draw selection
    		    // this is a stroke along the box and also 8 new selection handles
    		    if (settings.mySel === this) {
    		      context.strokeStyle = settings.mySelBoxColor;
    		      context.lineWidth = settings.mySelWidth;
    		      context.strokeRect(this.x,this.y,this.w,this.h);
    		      
    		      // draw the boxes
    		      var half = settings.mySelBoxSize / 2;
    		      // top left, middle, right
    		      settings.selectionHandles[0].x = this.x-half;
    		      settings.selectionHandles[0].y = this.y-half;
    		      
    		      settings.selectionHandles[1].x = this.x+this.w/2-half;
    		      settings.selectionHandles[1].y = this.y-half;
    		      
    		      settings.selectionHandles[2].x = this.x+this.w-half;
    		      settings.selectionHandles[2].y = this.y-half;
    		      
    		      //middle left
    		      settings.selectionHandles[3].x = this.x-half;
    		      settings.selectionHandles[3].y = this.y+this.h/2-half;
    		      
    		      //middle right
    		      settings.selectionHandles[4].x = this.x+this.w-half;
    		      settings.selectionHandles[4].y = this.y+this.h/2-half;
    		      
    		      //bottom left, middle, right
    		      settings.selectionHandles[6].x = this.x+this.w/2-half;
    		      settings.selectionHandles[6].y = this.y+this.h-half;
    		      
    		      settings.selectionHandles[5].x = this.x-half;
    		      settings.selectionHandles[5].y = this.y+this.h-half;
    		      
    		      settings.selectionHandles[7].x = this.x+this.w-half;
    		      settings.selectionHandles[7].y = this.y+this.h-half;
    		
    		      
    		      context.fillStyle = settings.mySelBoxColor;
    		      for (var i = 0; i < 8; i ++) {
    		        var cur = settings.selectionHandles[i];
    		        context.fillRect(cur.x, cur.y, settings.mySelBoxSize, settings.mySelBoxSize);
    		      }
    		    }
    		} // end draw
    	}
       //Code de notre plug-in ici
    	initCanvas($(this).attr('id'));
       return {
	       	//Initialize a new Box, add it, and invalidate the canvas
	       	addRect: function(text, x, y, w, h, fill) {
	       	  var rect = new BoxCanvas;
	       	  rect.x = x;
	       	  rect.y = y;
	       	  rect.w = w
	       	  rect.h = h;
	       	  rect.fill = fill;
	       	  rect.label = text;
	       	  settings.boxes2.push(rect);
	       	  invalidate();
			}, addText: function(text, x, y, options) {
				settings.labels.push({label: text, x: x, y: y, options:options});
			}
       };

	//wipes the canvas context
	function clear(c) {
	  c.clearRect(0, 0, WIDTH, HEIGHT);
	}
		
	// Main draw loop.
	// While draw is called as often as the INTERVAL variable demands,
	// It only ever does something if the canvas gets invalidated by our code
	function mainDraw() {
	  if (settings.canvasValid == false) {
	    clear(ctx);
	    
	    // Add stuff you want drawn in the background all the time here
	    
	    // draw all boxes
	    var l = settings.boxes2.length;
	    for (var i = 0; i < l; i++) {
	    	settings.boxes2[i].draw(ctx); // we used to call drawshape, but now each box draws itself
	    }
	    for (var i = 0; i < settings.labels.length; i++) {
	    	if('rotate' in settings.labels[i].options) {
	    		ctx.rotate(settings.labels[i].options.rotate);
	    	}
	    	if('font' in settings.labels[i].options) {
	    		ctx.font = settings.labels[i].options.font;
	    	}
	    	ctx.fillText(settings.labels[i].label, settings.labels[i].x, settings.labels[i].y);
	    	ctx.restore();
	    }
	    
	    // Add stuff you want drawn on top all the time here
	    
	    settings.canvasValid = true;
	  }
	}
		
	// Happens when the mouse is moving inside the canvas
	function myMove(e){
	  if (settings.isDrag) {
	    getMouse(e);
	    
	    settings.mySel.x = mx - offsetx;
	    settings.mySel.y = my - offsety;   
	    
	    // something is changing position so we better invalidate the canvas!
	    invalidate();
	  } else if (settings.isResizeDrag) {
	    // time ro resize!
	    var oldx = settings.mySel.x;
	    var oldy = settings.mySel.y;
	    
	    switch (settings.expectResize) {
	      case 0:
	        settings.mySel.x = mx;
	        settings.mySel.y = my;
	        settings.mySel.w += oldx - mx;
	        settings.mySel.h += oldy - my;
	        break;
	      case 1:
	        settings.mySel.y = my;
	        settings.mySel.h += oldy - my;
	        break;
	      case 2:
	        settings.mySel.y = my;
	        settings.mySel.w = mx - oldx;
	        settings.mySel.h += oldy - my;
	        break;
	      case 3:
	        settings.mySel.x = mx;
	        settings.mySel.w += oldx - mx;
	        break;
	      case 4:
	        settings.mySel.w = mx - oldx;
	        break;
	      case 5:
	        settings.mySel.x = mx;
	        settings.mySel.w += oldx - mx;
	        settings.mySel.h = my - oldy;
	        break;
	      case 6:
	        settings.mySel.h = my - oldy;
	        break;
	      case 7:
	        settings.mySel.w = mx - oldx;
	        settings.mySel.h = my - oldy;
	        break;
	    }
	    
	    invalidate();
	  }
	  
	  getMouse(e);
	  // if there's a selection see if we grabbed one of the selection handles
	  if (settings.mySel !== null && !settings.isResizeDrag) {
	    for (var i = 0; i < 8; i++) {
	      var cur = settings.selectionHandles[i];
	      // we dont need to use the ghost context because
	      // selection handles will always be rectangles
	      if (mx >= cur.x && mx <= cur.x + settings.mySelBoxSize &&
	          my >= cur.y && my <= cur.y + settings.mySelBoxSize) {
	        // we found one!
	    	settings.expectResize = i;
	        invalidate();
	        
	        switch (i) {
	          case 0:
	            this.style.cursor='nw-resize';
	            break;
	          case 1:
	            this.style.cursor='n-resize';
	            break;
	          case 2:
	            this.style.cursor='ne-resize';
	            break;
	          case 3:
	            this.style.cursor='w-resize';
	            break;
	          case 4:
	            this.style.cursor='e-resize';
	            break;
	          case 5:
	            this.style.cursor='sw-resize';
	            break;
	          case 6:
	            this.style.cursor='s-resize';
	            break;
	          case 7:
	            this.style.cursor='se-resize';
	            break;
	        }
	        return;
	      }
	      
	    }
	    // not over a selection box, return to normal
	    settings.isResizeDrag = false;
	    settings.expectResize = -1;
	    this.style.cursor='auto';
	  }
	}

	// initialize our canvas, add a ghost canvas, set draw loop
	// then add everything we want to intially exist on the canvas
	function initCanvas(canvasID) {
	  canvas = document.getElementById(canvasID);
	  HEIGHT = canvas.height;
	  WIDTH = canvas.width;
	  ctx = canvas.getContext('2d');
	  ghostcanvas = document.createElement('canvas');
	  ghostcanvas.height = HEIGHT;
	  ghostcanvas.width = WIDTH;
	  gctx = ghostcanvas.getContext('2d');
	  
	  //fixes a problem where double clicking causes text to get selected on the canvas
	  canvas.onselectstart = function () { return false; }
	  
	  // fixes mouse co-ordinate problems when there's a border or padding
	  // see getMouse for more detail
	  if (document.defaultView && document.defaultView.getComputedStyle) {
	    stylePaddingLeft = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingLeft'], 10)     || 0;
	    stylePaddingTop  = parseInt(document.defaultView.getComputedStyle(canvas, null)['paddingTop'], 10)      || 0;
	    styleBorderLeft  = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderLeftWidth'], 10) || 0;
	    styleBorderTop   = parseInt(document.defaultView.getComputedStyle(canvas, null)['borderTopWidth'], 10)  || 0;
	  }
	  
	  // make mainDraw() fire every INTERVAL milliseconds
	  setInterval(mainDraw, settings.INTERVAL);
	  
	  // set our events. Up and down are for dragging,
	  // double click is for making new boxes
	  canvas.onmousedown = myDown;
	  canvas.onmouseup = myUp;
	  //canvas.ondblclick = myDblClick;
	  canvas.onmousemove = myMove;
	  
	  // set up the selection handle boxes
	  for (var i = 0; i < 8; i ++) {
	    var rect = new BoxCanvas;
	    settings.selectionHandles.push(rect);
	  }
	  
	  // add custom initialization here:
	
	  
	  // add a large green rectangle
	  //addRect(60, 70, 60, 65, 'rgba(100,100,100,0.7)');
	  
	  // add a green-blue rectangle
	  //addRect(40, 20, 40, 40, 'rgba(100,100,100,0.7)');  
	  
	  // add a smaller purple rectangle
	  //addRect(45, 60, 25, 25, 'rgba(100,100,100,0.7)');
	}
	
	// Happens when the mouse is clicked in the canvas
	function myDown(e){
	  getMouse(e);
	  
	  //we are over a selection box
	  if(settings.expectResize !== -1) {
		 settings.isResizeDrag = true;
		 return;
	  }
	  
	  clear(gctx);
	  var l = settings.boxes2.length;
	  for (var i = l-1; i >= 0; i--) {
	    // draw shape onto ghost context
	    settings.boxes2[i].draw(gctx, 'black');
	    
	    // get image data at the mouse x,y pixel
	    var imageData = gctx.getImageData(mx, my, 1, 1);
	    var index = (mx + my * imageData.width) * 4;
	    
	    // if the mouse pixel exists, select and break
	    if (imageData.data[3] > 0) {
	      settings.mySel = settings.boxes2[i];
	      offsetx = mx - settings.mySel.x;
	      offsety = my - settings.mySel.y;
	      settings.mySelBoxSize.x = mx - offsetx;
	      settings.mySel.y = my - offsety;
	      settings.isDrag = true;
	      
	      invalidate();
	      clear(gctx);
	      return;
	    }
	    
	  }
	  // havent returned means we have selected nothing
	  settings.mySel = null;
	  // clear the ghost canvas for next time
	  clear(gctx);
	  // invalidate because we might need the selection border to disappear
	  invalidate();
	}
	
	function myUp(){
	  settings.isDrag = false;
	  settings.isResizeDrag = false;
	  settings.expectResize = -1;
	}
	
	// adds a new node
	function myDblClick(e) {
	  getMouse(e);
	  // for this method width and height determine the starting X and Y, too.
	  // so I left them as vars in case someone wanted to make them args for something and copy this code
	  var width = 20;
	  var height = 20;
	  addRect(mx - (width / 2), my - (height / 2), width, height, 'rgba(220,205,65,0.7)');
	}
	
	
	function invalidate() {
	  settings.canvasValid = false;
	}
	
	// Sets mx,my to the mouse position relative to the canvas
	// unfortunately this can be tricky, we have to worry about padding and borders
	function getMouse(e) {
	      var element = canvas, offsetX = 0, offsetY = 0;
	
	      if (element.offsetParent) {
	        do {
	          offsetX += element.offsetLeft;
	          offsetY += element.offsetTop;
	        } while ((element = element.offsetParent));
	      }
	
	      // Add padding and border style widths to offset
	      offsetX += stylePaddingLeft;
	      offsetY += stylePaddingTop;
	
	      offsetX += styleBorderLeft;
	      offsetY += styleBorderTop;
	
	      mx = e.pageX - offsetX;
	      my = e.pageY - offsetY
	}
	
	// If you dont want to use <body onLoad='init()'>
	// You could uncomment this init() reference and place the script reference inside the body tag
	//init();
	//window.initCanvas = initCanvas;
    };
})(jQuery);
