<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity\NativeObject;

use DecodeLabs\Monarch;
use DecodeLabs\Nuance\Entity\NativeObject;
use DecodeLabs\Nuance\Reflection;
use SplFileInfo as SplFileInfoObject;

class SplFileInfo extends NativeObject
{
    public function __construct(
        SplFileInfoObject $file,
    ) {
        parent::__construct($file);

        $path = $file->getPathname();

        if (class_exists(Monarch::class)) {
            $path = Monarch::$paths->prettify($path);
        }

        $this->text = $path;
        $this->itemName = basename($file->getPathname());
        $this->meta['type'] = $type = $file->getType();

        if ($type === 'link') {
            $this->meta['target'] = $file->getLinkTarget();
        }

        $this->meta['size'] = Reflection::formatFilesize($file->getSize());
        $this->meta['perms'] = decoct($file->getPerms());
        $this->meta['aTime'] = date('Y-m-d H:i:s', $file->getATime());
        $this->meta['mTime'] = date('Y-m-d H:i:s', $file->getMTime());
        $this->meta['cTime'] = date('Y-m-d H:i:s', $file->getCTime());

        $this->open = false;
    }
}
