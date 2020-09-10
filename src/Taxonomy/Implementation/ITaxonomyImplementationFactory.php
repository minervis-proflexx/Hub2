<?php

namespace srag\Plugins\Hub2\Taxonomy\Implementation;

use ilObject;
use srag\Plugins\Hub2\Taxonomy\ITaxonomy;

/**
 * Class ITaxonomyImplementationFactory
 *
 * @package srag\Plugins\Hub2\Taxonomy\Implementation
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface ITaxonomyImplementationFactory
{

    /**
     * @param ITaxonomy $Taxonomy
     * @param ilObject  $ilias_object
     *
     * @return ITaxonomyImplementation
     */
    public function taxonomy(ITaxonomy $Taxonomy, ilObject $ilias_object) : ITaxonomyImplementation;
}
