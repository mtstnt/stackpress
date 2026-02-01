<?php
    defined("ABSPATH") or die("You are not allowed to access this file.");
    
    $models = $data['posts'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Posts</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 30px auto; }
        .posts-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin: 0 -8px;
            justify-content: flex-start;
        }
        .post-card {
            background: #fff;
            border-radius: 7px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.09);
            padding: 20px 18px 16px 18px;
            margin: 8px 0;
            width: 280px;
            min-height: 120px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: box-shadow 0.18s, transform 0.18s;
        }
        .post-card:hover {
            box-shadow: 0 4px 18px rgba(0,0,0,0.17);
            transform: translateY(-4px) scale(1.025);
        }
        .post-title { 
            font-size: 1.18em; 
            font-weight: bold; 
            margin-bottom: 7px;
            color: #273c75;
        }
        .post-excerpt { 
            color: #444; 
            margin-bottom: 9px; 
        }
        .post-meta { 
            font-size: 0.97em; 
            color: #888; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom:18px;">All Posts</h1>
        <?php if (empty($models)): ?>
            <p>No posts found.</p>
        <?php else: ?>
        <div class="posts-grid">
            <?php foreach ($models as $post): ?>
                <div class="post-card">
                    <div>
                        <div class="post-title">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </div>
                        <div class="post-excerpt">
                            <?php
                                $excerpt = mb_substr($post['content'], 0, 80);
                                if (mb_strlen($post['content']) > 80) {
                                    $excerpt .= '&hellip;';
                                }
                                echo htmlspecialchars($excerpt);
                            ?>
                        </div>
                    </div>
                    <div class="post-meta">
                        By <?php echo htmlspecialchars($post['author']); ?> 
                        on <?php echo htmlspecialchars($post['date']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

