$.fn.imgdata = function(key){
	return this.find('.dataImg li:eq('+key+')').text();
}
$.fn.hdata = function(key){
	return this.find('.dataSet li:eq('+key+')').text();
}

function refreshAfterSuccess(response) {
	location.reload();
}
function refreshCheckbox(checkbox, values) {
	$(checkbox).find('.ck').customInput();
} 
function clearValuesForSelect(select) {
	$(select).empty();
    $(select).not("select.no-chzn, select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectmenu({
        	style: 'dropdown', transferClasses: true, width: null
    	});
    $(select).not("not(.chzn-select)").trigger('liszt:updated')
}
function refreshSelect(select, value) {
	$(select).selectmenu("destroy").selectmenu({style: "dropdown" });
}
function refreshChosen(select, value) {
	$(select).val(value);
	$(select).trigger('liszt:updated');
}
function reloadStructure(select, value) {
	refreshSelect(select, value);
	if($(select).find('option').length==1) {
		$parent = $(select).parent();
		$parent.nextAll(':not(.last)').hide();
	}
}
function customizeCause() {
	$(this).find(".choix").iphoneStyle({checkedLabel: "Cacher", uncheckedLabel: "Afficher", labelWidth:'65px'});
	$(this).find(".section:eq(2)").customInput();
}
function setTitleParentInTdb(response) {
	var $span = $('#title_processus_in_tdb span');
	if(response.hide==1) {
		$('#btn_parent_processus').hide();
		setTimeout(function() {
			$('#title_processus_in_tdb').html('Liste des macroprocessus');
			$('#title_processus_in_tdb').append($span);
		}, 1000);
	}
}
function redirectTo(response, url) {
	location.href = url;
}
function redirectModalToUrl(response) {
	location.href = response.url;
}
function activateModals() {
	$(".modal").bind("click", function (event) {
        $("#myModal").dialog("option", {
            modal: true
        }).dialog("open");
        event.preventDefault();
    });
}
function alertAfterModalError(thrownError, target) {
	$(target + ' a[data-dismiss="modal"]').click();
	showError("Erreur lors du traitement", 3000);
	setTimeout(function() {
		showError("Merci de le signaler à l'administrateur", 3000);
	}, 4000);
}

function todoOnCompleteWithError() {
	
}

function afterSuccesFiltrer(response, idTable) {
	$('#myModal').toggle();
	$('#myModal a[data-dismiss="modal"]').click();
	$(idTable).dataTable().fnDraw();
}
function closeAndNotifyAfterSuccess(response, idTable) {
	$('#myModal').toggle();
	$('#myModal a[data-dismiss="modal"]').click();
	if(response.status=='success') {
		showSuccess(response.text, 5000);
		$(idTable).dataTable().fnDraw();
	}
}
// options cookie 10 days
var options = { path: '/', expires: 10 };

function checkButton($div) {
	if($div.find('input[type="checkbox"]').is(':checked')) {
		$("#quiz_btn_next").removeClass('disable').addClass('normal');
		$("#quiz_btn_finish").addClass('disable').removeClass('normal');
	} else {
		$("#quiz_btn_next").addClass('disable').removeClass('normal');
		$("#quiz_btn_finish").removeClass('disable').addClass('normal');
	}
	if(!$div.prev().hasClass('reserver')) {
		$("#quiz_btn_previous").addClass('disable').removeClass('normal');
	}
	if(!$div.next().hasClass('reserver')) {
		$("#quiz_btn_next").addClass('disable').removeClass('normal');
		$("#quiz_btn_finish").removeClass('disable').addClass('normal');
	}
}

function isValidForm(form) {
	if($(form).not('[novalidate="novalidate"]').length==0) {
		return true;
	}
	if($(form).not('[novalidate="novalidate"]').validationEngine()) {
		return true;
	} else {
		return false;
	}
}

function toggle(obj, show) {
	if(show) {
		obj.fadeIn(500, function(e) {$(this).show()});
	} else {
		obj.fadeOut(300, function(e) {$(this).hide()});
	}
}

