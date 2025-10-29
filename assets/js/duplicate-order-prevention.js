jQuery(document).ready(function($) {
	var modal = $('#duplicate-order-prevention-modal');
	var content = $('#duplicate-order-prevention-content');
	var ignoreButton = $('#duplicate-order-prevention-ignore');
	var closeButton = $('#duplicate-order-prevention-close');

	// Use wp.i18n for translations
	const { __ } = wp.i18n;

	// Function to build the modal content from localized data
	function buildModalContent(data) {
		if (!data || data.length === 0) {
			return;
		}

		var html = '';

		data.forEach(function(item) {
			html += '<div class="border border-gray-300 rounded p-3">';
			html += '<p class="font-semibold mb-2">' + 
					__('Product: ', 'duplicate-order-prevention-for-woocommerce') + 
					item.product_name + '</p>';
			html += '<ul class="list-disc list-inside space-y-1">';

			item.orders.forEach(function(order) {
				html += '<li>';
				html += '<a href="' + order.order_url + '" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">';
				html += __('Order #', 'duplicate-order-prevention-for-woocommerce') + 
						order.order_id + ' (' + order.status + ')';
				html += '</a>';
				html += '</li>';
			});

			html += '</ul>';
			html += '</div>';
		});

		content.html(html);
	}

	// Show modal if duplicate data exists
	if (typeof duplicateOrderPreventionData !== 'undefined' && duplicateOrderPreventionData.length > 0) {
		buildModalContent(duplicateOrderPreventionData);
		modal.removeClass('hidden');

		// Prevent form submission while modal is visible
		$('form.checkout').on('submit', function(e) {
			if (!modal.hasClass('hidden')) {
				e.preventDefault();
				return false;
			}
		});
	}

	// Ignore button click handler
	ignoreButton.on('click', function() {
		// Add a hidden input to the checkout form to indicate ignoring duplicates
		if ($('input[name="duplicate_order_prevention_ignore"]').length === 0) {
			$('<input>').attr({
				type: 'hidden',
				name: 'duplicate_order_prevention_ignore',
				value: 'yes'
			}).appendTo('form.checkout');
		}

		modal.addClass('hidden');
		// Allow form submission now
		$('form.checkout').off('submit');
	});

	// Close button click handler (same as ignore)
	closeButton.on('click', function() {
		ignoreButton.trigger('click');
	});
});
