
/**
* Theme: Moltran Admin Template
* Author: Coderthemes
* SweetAlert - 
* Usage: $.SweetAlert.methodname
*/

$(document).ready(function(){  
    $('#menace').hide()
  });

!function($) {
    "use strict";

    var SweetAlert = function() {};

    //examples 
    SweetAlert.prototype.init = function() {
        
    //Basic
    $('#sa-basic').click(function(){
        swal("Here's a message!");
    });

    //A title with a text under
    $('#sa-title').click(function(){
        swal("Here's a message!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis")
    });

    //Success Message
    $('#sa-success').click(function(){
        swal("Good job!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis", "success")
    });

    //Warning Message
    $('#sa-warning').click(function(){
        swal({   
            title: "Are you sure?",   
            text: "You will not be able to recover this imaginary file!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, delete it!",   
            closeOnConfirm: false 
        }, function(){   
            swal("Deleted!", "Your imaginary file has been deleted.", "success"); 
        });
    });

    //Parameter

    
    //delete menace
    $('.dellall').click(function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var delButton = document.getElementById("menaces"+id); 
        swal({   
            title: "Etes-vous sûr?",   
            text: "Vous ne pourrez pas récupérer cette menace une fois supprimée!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#009292",   
            cancelButtonText: "Annuler",   
            confirmButtonText: "Oui, Je supprime!",      
            closeOnConfirm: false 
        }, function(isConfirm){   
            if (isConfirm) {     
                swal("Supprimée", "Menace supprimée.", "success");   
                delButton.click();  
            } else {     
                swal("Annulée", "Suppréssion annulée :)", "error");   
            } 
        });
    });

     //delete activite
     $('.dellbutton').click(function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var delButton = document.getElementById("activites"+id); 
        swal({   
            title: "Etes-vous sûr?",   
            text: "Vous ne pourrez pas récupérer cette activité une fois supprimée!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#009292",   
            cancelButtonText: "Annuler",   
            confirmButtonText: "Oui, Je supprime!",      
            closeOnConfirm: false 
        }, function(isConfirm){   
            if (isConfirm) {     
                swal("Supprimée", "Menace supprimée.", "success");   
                delButton.click();  
            } else {     
                swal("Annulée", "Suppréssion annulée :)", "error");   
            } 
        });
    });

    //Custom Image
    $('#sa-image').click(function(){
        swal({   
            title: "Sweet!",   
            text: "Here's a custom image.",   
            imageUrl: "assets/vendor/sweetalert/example/images/thumbs-up.jpg" 
        });
    });

    //Auto Close Timer
    $('#sa-close').click(function(){
         swal({   
            title: "Auto close alert!",   
            text: "I will close in 2 seconds.",   
            timer: 2000,   
            showConfirmButton: false 
        });
    });


    },
    //init
    $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
}(window.jQuery),

//initializing 
function($) {
    "use strict";
    $.SweetAlert.init()
}(window.jQuery);