$(document).ready(function() {
	$('.live_edit_form .live_edit').click(function(e) {
		if($(this).hasClass('active')==false) {
			$(this).siblings('.live_edit').each(function() {
				$(this).removeClass('active');
			});
			$('.live_edit_form .live_edit_content').slideUp(1000);
			$($(this).find('a').attr('href')).slideDown(1000);
			$(this).addClass('active');
		}
	});
	$('#quiz_btn_next').click(function(e) {
		if(!$(this).hasClass('normal')) {
			return e.preventDefault();
		}
		var $myselecdiv = $("div.reserver:visible");
		if($('div.reserver:visible input[type="checkbox"]').is(':checked')) {
			$myselecdiv.hide();
			$myselecdiv.next().show();
		} else {
			alert('non coché')
			return e.preventDefault();
		}
		checkButton($myselecdiv.next().next());
	});
	$('#quiz_btn_previous').click(function(e) {
		if(!$(this).hasClass('normal')) {
			return e.preventDefault();
		}
		var $myselecdiv = $("div.reserver:visible");
		$myselecdiv.hide();
		$myselecdiv.prev().show();
		checkButton($myselecdiv.prev().prev());
	});
	$('#header #account_info #societe_choice span').click(function() {
		if($('#header #account_info #societe_choice ul').is(":visible")) {
			$('#header #account_info #societe_choice ul').slideUp("slow");
			$('#header #account_info #societe_choice span').removeClass('scroll_up_menu');
		} else {
			$('#header #account_info #societe_choice ul').slideDown("slow");
			$('#header #account_info #societe_choice span').addClass('scroll_up_menu');
		}
	});
	$('html').click(function() {
		if($('#header #account_info #societe_choice ul').is(":visible")) {
			$('#header #account_info #societe_choice ul').slideUp("slow");
			$('#header #account_info #societe_choice span').removeClass('scroll_up_menu');
		}
	});

	$('#societe_choice').click(function(event){
	    event.stopPropagation();
	});
	
	$('#view_pa_tdb').click(function() {
		$(this).parent().parent().parent().parent().parent().slideUp('slow');
		$('#view_controle_tdb').parent().parent().parent().parent().parent().slideDown('slow');
	});
	$('#view_controle_tdb').click(function() {
		$(this).parent().parent().parent().parent().parent().slideUp('slow');
		$('#view_pa_tdb').parent().parent().parent().parent().parent().slideDown('slow');
	});
	$('#view_pa_tdb').click();
	// NewsUpdate

	// tabs
	$("ul.header.no-margin").siblings(".tab_content").find("ul.uibutton-group").css('margin-top', '-32px');
	$("ul.tabs li").fadeIn(400); 
	$("ul.tabs li:first").addClass("active").fadeIn(400); 
	$(".tab_content:first").fadeIn(); 
	$("ul.tabs li").live('click',function() {
		  $("ul.tabs li").removeClass("active");						   
		  $(this).addClass("active");  
		  var activeTab = $(this).find("a").attr("href"); 
		  $('.tab_content').fadeOut();		
		  $(activeTab).delay(400).fadeIn();		
		  ResetForm();
		  return false;
	});
	
		$('.events div.external-event').each(function() {
			var eventObject = {
				title: $.trim($(this).text()) 
			};
			$(this).data('eventObject', eventObject);
			$(this).draggable({
				zIndex: 999,
				revert: true,      
				revertDuration: 0 
			});
			
		});
	
	
	//dataTable
	$('.data_table').dataTable({
		"sDom": 'f<"clear">rt<"clear">',
		"aaSorting": [],
		"aoColumns": [ { "bSortable": false },{ "bSortable": false } ]
	});
	$('.static').dataTable({
		"sDom": '',
		"aoColumnDefs": [{ "bSortable" : false, "aTargets": ["no-sort"] }]
	});
	$('.data_table2').dataTable({
	"sDom": 'fCl<"clear">rtip',
	"sPaginationType": "full_numbers",
	 "aaSorting": []
	});
	$('.data_table3').dataTable({
	"sDom": 'fCl<"clear">rtip',
	 "aaSorting": [],
	  "aoColumns": [
			{ "bSortable": false },null,null,null,{ "bSortable": false }
	  ]
	});
	
	// input editor
	$("#editor,#editor2").cleditor();	
	$("textarea.editor").cleditor();	
	
	// form validationEngine
	$('form#validation').validationEngine();		
	$('form#validation_demo').validationEngine();	
	
	// input filter
	$('.numericonly input').autotab_magic().autotab_filter('numeric');
	$('.textonly input').autotab_magic().autotab_filter('text');
	$('.alphaonly input').autotab_magic().autotab_filter('alpha');
	$('.regexonly input').autotab_magic().autotab_filter({ format: 'custom', pattern: '[^0-9\.]' });
	$('.alluppercase input').autotab_magic().autotab_filter({ format: 'alphanumeric', uppercase: true });
});	

