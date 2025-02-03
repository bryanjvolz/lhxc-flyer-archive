jQuery(document).ready(function($) {
    $('body').on('click', '.toggle-new-venue', function() {
        var $select = $(this).siblings('.venue-select');
        var $input = $(this).siblings('.new-venue-input');

        if ($input.is(':hidden')) {
            $select.hide();
            $input.show();
            $(this).text('Use Existing Venue');
        } else {
            $select.show();
            $input.hide();
            $(this).text('Add New Venue');
        }
    });

    // Handle form submission
    $('body').on('submit', 'form.compat-item', function() {
        $('.new-venue-input:visible').each(function() {
            var $input = $(this);
            var $select = $input.siblings('.venue-select');
            if ($input.val()) {
                $select.val($input.val());
            }
        });
    });
});