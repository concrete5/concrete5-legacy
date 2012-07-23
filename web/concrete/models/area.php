<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * An area object is used within templates to mark certain portions of pages as editable and containers of dynamic content
 *
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Area extends Object {

	public $cID, $arID, $arHandle;
	public $c;

	/* area-specific attributes */

	/**
	 * limits the number of blocks in the area
	 * @var int
	*/
	public $maximumBlocks = -1; //

	/**
	 * sets a custom template for all blocks in the area
	 * @see Area::getCustomTemplates()
	 * @var array
	*/
	public $customTemplateArray = array();

	/**
	 * block type handle for the block to automatically activate on first_run
	 * @var string
	*/
	public $firstRunBlockTypeHandle;

	/**
	 * if set higher, any blocks that aren't rated high enough aren't seen (unless you have sufficient privs)
	 * @var int
	*/
	public $ratingThreshold = 0; //

	/**
	 * @var boolean
	*/
	public $showControls = true;


	/**
	 * @var array
	*/
	public $attributes = array();

	/**
	 * @var string
	*/
	public $enclosingStart = '';

	/**
	 * Denotes if we should run sprintf() on blockWrapperStart
	 * @var boolean
	*/
	public $enclosingStartHasReplacements = false;

	/**
	 * @var string
	*/
	public $enclosingEnd = '';

	/**
	 * Denotes if we should run sprintf() on blockWrapperStartEnd
	 * @var boolean
	*/
	public $enclosingEndHasReplacements = false;

	/* run-time variables */

	/**
	 * the number of blocks currently rendered in the area
	 * @see Area::getTotalBlocksInArea()
	 * @var int
	*/
	public $totalBlocks = 0;

	/**
	 * Array of Blocks within the current area
	 * not an array actually until it's set
	 * @see Area::getAreaBlocksArray()
	 * @var Block[]
	 */
	public $areaBlocksArray;

	/**
	 * The constructor is used primarily on page templates to create areas of content that are editable within the cms.
	 * ex: $a = new Area('Main'); $a->display($c)
	 * We actually use Collection::getArea() when we want to interact with a fully
	 * qualified Area object when dealing with a Page/Collection object
	 *
	 * @param string
	 * @return void
	*/
	public function __construct($arHandle) {
		$this->arHandle = $arHandle;
		$v = View::getInstance();
		if (!$v->editingEnabled()) {
			$this->showControls = false;
		}
	}


	/**
	 * returns the Collection's cID
	 * @return int
	*/
	public function getCollectionID() {return $this->cID;}

	/**
	 * returns the Collection object for the current Area
	 * @return Collection
	*/
	public function getAreaCollectionObject() {return $this->c;}

	/**
	 * whether or not it's a global area
	 * @return bool
	*/
	public function isGlobalArea() {return $this->arIsGlobal;}

	/**
	 * returns the arID of the current area
	 * @return int
	 */
	public function getAreaID() {return $this->arID;}

	/**
	 * returns the handle for the current area
	 * @return string
	*/
	public function getAreaHandle() {return $this->arHandle;}

	/**
	 * returns an array of custom templates
	 * @return array
	 */
	public function getCustomTemplates() {return $this->customTemplateArray;}

	/**
	 * sets a custom block template for blocks of a type specified by the btHandle
	 * @param string $btHandle handle for the block type
	 * @param string $temp string identifying the block template ex: breadcrumb
	 */
	public function setCustomTemplate($btHandle, $temp) {$this->customTemplateArray[$btHandle] = $temp;}

	/**
	 * Returns the total number of blocks in an area.
	 * @param Page $c must be passed if the display() method has not been run on the area object yet.
	 */
	public function getTotalBlocksInArea($c = false) {
		if (!is_array($this->areaBlocksArray) && is_object($c)) {
			$this->getAreaBlocksArray($c);
		}
		return $this->totalBlocks;

	}

	/**
	 * check if the area has permissions that override the page's permissions
	 * @return boolean
	 */
	public function overrideCollectionPermissions() {return $this->arOverrideCollectionPermissions; }

	/**
	 * @return int
	 */
	public function getAreaCollectionInheritID() {return $this->arInheritPermissionsFromAreaOnCID;}

	/**
	 * Sets the total number of blocks an area allows. Does not limit by type.
	 * @param int $num
	 * @return void
	 */
	public function setBlockLimit($num) {
		$this->maximumBlocks = $num;
	}

	/**
	 *
	 * @param $attr
	 * @param $val
	 * @return void
	 */
	public function setAttribute($attr, $val) {
		$this->attributes[$attr] = $val;
	}

	/**
	 *
	 * @param $attr
	 * @return
	 */
	public function getAttribute($attr) {
		return $this->attributes[$attr];
	}

	/**
	 * disables controls for the current area
	 * @return void
	 */
	public function disableControls() {
		$this->showControls = false;
	}


	/**
	 * determines if the current Area can accept additonal Blocks
	 * @return boolean
	 */
	public function areaAcceptsBlocks() {
		return (($this->maximumBlocks > $this->totalBlocks) || ($this->maximumBlocks == -1));
	}

	/**
	 * gets the maximum allowed number of blocks, -1 if unlimited
	 * @return int
	 */
	public function getMaximumBlocks() {return $this->maximumBlocks;}

	/**
	 *
	 * @return string
	 */
	function getAreaUpdateAction($task = 'update', $alternateHandler = null) {
		$valt = Loader::helper('validation/token');
		$token = '&' . $valt->getParameter();
		$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
		$c = $this->getAreaCollectionObject();
		if ($alternateHandler) {
			$str = $alternateHandler . "?atask={$task}&cID=" . $c->getCollectionID() . "&arHandle=" . $this->getAreaHandle() . $step . $token;
		} else {
			$str = DIR_REL . "/" . DISPATCHER_FILENAME . "?atask=" . $task . "&cID=" . $c->getCollectionID() . "&arHandle=" . $this->getAreaHandle() . $step . $token;
		}
		return $str;
	}


	/**
	 * Gets the Area object for the given page and area handle
	 * @param Page|Collection $c
	 * @param string $arHandle
	 * @return Area
	 */
	public static function get(&$c, $arHandle) {
		if (!is_object($c)) {
			return false;
		}

		$ca = new Cache();
		$a = Cache::get('area', $c->getCollectionID() . ':' . $arHandle);
		if ($a instanceof Area) {
			return $a;
		}

		$db = Loader::db();
		// First, we verify that this is a legitimate area
		$v = array($c->getCollectionID(), $arHandle);
		$q = "select arID, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal from Areas where cID = ? and arHandle = ?";
		$arRow = $db->getRow($q, $v);
		if ($arRow['arID'] > 0) {
			$area = new Area($arHandle);

			$area->arID = $arRow['arID'];
			$area->arOverrideCollectionPermissions = $arRow['arOverrideCollectionPermissions'];
			$area->arIsGlobal = $arRow['arIsGlobal'];
			$area->arInheritPermissionsFromAreaOnCID = $arRow['arInheritPermissionsFromAreaOnCID'];
			$area->cID = $c->getCollectionID();
			$area->c = &$c;

			Cache::set('area', $c->getCollectionID() . ':' . $arHandle, $area);

			return $area;
		}
	}

	/**
	 * Gets or creates if necessary an Area for the given Page, Handle
	 * @param Page|Collection $c
	 * @param string $arHandle
	 * @param boolean $arIsGlobal
	 * @return Area
	 */
	public static function getOrCreate(&$c, $arHandle, $arIsGlobal = 0) {

		/*
			different than get(), getOrCreate() is called by the templates. If no area record exists for the
			permissions cID / handle combination, we create one. This is to make our lives easier
		*/

		$area = Area::get($c, $arHandle);
		if (is_object($area)) {
			if ($area->isGlobalArea() == $arIsGlobal) {
				return $area;
			} else if (!$area->isGlobalArea() && !$arIsGlobal) {
				return $area;
			}
		}

		$cID = $c->getCollectionID();
		$db = Loader::db();
		if (!$arIsGlobal) {
			$arIsGlobal = 0;
		}
		$db->Replace('Areas', array('cID' => $cID, 'arHandle' => $arHandle, 'arIsGlobal' => $arIsGlobal), array('arHandle', 'cID'), true);

		if ($arIsGlobal) {
			// we create a stack for it
			Stack::getOrCreateGlobalArea($arHandle);
		}

		$area = Area::get($c, $arHandle); // we're assuming the insert succeeded
		$area->rescanAreaPermissionsChain();
		return $area;

	}

	/**
	 * Get all of the blocks within the current area for a given page
	 * @param Page|Collection $c
	 * @return Block[]
	 */
	public function getAreaBlocksArray($c) {
		if (is_array($this->areaBlocksArray)) {
			return $this->areaBlocksArray;
		}

		$this->cID = $c->getCollectionID();
		$this->c = $c;
		$this->areaBlocksArray = array();

		if ($this->arIsGlobal) {
			$blocks = array();
			$cp = new Permissions($c);
			if ($cp->canReadVersions()) {
				$c = Stack::getByName($this->arHandle);
			} else {
				$c = Stack::getByName($this->arHandle, 'ACTIVE');
			}
			if (is_object($c)) {
				$blocks = $c->getBlocks(STACKS_AREA_NAME);
				$globalArea = Area::get($c, STACKS_AREA_NAME);
			}
		} else {
			$blocks = $c->getBlocks($this->arHandle);
		}
		foreach($blocks as $ab) {
			if ($this->arIsGlobal && is_object($globalArea)) {
				$ab->setBlockAreaObject($globalArea);
			} else {
				$ab->setBlockAreaObject($this);
			}
			$this->areaBlocksArray[] = $ab;
			$this->totalBlocks++;
		}
		return $this->areaBlocksArray;
	}

	/**
	 * determins based on permissions what types of blocks, if any can be added to this area
	 * @param Page|Collection $c
	 * @param AreaPermissions
	 * @return boolean|BlockTypeList
	 */
	public function getAddBlockTypes(&$c, &$ap) {
		if ($ap->canAddBlocks()) {
			$bt = new BlockTypeList($ap->addBlockTypes);
		} else {
			$bt = false;
		}
		return $bt;
	}

	/**
	 * gets a list of all areas - no relation to the current page or area object
	 * possibly could be set as a static method??
	 * @return array
	 */
	public function getHandleList() {
		$db = Loader::db();
		$r = $db->Execute('select distinct arHandle from Areas order by arHandle asc');
		$handles = array();
		while ($row = $r->FetchRow()) {
			$handles[] = $row['arHandle'];
		}
		$r->Free();
		unset($r);
		unset($db);
		return $handles;
	}

	/**
	 * This function removes all permissions records for the current Area
	 * and sets it to inherit from the Page permissions
	 * @return void
	*/
	function revertToPagePermissions() {

		$db = Loader::db();
		$v = array($this->getAreaHandle(), $this->getCollectionID());
		$db->query("delete from AreaGroups where arHandle = ? and cID = ?", $v);
		$db->query("delete from AreaGroupBlockTypes where arHandle = ? and cID = ?", $v);
		$db->query("update Areas set arOverrideCollectionPermissions = 0 where arID = ?", array($this->getAreaID()));

		// now we set rescan this area to determine where it -should- be inheriting from
		$this->arOverrideCollectionPermissions = false;
		$this->rescanAreaPermissionsChain();

		$areac = $this->getAreaCollectionObject();
		if ($areac->isMasterCollection()) {
			$this->rescanSubAreaPermissionsMasterCollection($areac);
		} else if ($areac->overrideTemplatePermissions()) {
			// now we scan sub areas
			$this->rescanSubAreaPermissions();
		}

		$ca = new Cache();
		$a = Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());
	}

	/**
	 * unsets the page object, automatically called
	 *  @return void
	 */
	function __destruct() {
		unset($this->c);
	}


	/**
	 * Rescans the current Area's permissions ensuring that it's enheriting permissions properly up the chain
	 * @return void
	 */
	public function rescanAreaPermissionsChain() {
		$db = Loader::db();
		if ($this->overrideCollectionPermissions()) {
			return false;
		}
		// first, we obtain the inheritance of permissions for this particular collection
		$areac = $this->getAreaCollectionObject();
		if (is_a($areac, 'Page')) {
			if ($areac->getCollectionInheritance() == 'PARENT') {

				$cIDToCheck = $areac->getCollectionParentID();
				// first, we temporarily set the arInheritPermissionsFromAreaOnCID to whatever the arInheritPermissionsFromAreaOnCID is set to
				// in the immediate parent collection
				$arInheritPermissionsFromAreaOnCID = $db->getOne("select a.arInheritPermissionsFromAreaOnCID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($cIDToCheck, $this->getAreaHandle()));
				$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($arInheritPermissionsFromAreaOnCID, $this->getAreaID()));

				// now we do the recursive rescan to see if any areas themselves override collection permissions

				while ($cIDToCheck > 0) {
					$row = $db->getRow("select c.cParentID, c.cID, a.arHandle, a.arOverrideCollectionPermissions, a.arID from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($cIDToCheck, $this->getAreaHandle()));
					if ($row['arOverrideCollectionPermissions'] == 1) {
						break;
					} else {
						$cIDToCheck = $row['cParentID'];
					}
				}

				if (is_array($row)) {
					if ($row['arOverrideCollectionPermissions']) {
						// then that means we have successfully found a parent area record that we can inherit from. So we set
						// out current area to inherit from that COLLECTION ID (not area ID - from the collection ID)
						$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($row['cID'], $this->getAreaID()));
						$this->arInheritPermissionsFromAreaOnCID = $row['cID'];
					}
				}
			} else if ($areac->getCollectionInheritance() == 'TEMPLATE') {
				 // we grab an area on the master collection (if it exists)
				$doOverride = $db->getOne("select arOverrideCollectionPermissions from Pages c inner join Areas a on (c.cID = a.cID) where c.cID = ? and a.arHandle = ?", array($areac->getPermissionsCollectionID(), $this->getAreaHandle()));
				if ($doOverride) {
					$db->query("update Areas set arInheritPermissionsFromAreaOnCID = ? where arID = ?", array($areac->getPermissionsCollectionID(), $this->getAreaID()));
					$this->arInheritPermissionsFromAreaOnCID = $areac->getPermissionsCollectionID();
				}
			}
		}

		Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());
	}

	/**
	 * works a lot like rescanAreaPermissionsChain() but it works down. This is typically only
	 * called when we update an area to have specific permissions, and all areas that are on pagesbelow it with the same
	 * handle, etc... should now inherit from it.
	 * @return void
	 */
	function rescanSubAreaPermissions($cIDToCheck = null) {
		$db = Loader::db();
		if (!$cIDToCheck) {
			$cIDToCheck = $this->getCollectionID();
		}

		$v = array($this->getAreaHandle(), 'PARENT', $cIDToCheck);
		$r = $db->query("select Areas.arID, Areas.cID from Areas inner join Pages on (Areas.cID = Pages.cID) where Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cParentID = ?", $v);
		while ($row = $r->fetchRow()) {
			// these are all the areas we need to update.
			$db->query("update Areas set arInheritPermissionsFromAreaOnCID = " . $this->getAreaCollectionInheritID() . " where arID = " . $row['arID']);
			$this->rescanSubAreaPermissions($row['cID']);
		}

	}

	/**
	 * similar to rescanSubAreaPermissions, but for those who have setup their pages to inherit master collection permissions
	 * @see Area::rescanSubAreaPermissions()
	 * @return void
	 */
	function rescanSubAreaPermissionsMasterCollection($masterCollection) {
		if (!$masterCollection->isMasterCollection()) {
			return false;
		}

		// if we're not overriding permissions on the master collection then we set the ID to zero. If we are, then we set it to our own ID
		$toSetCID = ($this->overrideCollectionPermissions()) ? $masterCollection->getCollectionID() : 0;

		$db = Loader::db();
		$v = array($this->getAreaHandle(), 'TEMPLATE', $masterCollection->getCollectionID());
		$db->query("update Areas, Pages set Areas.arInheritPermissionsFromAreaOnCID = " . $toSetCID . " where Areas.cID = Pages.cID and Areas.arHandle = ? and cInheritPermissionsFrom = ? and arOverrideCollectionPermissions = 0 and cInheritPermissionsFromCID = ?", $v);
	}

	/**
	 * display's the Area in the page
	 * ex: $a = new Area('Main'); $a->display($c);
	 * If ENABLE_AREA_EVENTS is set, fires 'on_before_area_header', 'on_after_area_header',
	 * 'on_before_area_footer' and 'on_after_area_footer'.
	 * Event handlers have the opportunity to directly output their own wrapping html.
	 * @param Page|Collection $c
	 * @param Block[] $alternateBlockArray optional array of blocks to render instead of default behavior
	 * @return void
	 */
	function display(&$c, $alternateBlockArray = null) {

		if(!intval($c->cID)){
			//Invalid Collection
			return false;
		}

		if ($this->arIsGlobal) {
			$stack = Stack::getByName($this->arHandle);
		}
		$currentPage = Page::getCurrentPage();
		$ourArea = Area::getOrCreate($c, $this->arHandle, $this->arIsGlobal);
		if (count($this->customTemplateArray) > 0) {
			$ourArea->customTemplateArray = $this->customTemplateArray;
		}
		if (count($this->attributes) > 0) {
			$ourArea->attributes = $this->attributes;
		}
		if ($this->maximumBlocks > -1) {
			$ourArea->maximumBlocks = $this->maximumBlocks;
		}
		$ap = new Permissions($ourArea);
		$blocksToDisplay = ($alternateBlockArray) ? $alternateBlockArray : $ourArea->getAreaBlocksArray($c, $ap);
		$this->totalBlocks = $ourArea->getTotalBlocksInArea();
		$u = new User();

		$bv = new BlockView();

		// now, we iterate through these block groups (which are actually arrays of block objects), and display them on the page

		if (($this->showControls) && ($c->isEditMode() && ($ap->canAddBlocks() || $u->isSuperUser()))) {
			$bv->renderElement('block_area_header', array('a' => $ourArea));
		}

		// Event only fires if ENABLE_AREA_EVENTS is set. Provides an opportunity to intercept and modify/create whole area wrappers.
		if (! $c->isEditMode() && defined('ENABLE_AREA_EVENTS') && ENABLE_AREA_EVENTS && ENABLE_AREA_EVENTS !== 'false'){
			$ret1 = Events::fire('on_before_area_header', $this->arHandle, $this, $ourArea, $this->totalBlocks);
		}
		if ($ret1 !== null && is_string($ret1)){
			echo $ret1;
		} else {
			$bv->renderElement('block_area_header_view', array('a' => $ourArea));
		}

		// Event only fires if ENABLE_AREA_EVENTS is set. Provides an opportunity to intercept and modify/create whole area wrappers.
		if (! $c->isEditMode() && defined('ENABLE_AREA_EVENTS') && ENABLE_AREA_EVENTS && ENABLE_AREA_EVENTS !== 'false'){
			$ret2 = Events::fire('on_after_area_header', $this->arHandle, $this, $ourArea, $this->totalBlocks);
		}

		if ($ret2 !== null && is_string($ret2)){
			echo $ret2;
		} else {

			//display layouts tied to this area
			//Might need to move this to a better position
			$areaLayouts = $this->getAreaLayouts($c);
			if(is_array($areaLayouts) && count($areaLayouts)){
				foreach($areaLayouts as $layout){
					$layout->display($c,$this);
				}
				if($this->showControls && ($c->isArrangeMode() || $c->isEditMode())) {
					echo '<div class="ccm-layouts-block-arrange-placeholder ccm-block-arrange"></div>';
				}
			}

			$blockPositionInArea = 1; //for blockWrapper output

			foreach ($blocksToDisplay as $b) {
				$bv = new BlockView();
				$bv->setAreaObject($ourArea);

				// this is useful for rendering areas from one page
				// onto the next and including interactive elements
				if ($currentPage->getCollectionID() != $c->getCollectionID()) {
					$b->setBlockActionCollectionID($c->getCollectionID());
				}
				if ($this->arIsGlobal && is_object($stack)) {
					$b->setBlockActionCollectionID($stack->getCollectionID());
				}
				$p = new Permissions($b);
				if (($p->canWrite() || $p->canDeleteBlock()) && $c->isEditMode() && $this->showControls) {
					$includeEditStrip = true;
				}

				if ($p->canRead()) {
					if (!$c->isEditMode()) {
						$this->outputBlockWrapper(true, $b, $blockPositionInArea);
					}
					if ($includeEditStrip) {
						$bv->renderElement('block_controls', array(
							'a' => $ourArea,
							'b' => $b,
							'p' => $p
						));
						$bv->renderElement('block_header', array(
							'a' => $ourArea,
							'b' => $b,
							'p' => $p
						));
					}

					$bv->render($b);
					if ($includeEditStrip) {
						$bv->renderElement('block_footer');
					}
					if (!$c->isEditMode()) {
						$this->outputBlockWrapper(false, $b, $blockPositionInArea);
					}
				}

				$blockPositionInArea++;
			}
		}
		
		// Event only fires if ENABLE_AREA_EVENTS is set. Provides an opportunity to intercept and modify/create whole area wrappers.
		if (! $c->isEditMode() && defined('ENABLE_AREA_EVENTS') && ENABLE_AREA_EVENTS && ENABLE_AREA_EVENTS !== 'false'){
			$ret3 = Events::fire('on_before_area_footer', $this->arHandle, $this, $ourArea, $blockPositionInArea, $this->totalBlocks);
		}
		if ($ret3 !== null && is_string($ret3)){
			echo $ret3;
		} else {
			$bv->renderElement('block_area_footer_view', array('a' => $ourArea));
		}

		// Event only fires if ENABLE_AREA_EVENTS is set. Provides an opportunity to intercept and modify/create whole area wrappers.
		if (! $c->isEditMode() && defined('ENABLE_AREA_EVENTS') && ENABLE_AREA_EVENTS && ENABLE_AREA_EVENTS !== 'false'){
			$ret4 = Events::fire('on_after_area_footer', $this->arHandle, $this, $ourArea, $blockPositionInArea, $this->totalBlocks);
		}

		if (($this->showControls) && ($c->isEditMode() && ($ap->canAddBlocks() || $u->isSuperUser()))) {
			$bv->renderElement('block_area_footer', array('a' => $ourArea));
		}
	}

	/**
	 * outputs the block wrapers for each block
	 * Internal helper function for display()
	 * If ENABLE_AREA_EVENTS is set, fires 'on_output_block_wrapper_start' and 'on_output_block_wrapper_end'.
	 * Event handlers have the opportunity to return a modified wrapper.
	 * @return void
	 */
	protected function outputBlockWrapper($isStart, &$block, $blockPositionInArea) {
		static $th = null;
		$enclosing = $isStart ? $this->enclosingStart : $this->enclosingEnd;
		$hasReplacements = $isStart ? $this->enclosingStartHasReplacements : $this->enclosingEndHasReplacements;

		// Event only fires if ENABLE_AREA_EVENTS is set. Provides an opportunity to intercept and modify/create area block wrappers.
		if (defined('ENABLE_AREA_EVENTS') && ENABLE_AREA_EVENTS && ENABLE_AREA_EVENTS !== 'false'){
			if ($isStart){
				$evname = 'on_output_block_wrapper_start';
			} else {
				$evname = 'on_output_block_wrapper_end';
			}
			$ret = Events::fire($evname, $this->arHandle, $this, $block, $blockPositionInArea, $enclosing, $hasReplacements);
			// event is now responsible for 
			if ($ret !== null && is_string($ret)){
				$enclosing = $ret;
				$hasReplacements = false;
			}
		}

		if (!empty($enclosing) && $hasReplacements) {
			$bID = $block->getBlockID();
			$btHandle = $block->getBlockTypeHandle();
			$bName = ($btHandle == 'core_stack_display') ? Stack::getByID($block->getInstance()->stID)->getStackName() : $block->getBlockName();
			$th = is_null($th) ? Loader::helper('text') : $th;
			$bSafeName = $th->entities($bName);
			$alternatingClass = ($blockPositionInArea % 2 == 0) ? 'even' : 'odd';
			echo sprintf($enclosing, $bID, $btHandle, $bSafeName, $blockPositionInArea, $alternatingClass);
		} else {
			echo $enclosing;
		}
	}

	/**
	 * Gets all layout grid objects for a collection
	 * @param Page|Collection $c
	 * @return Layout[]
	 */
	public function getAreaLayouts($c){

		if( !intval($c->cID) ){
			//Invalid Collection
			return false;
		}

		$db = Loader::db();
		$vals = array( intval($c->cID), $c->getVersionID(), $this->getAreaHandle() );
		$sql = 'SELECT * FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND arHandle=? ORDER BY position ASC, cvalID ASC';
		$rows = $db->getArray($sql,$vals);

		$layouts=array();
		$i=0;
		if(is_array($rows)) foreach($rows as $row){
			$layout = Layout::getById( intval($row['layoutID']) );
			if( is_object($layout) ){

				$i++;

				//check position is correct, update if not
				if( $i != $row['position'] || $renumbering ){
					$renumbering=1;
					$db->query( 'UPDATE CollectionVersionAreaLayouts SET position=? WHERE cvalID=?' , array($i, $row['cvalID']) );
				}
				$layout->position=$i;

				$layout->cvalID = intval($row['cvalID']);

				$layout->setAreaObj( $this );

				$layout->setAreaNameNumber( intval($row['areaNameNumber']) );

				$layouts[]=$layout;
			}
		}

		return $layouts;
	}

	/**
	 * Exports the area to content format
	 * @todo need more documentation export?
	 */
	public function export($p, $page) {
		$area = $p->addChild('area');
		$area->addAttribute('name', $this->getAreaHandle());

		$blocks = $page->getBlocks($this->getAreaHandle());
		foreach($blocks as $bl) {
			$bl->export($area);
		}
	}


	/**
	 * Specify HTML to automatically print before blocks contained within the area
	 * Pass true for $hasReplacements if the $html contains sprintf replacements tokens.
	 * Available tokens:
	 *  %1$s -> Block ID
	 *  %2$s -> Block Type Handle
	 *  %3$s -> Block/Stack Name
	 *  %4$s -> Block position in area (first block is 1, second block is 2, etc.)
	 *  %5$s -> 'odd' or 'even' (useful for "zebra stripes" CSS classes)
	 * @param string $html
	 * @param boolean $hasReplacements
	 * @return void
	 */
	function setBlockWrapperStart($html, $hasReplacements = false) {
		$this->enclosingStart = $html;
		$this->enclosingStartHasReplacements = $hasReplacements;
	}

	/**
	 * Set HTML that automatically prints after any blocks contained within the area
	 * Pass true for $hasReplacements if the $html contains sprintf replacements tokens.
	 * See setBlockWrapperStart() comments for available tokens.
	 * @param string $html
	 * @param boolean $hasReplacements
	 * @return void
	 */
	function setBlockWrapperEnd($html, $hasReplacements = false) {
		$this->enclosingEnd = $html;
		$this->enclosingEndHasReplacements = $hasReplacements;
	}

	/**
	 * Does the work of updating the area content
	 * retrieves data from formatted $_POST when saving blocks through the editing ui
	 * @return void
	 */
	function update() {
		$db = Loader::db();

		// now it's permissions time

		$gIDArray = array();
		$uIDArray = array();
		if (is_array($_POST['areaRead'])) {
			foreach ($_POST['areaRead'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "r:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "r:";
				}
			}
		}

		if (is_array($_POST['areaReadAll'])) {
			foreach ($_POST['areaReadAll'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "rb:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "rb:";
				}
			}
		}

		if (is_array($_POST['areaEdit'])) {
			foreach ($_POST['areaEdit'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "wa:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "wa:";
				}
			}
		}

		if (is_array($_POST['areaDelete'])) {
			foreach ($_POST['areaDelete'] as $ugID) {
				if (strpos($ugID, 'uID') > -1) {
					$uID = substr($ugID, 4);
					$uIDArray[$uID] .= "db:";
				} else {
					$gID = substr($ugID, 4);
					$gIDArray[$gID] .= "db:";
				}
			}
		}

		$gBTArray = array();
		$uBTArray = array();
		if (is_array($_POST['areaAddBlockType'])) {
			foreach($_POST['areaAddBlockType'] as $btID => $ugArray) {
				// this gets us the block type that particular groups/users are given access to
				foreach($ugArray as $ugID) {
					if (strpos($ugID, 'uID') > -1) {
						$uID = substr($ugID, 4);
						$uBTArray[$uID][] = $btID;
					} else {
						$gID = substr($ugID, 4);
						$gBTArray[$gID][] = $btID;
					}
				}
			}
		}

		$db = Loader::db();
		$cID = $this->getCollectionID();
		$v = array($cID, $this->getAreaHandle());
		// update the Area record itself. Hopefully it's been created.
		$db->query("update Areas set arOverrideCollectionPermissions = 1, arInheritPermissionsFromAreaOnCID = 0 where arID = ?", array($this->getAreaID()));

		$db->query("delete from AreaGroups where cID = ? and arHandle = ?", $v);
		$db->query("delete from AreaGroupBlockTypes where cID = ? and arHandle = ?", $v);

		// now we iterate through, and add the permissions
		foreach ($gIDArray as $gID => $perms) {
		   // since this can now be either groups or users, we have prepended gID or uID to each gID value
			// we have to trim the trailing colon, if there is one
			$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
			$v = array($cID, $this->getAreaHandle(), $gID, $permissions);
			$q = "insert into AreaGroups (cID, arHandle, gID, agPermissions) values (?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
		}

		// iterate through and add user-level permissions
		foreach ($uIDArray as $uID => $perms) {
		   // since this can now be either groups or users, we have prepended gID or uID to each gID value
			// we have to trim the trailing colon, if there is one
			$permissions = (strrpos($perms, ':') == (strlen($perms) - 1)) ? substr($perms, 0, strlen($perms) - 1) : $perms;
			$v = array($cID, $this->getAreaHandle(), $uID, $permissions);
			$q = "insert into AreaGroups (cID, arHandle, uID, agPermissions) values (?, ?, ?, ?)";
			$r = $db->prepare($q);
			$res = $db->execute($r, $v);
		}

		foreach($uBTArray as $uID => $uBTs) {
			foreach($uBTs as $btID) {
				$v = array($cID, $this->getAreaHandle(), $uID, $btID);
				$q = "insert into AreaGroupBlockTypes (cID, arHandle, uID, btID) values (?, ?, ?, ?)";
				$r = $db->query($q, $v);
			}
		}

		foreach($gBTArray as $gID => $gBTs) {
			foreach($gBTs as $btID) {
				$v = array($cID, $this->getAreaHandle(), $gID, $btID);
				$q = "insert into AreaGroupBlockTypes (cID, arHandle, gID, btID) values (?, ?, ?, ?)";
				$r = $db->query($q, $v);
			}
		}

		// finally, we rescan subareas so that, if they are inheriting up the tree, they inherit from this place
		$this->arInheritPermissionsFromAreaOnCID = $this->getCollectionID(); // we don't need to actually save this on the area, but we need it for the rescan function
		$this->arOverrideCollectionPermissions = 1; // to match what we did above - useful for the rescan functions below

		$acobj = $this->getAreaCollectionObject();
		if ($acobj->isMasterCollection()) {
			// if we're updating the area on a master collection we need to go through to all areas set on subpages that aren't set to override to change them to inherit from this area
			$this->rescanSubAreaPermissionsMasterCollection($acobj);
		} else {
			$this->rescanSubAreaPermissions();
		}

		$a = Cache::delete('area', $this->getCollectionID() . ':' . $this->getAreaHandle());

	}
}