$(function() {
	$.cookie('hide_', '0', {});
	LResize();
	$(window).resize(function(){LResize(); });
    $(window).scroll(function (){ scrollmenu(); });
		
	 //exam ui slider element
	  $( "#slider-range-min" ).slider({
			range: "min",
			value: 212,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( "#amount" ).text( "$" + ui.value );
			}
		});
		$( "#amount" ).text( "$" + $( "#slider-range-min" ).slider( "value" ) );
		
		$( "#slider-range" ).slider({
			range: true,
			min: 0,
			max: 500,
			values: [ 75, 300 ],
			slide: function( event, ui ) {
				$( "#amount2" ).text( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
			}
		});
		$( "#amount2" ).text( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ) );
		
		$( "#slider" ).slider({
			value:100,
			min: 0,
			max: 500,
			step: 50,
			slide: function( event, ui ) {
				$( "#amount3" ).text( "$" + ui.value );
			}
		});
	$( "#amount3" ).text( "$" + $( "#slider" ).slider( "value" ) );
	$( "#eq > span" ).each(function() {
		// read initial values from markup and remove that
		var value = parseInt( $( this ).text(), 10 );
		$( this ).empty().slider({
			value: value,
			range: "min",
			animate: true,
			orientation: "vertical"
		});
	});
	$( "#red, #green, #blue" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 255,
		value: 127,
		slide: refreshSwatch,
		change: refreshSwatch
	});
	$( "#red" ).slider( "value", 190 );
	$( "#green" ).slider( "value", 221 );
	$( "#blue" ).slider( "value", 23 );
	  
	  
  	//datepicker
	$("input.datepicker").datepicker({ 
		autoSize: true,
		appendText: '(dd-mm-yyyy)',
		dateFormat: 'dd-mm-yy'
	});
	$( "div.datepickerInline" ).datepicker({ 
		dateFormat: 'dd-mm-yy',
		numberOfMonths: 1
	});	

	$( "input.birthday" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy-mm-dd'
    });
	

	//datetimepicker
   $("#datetimepicker").datetimepicker();
   $('#timepicker').timepicker({});
   
   	//Color picker 
	
	$('#colorPicker').ColorPicker({
		color: '#a4d143',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorPicker div').css('backgroundColor', '#' + hex);
		}
	});
	$('#colorPickerFlat').ColorPicker({flat: true,color: '#a4d143'});
	$('#colorpickerField').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val('#'+hex);
			$(el).ColorPickerHide();
			$('#colorpickerField').css('backgroundColor', '#' + hex);
		},
		onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb,el) {
				$('#colorpickerField').val('#'+hex);
				$('#colorpickerField').css('backgroundColor', '#' + hex);
			},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	}).bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});

	//button click
	$('.loading').live('click',function() { 
		  var str=$(this).attr('title'); 
		  var overlay=$(this).attr('rel'); 
		  loading('Patientez ...',overlay);
		  setTimeout("unloading()",1500); 
	  });
	$('#preloader').live('click',function(){
			unloading();
	 });
	
	// submit form 
	$('a.submit_form').live('click',function(){
		  var form_id=$(this).parents('form').attr('id');
		  $("#"+form_id).submit();
	})	

	// logout  
	$('.logout').live('click',function() { 
		  var str="Logout"; 
		  var overlay="1"; 
		  loading(str,overlay);
		  setTimeout("unloading()",1500);
		  setTimeout( "window.location.href='index.html'", 2000 );
	  });
		
	// tipsy tootip
	$('.tip a ').tipsy({gravity: 's',live: true});	
	$('.ntip a ').tipsy({gravity: 'n',live: true});	
	$('.wtip a ').tipsy({gravity: 'w',live: true});	
	$('.etip a,.Base').tipsy({gravity: 'e',live: true});	
	$('.netip a ').tipsy({gravity: 'ne',live: true});	
	$('.nwtip a , .setting').tipsy({gravity: 'nw',live: true});	
	$('.swtip a,.iconmenu li a ').tipsy({gravity: 'sw',live: true});	
	$('.setip a ').tipsy({gravity: 'se',live: true});	
	$('.wtip input').tipsy({ trigger: 'focus', gravity: 'w',live: true });
	$('.etip input').tipsy({ trigger: 'focus', gravity: 'e',live: true });
	$('.iconBox, div.logout').tipsy({gravity: 'ne',live: true });	
	$('.flot-graph').tipsy({gravity: 'ne',live: true ,trigger: 'click',});	
	
	  // profile webcam 
	  var camera = $('#camera'),screen =  $('#screen');	
	  webcam.set_api_url('upload.php');	
	  screen.html(
		  webcam.get_html(screen.width(), screen.height())
	  );
	  var shootEnabled = false;
	  $(".takeWebcam").click(function(){
		  $(".webcam").show('blind');
		  return false;
	  });
	  $("#closeButton").click(function(){
		  $(".webcam").hide('blind');
		  return false;
	  });
	  $('#takeButton').click(function(){
		  if(!shootEnabled){
			  return false;
		  }
		  webcam.freeze();
		  togglePane()
		  return false;
	  });
	  $('#retakeButton').click(function(){
		  webcam.reset();
		  togglePane()
		  return false;
	  });	
	  $('#uploadAvatar').click(function(){
		  webcam.upload();
		  togglePane()
		  webcam.reset();
		  return false;
	  });
	  webcam.set_hook('onLoad',function(){
		  shootEnabled = true;
	  });
	  webcam.set_hook('onError',function(e){
		  screen.html(e);
	  });
	  function togglePane(){
		  var visible = $(' .buttonPane:visible:first');
		  var hidden = $(' .buttonPane:hidden:first');	
		  visible.fadeOut('fast',function(){
			  hidden.show();
		  });
	  }
	  
	  	// images  editor tranfer
		$('#reflect').click(function() {
						$('.animate').animate({"reflect": true});	 
		});
		$('#reflectX').click(function() {
						$('.animate').animate({"reflectX": true});	 
		});
		$('#reflectY').click(function() {
						$('.animate').animate({"reflectY": true});	 
		});
		$('#reflectXY').click(function() {
						$('.animate').animate({"reflectXY": true});	 
		});
		$('#reflectYX').click(function() {
						$('.animate').animate({"reflectYX": true});	 
		});
	    ///////////////////////////////////////////////////////////////////////////		
		
		// images  editor crop			
	  $('#target').Jcrop({
			bgFade:     true,
			bgOpacity: .7,
			onChange: updateCoords,
			onSelect: updateCoords,
			aspectRatio: 4/3		   
					},function(){
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
        jcrop_api = this;
			var  x1=(boundx -240)/2;
			var  y1=(boundy -180)/2;
			var  x2=(x1 +240);
			var  y2=(y1 +180);
		jcrop_api.animateTo([x1,y1,x2,y2]);
      });
	  function updateCoords(c)
	  {
		  $('#x').val(c.x);
		  $('#y').val(c.y);
		  $('#w').val(c.w);
		  $('#h').val(c.h);
	  };
	 ///////////////////////////////////////////////////////////////////////////
	
	// spinner options 
	var itemListspinner = [
		{url: "http://ejohn.org", title: "John Resig"},
		{url: "http://bassistance.de/", title: "J&ouml;rn Zaefferer"},
		{url: "http://snook.ca/jonathan/", title: "Jonathan Snook"},
		{url: "http://rdworth.org/", title: "Richard Worth"},
		{url: "http://www.paulbakaus.com/", title: "Paul Bakaus"},
		{url: "http://www.yehudakatz.com/", title: "Yehuda Katz"},
		{url: "http://www.azarask.in/", title: "Aza Raskin"},
		{url: "http://www.karlswedberg.com/", title: "Karl Swedberg"},
		{url: "http://scottjehl.com/", title: "Scott Jehl"},
		{url: "http://jdsharp.us/", title: "Jonathan Sharp"},
		{url: "http://www.kevinhoyt.org/", title: "Kevin Hoyt"},
		{url: "http://www.codylindley.com/", title: "Cody Lindley"},
		{url: "http://malsup.com/jquery/", title: "Mike Alsup"}
	];
	
	var optionspinner = {
		'sDec': {decimals:2},
		'sMinMax':{min: -100, max: 100 },
		'sStep': {stepping: 0.25},
		'sCur': {currency: '$'},
		'sInline': {},
		'sLink': {
			init: function(e, ui) {
				for (var i=0; i<itemListspinner.length; i++) {
					ui.add('<a href="'+ itemListspinner[i].url +'" target="_blank">'+ itemListspinner[i].title +'</a>');
				}
			},
			format: '<a href="%(url)" target="_blank">%(title)</a>',
			items: itemListspinner
		}
	};	
	for (var n in optionspinner){
		$("#"+n).spinner(optionspinner[n]);
	}

	// uploadButton  ( Add file )
		$('#uploadButton').hover(function(){
			$('#upload_b').addClass('hover');
		},function(){
			$('#upload_b').removeClass('hover');
		});		
	
	// upload
	   $("input.fileupload").filestyle();
	// mutiupload
	  $('.uploadFile').live('click',function(){
		  $('#uploadify').uploadifyUpload(); 	
		   showSuccess('Uploading...');
	  })		
	
	// Sortable
	$("#picThumb").sortable({
		opacity: 0.6,handle : '.move', connectWith: '.picThumbUpload', items: '.picThumbUpload'
	});
	$("#main_menu").sortable({
		opacity: 0.6,connectWith: '.limenu',items: '.limenu'		
	});
	$( "#sortable" ).sortable({
		opacity: 0.6,revert: true,cursor: "move", zIndex:9000
	});
	

    // Effect 
	$('.SEclick, .SEmousedown, .SEclicktime,.SEremote,.SEremote2,.SEremote3,.SEremote4').jrumble();
	$('.SE').jrumble({
		x: 2,
		y: 2,
		rotation: 1
	});
	
	$('.alertMessage.error ').jrumble({
		x: 10,
		y: 10,
		rotation: 4
	});
	
	$('.alertMessage.success').jrumble({
		x: 4,
		y: 0,
		rotation: 0
	});
	
	$('.alertMessage.warning').jrumble({
		x: 0,
		y: 0,
		rotation: 5
	});

	$('.SE').hover(function(){
		$(this).trigger('startRumble');
	}, function(){
		$(this).trigger('stopRumble');
	});

	$('.SEclick').toggle(function(){
		$(this).trigger('startRumble');
	}, function(){
		$(this).trigger('stopRumble');
	});
	
	$('.SEmousedown').bind({
		'mousedown': function(){
			$(this).trigger('startRumble');
		},
		'mouseup': function(){
			$(this).trigger('stopRumble');
		}
	});
	
	$('.SEclicktime').click(function(){
		var demoTimeout;
		$this = $(this);
		clearTimeout(demoTimeout);
		$this.trigger('startRumble');
		demoTimeout = setTimeout(function(){$this.trigger('stopRumble');}, 1500)
	});
	$('.SEremote').hover(function(){
		$('.SEremote2').trigger('startRumble');
	}, function(){
		$('.SEremote2').trigger('stopRumble');
	});
	
	$('.SEremote2').hover(function(){
		$('.SEremote').trigger('startRumble');
	}, function(){
		$('.SEremote').trigger('stopRumble');
	})

	$('.SEremote3').hover(function(){
		$('.alertMessage').trigger('startRumble');
	}, function(){
		$('.alertMessage').trigger('stopRumble');
	})

	$('.SEremote4').hover(function(){
		$('.alertMessage.error').trigger('startRumble');
	}, function(){
		$('.alertMessage.error').trigger('stopRumble');
	})


	// checkbox,selectbox customInput 
	 $('input[placeholder], textarea[placeholder]').placeholder();
	//$('.ck,.chkbox,.checkAll ,input:radio').customInput();	
	$('.ck,.chkbox,.checkAll').customInput();	
	$('.limit3m').limitInput({max:3,disablelabels:true});
	// select boxes
	$(function() {
        $(' select').not("select.no-chzn, select.chzn-select,select[multiple],select#box1Storage,select#box2Storage").selectmenu({
            style: 'dropdown',
            transferClasses: true,
            width: null
        });
    });
	// Dual select boxes
	$.configureBoxes();
	
	$(".dataTables_wrapper .dataTables_length select").addClass("small");
	$("table tbody tr td:first-child .custom-checkbox:first-child").css("margin: 0px 3px 3px 3px");
	 // mutiselection
	$(".chzn-select").chosen();
	$(".chzn-select[disabled]").removeAttr('disabled');
	
	// checkbox iphoneStyle
	$(".on_off_checkbox").iphoneStyle(); 
	$(".show_email").iphoneStyle({
		  checkedLabel: "show Email",
		  uncheckedLabel: "Don't show ",
		  labelWidth:'85px'
	});
	$(".preOrder").iphoneStyle({
		  checkedLabel: "in stocks",
		  uncheckedLabel: "out stocks",
		  labelWidth:'76px'
	}); 
	$(".online").iphoneStyle({
		  checkedLabel: "online",
		  uncheckedLabel: "offline ",
		  labelWidth:'55px'
	}); 
	$(".show_conmap").iphoneStyle({
		  checkedLabel: "show map",
		  uncheckedLabel: "Don't show ",
		  labelWidth:'85px',
		  onChange: function() {
				var chek=$(".show_conmap").attr('checked');
					  if(chek){
						  $(".disabled_map").fadeOut();
					  }else{
						 $(".disabled_map").fadeIn();
					  }
		}
	});
	
	 // checkbox  All in Table
	$(".checkAll").live('click',function(){
		  var table=$(this).parents('table').attr('id');
		  var checkedStatus = this.checked;
		  var id= this.id;
		 $( "table#"+table+" tbody tr td:first-child input:checkbox").each(function() {
			this.checked = checkedStatus;
				if (this.checked) {
					$(this).attr('checked', $('.' + id).is(':checked'));
					$('label[for='+$(this).attr('id')+']').addClass('checked ');
				}else{
					$(this).attr('checked', $('.' + id).is(''));
					$('label[for='+$(this).attr('id')+']').removeClass('checked ');
					}
		});	 
	});		
	
	// icon  gray Hover
	$('.iconBox.gray').hover(function(){
		  var name=$(this).find('img').attr('alt');
		  $(this).find('img').animate({ opacity: 0.5 }, 0, function(){
			    $(this).attr('src','images/icon/color_18/'+name+'.png').animate({ opacity: 1 }, 700);									 
		 });
	},function(){
		  var name=$(this).find('img').attr('alt');
		  $(this).find('img').attr('src','images/icon/gray_18/'+name+'.png');
	 })
	// icon  Logout 
	$('div.logout').hover(function(){
		  var name=$(this).find('img').attr('alt');
		  $(this).find('img').animate({ opacity: 0.4 }, 200, function(){
			    $(this).attr('src','images/'+name+'.png').animate({ opacity: 1 }, 500);									 
		 });
	},function(){
		  var name=$(this).find('img').attr('name');
		  $(this).find('img').animate({ opacity: 0.5 }, 200, function(){
			    $(this).attr('src','images/'+name+'.png').animate({ opacity: 1 }, 500);									 
		 });
	 })
	// icon  setting 
	$('div.setting').hover(function(){
		$(this).find('img').addClass('gearhover');
	},function(){
		$(this).find('img').removeClass('gearhover');
	 })
	
	// shoutcutBox   Hover
	$('.shoutcutBox').hover(function(){
		  $(this).animate({ top: '+=5'}, 200);
	},function(){
		$(this).animate({ top: '0'}, 200);
	 })
	
	// mainmenu   Hover
