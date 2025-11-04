<?php

/**
 * Nuance
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

class Binary extends NativeString implements Structured
{
    use StructuredTrait;

    public function getHex(): string
    {
        return bin2hex($this->value);
    }

    /**
     * @return list<string>
     */
    public function splitChunks(): array
    {
        return explode("\n", trim(chunk_split($this->getHex(), 2, "\n")));
    }

    /**
     * @return list<list<string>>
     */
    public function splitChunkRows(
        int $length = 16
    ): array {
        $chunks = $this->splitChunks();
        $rows = [];
        $row = [];

        foreach ($chunks as $chunk) {
            $row[] = $chunk;

            if (count($row) >= $length) {
                $rows[] = $row;
                $row = [];
            }
        }

        if (!empty($row)) {
            $rows[] = $row;
        }

        return $rows;
    }
}
