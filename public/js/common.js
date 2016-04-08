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

$('.table-sort').tablesorter({stringTo: "min"});
$('.table-sort-data').tablesorter({stringTo:"min",textExtraction:function(node){return node.dataset.sort;}}); 


$('[data-toggle="tooltip"]').tooltip();