/*	$('.main_menu li ul li a').live({
        mouseenter: function(){
		   $(this).animate({ paddingLeft: '35'}, 200);
           },
        mouseleave: function(){
		$(this).animate({ paddingLeft: '30'}, 200);
           }
       }
    );*/


	// hide notify  Message with click
	$('#alertMessage').live('click',function(){
	  $(this).stop(true,true).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });						 
	});
	
	// images hover
	$('.picHolder,.SEdemo').hover(
		  function() {
			  $(this).find('.picTitle').fadeTo(200, 1);
		  },function() {
			  $(this).find('.picTitle').fadeTo(200, 0);
		  }
	  )	
		  
				  
	//droppable 			   	
	// move images  to  news album
	$('.album').droppable({
		hoverClass: 'over',
		activeClass: 'dragging',
		drop:function(event,ui){
			
			 if($(this).hasClass('selected')) return false;
			loading('Moving');
			
			ui.helper.fadeOut(400);
			setTimeout("unloading()",1500); 		

		},
		tolerance:'pointer'
	});
	  $('.picPreview').droppable({
		  hoverClass: 'picPreview-hover',
		  activeClass: 'picPreview-hover',
		   drop: function( event, ui ) { 
			   $('#image-albumPreview').attr('src',ui.draggable.find('img').attr('src'));
		   }
	});	
  	$('.deletezone').droppable({
		hoverClass: 'deletezoneover',
		activeClass: 'deletezonedragging',
		drop:function(event,ui){	

		   var data ='id='+ ui.draggable.imgdata(0); 
		   var name =ui.draggable.imgdata(1); 

		$.confirm({
		'title': 'DELETE DIALOG BOX','message': "<strong>YOU WANT TO DELETE </strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'special',
		'action': function(){
					loading('Deleting',1);
						   ui.helper.fadeOut(function(){ ui.helper.remove(); 
						  setTimeout("unloading()",1500); 	
					});
			}},'No'	: {'class'	: ''}}});
		},
		tolerance:'pointer'
	});
	$('.album.load').live('click',function(e){
		  $('.album').removeClass('selected',1000);
		  var albumid=$(this).attr('id');
		  
		  $(this).addClass('selected',1000);
		  
		  loadalbum(albumid);
	})	
	$(".albumDelete").live('click',function() { 
		   var dataSet=$(this).parents('form');
		   var name = $(this).attr("name");
		   var data ='id='+ $(this).attr("id");   
		   albumDelete(data,name,dataSet);
	});
	$('#editAlbum.editOn').live('click',function(){
												   
	$('.album_edit').fadeIn(400);
	$('.boxtitle').css({'margin-left':'207px'});
	$('.boxtitle .texttip').hide();
		$(this).html('close edit').attr('title','Click here to Close edit  ').removeClass('editOn').addClass('editOff');
		imgRow();
	})
	$('#editAlbum.editOff').live('click',function(){			
												   
		$('.album_edit').fadeOut(400,function(){
		$('.boxtitle .texttip').show();
				 $('.boxtitle').css({'margin-left':'0'});
				 imgRow();
		});
		$(this).html('edit album').attr('title','Click here to edit  Album ').removeClass('editOff').addClass('editOn');
		 
	})
	
	// mouseenter Over album with  CSS3
	$(".preview").delegate('img', 'mouseenter', function() {
		  if ($(this).hasClass('stackphotos')) {
		  var $parent = $(this).parent();
		  $parent.find('img#photo1').addClass('rotate1');
		  $parent.find('img#photo2').addClass('rotate2');
		  $parent.find('img#photo3').addClass('rotate3');
		  }
	  }).delegate('img', 'mouseleave', function() {
		  $('img#photo1').removeClass('rotate1');
		  $('img#photo2').removeClass('rotate2');
		  $('img#photo3').removeClass('rotate3');
	});
	
	$('.msg').live({
        mouseenter: function(){
			$(this).find('.toolMsg').show();
           },mouseleave: function(){
			$(this).find('.toolMsg').hide();
           }
       }
    );
	
	// on click callback
	$('#open_icon,#open_icon2,#open_icon3').click(function(){	
		  var callback=$(this).attr('id');
		  var type=$(this).attr('title');
		   var input=$(this).attr('rel');
		  fileDialogCallback(callback,type,input);					   
	});
	// on focus  callback
	$('.fileDialog').live('focus',function(){
		  var callback,input =$(this).attr('id');
		  var type=$(this).attr('title');
		  fileDialogCallback(callback,type,input);										
	})


	// Confirm Delete.
	$(".Delete").live('click',function() { 
		  var row=$(this).parents('tr');
		  var dataSet=$(this).parents('form');
		  var id = $(this).attr("id");
		  var name = $(this).attr("name");
		  var data ='id='+id;
		  Delete(data,name,row,0,dataSet);
	});
	$(".DeleteAll").live('click',function() {			
		  var rel=$(this).attr('rel');	
		  var row=$(this).parents('.tab_content').attr('id');	
		  var row=row+' .load_page ';
		  if(!rel) { 
			  var rel=0;
			  var row=$('#load_data').attr('id');	 
		  }  
		  var dataSet=$('form:eq('+rel+')');					   
		  var	data=$('form:eq('+rel+')').serialize();
		  var name = 'All File Select';
		 Delete(data,name,row,2,dataSet);
	});
	
	
	// Overlay form
	$(".on_load_filter").live('click',function(){	
		$('body').append('<div id="overlay"></div>');
		$('#overlay').css('opacity',0.4).fadeIn(400);
		var activeLoad = $(this).attr("name");
		$(activeLoad).fadeIn(400, function() {
			$('#main_content').fadeOut();
		});
		$(activeLoad).css({'position':'relative','z-index':'1001'});
	 });
	
	$(".on_load_page").live('click',function(){	 
		var activeLoad = $(this).attr("name");	
		$(activeLoad).fadeIn(400, function() {
			$('#overlay').fadeOut(function(){
				$('#filter_content').css({'z-index':'','box-shadow':'','-moz-box-shadow':'','-webkit-box-shadow':''});
				$('#filter_content').fadeOut();
			}); 
			$('#overlay').remove();
		}); 
	});
	
	$(".on_load_page_refresh").live('click',function(){	 
		var activeLoad = $(this).attr("name");	
		$(activeLoad).fadeIn(400, function() {
			$('#overlay').fadeOut(function(){
				$('#filter_content').css({'z-index':'','box-shadow':'','-moz-box-shadow':'','-webkit-box-shadow':''});
				$('#filter_content').fadeOut();
			}); 
			$('#overlay').remove();
		}); 
		 location.reload();
	});
	
	// Calendar 
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		


	// bind all formError elements to close on click
	$(".formError").siblings('input, select, div.chzn-container').not('select.chzn-select').css('display', 'block');
	$(".formError").live("click", function() {
        $(this).fadeOut(150, function() {
            // remove prompt once invisible
            $(this).remove();
        });
    });

    function showTooltip(x, y, contents) {
        $('<div id="tooltip" >' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y -13,
            left: x + 10
        }).appendTo("body").show();
    }

    var previousPoint = null;
    $(".chart_flot").bind("plothover", function(event, pos, item) {
												
        $("#x").text(pos.x);
        $("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

			$(this).attr('title',item.series.label);
			$(this).trigger('click');
                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];

                showTooltip(item.pageX, item.pageY, "<b>" + item.series.label + "</b> : " + y);
            }
        }  else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
	
	
	});		


	// check browser fixbug
	var mybrowser=navigator.userAgent;
	if(mybrowser.indexOf('MSIE')>0){$(function() {	
			   $('.formEl_b fieldset').css('padding-top', '0');
				$('div.section label small').css('font-size', '10px');
				$('div.section  div .select_box').css({'margin-left':'-5px'});
				$('.iPhoneCheckContainer label').css({'padding-top':'6px'});
				$('.uibutton').css({'padding-top':'6px'});
				$('.uibutton.icon:before').css({'top':'1px'});
				$('.dataTables_wrapper .dataTables_length ').css({'margin-bottom':'10px'});
		});
	}
	if(mybrowser.indexOf('Firefox')>0){ $(function() {	
			   $('.formEl_b fieldset  legend').css('margin-bottom', '0px');	
			   $('table .custom-checkbox label').css('left', '3px');
		  });
	}	
	if(mybrowser.indexOf('Presto')>0){
		$('select').css('padding-top', '8px');
	}
	if(mybrowser.indexOf('Chrome')>0){$(function() {
			//$('div.tab_content ul.uibutton-group').css('margin-top', '-49px;padding-right: 5px;');
			$('div.section  div .select_box').css({'margin-top':'0px','margin-left':'-2px'});
			$('select').css('padding', '6px');
			$('table .custom-checkbox label').css('left', '3px');
		});
	}		
	if(mybrowser.indexOf('Safari')>0){}		



	  
	  
	  function fileDialogCallback(callback,type,input){
			$('<div id="finder_'+callback+'"/>').elfinder({
				 url : 'components/elfinder/connectors/php/connector-'+type+'.php', editorCallback : function(url) { $('#'+input).val(url);
				},closeOnEditorCallback : true, dialog : { title : 'File manager',modal : true,width : 700 }
			})							   
	  }
	  function fileDialog(callback,type){
			$('<div id="finder_'+callback+'"/>').elfinder({
				  url : 'components/elfinder/connectors/php/connector-'+type+'.php',dialog : { title : 'File manager',modal : true,width : 700 }
			})							   
	  }
		  
