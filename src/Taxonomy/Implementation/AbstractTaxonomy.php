<?php

namespace srag\Plugins\Hub2\Taxonomy\Implementation;

use ilHub2Plugin;
use ilObjTaxonomy;
use ilTaxonomyTree;
use srag\DIC\Hub2\DICTrait;
use srag\Plugins\Hub2\Taxonomy\ITaxonomy;
use srag\Plugins\Hub2\Taxonomy\Node\INode;
use srag\Plugins\Hub2\Utils\Hub2Trait;

/**
 * Class AbstractTaxonomy
 * @package srag\Plugins\Hub2\Taxonomy\Implementation
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class AbstractTaxonomy implements ITaxonomyImplementation
{
    use DICTrait;
    use Hub2Trait;

    public const PLUGIN_CLASS_NAME = ilHub2Plugin::class;
    /**
     * @var int
     */
    protected $tree_root_id;
    /**
     * @var ilTaxonomyTree
     */
    protected $tree;
    /**
     * @var array
     */
    protected $childs = [];
    /**
     * @var ilObjTaxonomy
     */
    protected $ilObjTaxonomy;
    /**
     * @var ITaxonomy
     */
    protected $taxonomy;
    /**
     * @var int
     */
    protected $ilias_parent_id;

    /**
     * Taxonomy constructor
     * @param ITaxonomy $taxonomy
     */
    public function __construct(ITaxonomy $taxonomy, int $ilias_parent_id)
    {
        $this->taxonomy = $taxonomy;
        $this->ilias_parent_id = $ilias_parent_id;
    }

    /**
     * @return bool
     */
    protected function taxonomyExists(): bool
    {
        $childsByType = self::dic()->tree()->getChildsByType($this->getILIASParentId(), 'tax');
        if (!count($childsByType)) {
            return false;
        }
        foreach ($childsByType as $value) {
            if ($value["title"] === $this->getTaxonomy()->getTitle()) {
                $this->ilObjTaxonomy = new ilObjTaxonomy($value["obj_id"]);

                return true;
            }
        }

        return false;
    }

    /**
     *
     */
    protected function initTaxTree()
    {
        $this->tree = $this->ilObjTaxonomy->getTree();
        $this->tree_root_id = $this->tree->readRootId();
        $this->setChildrenByParentId($this->tree_root_id);
    }

    /**
     * @param int $parent_id
     */
    protected function setChildrenByParentId($parent_id)
    {
        foreach ($this->tree->getChildsByTypeFilter($parent_id, array("taxn")) as $item) {
            $this->childs[$item['obj_id']] = $item['title'];
            $this->setChildrenByParentId($item['obj_id']);
        }
    }

    /**
     * @param INode $node
     * @return bool
     */
    protected function nodeExists(INode $node): bool
    {
        return in_array($node->getTitle(), $this->childs);
    }

    /**
     * @inheritdoc
     */
    abstract public function write();

    /**
     * @inheritdoc
     */
    public function getTaxonomy(): ITaxonomy
    {
        return $this->taxonomy;
    }

    /**
     * @inheritdoc
     */
    public function getILIASParentId(): int
    {
        return $this->ilias_parent_id;
    }
}
