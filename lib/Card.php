<?php
namespace Memory;

class Card
{
    private string $id;
    private string $image;
    private bool $matched = false;

    public function __construct(string $id, string $image)
    {
        $this->id = $id;
        $this->image = $image;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function isMatched(): bool
    {
        return $this->matched;
    }

    public function setMatched(bool $v = true): void
    {
        $this->matched = $v;
    }
}