function Delete(data,name,row,type,dataSet){
		var loadpage = dataSet.hdata(0);
		var url = dataSet.hdata(1);
		var table = dataSet.hdata(2);
		var data = data+"&tabel="+table;
$.confirm({
'title': '_DELETE DIALOG BOX','message': " <strong>YOU WANT TO DELETE </strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'special',
'action': function(){
			loading('Checking');
							 $('#preloader').html('Deleting...');
							 if(type==0){ row.slideUp(function(){   showSuccess('Success',5000); unloading(); }); return false;}
							  if(type==1){ row.slideUp(function(){   showSuccess('Success',5000); unloading(); }); return false;}
								setTimeout("unloading();",900); 		 
				 }},'No'	: {'class'	: ''}}});}

	  function albumDelete(data,name,dataSet){
			  var loadpage = dataSet.hdata(0);
			  var row = dataSet.hdata(2);
			  $.confirm({
			  'title': '_DELETE DIALOG BOX','message': "<strong>YOU WANT TO DELETE </strong><br /><font color=red>' "+ name +" ' </font> ",'buttons': {'Yes': {'class': 'special',
			  'action': function(){
						  loading('Checking',1);
						  setTimeout("unloading()",1500); 	  
				}},'No'	: {'class'	: ''}}});
	  }
	  
	  
	  function loadalbum(albumid){
			  loading('Loading');
			  $(' .albumImagePreview').show();
			 imgRow()
			  $(' .albumImagePreview').fadeOut(function(){ 
														
					  $("#uploadAlbum").attr('href','modalupload.html'); 		
					  $('#uploadAlbum').removeClass('disable secure ').addClass('special add ');
					  $('#uploadButtondisable').css({'display':'none'});
					  $('.screen-msg').hide();  setTimeout("unloading()",500); 	 																	
			  }).delay(400).fadeIn();			
			   
		  }


	  function ResetForm(){
		  $('form').each(function(index) {	  
			var form_id=$('form:eq('+index+')').attr('id');
				  if(form_id){ 
					  $('#'+form_id).get(0).reset(); 
					  $('#'+form_id).validationEngine('hideAll');
							  var editor=$('#'+form_id).find('#editor').attr('id');
							  if(editor){
								   $('#editor').cleditor()[0].clear();
							  }
				  } 
		  });	
	  }


	function hexFromRGB(r, g, b) {
		var hex = [
			r.toString( 16 ),
			g.toString( 16 ),
			b.toString( 16 )
		];
		$.each( hex, function( nr, val ) {
			if ( val.length === 1 ) {
				hex[ nr ] = "0" + val;
			}
		});
		return hex.join( "" ).toUpperCase();
	}
	function refreshSwatch() {
		var red = $( "#red" ).slider( "value" ),
			green = $( "#green" ).slider( "value" ),
			blue = $( "#blue" ).slider( "value" ),
			hex = hexFromRGB( red, green, blue );
		$( "#swatch" ).css( "background-color", "#" + hex );
	}
	  

	  
	  function showError(str,delay){	
		  if(delay){
			  $('#alertMessage').removeClass('success info warning').addClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
					  $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });																														   																											
				});
			  return false;
		  }
		  $('#alertMessage').addClass('error').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);	
	  }
	  function showSuccess(str,delay){
		  if(delay){
			  $('#alertMessage').removeClass('error info warning').addClass('success').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
					  $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });																														   																											
				});
			  return false;
		  }
			  $('#alertMessage').addClass('success').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);	
	  }
	  function showWarning(str,delay){
		  if(delay){
			  $('#alertMessage').removeClass('error success  info').addClass('warning').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
					  $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });																														   																											
				});
			  return false;
		  }
	  }
	  function showInfo(str,delay){
		  if(delay){
			  $('#alertMessage').removeClass('error success  warning').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500,function(){
					  $(this).delay(delay).animate({ opacity: 0,right: '-20'}, 500,function(){ $(this).hide(); });																														   																											
				});
			  return false;
		  }
			  $('#alertMessage').html(str).stop(true,true).show().animate({ opacity: 1,right: '10'}, 500);	
	  }


	  
	  function hide_panel() { 
			if(Get_Cookie('hide_')=='1'){$('#show_menu').show();}else{$('#hide_menu').show();}
	  }	
	  
	  
	  function loading(name,overlay) { 
			$('body').append('<div id="overlay"></div><div id="preloader">'+name+'..</div>');
					if(overlay==1){
					  $('#overlay').css('opacity',0.4).fadeIn(400,function(){  $('#preloader').fadeIn(400);	});
					  return  false;
			   }
			$('#preloader').fadeIn();	  
	   }
	   
	   
	  function unloading() { 
			$('#preloader').fadeOut(400,function(){ $('#overlay').fadeOut(); }).remove();
	   }
	
 function imgRow(){	
		var maxrow=$('.albumpics').width();
		if(maxrow){
				maxItem= Math.floor(maxrow/160);
				maxW=maxItem*160;
				mL=(maxrow-maxW)/2;
				$('.albumpics ul').css({
						'width'	:	maxW	,
						'marginLeft':mL
		 })
	}}	
	function scrollmenu(){	
			if($(window).scrollTop()>=1){			   
			  $("#header ").css("z-index", "50"); 
		  }else{
			  $("#header ").css("z-index", "47"); 
		 }
	}
	
	
 function LResize() {
	 imgRow();
	 scrollmenu();
	$("#shadowhead").show();
		if($(window).width()<=768){
			$('body').addClass('nobg');$('#hide_menu').hide();$('#show_menu').hide();			
			$('#shadowhead').css({ position: "fixed" });	
			$(' .column_left, .column_right ,.grid2,.grid3,.grid1').css({ width:"100%",float:"none",padding:"0",marginBottom: "20px" });			  	
			if($('#left_menu').length) {
				$('#hide_menu_icon').show();$('#show_menu_icon').hide();
				$('#left_menu,#load_menu').css({ left: "0px" });$('#content').css({ marginLeft: "70px" });	
				$('#main_menu').removeClass('main_menu').addClass('iconmenu');
				$('#main_menu li').each(function() {	  
					var title = $(this).find('b').text();
					$(this).find('a').attr('title', title);
				});
				$('#main_menu li a').find('b').hide();	
				$('#main_menu li ').find('ul').hide();
			}	
		}
		if($(window).width()>1024) {
			$('#main_menu').removeClass('iconmenu ').addClass('main_menu');
			$('#main_menu li a').find('b').show();	
			$('#hide_menu_icon').hide();$('#show_menu_icon').hide();
			$('.column_left,.column_right,.grid2').css({ width:"49%",float:"left"});
			$('.column_left').css({ 'padding-right':"1%"});
			$('.column_right').css({ 'padding-left':"1%"});
			 if($('#left_menu').length){
				$('#hide_menu').show(); $('#show_menu').hide();
				$('#left_menu,#load_menu').css({ left: "0px" });
				$('#content').css({ marginLeft: "240px" });
				$('body').removeClass('nobg').addClass('dashborad');
				$('#shadowhead').css({ position: "absolute" });	
			}	
		} else {
			   $(' .column_left, .column_right ,.grid2,.grid3,.grid1').css({ width:"100%",float:"none",padding:"0",marginBottom: "20px" });
		}
}
		