
<?php
require_once __DIR__ . '/../../src/Autoloader.php';

use StackPress\Config\Config;
use StackPress\Controller\PostController;

Config::getInstance();
require_once __DIR__ . '/common/header.php';

$controller = new PostController();
$deleteSuccess = '';
$deleteError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    
    try {
        if ($controller->delete($deleteId)) {
            $deleteSuccess = "Post #$deleteId deleted successfully.";
        } else {
            $deleteError = "Post not found or already deleted.";
        }
    } catch (Exception $e) {
        $deleteError = "Error: " . $e->getMessage();
    }
}

$data = $controller->index();
$posts = array_map(fn($p) => $p->toArray(), $data['posts']);
?>
<main class="admin-dashboard">
    <h2>All Posts</h2>
    <p>
        <a class="admin-btn admin-btn-success" href="/public/admin/form.php">+ New Post</a>
        <a class="admin-btn admin-btn-success" href="/public/admin/publish.php">Publish</a>
        <a class="admin-btn admin-btn-primary" href="/public/build/index.html" target="_blank">View Site</a>
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
                        <a href="/public/admin/form.php?id=<?php echo urlencode($post['id']); ?>" class="admin-action-btn edit">Edit</a>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                            <button type="submit" class="admin-action-btn delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>
<?php require_once __DIR__ . '/common/footer.php'; ?>
