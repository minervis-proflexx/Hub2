<?php

namespace srag\Plugins\Hub2\Sync\Processor\Course;

use ilCalendarCategory;
use ilContainer;
use ilContainerSortingSettings;
use ilCopyWizardOptions;
use ilLink;
use ilMailMimeSenderFactory;
use ilMD;
use ilMDLanguageItem;
use ilMimeMail;
use ilObjCategory;
use ilObjCourse;
use ilRepUtil;
use ilSession;
use ilSoapFunctions;
use srag\DIC\Hub2\Version\Version;
use srag\Plugins\Hub2\Exception\HubException;
use srag\Plugins\Hub2\Object\Course\CourseDTO;
use srag\Plugins\Hub2\Object\DTO\IDataTransferObject;
use srag\Plugins\Hub2\Object\ObjectFactory;
use srag\Plugins\Hub2\Origin\Config\Course\CourseOriginConfig;
use srag\Plugins\Hub2\Origin\IOrigin;
use srag\Plugins\Hub2\Origin\IOriginImplementation;
use srag\Plugins\Hub2\Origin\OriginRepository;
use srag\Plugins\Hub2\Origin\Properties\Course\CourseProperties;
use srag\Plugins\Hub2\Sync\IObjectStatusTransition;
use srag\Plugins\Hub2\Sync\Processor\MetadataSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\ObjectSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\TaxonomySyncProcessor;

