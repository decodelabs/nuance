<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Structure;

class Container
{
    public protected(set) string $type;
    public protected(set) string $id;
    public string $renderedName = '';
    public ?int $objectId = null;
    public bool $open = true;
    public bool $sensitive = false;

    /**
     * @var array<string,Section>
     */
    public protected(set) array $sections = [];

    public function __construct(
        string $type,
        string $id,
        ?int $objectId = null,
        bool $open = true,
        bool $sensitive = false
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->objectId = $objectId;
        $this->open = $open;
        $this->sensitive = $sensitive;
    }

    public function addSection(
        Section $section
    ): void {
        $this->sections[$section->key] = $section;
    }

    public function getSection(
        string $key
    ): ?Section {
        return $this->sections[$key] ?? null;
    }

    public function removeSection(
        string $key
    ): void {
        unset($this->sections[$key]);
    }

    /**
     * @return array<Section>
     */
    public function getOpenSections(): array
    {
        return array_filter(
            $this->sections,
            static fn (Section $section) => $section->open
        );
    }

    public function sortSections(): void
    {
        uasort(
            $this->sections,
            static fn (Section $a, Section $b) => $a->priority <=> $b->priority
        );
    }

    public function getOpenId(): ?string
    {
        $openId = null;
        $firstId = null;

        foreach ($this->sections as $section) {
            if ($firstId === null) {
                $firstId = $section->id;
            }

            if (
                $openId === null &&
                $section->open
            ) {
                $openId = $section->id;
            }
        }

        return $openId ?? $firstId;
    }
}
