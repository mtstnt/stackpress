<?php

namespace StackPress\Repository;

use StackPress\Model\Post;
use StackPress\Config\Config;

class PostRepository extends DataRepository {
    public function __construct(?string $dataPath = null) {
        $config = Config::getInstance();
        parent::__construct(
            $dataPath ?? $config->getDataPath(),
            'posts.json'
        );
    }

    public function findAll(): array {
        $data = $this->loadData();
        $posts = [];

        foreach ($data as $item) {
            $posts[] = Post::fromArray($item);
        }

        return $posts;
    }

    public function findById(int $id): ?Post {
        $posts = $this->findAll();

        foreach ($posts as $post) {
            if ($post->getId() === $id) {
                return $post;
            }
        }

        return null;
    }

    public function save(Post $post): void {
        $posts = $this->findAll();
        $found = false;

        foreach ($posts as $index => $existingPost) {
            if ($existingPost->getId() === $post->getId()) {
                $posts[$index] = $post;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $posts[] = $post;
        }

        $data = array_map(fn($p) => $p->toArray(), $posts);
        $this->saveData($data);
    }

    public function delete(int $id): bool {
        $posts = $this->findAll();
        $initialCount = count($posts);

        $posts = array_filter($posts, fn($p) => $p->getId() !== $id);

        if (count($posts) === $initialCount) {
            return false;
        }

        $data = array_map(fn($p) => $p->toArray(), array_values($posts));
        $this->saveData($data);

        return true;
    }

    public function getNextId(): int {
        $posts = $this->findAll();
        $maxId = 0;

        foreach ($posts as $post) {
            if ($post->getId() > $maxId) {
                $maxId = $post->getId();
            }
        }

        return $maxId + 1;
    }

    public function count(): int {
        return count($this->findAll());
    }
}