/**
 * Class CourseSyncProcessor
 *
 * @package srag\Plugins\Hub2\Sync\Processor\Course
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class CourseSyncProcessor extends ObjectSyncProcessor implements ICourseSyncProcessor
{

    use TaxonomySyncProcessor;
    use MetadataSyncProcessor;
    /**
     * @var CourseProperties
     */
    protected $props;
    /**
     * @var CourseOriginConfig
     */
    protected $config;
    /**
     * @var ICourseActivities
     */
    protected $courseActivities;
    /**
     * @var array
     */
    protected static $properties
        = [
            'title',
            'description',
            'importantInformation',
            'contactResponsibility',
            'contactEmail',
            'owner',
            'subscriptionLimitationType',
            'viewMode',
            'contactName',
            'syllabus',
            'contactConsultation',
            'contactPhone',
            'activationType',
            'numberOfPreviousSessions',
            'numberOfNextSessions',
            'orderType',
            'activationStart',
            'activationEnd'
        ];
    /**
     * @var Version
     */
    protected $version;

    /**
     * @param IOrigin                 $origin
     * @param IOriginImplementation   $implementation
     * @param IObjectStatusTransition $transition
     * @param ICourseActivities       $courseActivities
     */
    public function __construct(IOrigin $origin, IOriginImplementation $implementation, IObjectStatusTransition $transition, ICourseActivities $courseActivities)
    {
        parent::__construct($origin, $implementation, $transition);
        $this->props = $origin->properties();
        $this->config = $origin->config();
        $this->courseActivities = $courseActivities;
        $this->version = new Version();
    }


    /**
     * @return array
     */
    public static function getProperties()
    {
        return self::$properties;
    }


    /**
     * @inheritdoc
     *
     * @param CourseDTO $dto
     */
    protected function handleCreate(IDataTransferObject $dto)/*: void*/
    {
        // Find the refId under which this course should be created
        $parentRefId = $this->determineParentRefId($dto);
        // Check if we should create some dependence categories
        $parentRefId = $this->buildDependenceCategories($dto, $parentRefId);

        if ($template_id = $dto->getTemplateId()) {
            // copy from template
            if (!ilObjCourse::_exists($template_id, true)) {
                throw new HubException('Creation of course with ext_id = ' . $dto->getExtId() . ' failed: template course with ref_id = '
                    . $template_id . ' does not exist in ILIAS');
            }
            $return = $this->cloneAllObject($parentRefId, $template_id, $this->getCloneOptions($template_id));
            $this->current_ilias_object = $ilObjCourse = new ilObjCourse($return);
        } else {
            // create new one
            $this->current_ilias_object = $ilObjCourse = new ilObjCourse();
            $ilObjCourse->setImportId($this->getImportId($dto));
            $ilObjCourse->create();
            $ilObjCourse->createReference();
            $ilObjCourse->putInTree($parentRefId);
            $ilObjCourse->setPermissions($parentRefId);
            $this->writeRBACLog($ilObjCourse->getRefId());
        }

        // Pass properties from DTO to ilObjUser
        foreach (self::getProperties() as $property) {
            $setter = "set" . ucfirst($property);
            $getter = "get" . ucfirst($property);
            if ($dto->$getter() !== null && $setter !== "setActivationType") {
                if ($property === "activationStart" || $property === "activationEnd") {
                    $ilObjCourse->$setter($dto->$getter() !== null ? $dto->$getter()->get(IL_CAL_UNIX) : null);
                } else {
                    $ilObjCourse->$setter($dto->$getter());
                }
            }
        }

        // Course Start and Ane are handled differently in ILIAS 5.4 and 6
        if ($dto->getCourseStart() && $dto->getCourseEnd()) {
            if ($this->version->is54()) {
                $ilObjCourse->setCourseStart($dto->getCourseStart());
                $ilObjCourse->setCourseEnd($dto->getCourseEnd());
            } elseif ($this->version->is6()) {
                $ilObjCourse->setCoursePeriod($dto->getCourseStart(), $dto->getCourseEnd());
            }
        }


        if ($dto->getDidacticTemplate() > 0) {
            $ilObjCourse->applyDidacticTemplate($dto->getDidacticTemplate());
        }
        if ($dto->getIcon() !== '') {
            $ilObjCourse->saveIcons($dto->getIcon());
        }
        if ($this->props->get(CourseProperties::SET_ONLINE)) {
            $ilObjCourse->setOfflineStatus(false);
            //Does not exist in 5.4
            //$ilObjCourse->setActivationType(IL_CRS_ACTIVATION_UNLIMITED);
        }

        if ($this->props->get(CourseProperties::CREATE_ICON)) {
            // TODO
            //			$this->updateIcon($this->ilias_object);
            //			$this->ilias_object->update();
        }
        if ($this->props->get(CourseProperties::SEND_CREATE_NOTIFICATION)) {
            $this->sendMailNotifications($dto, $ilObjCourse);
        }
        $this->setSubscriptionType($dto, $ilObjCourse);

        $this->setLanguage($dto, $ilObjCourse);
        $ilObjCourse->enableSessionLimit($dto->isSessionLimitEnabled());

        $ilObjCourse->update();

        $this->handleOrdering($dto, $ilObjCourse);

        $this->handleAppointementsColor($ilObjCourse, $dto);
    }


    /**
     * @param IDataTransferObject $dto
     */
    protected function handleOrdering(IDataTransferObject $dto, ilObjCourse $ilObjCourse)
    {
        $settings = new ilContainerSortingSettings($ilObjCourse->getId());
        $settings->setSortMode($dto->getOrderType());

        switch ($dto->getOrderType()) {
            case ilContainer::SORT_TITLE:
            case ilContainer::SORT_ACTIVATION:
            case ilContainer::SORT_CREATION:
                $settings->setSortDirection($dto->getOrderDirection());
                break;
            case ilContainer::SORT_MANUAL:
                /**
                 * TODO: set order direction for manual sorting
                 */
                break;
        }

        $settings->update();
    }


    /**
     * @param $source_id
     *
     * @return array
     */
    protected function getCloneOptions($source_id)
    {
        $options = [];
        foreach (self::dic()->tree()->getSubTree($root = self::dic()->tree()->getNodeData($source_id)) as $node) {
            if ($node['type'] == 'rolf') {
                continue;
            }

            if (self::dic()->objDefinition()->allowCopy($node['type'])) {
                $options[$node['ref_id']] = ['type' => ilCopyWizardOptions::COPY_WIZARD_COPY];
            }
            // this should be a config
            //            else if (self::LINK_IF_COPY_NOT_POSSIBLE && self::dic()->objDefinition()->allowLink($node['type'])) {
            //                $options[$node['ref_id']] = ['type' => ilCopyWizardOptions::COPY_WIZARD_LINK];
            //            }

        }

        return $options;
    }


    /**
     * This is a leaner version of ilContainer::cloneAllObject, which doens't use soap
     *
     * @param int   $parent_ref_id
     * @param int   $clone_source
     * @param array $options
     *
     * @return int $ref_id
     */
    public function cloneAllObject(int $parent_ref_id, int $clone_source, array $options) : int
    {
        // Save wizard options
        $copy_id = ilCopyWizardOptions::_allocateCopyId();
        $wizard_options = ilCopyWizardOptions::_getInstance($copy_id);
        $wizard_options->saveOwner(self::dic()->user()->getId());
        $wizard_options->saveRoot($clone_source);

        // add entry for source container
        $wizard_options->initContainer($clone_source, $parent_ref_id);

        foreach ($options as $source_id => $option) {
            $wizard_options->addEntry($source_id, $option);
        }
        $wizard_options->read();
        $wizard_options->storeTree($clone_source);

        // Duplicate session to avoid logout problems with backgrounded SOAP calls
        $new_session_id = ilSession::_duplicate($_COOKIE['PHPSESSID']);

        $wizard_options->disableSOAP();
        $wizard_options->read();

        require_once 'webservice/soap/include/inc.soap_functions.php';
        $parent_ref_id = ilSoapFunctions::ilClone($new_session_id . '::' . $_COOKIE['ilClientId'], $copy_id);

        return $parent_ref_id;
    }


    /**
     * @param CourseDTO   $dto
     * @param ilObjCourse $ilObjCourse
     */
    protected function setLanguage(CourseDTO $dto, ilObjCourse $ilObjCourse)
    {
        $md_general = (new ilMD($ilObjCourse->getId()))->getGeneral();
        //Note: this is terribly stupid, but the best (only) way if found to get to the
        //lang id of the primary language of some object. There seems to be multy lng
        //support however, not through the GUI. Maybe there is some bug in the generation
        //of the respective metadata form. See: initQuickEditForm() in ilMDEditorGUI
        $language = $md_general->getLanguage(array_pop($md_general->getLanguageIds()));
        $language->setLanguage(new ilMDLanguageItem($dto->getLanguageCode()));
        $language->update();
    }


    /**
     * @param CourseDTO   $dto
     * @param ilObjCourse $ilObjCourse
     */
    protected function setSubscriptionType(CourseDTO $dto, ilObjCourse $ilObjCourse)
    {
        //There is some weird connection between subscription limitation type ond subscription type, see e.g. ilObjCourseGUI
        $ilObjCourse->setSubscriptionType($dto->getSubscriptionLimitationType());
        if ($dto->getSubscriptionLimitationType() == CourseDTO::SUBSCRIPTION_TYPE_DEACTIVATED) {
            $ilObjCourse->setSubscriptionLimitationType(IL_CRS_SUBSCRIPTION_DEACTIVATED);
        } else {
            $ilObjCourse->setSubscriptionLimitationType(IL_CRS_SUBSCRIPTION_UNLIMITED);
        }
    }


    /**
     * @param CourseDTO $dto
     */
    protected function sendMailNotifications(CourseDTO $dto, ilObjCourse $ilObjCourse)
    {
        $mail = new ilMimeMail();
        $sender_factory = new ilMailMimeSenderFactory(self::dic()->settings());
        $sender = null;
        if ($this->props->get(CourseProperties::CREATE_NOTIFICATION_FROM)) {
            $sender = $sender_factory->userByEmailAddress($this->props->get(CourseProperties::CREATE_NOTIFICATION_FROM));
        } else {
            $sender = $sender_factory->system();
        }
        $mail->From($sender);
        $mail->To($dto->getNotificationEmails());
        $mail->Subject($this->props->get(CourseProperties::CREATE_NOTIFICATION_SUBJECT));
        $mail->Body($this->replaceBodyTextForMail($this->props->get(CourseProperties::CREATE_NOTIFICATION_BODY), $ilObjCourse));
        $mail->Send();
    }


    protected function replaceBodyTextForMail($body, ilObjCourse $ilObjCourse)
    {
        foreach (CourseProperties::$mail_notification_placeholder as $ph) {
            $replacement = '[' . $ph . ']';

            switch ($ph) {
                case 'title':
                    $replacement = $ilObjCourse->getTitle();
                    break;
                case 'description':
                    $replacement = $ilObjCourse->getDescription();
                    break;
                case 'responsible':
                    $replacement = $ilObjCourse->getContactResponsibility();
                    break;
                case 'notification_email':
                    $replacement = $ilObjCourse->getContactEmail();
                    break;
                case 'shortlink':
                    $replacement = ilLink::_getStaticLink($ilObjCourse->getRefId(), 'crs');
                    break;
            }
            $body = str_ireplace('[' . strtoupper($ph) . ']', $replacement, $body);
        }

        return $body;
    }


    /**
     * @inheritdoc
     *
     * @param CourseDTO $dto
     */
    protected function handleUpdate(IDataTransferObject $dto, $ilias_id)/*: void*/
    {
        $this->current_ilias_object = $ilObjCourse = $this->findILIASCourse($ilias_id);
        if ($ilObjCourse === null) {
            return;
        }
        // Update some properties if they should be updated depending on the origin config
        foreach (self::getProperties() as $property) {
            if (!$this->props->updateDTOProperty($property)) {
                continue;
            }
            $setter = "set" . ucfirst($property);
            $getter = "get" . ucfirst($property);
            if ($dto->$getter() !== null && $setter !== "setActivationType") {
                if ($property === "activationStart" || $property === "activationEnd") {
                    $ilObjCourse->$setter($dto->$getter() !== null ? $dto->$getter()->get(IL_CAL_UNIX) : null);
                } else {
                    $ilObjCourse->$setter($dto->$getter());
                }
            }
        }
        if ($this->props->updateDTOProperty("didacticTemplate") && $dto->getDidacticTemplate() > 0) {
            $ilObjCourse->applyDidacticTemplate($dto->getDidacticTemplate());
        }
        if ($this->props->updateDTOProperty("icon")) {
            if ($dto->getIcon() !== '') {
                $ilObjCourse->saveIcons($dto->getIcon());
            } else {
                $ilObjCourse->removeCustomIcon();
            }
        }
        if ($this->props->updateDTOProperty("enableSessionLimit")) {
            $ilObjCourse->enableSessionLimit($dto->isSessionLimitEnabled());
        }
        if ($this->props->updateDTOProperty("subscriptionLimitationType")) {
            $this->setSubscriptionType($dto, $ilObjCourse);
        }
        if ($this->props->updateDTOProperty("languageCode")) {
            $this->setLanguage($dto, $ilObjCourse);
        }
        if ($this->props->get(CourseProperties::SET_ONLINE_AGAIN)) {
            $ilObjCourse->setOfflineStatus(false);
            //Does not exist in 5.4
            //$ilObjCourse->setActivationType(IL_CRS_ACTIVATION_UNLIMITED);
        }

        if ($this->props->updateDTOProperty("enableSessionLimit")) {
            $ilObjCourse->enableSessionLimit($dto->isSessionLimitEnabled());
        }

        if (!self::dic()->tree()->isInTree($ilObjCourse->getRefId())) {
            $parentRefId = $this->determineParentRefId($dto);

            $ilObjCourse->putInTree($parentRefId);
        } else {
        if ($this->props->get(CourseProperties::MOVE_COURSE)) {
            $this->moveCourse($ilObjCourse, $dto);
        }
        }

        if ($this->props->updateDTOProperty("appointementsColor")) {
            $this->handleAppointementsColor($ilObjCourse, $dto);
        }

        $ilObjCourse->update();
    }


    /**
     * @param ilObjCourse $ilObjCourse
     * @param CourseDTO   $dto
     */
    protected function handleAppointementsColor(ilObjCourse $ilObjCourse, CourseDTO $dto)
    {
        if (!empty($dto->getAppointementsColor())) {
            self::dic()->objDataCache()->deleteCachedEntry($ilObjCourse->getId());
            /**
             * @var $cal_cat ilCalendarCategory
             */
            $cal_cat = ilCalendarCategory::_getInstanceByObjId($ilObjCourse->getId());
            $cal_cat->setColor($dto->getAppointementsColor());
            $cal_cat->update();
        }
    }


    /**
     * @inheritdoc
     *
     * @param CourseDTO $dto
     */
    protected function handleDelete(IDataTransferObject $dto, $ilias_id)/*: void*/
    {
        $this->current_ilias_object = $ilObjCourse = $this->findILIASCourse($ilias_id);
        if ($ilObjCourse === null) {
            return;
        }
        if ($this->props->get(CourseProperties::DELETE_MODE) == CourseProperties::DELETE_MODE_NONE) {
            return;
        }
        switch ($this->props->get(CourseProperties::DELETE_MODE)) {
            case CourseProperties::DELETE_MODE_OFFLINE:
                $ilObjCourse->setOfflineStatus(true);
                $ilObjCourse->update();
                break;
            case CourseProperties::DELETE_MODE_DELETE:
                $ilObjCourse->delete();
                break;
            case CourseProperties::DELETE_MODE_MOVE_TO_TRASH:
                self::dic()->tree()->moveToTrash($ilObjCourse->getRefId(), true);
                break;
            case CourseProperties::DELETE_MODE_DELETE_OR_OFFLINE:
                if ($this->courseActivities->hasActivities($ilObjCourse)) {
                    $ilObjCourse->setOfflineStatus(true);
                    $ilObjCourse->update();
                } else {
                    self::dic()->tree()->moveToTrash($ilObjCourse->getRefId(), true);
                }
                break;
        }
    }


    /**
     * @param CourseDTO $course
     *
     * @return int
     * @throws HubException
     */
    protected function determineParentRefId(CourseDTO $course)
    {
        if ($course->getParentIdType() == CourseDTO::PARENT_ID_TYPE_REF_ID) {
            if (self::dic()->tree()->isInTree($course->getParentId())) {
                return $course->getParentId();
            }
            // The ref-ID does not exist in the tree, use the fallback parent ref-ID according to the config
            $parentRefId = $this->config->getParentRefIdIfNoParentIdFound();
            if (!self::dic()->tree()->isInTree($parentRefId)) {
                throw new HubException("Could not find the fallback parent ref-ID in tree: '{$parentRefId}'");
            }

            return $parentRefId;
        }
        if ($course->getParentIdType() == CourseDTO::PARENT_ID_TYPE_EXTERNAL_EXT_ID) {
            // The stored parent-ID is an external-ID from a category.
            // We must search the parent ref-ID from a category object synced by a linked origin.
            // --> Get an instance of the linked origin and lookup the category by the given external ID.
            $linkedOriginId = $this->config->getLinkedOriginId();
            if (!$linkedOriginId) {
                throw new HubException("Unable to lookup external parent ref-ID because there is no origin linked");
            }
            $originRepository = new OriginRepository();
            $origin = array_pop(array_filter($originRepository->categories(), function ($origin) use ($linkedOriginId) {
                /** @var IOrigin $origin */
                return $origin->getId() == $linkedOriginId;
            }));
            if ($origin === null) {
                $msg = "The linked origin syncing categories was not found, please check that the correct origin is linked";
                throw new HubException($msg);
            }
            $objectFactory = new ObjectFactory($origin);
            $category = $objectFactory->category($course->getParentId());
            if (!$category->getILIASId()) {
                throw new HubException("The linked category (" . $category->getExtId() . ") does not (yet) exist in ILIAS for course: "
                    . $course->getExtId());
            }
            if (!self::dic()->tree()->isInTree($category->getILIASId())) {
                throw new HubException("Could not find the ref-ID of the parent category in the tree: '{$category->getILIASId()}'");
            }

            return $category->getILIASId();
        }

        return 0;
    }


    /**
     * @param CourseDTO $object
     * @param int       $parentRefId
     *
     * @return int
     */
    protected function buildDependenceCategories(CourseDTO $object, $parentRefId)
    {
        if ($object->getFirstDependenceCategory() !== null) {
            $parentRefId = $this->buildDependenceCategory($object->getFirstDependenceCategory(), $parentRefId, 1);
        }
        if ($object->getFirstDependenceCategory() !== null
            && $object->getSecondDependenceCategory() !== null
        ) {
            $parentRefId = $this->buildDependenceCategory($object->getSecondDependenceCategory(), $parentRefId, 2);
        }
        if ($object->getFirstDependenceCategory() !== null
            && $object->getSecondDependenceCategory() !== null
            && $object->getThirdDependenceCategory() !== null
        ) {
            $parentRefId = $this->buildDependenceCategory($object->getThirdDependenceCategory(), $parentRefId, 3);
        }
        if ($object->getFirstDependenceCategory() !== null
            && $object->getSecondDependenceCategory() !== null
            && $object->getThirdDependenceCategory() !== null
            && $object->getFourthDependenceCategory() !== null
        ) {
            $parentRefId = $this->buildDependenceCategory($object->getFourthDependenceCategory(), $parentRefId, 4);
        }

        return $parentRefId;
    }


    /**
     * Creates a category under the given $parentRefId if it does not yet exist.
     * Note that this implementation is copied over from the old hub plugin: We check if we
     * find a category having the same title. If not, a new category is created.
     * It would be better to identify the category over the unique import ID and then update
     * the title of the category, if necessary.
     *
     * @param string $title
     * @param int    $parentRefId
     * @param int    $level
     *
     * @return int
     */
    protected function buildDependenceCategory($title, $parentRefId, $level)
    {
        static $cache = [];
        // We use a cache for created dependence categories to save some SQL queries
        $cacheKey = hash("sha256", $title . $parentRefId . $level);
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }
        $categories = self::dic()->tree()->getChildsByType($parentRefId, 'cat');
        $matches = array_filter($categories, function ($category) use ($title) {
            return $category['title'] == $title;
        });
        if (count($matches) > 0) {
            $category = array_pop($matches);
            $cache[$cacheKey] = $category['ref_id'];

            return $category['ref_id'];
        }
        // No category with the given title found, create it!
        $importId = self::IMPORT_PREFIX . implode('_', [
                $this->origin->getId(),
                $parentRefId,
                'depth',
                $level,
            ]);
        $ilObjCategory = new ilObjCategory();
        $ilObjCategory->setTitle($title);
        $ilObjCategory->setImportId($importId);
        $ilObjCategory->create();
        $ilObjCategory->createReference();
        $ilObjCategory->putInTree($parentRefId);
        $ilObjCategory->setPermissions($parentRefId);
        $this->writeRBACLog($ilObjCategory->getRefId());
        $cache[$cacheKey] = $ilObjCategory->getRefId();

        return $ilObjCategory->getRefId();
    }


    /**
     * @param int $iliasId
     *
     * @return ilObjCourse|null
     */
    protected function findILIASCourse($iliasId)
    {
        if (!ilObjCourse::_exists($iliasId, true)) {
            return null;
        }

        return new ilObjCourse($iliasId);
    }


    /**
     * Move the course to a new parent.
     * Note: May also create the dependence categories
     *
     * @param ilObjCourse $ilObjCourse
     * @param CourseDTO   $course
     */
    protected function moveCourse(ilObjCourse $ilObjCourse, CourseDTO $course)
    {
        $parentRefId = $this->determineParentRefId($course);
        $parentRefId = $this->buildDependenceCategories($course, $parentRefId);
        if (self::dic()->tree()->isDeleted($ilObjCourse->getRefId())) {
            $ilRepUtil = new ilRepUtil();
            $ilRepUtil->restoreObjects($parentRefId, [$ilObjCourse->getRefId()]);
        }
        $oldParentRefId = self::dic()->tree()->getParentId($ilObjCourse->getRefId());
        if ($oldParentRefId == $parentRefId) {
            return;
        }
        self::dic()->tree()->moveTree($ilObjCourse->getRefId(), $parentRefId);
        self::dic()->rbacadmin()->adjustMovedObjectPermissions($ilObjCourse->getRefId(), $oldParentRefId);
    }
}
