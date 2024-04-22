<?php

namespace srag\Plugins\Hub2\Origin;

use srag\Plugins\Hub2\Log\ILog;
use srag\Plugins\Hub2\Object\DTO\IDataTransferObject;
use srag\Plugins\Hub2\Object\HookObject;
use srag\Plugins\Hub2\Exception\ConnectionFailedException;
use srag\Plugins\Hub2\Parser\Csv;

/**
 * Class AbstractCSVOriginImplementation
 */
abstract class AbstractCSVOriginImplementation extends AbstractOriginImplementation
{
    use FileConnection;
    /**
     * @var Csv
     */
    protected $csv_parser;
    /**
     * @var array
     */
    protected $csv = [];

    protected function getEnclosure() : string
    {
        return '"';
    }

    protected function getSeparator() : string
    {
        return ";";
    }

    public function parseData() : int
    {
        $this->csv_parser = new Csv(
            $this->file_path,
            $this->getUniqueField(),
            $this->getMandatoryColumns(),
            $this->getColumnMapping(),
            $this->getEnclosure(),
            $this->getSeparator()
        );

        foreach ($this->getFilters() as $filter) {
            $this->csv_parser->addFilter($filter);
        }

        $this->csv = $this->csv_parser->parseData();
        return count($this->csv);
    }

    abstract protected function getMandatoryColumns() : array;

    protected function getColumnMapping() : array
    {
        return [];
    }

    abstract protected function getUniqueField() : string;

    /**
     * @return IDataTransferObject[]
     */
    abstract protected function buildObjectsFromCSV(array $csv_data);

    /**
     * @return IDataTransferObject[]
     */
    public function buildObjects()
    {
        return $this->buildObjectsFromCSV($this->csv);
    }

    protected function getFilter() : \Closure
    {
        return static function (array $item) : bool {
            return true;
        };
    }

    protected function getFilters() : array
    {
        return [
            $this->getFilter()
        ];
    }

    public function handleLog(ILog $log) : void
    {
        // TODO: Implement handleLog() method.
    }

    public function beforeCreateILIASObject(HookObject $hook) : void
    {
        // TODO: Implement beforeCreateILIASObject() method.
    }

    public function afterCreateILIASObject(HookObject $hook) : void
    {
        // TODO: Implement afterCreateILIASObject() method.
    }

    public function beforeUpdateILIASObject(HookObject $hook) : void
    {
        // TODO: Implement beforeUpdateILIASObject() method.
    }

    public function afterUpdateILIASObject(HookObject $hook) : void
    {
        // TODO: Implement afterUpdateILIASObject() method.
    }

    public function beforeDeleteILIASObject(HookObject $hook) : void
    {
        // TODO: Implement beforeDeleteILIASObject() method.
    }

    public function afterDeleteILIASObject(HookObject $hook) : void
    {
        // TODO: Implement afterDeleteILIASObject() method.
    }

    public function beforeSync() : void
    {
        // TODO: Implement beforeSync() method.
    }

    public function afterSync() : void
    {
        // TODO: Implement afterSync() method.
    }
}
