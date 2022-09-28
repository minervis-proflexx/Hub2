<?php

namespace srag\Plugins\Hub2\FileDrop\ResourceStorage;

use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\DI\Container;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;

/**
 * Interface ResourceStorage7
 *
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class ResourceStorage7 implements ResourceStorage
{

    /**
     * @var \ILIAS\ResourceStorage\Services
     */
    protected $services;
    /**
     * @var Stakeholder7
     */
    protected $stakeholder;

    public function __construct()
    {
        global $DIC;
        /**
         * @var $DIC Container
         */
        $this->services = $DIC->resourceStorage();
        $this->stakeholder = new Stakeholder7();
    }

    public function fromUpload(UploadResult $u): string
    {
        return $this->services->manage()->upload(
            $u,
            $this->stakeholder
        );
    }

    public function fromPath(string $u, string $mime_type = null): string
    {
        $stream = Streams::ofResource(fopen($u, "r"));
        return $this->services->manage()->stream(
            $stream,
            $this->stakeholder
        );
    }

    public function getDataURL(string $identification): string
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return '';
        }
        return $this->services->consume()->src($identification)->getSrc();
    }

    public function remove(string $identification): bool
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return false;
        }
        $this->services->manage()->remove($identification, $this->stakeholder);
        return true;
    }

    public function getRevisionInfo(string $identification): array
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return [];
        }
        $info = $this->services->manage()->getCurrentRevision($identification)->getInformation();
        $title = $info->getTitle();
        $size = $info->getSize();
        $mime_type = $info->getMimeType();

        return [
            'title' => $title,
            'size' => $size,
            'mime_type' => $mime_type
        ];
    }

    public function has(string $identification): bool
    {
        return $this->services->manage()->find($identification) instanceof ResourceIdentification;
    }

    public function getString(string $identification): string
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return '';
        }
        return $this->services->consume()->stream($identification)->getStream()->getContents();
    }

    public function getPath(string $identification): string
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return '';
        }
        return $this->services->consume()->stream($identification)->getStream()->getMetadata('uri');
    }

    public function fromString(string $content, string $mime_type = null): string
    {
        $stream = Streams::ofString($content);
        $identification = $this->services->manage()->stream(
            $stream,
            $this->stakeholder,
            'imported_file'
        );
        if ($mime_type !== null) {
            $revision = $this->services->manage()->getCurrentRevision($identification);
            $information = $revision->getInformation();
            $information->setMimeType($mime_type);
            $revision->setInformation($information);
            $this->services->manage()->updateRevision($revision);
        }

        return $identification;
    }

    public function download(string $identification): void
    {
        $identification = $this->services->manage()->find($identification);
        if ($identification === null) {
            return;
        }
        $this->services->consume()->download($identification)->run();
    }
}
