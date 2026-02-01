# StackPress - Static Site Builder with Admin Panel

StackPress is a lightweight static site builder with an admin panel for managing content through JSON files. No database required.

## Project Structure

```
stackpress/
├── data/                      # JSON data files
│   └── posts.json            # Posts data (example)
├── public/                    # Public web files
│   ├── admin/                # Admin panel
│   │   ├── index.php        # List items (posts, pages, etc.)
│   │   ├── form.php         # Create/Edit form
│   │   ├── publish.php      # Build static site
│   │   └── common/          # Shared admin templates
│   │       ├── header.php
│   │       └── footer.php
│   └── build/               # Generated static HTML files
├── src/                      # Core application code (DO NOT EDIT)
│   ├── Autoloader.php      # PSR-4 autoloader
│   ├── App.php             # Application entry point
│   ├── Config/
│   │   └── Config.php      # Configuration manager
│   ├── Model/              # Data models
│   │   └── Post.php       # Post model (example)
│   ├── Repository/         # Data access layer
│   │   ├── DataRepository.php  # Base repository
│   │   └── PostRepository.php  # Posts repository (example)
│   ├── Controller/         # Business logic
│   │   ├── PostController.php  # Posts controller (example)
│   │   └── BuildController.php # Site builder controller
│   └── Service/           # Application services
│       └── Builder.php    # Static site builder
├── views/                    # Public view templates (EDIT THIS)
│   └── index.php          # Public site template
└── AGENTS.md              # This file
```

## Getting Started

1. **No Composer Required**: All code is ready to use. Simply copy files to your server.
2. **No Database**: Uses JSON files in `/data/` directory.
3. **Edit Only Views**: Modify `/views/` to customize your public site.

## How It Works

### Architecture

The application follows a clean separation of concerns:

- **Models** (`src/Model/`): Define data structure and business rules
- **Repositories** (`src/Repository/`): Handle data persistence (JSON files)
- **Controllers** (`src/Controller/`): Process requests and coordinate actions
- **Services** (`src/Service/`): Handle complex operations (building static site)
- **Views** (`views/`): Display data to users (public site only)

### Autoloading

The autoloader automatically loads classes following PSR-4 naming convention:

```php
use StackPress\Controller\PostController;
```

This loads `src/Controller/PostController.php`

### Building Static Site

Access `/public/admin/publish.php` to build the static site:
1. Loads all JSON data from `/data/`
2. Renders view templates from `/views/`
3. Outputs HTML files to `/public/build/`

## Adding New Data Types

### Step 1: Create JSON Data File

Create a new JSON file in `/data/`:

```json
[
    {
        "id": 1,
        "title": "First Product",
        "price": 99.99,
        "description": "Product description"
    }
]
```

Save as `/data/products.json`

### Step 2: Create Model

Create `src/Model/Product.php`:

```php
<?php

namespace StackPress\Model;

class Product {
    private int $id;
    private string $title;
    private float $price;
    private string $description;

    public function __construct(int $id, string $title, float $price, string $description) {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->description = $description;
    }

    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description
        ];
    }

    public static function fromArray(array $data): self {
        return new self($data['id'], $data['title'], $data['price'], $data['description']);
    }
}
```

### Step 3: Create Repository

Create `src/Repository/ProductRepository.php`:

```php
<?php

namespace StackPress\Repository;

use StackPress\Model\Product;
use StackPress\Config\Config;

class ProductRepository extends DataRepository {
    public function __construct(?string $dataPath = null) {
        $config = Config::getInstance();
        parent::__construct($dataPath ?? $config->getDataPath(), 'products.json');
    }

    public function findAll(): array {
        return array_map(fn($item) => Product::fromArray($item), $this->loadData());
    }

    public function findById(int $id): ?Product {
        foreach ($this->findAll() as $product) {
            if ($product->getId() === $id) return $product;
        }
        return null;
    }

    public function save(Product $product): void {
        $products = $this->findAll();
        $found = false;
        foreach ($products as $index => $existing) {
            if ($existing->getId() === $product->getId()) {
                $products[$index] = $product;
                $found = true;
                break;
            }
        }
        if (!$found) $products[] = $product;
        $this->saveData(array_map(fn($p) => $p->toArray(), $products));
    }

    public function delete(int $id): bool {
        $products = array_filter($this->findAll(), fn($p) => $p->getId() !== $id);
        if (count($products) === count($this->findAll())) return false;
        $this->saveData(array_map(fn($p) => $p->toArray(), array_values($products)));
        return true;
    }

    public function getNextId(): int {
        $maxId = 0;
        foreach ($this->findAll() as $product) {
            if ($product->getId() > $maxId) $maxId = $product->getId();
        }
        return $maxId + 1;
    }
}
```

