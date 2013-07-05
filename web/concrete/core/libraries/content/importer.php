<?

/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A way to import concrete5 content.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Content_Importer {
	
	protected static $mcBlockIDs = array();
	protected static $cmpOutputControlIDs = array();

	public function importContentFile($file) {
		$sx = simplexml_load_file($file);
		$this->doImport($sx);
	}

	public function importContentString($string) {
		$sx = simplexml_load_string($string);
		$this->doImport($sx);
	}

	protected function doImport($sx) {
		$this->importSinglePageStructure($sx);
		$this->importStacksStructure($sx);
		$this->importBlockTypes($sx);
		$this->importBlockTypeSets($sx);
		$this->importConversationEditors($sx);
		$this->importConversationRatingTypes($sx);
		$this->importConversationFlagTypes($sx);
		$this->importComposerTargetTypes($sx);
		$this->importComposerControlTypes($sx);
		$this->importBannedWords($sx);
		$this->importFeatures($sx);
		$this->importFeatureCategories($sx);
		$this->importGatheringDataSources($sx);
		$this->importGatheringItemTemplateTypes($sx);
		$this->importGatheringItemTemplates($sx);
		$this->importAttributeCategories($sx);
		$this->importAttributeTypes($sx);
		$this->importWorkflowTypes($sx);
		$this->importWorkflowProgressCategories($sx);
		$this->importAttributes($sx);
		$this->importAttributeSets($sx);
		$this->importThemes($sx);
		$this->importPermissionCategories($sx);
		$this->importPermissionAccessEntityTypes($sx);
		$this->importTaskPermissions($sx);
		$this->importPermissions($sx);
		$this->importJobs($sx);
		$this->importJobSets($sx);
		// import bare page types first, then import structure, then page types blocks, attributes and composer settings, then page content, because we need the structure for certain attributes and stuff set in master collections (like composer)
		$this->importPageTypesBase($sx);
		$this->importPageStructure($sx);
		$this->importPageTypeDefaults($sx);
		$this->importSinglePageContent($sx);
		$this->importStacksContent($sx);
		$this->importPageContent($sx);
		$this->importPackages($sx);
		$this->importConfigValues($sx);
		$this->importSystemCaptchaLibraries($sx);
		$this->importSystemContentEditorSnippets($sx);
		$this->importComposers($sx);
	}
	
	protected static function getPackageObject($pkgHandle) {
		$pkg = false;
		if ($pkgHandle) {
			$pkg = Package::getByHandle($pkgHandle);
		}
		return $pkg;		
	}

	protected function importStacksStructure(SimpleXMLElement $sx) {
		if (isset($sx->stacks)) {
			foreach($sx->stacks->stack as $p) {
				if (isset($p['type'])) {
					$type = Stack::mapImportTextToType($p['type']);
					Stack::addStack($p['name'], $type);
				} else {
					Stack::addStack($p['name']);
				}
			}
		}
	}

	protected function importStacksContent(SimpleXMLElement $sx) {
		if (isset($sx->stacks)) {
			foreach($sx->stacks->stack as $p) {
				$stack = Stack::getByName($p['name']);
				if (isset($p->area)) {
					$this->importPageAreas($stack, $p);
				}
			}
		}
	}
	
	protected function importSinglePageStructure(SimpleXMLElement $sx) {
		if (isset($sx->singlepages)) {
			foreach($sx->singlepages->page as $p) {
				$pkg = ContentImporter::getPackageObject($p['package']);
				$spl = SinglePage::add($p['path'], $pkg);
				if (is_object($spl)) { 
					if (isset($p['root']) && $p['root'] == true) {
						$spl->moveToRoot();
					}
					if ($p['name']) {
						$spl->update(array('cName' => $p['name'], 'cDescription' => $p['description']));
					}
				}
			}
		}
	}

	protected function importSinglePageContent(SimpleXMLElement $sx) {
		if (isset($sx->singlepages)) {
			foreach($sx->singlepages->page as $px) {
				$page = Page::getByPath($px['path'], 'RECENT');
				if (isset($px->area)) {
					$this->importPageAreas($page, $px);
				}
				if (isset($px->attributes)) {
					foreach($px->attributes->children() as $attr) {
						$ak = CollectionAttributeKey::getByHandle($attr['handle']);
						if (is_object($ak)) { 
							$page->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
						}
					}
				}
			}
		}
	}

	protected function setupPageNodeOrder($pageNodeA, $pageNodeB) {
		$pathA = (string) $pageNodeA['path'];
		$pathB = (string) $pageNodeB['path'];
		$numA = count(explode('/', $pathA));
		$numB = count(explode('/', $pathB));
		if ($numA == $numB) {
			if (intval($pageNodeA->originalPos) < intval($pageNodeB->originalPos)) {
				return -1;
			} else if (intval($pageNodeA->originalPos) > intval($pageNodeB->originalPos)) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return ($numA < $numB) ? -1 : 1;
		}
	}
	
	protected function importPageContent(SimpleXMLElement $sx) {
		if (isset($sx->pages)) {
			foreach($sx->pages->page as $px) {
				if ($px['path'] != '') {
					$page = Page::getByPath($px['path'], 'RECENT');
				} else {
					$page = Page::getByID(HOME_CID, 'RECENT');
				}
				if (isset($px->area)) {
					$this->importPageAreas($page, $px);
				}
				if (isset($px->attributes)) {
					foreach($px->attributes->children() as $attr) {
						$ak = CollectionAttributeKey::getByHandle($attr['handle']);
						if (is_object($ak)) { 
							$page->setAttribute((string) $attr['handle'], $ak->getController()->importValue($attr));
						}
					}
				}
				$page->reindex();
			}
		}
	}
	
	protected function importPageStructure(SimpleXMLElement $sx) {
		if (isset($sx->pages)) {
			$nodes = array();
			$i = 0;
			foreach($sx->pages->page as $p) {
				$p->originalPos = $i;
				$nodes[] = $p;
				$i++;
			}
			usort($nodes, array('ContentImporter', 'setupPageNodeOrder'));
			$home = Page::getByID(HOME_CID, 'RECENT');

			foreach($nodes as $px) {
				$pkg = ContentImporter::getPackageObject($px['package']);
				$data = array();
				$user = (string) $px['user'];
				if ($user != '') {
					$ui = UserInfo::getByUserName($user);
					if (is_object($ui)) {
						$data['uID'] = $ui->getUserID();
					} else {
						$data['uID'] = USER_SUPER_ID;
					}	
				}
				$cDatePublic = (string) $px['public-date'];
				if ($cDatePublic) {
					$data['cDatePublic'] = $cDatePublic;
				}

				$data['pkgID'] = 0;
				if (is_object($pkg)) {
					$data['pkgID'] = $pkg->getPackageID();
				}
				$args = array();
				$ct = CollectionType::getByHandle($px['pagetype']);
				if ($px['path'] == '') {
					// home page
					$page = $home;
					$args['ctID'] = $ct->getCollectionTypeID();
				} else {
					$page = Page::getByPath($px['path']);
					if (!is_object($page) || ($page->isError())) {
						$lastSlash = strrpos((string) $px['path'], '/');
						$parentPath = substr((string) $px['path'], 0, $lastSlash);
						$data['cHandle'] = substr((string) $px['path'], $lastSlash + 1);
						if (!$parentPath) {
							$parent = $home;
						} else {
							$parent = Page::getByPath($parentPath);
						}
						$page = $parent->add($ct, $data);
					}
				}

				$args['cName'] = $px['name'];
				$args['cDescription'] = $px['description'];
				$args['ctID'] = $ct->getCollectionTypeID();
				$page->update($args);
			}
		}
	}
	
	protected function importPageAreas(Page $page, SimpleXMLElement $px) {
		foreach($px->area as $ax) {
			if (isset($ax->block)) {
				foreach($ax->block as $bx) {
					if ($bx['type'] != '') {
						// we check this because you might just get a block node with only an mc-block-id, if it's an alias
						$bt = BlockType::getByHandle($bx['type']);
						$btc = $bt->getController();
						$btc->import($page, (string) $ax['name'], $bx);
					} else if ($bx['mc-block-id'] != '') {
					
						// we find that block in the master collection block pool and alias it out
						$bID = array_search((string) $bx['mc-block-id'], self::$mcBlockIDs);
						if ($bID) {
							$mc = Page::getByID($page->getMasterCollectionID(), 'RECENT');
							$block = Block::getByID($bID, $mc, (string) $ax['name']);
							$block->alias($page);
						}
					}
				}
			}
		}
	}

	public static function addMasterCollectionBlockID($b, $id) {
		self::$mcBlockIDs[$b->getBlockID()] = $id;
	}
	
	public static function getMasterCollectionTemporaryBlockID($b) {
		if (isset(self::$mcBlockIDs[$b->getBlockID()])) {
			return self::$mcBlockIDs[$b->getBlockID()];
		}
	}

	public static function addComposerOutputControlID(ComposerFormLayoutSetControl $control, $id) {
		self::$cmpOutputControlIDs[$id] = $control->getComposerFormLayoutSetControlID();
	}
	
	public static function getComposerFormLayoutSetControlFromTemporaryID($id) {
		if (isset(self::$cmpOutputControlIDs[$id])) {
			return self::$cmpOutputControlIDs[$id];
		}
	}
	
	protected function importPageTypesBase(SimpleXMLElement $sx) {
		if (isset($sx->pagetypes)) {
			foreach($sx->pagetypes->pagetype as $ct) {
				$pkg = ContentImporter::getPackageObject($ct['package']);
				$ctt = CollectionType::getByHandle($ct['handle']);
				if (!is_object($ctt)) { 
					$ctr = CollectionType::add(array(
						'ctHandle' => $ct['handle'],
						'ctName' => $ct['name'],
						'ctIcon' => $ct['icon'],
						'ctIsInternal' => (string) $ct['internal']
					), $pkg);
				}
			}
		}
	}

	protected function importPageTypeDefaults(SimpleXMLElement $sx) {
		$db = Loader::db();
		if (isset($sx->pagetypes)) {
			foreach($sx->pagetypes->pagetype as $ct) {
				$ctr = CollectionType::getByHandle((string) $ct['handle']);
				$mc = Page::getByID($ctr->getMasterCollectionID(), 'RECENT');
				if (isset($ct->page)) {
					$this->importPageAreas($mc, $ct->page);
				}
			}
		}
	}

	protected function importBlockTypes(SimpleXMLElement $sx) {
		if (isset($sx->blocktypes)) {
			foreach($sx->blocktypes->blocktype as $bt) {
				$pkg = ContentImporter::getPackageObject($bt['package']);
				if (is_object($pkg)) {
					BlockType::installBlockTypeFromPackage($bt['handle'], $pkg);
				} else {
					BlockType::installBlockType($bt['handle']);				
				}
			}
		}
	}

	protected function importWorkflowTypes(SimpleXMLElement $sx) {
		if (isset($sx->workflowtypes)) {
			foreach($sx->workflowtypes->workflowtype as $wt) {
				$pkg = ContentImporter::getPackageObject($wt['package']);
				$name = $wt['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($wt['handle']);
				}
				$type = WorkflowType::add($wt['handle'], $name, $pkg);
			}
		}
	}

	protected function importAttributeTypes(SimpleXMLElement $sx) {
		if (isset($sx->attributetypes)) {
			foreach($sx->attributetypes->attributetype as $at) {
				$pkg = ContentImporter::getPackageObject($at['package']);
				$name = $at['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($at['handle']);
				}
				$type = AttributeType::add($at['handle'], $name, $pkg);
				if (isset($at->categories)) {
					foreach($at->categories->children() as $cat) {
						$catobj = AttributeKeyCategory::getByHandle((string) $cat['handle']);
						$catobj->associateAttributeKeyType($type);
					}
				}
			}
		}
	}

	protected function importPermissionAccessEntityTypes(SimpleXMLElement $sx) {
		if (isset($sx->permissionaccessentitytypes)) {
			foreach($sx->permissionaccessentitytypes->permissionaccessentitytype as $pt) {
				$pkg = ContentImporter::getPackageObject($pt['package']);
				$name = $pt['name'];
				if (!$name) {
					$name = Loader::helper('text')->unhandle($pt['handle']);
				}
				$type = PermissionAccessEntityType::add($pt['handle'], $name, $pkg);
				if (isset($pt->categories)) {
					foreach($pt->categories->children() as $cat) {
						$catobj = PermissionKeyCategory::getByHandle((string) $cat['handle']);
						$catobj->associateAccessEntityType($type);
					}
				}
			}
		}
	}
	
	protected function importPackages(SimpleXMLElement $sx) {
		if (isset($sx->packages)) {
			foreach($sx->packages->package as $p) {
				$pkg = Loader::package((string) $p['handle']);
				$pkg->install();
			}
		}
	}
	
	protected function importThemes(SimpleXMLElement $sx) {
		if (isset($sx->themes)) {
			foreach($sx->themes->theme as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ptHandle = (string) $th['handle'];
				$pt = PageTheme::getByHandle($ptHandle);
				if (!is_object($pt)) {
					$pt = PageTheme::add($ptHandle, $pkg);
				}
				if ($th['activated'] == '1') {
					$pt->applyToSite();
				}
			}
		}
	}

	protected function importComposerTargetTypes(SimpleXMLElement $sx) {
		if (isset($sx->composertargettypes)) {
			foreach($sx->composertargettypes->type as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ce = ComposerTargetType::add((string) $th['handle'], (string) $th['name'], $pkg);
			}
		}
	}

	protected function importComposerControlTypes(SimpleXMLElement $sx) {
		if (isset($sx->composercontroltypes)) {
			foreach($sx->composercontroltypes->type as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ce = ComposerControlType::add((string) $th['handle'], (string) $th['name'], $pkg);
			}
		}
	}

	protected function importComposers(SimpleXMLElement $sx) {
		if (isset($sx->composers)) {
			foreach($sx->composers->composer as $cm) {
				Composer::import($cm);
			}
		}
	}

	protected function importConversationEditors(SimpleXMLElement $sx) {
		if (isset($sx->conversationeditors)) {
			foreach($sx->conversationeditors->editor as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ce = ConversationEditor::add((string) $th['handle'], (string) $th['name'], $pkg);
				if ($th['activated'] == '1') {
					$ce->activate();
				}
			}
		}
	}

	protected function importConversationRatingTypes(SimpleXMLElement $sx) {
		if (isset($sx->conversationratingtypes)) {
			foreach($sx->conversationratingtypes->conversationratingtype as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$ce = ConversationRatingType::add((string) $th['handle'], (string) $th['name'], $th['points'], $pkg);
			}
		}
	}


	protected function importBannedWords(SimpleXMLElement $sx) {
		if (isset($sx->banned_words)) {
			foreach($sx->banned_words->banned_word as $p) {
				$bw = BannedWord::add(str_rot13($p));
			}
		}
	}

	protected function importConversationFlagTypes(SimpleXMLElement $sx) {
		if (isset($sx->flag_types)) {
			foreach($sx->flag_types->flag_type as $p) {
				$bw = ConversationFlagType::add($p);
			}
		}
	}

	protected function importSystemCaptchaLibraries(SimpleXMLElement $sx) {
		if (isset($sx->systemcaptcha)) {
			Loader::model('system/captcha/library');
			foreach($sx->systemcaptcha->library as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$scl = SystemCaptchaLibrary::add($th['handle'], $th['name'], $pkg);
				if ($th['activated'] == '1') {
					$scl->activate();
				}
			}
		}
	}

	protected function importSystemContentEditorSnippets(SimpleXMLElement $sx) {
		if (isset($sx->systemcontenteditorsnippets)) {
			foreach($sx->systemcontenteditorsnippets->snippet as $th) {
				$pkg = ContentImporter::getPackageObject($th['package']);
				$scs = SystemContentEditorSnippet::add($th['handle'], $th['name'], $pkg);
				if ($th['activated'] == '1') {
					$scs->activate();
				}
			}
		}
	}

	protected function importJobs(SimpleXMLElement $sx) {
		Loader::model('job');
		if (isset($sx->jobs)) {
			foreach($sx->jobs->job as $jx) {
				$pkg = ContentImporter::getPackageObject($jx['package']);
				if (is_object($pkg)) {
					Job::installByPackage($jx['handle'], $pkg);
				} else {
					Job::installByHandle($jx['handle']);				
				}
			}
		}
	}

	protected function importJobSets(SimpleXMLElement $sx) {
		if (isset($sx->jobsets)) {
			foreach($sx->jobsets->jobset as $js) {
				$pkg = ContentImporter::getPackageObject($js['package']);
				$jso = JobSet::getByName((string) $js['name']);
				if (!is_object($jso)) {
					$jso = JobSet::add((string) $js['name']);
				}
				foreach($js->children() as $jsk) {
					$j = Job::getByHandle((string) $jsk['handle']);
					if (is_object($j)) { 	
						$jso->addJob($j);
					}
				}
			}
		}
	}

	protected function importConfigValues(SimpleXMLElement $sx) {
		if (isset($sx->config)) {
			$db = Loader::db();
			$configstore = new ConfigStore($db);
			foreach($sx->config->children() as $key) {
				$pkg = ContentImporter::getPackageObject($key['package']);
				if (is_object($pkg)) {
					$configstore->set($key->getName(), (string) $key, $pkg->getPackageID());
				} else {
					$configstore->set($key->getName(), (string) $key);
				}
			}
		}
	}

	protected function importTaskPermissions(SimpleXMLElement $sx) {
		if (isset($sx->taskpermissions)) {
			foreach($sx->taskpermissions->taskpermission as $tp) {
				$pkg = ContentImporter::getPackageObject($tp['package']);
				$tpa = TaskPermission::addTask($tp['handle'], $tp['name'], $tp['description'], $pkg);
				if (isset($tp->access)) {
					foreach($tp->access->children() as $ch) {
						if ($ch->getName() == 'group') {
							$g = Group::getByName($ch['name']);
							if (!is_object($g)) {
								$g = Group::add($ch['name'], $ch['description']);
							}
							$tpa->addAccess($g);
						}
					}
				}
			}
		}
	}

	protected function importPermissionCategories(SimpleXMLElement $sx) {
		if (isset($sx->permissioncategories)) {
			foreach($sx->permissioncategories->category as $pkc) {
				$pkg = ContentImporter::getPackageObject($akc['package']);
				$pkx = PermissionKeyCategory::add((string) $pkc['handle'], $pkg);
			}
		}
	}

	protected function importWorkflowProgressCategories(SimpleXMLElement $sx) {
		if (isset($sx->workflowprogresscategories)) {
			foreach($sx->workflowprogresscategories->category as $wpc) {
				$pkg = ContentImporter::getPackageObject($wpc['package']);
				$wkx = WorkflowProgressCategory::add((string) $wpc['handle'], $pkg);
			}
		}
	}

	protected function importPermissions(SimpleXMLElement $sx) {
		if (isset($sx->permissionkeys)) {
			foreach($sx->permissionkeys->permissionkey as $pk) {
				$pkc = PermissionKeyCategory::getByHandle((string) $pk['category']);
				$pkg = ContentImporter::getPackageObject($pk['package']);
				$txt = Loader::helper('text');
				$className = $txt->camelcase($pkc->getPermissionKeyCategoryHandle());
				$c1 = $className . 'PermissionKey';
				$pkx = call_user_func(array($c1, 'import'), $pk);	
				if (isset($pk->access)) {
					foreach($pk->access->children() as $ch) {
						if ($ch->getName() == 'group') {
							$g = Group::getByName($ch['name']);
							if (!is_object($g)) {
								$g = Group::add($g['name'], $g['description']);
							}
							$pae = GroupPermissionAccessEntity::getOrCreate($g);
							$pa = PermissionAccess::create($pkx);
							$pa->addListItem($pae);
							$pt = $pkx->getPermissionAssignmentObject();
							$pt->assignPermissionAccess($pa);
						}
					}
				}
			
			}
		}
	}

	protected function importFeatures(SimpleXMLElement $sx) {
		if (isset($sx->features)) {
			foreach($sx->features->feature as $fea) {
				$feHasCustomClass = false;
				if ($fea['has-custom-class']) {
					$feHasCustomClass = true;
				}
				$pkg = ContentImporter::getPackageObject($fea['package']);
				$fx = Feature::add((string) $fea['handle'], (string) $fea['score'], $feHasCustomClass, $pkg);
			}
		}
	}

	protected function importFeatureCategories(SimpleXMLElement $sx) {
		if (isset($sx->featurecategories)) {
			foreach($sx->featurecategories->featurecategory as $fea) {
				$pkg = ContentImporter::getPackageObject($fea['package']);
				$fx = FeatureCategory::add($fea['handle'], $pkg);
			}
		}
	}
	
	protected function importAttributeCategories(SimpleXMLElement $sx) {
		if (isset($sx->attributecategories)) {
			foreach($sx->attributecategories->category as $akc) {
				$pkg = ContentImporter::getPackageObject($akc['package']);
				$akx = AttributeKeyCategory::add($akc['handle'], $akc['allow-sets'], $pkg);
			}
		}
	}
	
	protected function importAttributes(SimpleXMLElement $sx) {
		if (isset($sx->attributekeys)) {
			foreach($sx->attributekeys->attributekey as $ak) {
				$akc = AttributeKeyCategory::getByHandle($ak['category']);
				$pkg = ContentImporter::getPackageObject($ak['package']);
				$type = AttributeType::getByHandle($ak['type']);
				$txt = Loader::helper('text');
				$className = $txt->camelcase($akc->getAttributeKeyCategoryHandle());
				$c1 = $className . 'AttributeKey';
				$ak = call_user_func(array($c1, 'import'), $ak);				
			}
		}
	}

	protected function importAttributeSets(SimpleXMLElement $sx) {
		if (isset($sx->attributesets)) {
			foreach($sx->attributesets->attributeset as $as) {
				$akc = AttributeKeyCategory::getByHandle($as['category']);
				$pkg = ContentImporter::getPackageObject($as['package']);
				$set = $akc->addSet((string) $as['handle'], (string) $as['name'], $pkg, $as['locked']);
				foreach($as->children() as $ask) {
					$ak = $akc->getAttributeKeyByHandle((string) $ask['handle']);
					if (is_object($ak)) { 	
						$set->addKey($ak);
					}
				}
			}
		}
	}

	protected function importGatheringDataSources(SimpleXMLElement $sx) {
		if (isset($sx->gatheringsources)) {
			foreach($sx->gatheringsources->gatheringsource as $ags) {
				$pkg = ContentImporter::getPackageObject($ags['package']);
				$source = GatheringDataSource::add((string) $ags['handle'], (string) $ags['name'], $pkg);
			}
		}
	}

	protected function importGatheringItemTemplateTypes(SimpleXMLElement $sx) {
		if (isset($sx->gatheringitemtemplatetypes)) {
			foreach($sx->gatheringitemtemplatetypes->gatheringitemtemplatetype as $at) {
				$pkg = ContentImporter::getPackageObject($wt['package']);
				$type = GatheringItemTemplateType::add((string) $at['handle'], $pkg);
			}
		}
	}


	protected function importGatheringItemTemplates(SimpleXMLElement $sx) {
		if (isset($sx->gatheringitemtemplates)) {
			foreach($sx->gatheringitemtemplates->gatheringitemtemplate as $at) {
				$pkg = ContentImporter::getPackageObject($at['package']);
				$type = GatheringItemTemplateType::getByHandle((string) $at['type']);
				$gatHasCustomClass = false;
				$gatForceDefault = false;
				$gatFixedSlotWidth = 0;
				$gatFixedSlotHeight = 0;
				if ($at['has-custom-class']) {
					$gatHasCustomClass = true;
				}
				if ($at['force-default']) {
					$gatForceDefault = true;
				}
				if ($at['fixed-slot-width']) {
					$gatFixedSlotWidth = (string) $at['fixed-slot-width'];
				}
				if ($at['fixed-slot-height']) {
					$gatFixedSlotHeight = (string) $at['fixed-slot-height'];
				}
				$template = GatheringItemTemplate::add($type, (string) $at['handle'], (string) $at['name'], $gatFixedSlotWidth, $gatFixedSlotHeight, $gatHasCustomClass, $gatForceDefault, $pkg);
				foreach($at->children() as $fe) {
					$feo = Feature::getByHandle((string) $fe['handle']);
					if (is_object($feo)) { 	
						$template->addGatheringItemTemplateFeature($feo);
					}
				}
			}
		}
	}


	protected function importBlockTypeSets(SimpleXMLElement $sx) {
		if (isset($sx->blocktypesets)) {
			foreach($sx->blocktypesets->blocktypeset as $bts) {
				$pkg = ContentImporter::getPackageObject($bts['package']);
				$set = BlockTypeSet::add((string) $bts['handle'], (string) $bts['name'], $pkg);
				foreach($bts->children() as $btk) {
					$bt = BlockType::getByHandle((string) $btk['handle']);
					if (is_object($bt)) { 	
						$set->addBlockType($bt);
					}
				}
			}
		}
	}

	public static function getValue($value) {
		if (preg_match('/\{ccm:export:page:(.*)\}|\{ccm:export:file:(.*)\}|\{ccm:export:image:(.*)\}|\{ccm:export:pagetype:(.*)\}/i', $value, $matches)) {
			if ($matches[1]) {
				$c = Page::getByPath($matches[1]);
				return $c->getCollectionID();
			}
			if ($matches[2]) {
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[2]));
				return $fID;
			}
			if ($matches[3]) {
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($matches[3]));
				return $fID;
			}
			if ($matches[4]) {
				$ct = CollectionType::getByHandle($matches[4]);
				return $ct->getCollectionTypeID();
			}
		} else {
			return $value;
		}
	}	

}
