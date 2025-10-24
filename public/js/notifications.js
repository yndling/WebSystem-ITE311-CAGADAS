$(document).ready(function() {
    // Load notifications on page load
    loadNotifications();

    // Set interval to fetch notifications every 60 seconds for real-time updates
    setInterval(loadNotifications, 60000);

    // Function to load notifications
    function loadNotifications() {
        $.get(window.baseUrl + 'notifications')
            .done(function(data) {
                if (data.unreadCount !== undefined) {
                    // Update notification badge
                    var badge = $('#notificationBadge');
                    if (data.unreadCount > 0) {
                        badge.text(data.unreadCount).show();
                    } else {
                        badge.hide();
                    }

                    // Update notifications dropdown
                    var dropdown = $('#notificationList');
                    dropdown.empty();
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(function(notification) {
                            var itemClass = notification.is_read ? 'alert alert-secondary' : 'alert alert-info';
                            var item = '<li class="' + itemClass + ' mb-1 p-2">' +
                                '<small>' + notification.message + '</small><br>' +
                                '<small class="text-muted">' + notification.created_at + '</small>' +
                                '<button class="btn btn-sm btn-outline-primary ms-2 mark-read-btn" data-id="' + notification.id + '">Mark as Read</button>' +
                                '</li>';
                            dropdown.append(item);
                        });
                        dropdown.append('<li><hr class="dropdown-divider"></li>');
                        dropdown.append('<li><a class="dropdown-item text-center" href="#">View All Notifications</a></li>');
                    } else {
                        dropdown.append('<li class="dropdown-item-text">No notifications</li>');
                    }
                }
            })
            .fail(function() {
                console.log('Failed to load notifications');
            });
    }

    // Handle mark as read button clicks
    $(document).on('click', '.mark-read-btn', function(e) {
        e.preventDefault();
        var button = $(this);
        var notificationId = button.data('id');
        var item = button.closest('li');

        $.post(window.baseUrl + 'notifications/mark_read/' + notificationId)
            .done(function(data) {
                if (data.success) {
                    item.removeClass('alert-info').addClass('alert-secondary');
                    button.remove();
                    loadNotifications(); // Reload to update badge
                }
            })
            .fail(function() {
                console.log('Failed to mark notification as read');
            });
    });
});