### Step 4: Create Controller

Create `src/Controller/ProductController.php`:

```php
<?php

namespace StackPress\Controller;

use StackPress\Repository\ProductRepository;
use StackPress\Model\Product;

class ProductController {
    private ProductRepository $repository;

    public function __construct(?ProductRepository $repository = null) {
        $this->repository = $repository ?? new ProductRepository();
    }

    public function index(): array {
        return ['products' => $this->repository->findAll()];
    }

    public function create(array $data): Product {
        $this->validate($data);
        $nextId = $this->repository->getNextId();
        $product = new Product($nextId, $data['title'], (float)$data['price'], $data['description']);
        $this->repository->save($product);
        return $product;
    }

    public function update(int $id, array $data): ?Product {
        $product = $this->repository->findById($id);
        if (!$product) return null;
        $this->validate($data);
        $product->setTitle($data['title']);
        $product->setPrice((float)$data['price']);
        $product->setDescription($data['description']);
        $this->repository->save($product);
        return $product;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?Product {
        return $this->repository->findById($id);
    }

    private function validate(array $data): void {
        if (empty(trim($data['title'] ?? ''))) throw new \InvalidArgumentException('Title is required');
        if (empty($data['price'] ?? '')) throw new \InvalidArgumentException('Price is required');
        if (empty(trim($data['description'] ?? ''))) throw new \InvalidArgumentException('Description is required');
    }
}
```

### Step 5: Create View Template

Create `views/products.php`:

```php
<?php defined("ABSPATH") or die("Direct access not allowed"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .price { color: #27ae60; font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Our Products</h1>
        <?php if (empty($data['products'])): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($data['products'] as $product): ?>
                    <div class="product-card">
                        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
```

### Step 6: Create Admin Interface (Optional)

Create admin files similar to `posts` admin files:
- `public/admin/products/index.php` - List products
- `public/admin/products/form.php` - Create/edit products

Follow the same pattern as the posts admin files.

## Working with Views

### Available Data in Views

When the builder runs, all data from `/data/` is available:

```php
<?php
defined("ABSPATH") or die("Direct access not allowed");

// Data is automatically loaded and available
$posts = $data['posts'];
$products = $data['products'];
// ... any JSON file in /data/ is available as $data['filename']
?>
```

### Best Practices

1. Always use `defined("ABSPATH")` at the top
2. Use `htmlspecialchars()` to escape output
3. Keep logic minimal in views
4. Use CSS in `<style>` blocks or external stylesheets
5. Make views responsive

## Admin Panel

### Current Admin Files

- `/public/admin/index.php` - Posts list
- `/public/admin/form.php` - Post create/edit
- `/public/admin/publish.php` - Build static site

### Customizing Admin

Admin files use the controllers directly:

```php
<?php
require_once ABSPATH . '/src/Autoloader.php';

use StackPress\Controller\PostController;

$controller = new PostController();

// Get all posts
$data = $controller->index();
$posts = $data['posts'];

// Create new post
$post = $controller->create([
    'title' => 'New Post',
    'content' => 'Content here',
    'author' => 'John Doe'
]);

// Update existing post
$post = $controller->update(1, [
    'title' => 'Updated Title',
    'content' => 'Updated content',
    'author' => 'Jane Doe'
]);

// Delete post
$controller->delete(1);
```

## Configuration

Edit `src/Config/Config.php` to change paths:

```php
private function __construct() {
    define('ABSPATH', dirname(__DIR__, 2));
    $this->config = [
        'data_path' => ABSPATH . '/data',
        'views_path' => ABSPATH . '/views',
        'build_path' => ABSPATH . '/public/build',
        'admin_path' => ABSPATH . '/public/admin'
    ];
}
```

## Troubleshooting

### Permission Issues

Ensure write permissions on:
- `/data/` - For creating/modifying JSON files
- `/public/build/` - For generating static files

### Autoloader Not Working

- Ensure `src/Autoloader.php` is included
- Check namespace matches file path
- Verify class names match filenames

### JSON Errors

- Validate JSON using https://jsonlint.com/
- Ensure all data files are valid JSON arrays
- Check for proper UTF-8 encoding

## Development

### Testing Locally

1. Use PHP's built-in server:
   ```bash
   cd public
   php -S localhost:8000
   ```

2. Access admin at: http://localhost:8000/admin/
3. View built site at: http://localhost:8000/build/

### Deployment

1. Upload entire directory to web server
2. Ensure `/data/` and `/public/build/` are writable
3. Access admin at `/public/admin/`
4. Publish site via admin panel

## License

Free to use and modify for any purpose.
