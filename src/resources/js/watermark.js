/**
 * @uses bryglab\watermark\watermark::clear
 */

const $button = document.getElementById('settings-cleanDirectory');

// Add event listener to the button
$button.addEventListener('click', function(e) {
    e.preventDefault();
    const params = new FormData();
    params.append($button.dataset.csrfTokenName, $button.dataset.csrfTokenValue);
    // Send the request
    fetch('/admin/actions/watermark/watermark/clear', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: params,
    }).then(function(e) {
            // Check if the request was successful
            if (e.ok) {
                Craft.cp.displayNotice('Watermark directory cleaned.');
            } else {
                Craft.cp.displayError('An error occurred.');
            }
        }
    );
});