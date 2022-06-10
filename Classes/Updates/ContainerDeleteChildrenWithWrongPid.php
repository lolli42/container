<?php

declare(strict_types=1);

namespace B13\Container\Updates;

/*
 * This file is part of TYPO3 CMS-based extension "container" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Container\Integrity\Error\WrongPidError;
use B13\Container\Integrity\Integrity;
use B13\Container\Integrity\IntegrityFix;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class ContainerDeleteChildrenWithWrongPid implements UpgradeWizardInterface
{
    public const IDENTIFIER = 'container_deleteChildrenWithWrongPid';

    /**
     * @var Integrity
     */
    protected $integrity;

    /**
     * @var IntegrityFix
     */
    protected $integrityFix;

    public function __construct(Integrity $integrity, IntegrityFix $integrityFix)
    {
        $this->integrity = $integrity;
        $this->integrityFix = $integrityFix;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * @return string Title of this updater
     */
    public function getTitle(): string
    {
        return 'EXT:container: Delete "container" children whith wrong pid';
    }

    /**
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'if you update from Version < 1.3 you may have children with wrong pid and they was never shown in BE/FE';
    }

    public function updateNecessary(): bool
    {
        $res = $this->integrity->run();
        foreach ($res['errors'] as $error) {
            if ($error instanceof WrongPidError) {
                return true;
            }
        }
        return false;
    }

    public function executeUpdate(): bool
    {
        $res = $this->integrity->run();
        foreach ($res['errors'] as $error) {
            if ($error instanceof WrongPidError) {
                $this->integrityFix->deleteChildrenWithWrongPid($error);
            }
        }
        return true;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
