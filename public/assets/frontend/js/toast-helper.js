/**
 * Toast Helper - Prevents duplicate toast notifications
 * This helper checks if Laravel Notify is being used before showing a toast
 */

// Global flag to track if Laravel Notify is active
window.laravelNotifyActive = false;

// Function to safely show toast only if Laravel Notify isn't handling it
window.showToast = function(options) {
    // Check if Laravel Notify has shown a notification recently
    if (window.laravelNotifyActive) {
        console.log('Toast suppressed - Laravel Notify is handling notifications');
        return;
    }

    // Default options
    var defaults = {
        text: '',
        heading: '',
        icon: 'success',
        showHideTransition: 'slide',
        hideAfter: 3000,
        position: 'top-right',
        loader: true,
        loaderBg: '#9EC600'
    };

    // Merge options with defaults
    var settings = $.extend({}, defaults, options);

    // Show the toast
    $.toast(settings);
};

// Override Laravel Notify to set our flag
$(document).ready(function() {
    // Check if notify.js elements exist
    var notifyElements = $('.notify, [data-notify], .notifyjs-wrapper, .notifyjs-container');
    
    if (notifyElements.length > 0) {
        window.laravelNotifyActive = true;
        console.log('Laravel Notify detected - manual toasts will be suppressed');
    }

    // Watch for dynamically added notify elements
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        var isNotify = $(node).hasClass('notify') || 
                                     $(node).hasClass('notifyjs-wrapper') ||
                                     $(node).hasClass('notifyjs-container') ||
                                     $(node).find('.notify, .notifyjs-wrapper').length > 0;
                        
                        if (isNotify) {
                            window.laravelNotifyActive = true;
                            // Reset flag after a delay
                            setTimeout(function() {
                                window.laravelNotifyActive = false;
                            }, 500);
                        }
                    }
                });
            }
        });
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});