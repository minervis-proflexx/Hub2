<?php

namespace srag\Plugins\Hub2\Taxonomy;

use ilHub2Plugin;
use srag\DIC\Hub2\DICTrait;
use srag\Plugins\Hub2\Taxonomy\Node\INode;
use srag\Plugins\Hub2\Taxonomy\Node\Node;
use srag\Plugins\Hub2\Utils\Hub2Trait;

/**
 * Class TaxonomyFactory
 *
 * @package srag\Plugins\Hub2\Taxonomy
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class TaxonomyFactory implements ITaxonomyFactory
{

    use DICTrait;
    use Hub2Trait;
    const PLUGIN_CLASS_NAME = ilHub2Plugin::class;


    /**
     * @inheritdoc
     */
    public function select(string $title) : ITaxonomy
    {
        return new Taxonomy($title, ITaxonomy::MODE_SELECT);
    }


    /**
     * @inheritdoc
     */
    public function create(string $title) : ITaxonomy
    {
        return new Taxonomy($title, ITaxonomy::MODE_CREATE);
    }


    /**
     * @inheritdoc
     */
    public function node(string $node_title) : INode
    {
        return new Node($node_title);
    }
}
