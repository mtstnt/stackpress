<?php

namespace StackPress\Controller;

use StackPress\Repository\PostRepository;
use StackPress\Model\Post;

class PostController {
    private PostRepository $repository;

    public function __construct(?PostRepository $repository = null) {
        $this->repository = $repository ?? new PostRepository();
    }

    public function index(): array {
        return [
            'posts' => $this->repository->findAll()
        ];
    }

    public function create(array $data): Post {
        $this->validatePostData($data);

        $nextId = $this->repository->getNextId();
        $post = new Post(
            $nextId,
            $data['title'],
            $data['content'],
            $data['author'],
            date('Y-m-d')
        );

        $this->repository->save($post);
        return $post;
    }

    public function update(int $id, array $data): ?Post {
        $post = $this->repository->findById($id);

        if ($post === null) {
            return null;
        }

        $this->validatePostData($data);

        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setAuthor($data['author']);
        $post->setDate(date('Y-m-d'));

        $this->repository->save($post);
        return $post;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?Post {
        return $this->repository->findById($id);
    }

    private function validatePostData(array $data): void {
        if (empty(trim($data['title'] ?? ''))) {
            throw new \InvalidArgumentException('Title is required');
        }

        if (empty(trim($data['content'] ?? ''))) {
            throw new \InvalidArgumentException('Content is required');
        }

        if (empty(trim($data['author'] ?? ''))) {
            throw new \InvalidArgumentException('Author is required');
        }
    }
}
