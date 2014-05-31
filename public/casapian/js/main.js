(function($){

	function setUi(context){
		$.extend($.tablesorter.themes.bootstrap, {
		    // these classes are added to the table. To see other table classes available,
		    // look here: http://twitter.github.com/bootstrap/base-css.html#tables
		    table      : 'table table-bordered',
		    header     : 'bootstrap-header', // give the header a gradient background
		    footerRow  : '',
		    footerCells: '',
		    icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
		    sortNone   : 'bootstrap-icon-unsorted',
		    sortAsc    : 'icon-chevron-up',
		    sortDesc   : 'icon-chevron-down',
		    active     : '', // applied when column is sorted
		    hover      : '', // use custom css here - bootstrap class may not override it
		    filterRow  : '', // filter row class
		    even       : '', // odd row zebra striping
		    odd        : ''  // even row zebra striping
		  });

		  // call the tablesorter plugin and apply the uitheme widget
		  $("table").tablesorter({
		    // this will apply the bootstrap theme if "uitheme" widget is included
		    // the widgetOptions.uitheme is no longer required to be set
		    theme : "bootstrap",

		    widthFixed: true,

		    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

		    // widget code contained in the jquery.tablesorter.widgets.js file
		    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
		    widgets : [ "uitheme", "filter", "zebra" ],

		    widgetOptions : {
		      // using the default zebra striping class name, so it actually isn't included in the theme variable above
		      // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
		      zebra : ["even", "odd"],

		      // reset filters button
		      filter_reset : ".reset"

		      // set the uitheme widget to use the bootstrap theme class names
		      // this is no longer required, if theme is set
		      // ,uitheme : "bootstrap"

		    }
		  })
		  .tablesorterPager({

		    // target the pager markup - see the HTML block below
		    container: $(".pager"),

		    // target the pager page select dropdown - choose a page
		    cssGoto  : ".pagenum",

		    // remove rows from the table to speed up the sort of large tables.
		    // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
		    removeRows: false,

		    // output string - default is '{page}/{totalPages}';
		    // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
		    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

		  });
		$(".datetime-picker",context).datetimepicker({
			"language":"en"
		});
		$("select",context).select2({
            placeholder:"select value",
            allowClear: true
        });
		$("table tr[data-href]",context).on("click",function(e){
			if(!$(e.target).is("input")){
				document.location.href = $(this).attr("data-href");
			}
		});
		console.log($(".btn-danger",context).length);
		$(".btn-danger",context).on("click",function(e){
			var btn = $(this)
			if(!btn.hasClass("true")){
				e.preventDefault();

				bootbox.confirm("Are you sure you want to delete this record?", function(result){
					if(result){
						btn.addClass("true");
						btn.click();
					}
				})
			}
		});
	}
	$(document).ready(function(){
		setUi("body");
		$(".btn-modal").on("click",function(e){
			var context = $(this);
			$.fancybox.open({
				type:'ajax',
				href:context.attr("href"),
				afterShow:function(){
					var fancybox = $(".fancybox-wrap");
					setUi(fancybox);

					$("form",fancybox).ajaxForm(function(response){
						var selectBox = context.parent().find("select");
						var label = response[context.attr("data-field")];
						var value = response[context.attr("data-key")];

						var temp = selectBox.select2('val');
						temp.push(value);

						selectBox.append("<option value='"+value+"' selected='selected'>"+label+"</option>")
						selectBox.select2("val",temp);
						$.fancybox.close();
					});
				}
			});
			e.preventDefault();
		})

	})
})(jQuery);