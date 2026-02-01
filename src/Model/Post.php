<?php

namespace StackPress\Model;

class Post {
    private int $id;
    private string $title;
    private string $content;
    private string $author;
    private string $date;

    public function __construct(int $id, string $title, string $content, string $author, string $date) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->date = $date;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function setAuthor(string $author): void {
        $this->author = $author;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function setDate(string $date): void {
        $this->date = $date;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author,
            'date' => $this->date
        ];
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['author'],
            $data['date']
        );
    }

    public function getExcerpt(int $length = 60): string {
        $excerpt = mb_substr($this->content, 0, $length);
        if (mb_strlen($this->content) > $length) {
            $excerpt .= '...';
        }
        return $excerpt;
    }
}
