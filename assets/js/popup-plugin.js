jQuery(document).ready(function($) {
    // Get the popup display delay from the WordPress backend (in seconds)
    var delayInSeconds = parseInt(delayPopupSettings.delayInSeconds); ;
    var delayInMilliseconds = delayInSeconds * 1000; // Convert to milliseconds
    console.log(delayInMilliseconds);
    // Function to display the popup
    function showPopup() {
        $('#confetti-popup').show();
    }

    // Use setTimeout to trigger the popup after the specified delay
    setTimeout(showPopup, delayInMilliseconds);

    // Close the popup when the close button is clicked
    $('#confetti-popup-close').on('click', function() {
        $('#confetti-popup').hide();
    });

    // Close the popup when clicking outside of it
    // $(document).on('click', function(event) {
    //     if (!$(event.target).closest('#confetti-popup').length) {
    //         $('#confetti-popup').fadeOut();
    //     }
    // });
    // Popup Hide Show
    jQuery(document).ready(function($) {
            // Checkbox change event
            $('#content-toggle').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#confetti-popup').show();
                } else {
                    $('#confetti-popup').hide();
                }
            });
        });
});