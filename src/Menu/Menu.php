<?php

namespace srag\Plugins\Hub2\Menu;

use hub2MainGUI;
use ilAdministrationGUI;
use ilHub2ConfigGUI;
use ilHub2Plugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\MainMenu\Provider\StandardTopItemsProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilObjComponentSettingsGUI;
use srag\DIC\Hub2\DICTrait;
use srag\Plugins\Hub2\Config\ArConfig;
use srag\Plugins\Hub2\Utils\Hub2Trait;

/**
 * Class Menu
 *
 * @package srag\Plugins\Hub2\Menu
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticPluginMainMenuProvider
{
    use DICTrait;
    use Hub2Trait;

    public const PLUGIN_CLASS_NAME = ilHub2Plugin::class;

    protected static $polyfill_init = false;

    /**
     * @inheritdoc
     */
    public function getStaticTopItems(): array
    {
        return [];
    }

    protected function initPolyfill()
    {
        if (!self::$polyfill_init) {
            // Polyfill
            if (!function_exists('array_key_first')) {
                function array_key_first(array $array)
                {
                    foreach ($array as $key => $value) {
                        return $key;
                    }
                }
            }
            self::$polyfill_init = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function getStaticSubItems(): array
    {
        $this->initPolyFill();
        $obj_id = array_key_first(\ilObject2::_getObjectsByType('cmps') ?? []);
        if (!$obj_id) {
            return [];
        }
        if (class_exists(StandardTopItemsProvider::class)) {
            $s = StandardTopItemsProvider::getInstance();
            $parent = $s->getAdministrationIdentification();
        } else {
            global $DIC;
            $p = new \ilAdmGlobalScreenProvider($DIC);
            $parent = $p->getTopItem();
        }

        $ref_id = array_key_first(\ilObject2::_getAllReferences($obj_id) ?? []);
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "ref_id", $ref_id);
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "ctype", IL_COMP_SERVICE);
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "cname", "Cron");
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "slot_id", "crnhk");
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "pname", ilHub2Plugin::PLUGIN_NAME);

        $action = self::dic()->ctrl()->getLinkTargetByClass(
            [
                ilAdministrationGUI::class,
                ilObjComponentSettingsGUI::class,
                ilHub2ConfigGUI::class,
            ],
            hub2MainGUI::CMD_INDEX
        );
        return [
            $this->symbol(
                $this->mainmenu->link($this->if->identifier(ilHub2Plugin::PLUGIN_ID . "_configuration"))
                               ->withParent($parent)
                               ->withTitle('HUB Sync')
                               ->withAction($action)
                               ->withAvailableCallable(
                                   function (): bool {
                                       return self::plugin()->getPluginObject()->isActive();
                                   }
                               )
                               ->withVisibilityCallable(
                                   function (): bool {
                                       $config = ArConfig::find(ArConfig::KEY_ADMINISTRATE_HUB_ROLE_IDS);
                                       if (null !== $config) {
                                           // replace outer brackets from array string and convert values to int
                                           $roles = preg_replace("/[\[\]']+/", '', $config->getValue());
                                           $roles = array_map('intval', explode(',', $roles));
                                           // add at least default admin role id (doesn't matter if it's repeatedly)
                                           $roles[] = 2;
                                       } else {
                                           $roles = [2];
                                       }

                                       return self::dic()->rbacreview()->isAssignedToAtLeastOneGivenRole(
                                           self::dic()->user()->getId(),
                                           $roles
                                       );
                                   }
                               )
            ),
        ];
    }

    /**
     * @param AbstractBaseItem $entry
     * @return AbstractBaseItem
     */
    protected function symbol(AbstractBaseItem $entry): AbstractBaseItem
    {
        if (self::version()->is6()) {
            $entry = $entry->withSymbol(
                self::dic()->ui()->factory()->symbol()->icon()->custom(
                    './Customizing/global/plugins/Services/Cron/CronHook/Hub2/templates/hub2_icon_outlined.svg',
                    ilHub2Plugin::PLUGIN_NAME
                )
            );
        }

        return $entry;
    }
}
