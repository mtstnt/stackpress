<?php
define("ABSPATH", dirname(__DIR__, 2));
require_once __DIR__ . '/common/header.php';

// Path to the posts data JSON file
$postsFile = ABSPATH . '/data/posts.json';

// Load posts from JSON file
$posts = [];
if (file_exists($postsFile)) {
    $json = file_get_contents($postsFile);
    $posts = json_decode($json, true);
}

// Get post by ID, or prepare empty for create
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$post = [
    'title' => '',
    'content' => '',
    'author' => ''
];
$notFound = false;
if ($id !== null) {
    $found = false;
    foreach ($posts as $item) {
        if ($item['id'] == $id) {
            $post = $item;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $notFound = true;
    }
}

// Handle form submissions
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Delete post by ID
        $newPosts = [];
        foreach ($posts as $item) {
            if ($item['id'] != $id) {
                $newPosts[] = $item;
            }
        }
        if (count($newPosts) < count($posts)) {
            file_put_contents($postsFile, json_encode($newPosts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = 'Post deleted successfully.';
            $posts = $newPosts;
            $post = [
                'title' => '',
                'content' => '',
                'author' => ''
            ];
            $id = null;
        } else {
            $error = 'Post not found to delete.';
        }
    } elseif (isset($_POST['save'])) {
        // Validate input
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author = trim($_POST['author'] ?? '');

        if ($title === '' || $content === '' || $author === '') {
            $error = 'All fields are required.';
        } else {
            if ($id !== null) {
                // Update existing post
                $updated = false;
                foreach ($posts as &$item) {
                    if ($item['id'] == $id) {
                        $item['title'] = $title;
                        $item['content'] = $content;
                        $item['author'] = $author;
                        $item['date'] = date('Y-m-d'); // Optionally update date
                        $updated = true;
                        $post = $item;
                        break;
                    }
                }
                unset($item);
                if ($updated) {
                    file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $success = 'Post updated successfully.';
                } else {
                    $error = 'Post not found.';
                }
            } else {
                // Create new post
                $maxId = 0;
                foreach ($posts as $item) {
                    if ($item['id'] > $maxId) {
                        $maxId = $item['id'];
                    }
                }
                $newPost = [
                    'id' => $maxId + 1,
                    'title' => $title,
                    'content' => $content,
                    'author' => $author,
                    'date' => date('Y-m-d')
                ];
                $posts[] = $newPost;
                file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $success = 'Post created successfully.';
                $post = [
                    'title' => '',
                    'content' => '',
                    'author' => ''
                ];
            }
        }
    }
}
?>

<main class="admin-form">
    <h2>
        <?php if ($id !== null): ?>
            Edit Post #<?php echo htmlspecialchars($id); ?>
        <?php else: ?>
            Create New Post
        <?php endif; ?>
    </h2>

    <?php if($notFound): ?>
        <div class="admin-message error">Post not found.</div>
    <?php else: ?>
        <?php if ($success): ?>
            <div class="admin-message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="admin-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>
                Title: <br />
                <input required type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" />
            </label><br><br>

            <label>
                Content: <br />
                <textarea required name="content" rows="8" cols="60"><?php echo htmlspecialchars($post['content']); ?></textarea>
            </label><br><br>

            <label>
                Author: <br />
                <input required type="text" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" />
            </label><br><br>

            <button type="submit" name="save">
                <?php echo ($id !== null ? 'Update Post' : 'Create Post'); ?>
            </button>
            <?php if ($id !== null): ?>
                <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this post?');" style="background:#c33;color:#fff;">
                    Delete Post
                </button>
            <?php endif; ?>
        </form>
    <?php endif; ?>

    <p>
        <a href="/public/admin/index.php">&larr; Back to Posts</a>
    </p>
</main>


<?php require_once __DIR__ . '/common/footer.php'; ?>
