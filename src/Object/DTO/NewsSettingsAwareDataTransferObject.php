<?php

/*********************************************************************
 * This Code is licensed under the GPL-3.0 License and is Part of a
 * ILIAS Plugin developed by sr solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

namespace srag\Plugins\Hub2\Object\DTO;

use srag\Plugins\Hub2\Object\General\NewsSettings;

/**
 * @author Fabian Schmid <fabian@sr.solutions>
 */
trait NewsSettingsAwareDataTransferObject
{
    protected ?NewsSettings $newsSettings = null;

    public function getNewsSettings(): ?NewsSettings
    {
        return $this->newsSettings;
    }

    public function setNewsSettings(?NewsSettings $newsSettings): INewsSettingsAwareDataTransferObject
    {
        $this->newsSettings = $newsSettings;
        return $this;
    }
}
