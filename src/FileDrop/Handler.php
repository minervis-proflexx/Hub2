<?php

namespace srag\Plugins\Hub2\FileDrop;

use ilContext;
use ilHub2Plugin;
use ilInitialisation;
use srag\Plugins\Hub2\Exception\ShortlinkException;
use ILIAS\DI\HTTPServices;
use srag\Plugins\Hub2\Origin\OriginRepository;
use srag\Plugins\Hub2\Origin\OriginFactory;
use srag\Plugins\Hub2\Origin\IOrigin;
use srag\Plugins\Hub2\Origin\Config\IOriginConfig;
use srag\Plugins\Hub2\FileDrop\ResourceStorage\Factory;

/**
 * Class Handler
 * @author Fabian Schmid <fabian@sr.solutions>
 */
class Handler
{
    const PLUGIN_BASE = "Customizing/global/plugins/Services/Cron/CronHook/Hub2/";
    const DROP_FILE = "file_drop.php";
    const METHOD = 'POST';
    const PHP_AUTH_USER = 'PHP_AUTH_USER';
    const PHP_AUTH_PW = 'PHP_AUTH_PW';
    const FD_CONTAINER = 'fd_container';
    /**
     * @var \ILIAS\FileUpload\DTO\UploadResult[]
     */
    private $uploaded_files = [];
    /**
     * @var \ILIAS\FileUpload\FileUpload
     */
    private $upload;
    /**
     * @var ResourceStorage\ResourceStorage
     */
    private $storage;
    /**
     * @var ResourceStorage\Stakeholder6|ResourceStorage\Stakeholder7
     */
    private $stakeholder;
    /**
     * @var string
     */
    protected $file_drop_container = '';
    /**
     * @var bool
     */
    protected $init = false;
    /**
     * @var null|HTTPServices
     */
    private $http = null;

    /**
     * Handler constructor
     * @param string $ext_id
     */
    public function __construct()
    {
        $this->init = false;
        $this->tryILIASInitPublic();
        global $DIC;
        $this->http = $DIC->http();
        $this->upload = $DIC->upload();
        $this->file_drop_container = '';
        $f = new Factory();
        $this->storage = $f ->storage();
        $this->stakeholder = $f ->stakeholder();
    }

    public static function getURL(string $file_drop_container): string
    {
        return ILIAS_HTTP_PATH
            . '/' . self::PLUGIN_BASE . self::DROP_FILE
            . '?' . self::FD_CONTAINER . '=' . $file_drop_container;
    }

    private function getOriginByFileDropContainer(string $file_drop_container): IOrigin
    {
        // currently we map the file drop container to the origin id
        $origin_id = (int) ltrim($file_drop_container, 'o');
        $repo = new OriginFactory();
        $origin = $repo->getById($origin_id);
        if ($origin === null) {
            throw new \LogicException('Origin not found');
        }
        return $origin;
    }

    /**
     * @return void
     * @throws \ILIAS\FileUpload\Exception\IllegalStateException
     */
    protected function processFiles(): void
    {
        $origin = $this->getOriginByFileDropContainer($this->file_drop_container);
        $this->upload->process();
        $this->uploaded_files = $this->upload->getResults();

        if(count($this->uploaded_files) > 1) {
            throw new \LogicException('currently only one file is supported');
        }

        $result = end($this->uploaded_files);

        if ($result->getStatus()->getCode() !== \ILIAS\FileUpload\DTO\ProcessingStatus::OK) {
            $this->throwException('Upload failed: ' . $result->getStatus()->getMessage());
        }
        $current_rid = $origin->config()->get(IOriginConfig::FILE_DROP_RID);
        $rid = $this->storage->replaceUpload($result, $current_rid ?? '');
        $origin->config()->setData([IOriginConfig::FILE_DROP_RID => $rid]);
        $origin->store();
        echo $result->getName() . " uploaded successfully in $rid<br>";
    }

    /**
     * @param $DIC
     * @return void
     */
    protected function checkAuth(string $file_drop_token): bool
    {
        $origin = $this->getOriginByFileDropContainer($this->file_drop_container);
        $user = $origin->config()->get(IOriginConfig::FILE_DROP_USER);
        $password = $origin->config()->get(IOriginConfig::FILE_DROP_PASSWORD);

        $this->http->request()->getMethod() === self::METHOD || $this->throwException('Method not allowed');
        $this->http->request()->getServerParams()[self::PHP_AUTH_USER] === $user
        || $this->throwException('Authentication failed');
        $this->http->request()->getServerParams()[self::PHP_AUTH_PW] === $password
        || $this->throwException('Authentication failed');

        return true;
    }

    private function throwException(string $message): void
    {
        throw new \LogicException($message);
    }

    /**
     * @throws ShortlinkException
     */
    public function process()
    {
        global $DIC;

        if (!$this->init || !$DIC->isDependencyAvailable('database')) {
            throw new ShortlinkException("ILIAS not initialized, aborting...");
        }

        // BASIC CHECKS
        $this->file_drop_container = $this->http->request()->getQueryParams()[self::FD_CONTAINER] ?? '';
        if ($this->checkAuth($this->file_drop_container)) {
            $this->processFiles();
        }
    }

    public function tryILIASInit()
    {
        $this->prepareILIASInit();

        require_once("Services/Init/classes/class.ilInitialisation.php");
        ilInitialisation::initILIAS();

        $this->init = true;
        global $DIC;
        $this->http = $DIC->http();
    }

    /**
     *
     */
    public function tryILIASInitPublic()
    {
        $this->prepareILIASInit();
        global $DIC;

        require_once 'Services/Context/classes/class.ilContext.php';
        ilContext::init(ilContext::CONTEXT_WAC);
        require_once "Services/Init/classes/class.ilInitialisation.php";
        ilInitialisation::initILIAS();
        $ilAuthSession = $DIC["ilAuthSession"];
        $ilAuthSession->init();
        $ilAuthSession->regenerateId();
        $a_id = (int) ANONYMOUS_USER_ID;
        $ilAuthSession->setUserId($a_id);
        $ilAuthSession->setAuthenticated(false, $a_id);
        $DIC->user()->setId($a_id);
        $this->init = true;
    }

    /**
     *
     */
    protected function prepareILIASInit()
    {
        $GLOBALS['COOKIE_PATH'] = '/';
        $_GET["client_id"] = $_COOKIE['ilClientId'];
    }
}
