<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .announcement { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .announcement h3 { margin-top: 0; }
        .date { color: #666; font-size: 0.9em; }
        .no-announcements { text-align: center; color: #999; }
    </style>
</head>
<body>
    <h1>Announcements</h1>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="color: red; background-color: #ffe6e6; padding: 10px; border: 1px solid red; margin-bottom: 20px;">
            <?php echo session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <h3><?php echo esc($announcement['title']); ?></h3>
                <p><?php echo esc($announcement['content']); ?></p>
                <div class="date">Posted on: <?php echo esc($announcement['created_at']); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-announcements">No announcements available at the moment.</p>
    <?php endif; ?>
</body>
</html>
