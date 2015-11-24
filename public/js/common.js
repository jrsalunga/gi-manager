/*!
 * CommonJS
 * 
 */

$.ajaxSetup({
	headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	},
	beforeSend: function(jqXHR, obj) {
  
	}
});


$('.alert').not('.alert-important').delay(5000).slideUp(300);