
<?php
define("ABSPATH", dirname(__DIR__, 2));

// Optionally check for admin authentication here

require_once __DIR__ . '/common/header.php';

// Handle delete operation
$deleteSuccess = '';
$deleteError = '';
$postsFile = ABSPATH . '/data/posts.json';

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    if (file_exists($postsFile)) {
        $json = file_get_contents($postsFile);
        $posts = json_decode($json, true);

        $found = false;
        foreach ($posts as $idx => $p) {
            if ($p['id'] == $deleteId) {
                $found = true;
                array_splice($posts, $idx, 1);
                break;
            }
        }
        if ($found) {
            if (file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT))) {
                $deleteSuccess = "Post #$deleteId deleted successfully.";
            } else {
                $deleteError = "Error saving changes to posts file.";
            }
        } else {
            $deleteError = "Post not found or already deleted.";
        }
    } else {
        $deleteError = "Posts file not found.";
    }
}

// Reload posts list after possible deletion
$posts = [];
if (file_exists($postsFile)) {
    $json = file_get_contents($postsFile);
    $posts = json_decode($json, true);
}
?>
<main class="admin-dashboard">
    <h2>All Posts</h2>
    <p>
        <a class="admin-btn" href="/public/admin/form.php" style="padding:4px 14px;border-radius:4px;background:#27ae60;color:#fff;text-decoration:none;">+ New Post</a>
        <a class="admin-btn" href="/public/admin/publish.php" style="padding:4px 14px;border-radius:4px;background:#27ae60;color:#fff;text-decoration:none;">Publish</a>
        <a class="admin-btn" href="/public/build/index.html" target="_blank" style="padding:4px 14px;border-radius:4px;background:#27ae60;color:#fff;text-decoration:none;">View Site</a>
    </p>
    <?php if (!empty($deleteSuccess)): ?>
        <div class="admin-message success"><?php echo htmlspecialchars($deleteSuccess); ?></div>
    <?php endif; ?>
    <?php if (!empty($deleteError)): ?>
        <div class="admin-message error"><?php echo htmlspecialchars($deleteError); ?></div>
    <?php endif; ?>
    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Excerpt</th>
                <th>Author</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['id']); ?></td>
                    <td>
                        <a href="/public/admin/form.php?id=<?php echo urlencode($post['id']); ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </td>
                    <td>
                        <?php
                            $excerpt = mb_substr($post['content'], 0, 60);
                            if (mb_strlen($post['content']) > 60) {
                                $excerpt .= '...';
                            }
                            echo htmlspecialchars($excerpt);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($post['author']); ?></td>
                    <td><?php echo htmlspecialchars($post['date']); ?></td>
                    <td>
                        <a href="/public/admin/form.php?id=<?php echo urlencode($post['id']); ?>" class="admin-action-btn" style="padding:2px 8px;background:#2980b9;color:#fff;text-decoration:none;border-radius:3px;">Edit</a>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                            <button type="submit" class="admin-action-btn" style="background:#c33;color:#fff;padding:2px 8px;border:none;border-radius:3px;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/common/footer.php'; ?>
