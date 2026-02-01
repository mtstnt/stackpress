<?php
require_once __DIR__ . '/../../src/Autoloader.php';

use StackPress\Config\Config;
use StackPress\Controller\PostController;

Config::getInstance();
require_once __DIR__ . '/common/header.php';

$controller = new PostController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$success = '';
$error = '';
$notFound = false;

$postModel = null;
if ($id !== null) {
    $postModel = $controller->findById($id);
    if ($postModel === null) {
        $notFound = true;
    }
}

$post = [
    'title' => '',
    'content' => '',
    'author' => ''
];

if ($postModel !== null) {
    $post = [
        'title' => $postModel->getTitle(),
        'content' => $postModel->getContent(),
        'author' => $postModel->getAuthor()
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        try {
            if ($controller->delete($id)) {
                $success = 'Post deleted successfully.';
                $id = null;
                $notFound = false;
                $post = ['title' => '', 'content' => '', 'author' => ''];
            } else {
                $error = 'Post not found.';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['save'])) {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'author' => trim($_POST['author'] ?? '')
        ];

        try {
            if ($id !== null) {
                $postModel = $controller->update($id, $data);
                if ($postModel !== null) {
                    $success = 'Post updated successfully.';
                    $post = [
                        'title' => $postModel->getTitle(),
                        'content' => $postModel->getContent(),
                        'author' => $postModel->getAuthor()
                    ];
                } else {
                    $error = 'Post not found.';
                }
            } else {
                $postModel = $controller->create($data);
                $success = 'Post created successfully.';
                $post = ['title' => '', 'content' => '', 'author' => ''];
            }
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
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
            <label for="title">Title</label>
            <input required type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" />

            <label for="content">Content</label>
            <textarea required id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>

            <label for="author">Author</label>
            <input required type="text" id="author" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" />

            <button type="submit" name="save">
                <?php echo ($id !== null ? 'Update Post' : 'Create Post'); ?>
            </button>
            <?php if ($id !== null): ?>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this post?');" class="delete">
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
