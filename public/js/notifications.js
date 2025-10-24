$(document).ready(function() {
    // Load notifications on page load
    loadNotifications();

    // Function to load notifications
    function loadNotifications() {
        $.get('<?= base_url('notifications') ?>')
            .done(function(data) {
                if (data.status === 'success') {
                    // Update notification badge
                    var badge = $('#notificationBadge');
                    if (data.unread_count > 0) {
                        badge.text(data.unread_count).show();
                    } else {
                        badge.hide();
                    }

                    // Update notifications dropdown
                    var dropdown = $('#notificationList');
                    dropdown.empty();
                    if (data.notifications.length > 0) {
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

        $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId)
            .done(function(data) {
                if (data.status === 'success') {
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
