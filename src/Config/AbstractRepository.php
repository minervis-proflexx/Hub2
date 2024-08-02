<?php

namespace srag\Plugins\Hub2\Config;

use ilDateTime;
use ilDateTimeException;
use LogicException;
use srag\DIC\Hub2\DICTrait;

/**
 * Class AbstractRepository
 *
 * @package srag\ActiveRecordConfig\Hub2\Config
 */
abstract class AbstractRepository
{
    protected \ilDBInterface $db;

    /**
     * AbstractRepository constructor
     */
    protected function __construct()
    {
        global $DIC;
        $this->db = $DIC->database();
        Config::setTableName($this->getTableName());
    }

    /**
     *
     */
    public function dropTables(): void
    {
        $this->db->dropTable(Config::getTableName(), false);
    }

    abstract public function factory(): AbstractFactory;

    /**
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (isset($this->getFields()[$name])) {
            $field = $this->getFields()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            $default_value = $field[1] ?? Config::TYPE_STRING;

            switch ($type) {
                case Config::TYPE_STRING:
                    return $this->getStringValue($name, $default_value);

                case Config::TYPE_INTEGER:
                    return $this->getIntegerValue($name, $default_value);

                case Config::TYPE_DOUBLE:
                    return $this->getFloatValue($name, $default_value);

                case Config::TYPE_BOOLEAN:
                    return $this->getBooleanValue($name, $default_value);

                case Config::TYPE_TIMESTAMP:
                    return $this->getTimestampValue($name, $default_value);

                case Config::TYPE_DATETIME:
                    return $this->getDateTimeValue($name, $default_value);

                case Config::TYPE_JSON:
                    $assoc = (bool) $field[2];

                    return $this->getJsonValue($name, $assoc, $default_value);

                default:
                    throw new LogicException("Invalid type $type!");
            }
        }

        throw new LogicException("Invalid field $name!");
    }

    public function getValues(): array
    {
        $values = [];

        foreach ($this->getFields() as $name) {
            $values[$name] = $this->getValue($name);
        }

        return $values;
    }

    /**
     *
     */
    public function installTables(): void
    {
        Config::updateDB();
    }

    public function removeValue(string $name): void
    {
        $config = $this->getConfig($name, false);

        $this->deleteConfig($config);
    }

    /**
     * @param mixed $value
     */
    public function setValue(string $name, $value): void
    {
        if (isset($this->getFields()[$name])) {
            $field = $this->getFields()[$name];
            if (!is_array($field)) {
                $field = [$field];
            }

            $type = $field[0];

            switch ($type) {
                case Config::TYPE_STRING:
                    $this->setStringValue($name, $value);

                    return;

                case Config::TYPE_INTEGER:
                    $this->setIntegerValue($name, $value);

                    return;

                case Config::TYPE_DOUBLE:
                    $this->setFloatValue($name, $value);

                    return;

                case Config::TYPE_BOOLEAN:
                    $this->setBooleanValue($name, $value);

                    return;

                case Config::TYPE_TIMESTAMP:
                    $this->setTimestampValue($name, $value);

                    return;

                case Config::TYPE_DATETIME:
                    $this->setDateTimeValue($name, $value);

                    return;

                case Config::TYPE_JSON:
                    $this->setJsonValue($name, $value);

                    return;

                default:
                    throw new LogicException("Invalid type $type!");
            }
        }

        throw new LogicException("Invalid field $name!");
    }

    public function setValues(array $values, bool $remove_exists = false): void
    {
        if ($remove_exists) {
            Config::truncateDB();
        }

        foreach ($values as $name => $value) {
            $this->setValue($name, $value);
        }
    }

    protected function deleteConfig(Config $config): void
    {
        $config->delete();
    }

    /**
     * @param mixed $default_value
     *
     */
    protected function getBooleanValue(string $name, $default_value = false): bool
    {
        return filter_var($this->getXValue($name, $default_value), FILTER_VALIDATE_BOOLEAN);
    }

    protected function getConfig(string $name, bool $store_new = true): Config
    {
        /**
         * @var Config $config
         */

        $config = Config::where([
            "name" => $name
        ])->first();

        if ($config === null) {
            $config = $this->factory()->newInstance();

            $config->setName($name);

            if ($store_new) {
                $this->storeConfig($config);
            }
        }

        return $config;
    }

