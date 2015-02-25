<?php

namespace Creads\Api2Symfony\Diff;

/**
 * Compare to controller definitions
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class DiffEngine
{
    /**
     * compare
     *
     * @param Gnugat/Medio/Model/File $oldClass
     * @param Gnugat/Medio/Model/File $newClass
     *
     * @return Pitpit\Component\Diff\Diff
     */
    public function compare($oldClass, $newClass);
}
