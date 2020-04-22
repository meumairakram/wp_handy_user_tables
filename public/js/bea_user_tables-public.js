(function( $ ) {
	'use strict';

		$(function() {
			if($('#bea_user_table').length > 0) {

				// Configuring options to fetch Tables Data.
				var table = new Tabulator("#bea_user_table", {
					ajaxURL:bea_ajax_obj.bea_ajax_url+'?action=beausertabledata&wp_nonce='+bea_ajax_obj.bea_ajax_nonce, //ajax URL
					ajaxConfig:{
						method:"POST"
					},
					pagination:"remote",
					paginationSize:10,
					ajaxFiltering:true,
					ajaxSorting:true,
					ajaxResponse:function(url, params, response){
						// Triggering Filter by Role Select options.
						updateFilterValues(response);
					
						return response; //return the response data to tabulator
					},

				});

				// initializing tabular tables
				table.setData();

			}

			// Filter Selectors 
			var bea_filters = jQuery('#bea_ut_filters');
			let filter_role_selector = bea_filters.find('select#filter_by');


			// Filter Selector On Change Listener
			filter_role_selector.change(function() {
				// Call back on Filter By Selector Change

				var filter_value = $(this).val();
				if(filter_value != 'none') {
					table.setFilter("role", "=", filter_value);
				} else {
					table.clearFilter(true);
				}
				


				
			});		
			

		});

		// function to update the filter options on the basis of possible values.
		function updateFilterValues(obj) {
			var allRoles = [];

			obj.data.forEach((record) => {
				
				allRoles.push(record.role);
			});

			allRoles = allRoles.filter(distinct);

			var filter_by_dropdown = $('select#filter_by');

			if(filter_by_dropdown.hasClass('populated') == false) {
				allRoles.forEach((role) => {
					filter_by_dropdown.append('<option value="'+role+'">'+role+'</option>');
					
				}); 
				filter_by_dropdown.addClass('populated');
			}

		}

		// a filter function to filter the Distint values in an array... 
		//  * Return : array();
		const distinct = (value,index,self) => {
			return self.indexOf(value) === index;
		}
		
	 
})( jQuery );