    /**
     * @param ilDateTime|null $default_value
     *
     */
    protected function getDateTimeValue(string $name, /*?*/ ilDateTime $default_value = null): ?ilDateTime
    {
        $value = $this->getXValue($name);

        if ($value !== null) {
            try {
                $value = new ilDateTime(IL_CAL_DATETIME, $value);
            } catch (ilDateTimeException $ex) {
                $value = $default_value;
            }
        } else {
            $value = $default_value;
        }

        return $value;
    }

    abstract protected function getFields(): array;

    /**
     * @param mixed $default_value
     *
     */
    protected function getFloatValue(string $name, $default_value = 0.0): float
    {
        return (float) $this->getXValue($name, $default_value);
    }

    /**
     * @param mixed $default_value
     *
     */
    protected function getIntegerValue(string $name, $default_value = 0): int
    {
        return (int) $this->getXValue($name, $default_value);
    }

    /**
     * @param mixed $default_value
     *
     * @return mixed
     */
    protected function getJsonValue(string $name, bool $assoc = false, $default_value = null)
    {
        return json_decode(
            $this->getXValue($name, json_encode($default_value, JSON_THROW_ON_ERROR)),
            $assoc,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param mixed $default_value
     *
     */
    protected function getStringValue(string $name, $default_value = ""): string
    {
        return (string) $this->getXValue($name, $default_value);
    }

    abstract protected function getTableName(): string;

    protected function getTimestampValue(string $name, int $default_value = 0): int
    {
        $value = $this->getDateTimeValue($name);

        if ($value instanceof \ilDateTime) {
            return $value->getUnixTime();
        }
        return $default_value;
    }

    /**
     * @param mixed $default_value
     * @return mixed
     */
    protected function getXValue(string $name, $default_value = null)
    {
        $config = $this->getConfig($name);

        $value = $config->getValue();

        if ($value === null) {
            return $default_value;
        }

        return $value;
    }

    protected function isNullValue(string $name): bool
    {
        return ($this->getXValue($name) === null);
    }

    /**
     * @param mixed $value
     */
    protected function setBooleanValue(string $name, $value): void
    {
        $this->setXValue($name, json_encode(filter_var($value, FILTER_VALIDATE_BOOLEAN), JSON_THROW_ON_ERROR));
    }

    /**
     * @param ilDateTime|null $value
     */
    protected function setDateTimeValue(string $name, /*?*/ ilDateTime $value = null): void
    {
        if ($value instanceof \ilDateTime) {
            $this->setXValue($name, $value->get(IL_CAL_DATETIME));
        } else {
            $this->setNullValue($name);
        }
    }

    /**
     * @param mixed $value
     */
    protected function setFloatValue(string $name, $value): void
    {
        $this->setXValue($name, (float) $value);
    }

    /**
     * @param mixed $value
     */
    protected function setIntegerValue(string $name, $value): void
    {
        $this->setXValue($name, (int) $value);
    }

    /**
     * @param mixed $value
     */
    protected function setJsonValue(string $name, $value): void
    {
        $this->setXValue($name, json_encode($value, JSON_THROW_ON_ERROR));
    }

    protected function setNullValue(string $name): void
    {
        $this->setXValue($name, null);
    }

    /**
     * @param mixed $value
     */
    protected function setStringValue(string $name, $value): void
    {
        $this->setXValue($name, (string) $value);
    }

    protected function setTimestampValue(string $name, int $value): void
    {
        if ($value !== null) {
            try {
                $this->setDateTimeValue($name, new ilDateTime(IL_CAL_UNIX, $value));
            } catch (ilDateTimeException $ex) {
            }
        } else {
            // Fix `@null`
            $this->setNullValue($name);
        }
    }

    /**
     * @param mixed $value
     */
    protected function setXValue(string $name, $value): void
    {
        $config = $this->getConfig($name, false);

        $config->setValue($value);

        $this->storeConfig($config);
    }

    protected function storeConfig(Config $config): void
    {
        $config->store();
    }
}
