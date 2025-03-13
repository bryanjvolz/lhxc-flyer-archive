jQuery(document).ready(function($) {
    $('body').on('click', '.toggle-new-venue', function() {
        var $select = $(this).siblings('.venue-select');
        var $input = $(this).siblings('.new-venue-input');

        if ($input.is(':hidden')) {
            $select.hide();
            $input.show().attr('name', $select.attr('name'));
            $select.removeAttr('name');
            $(this).text('Use Existing Venue');
        } else {
            $select.show().attr('name', $input.attr('name'));
            $input.hide().removeAttr('name');
            $(this).text('Add New Venue');
        }
    });

    // Update form submission handling
    $('body').on('submit', 'form.compat-item', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $input = $form.find('.new-venue-input:visible');
        var $select = $form.find('.venue-select');

        if ($input.length && $input.val()) {
            var newVenue = $input.val();

            // Update hidden input with new venue value
            if (!$form.find('input[name="new_venue"]').length) {
                $form.append('<input type="hidden" name="new_venue" value="' + newVenue + '">');
            } else {
                $form.find('input[name="new_venue"]').val(newVenue);
            }

            // Update select value
            $select.val(newVenue);
        }

        // Submit the form
        $form[0].submit();
    });
});