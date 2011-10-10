<?php defined('C5_EXECUTE') or die("Access Denied.");
/**
 * An events framework for Concrete. System events like "on_user_add" can be hooked into, so that when a user is added to the system, the new UserInfo object is passed to developers' custom functions.
 * Current events include:
 * on_user_add
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Events {
	
	const EVENT_TYPE_PAGETYPE = "page_type";
	const EVENT_TYPE_GLOBAL = "global";
	
	/**
	 * @var array containing all registered events with their callbacks
	 */
	private static $registeredEvents = array();
	
	/** 
	 * Enables events if they haven't been enabled yet.
	 * This happens automatically if a particular 3rd party addon requires it
	 * @return void
	 */
	public static function enableEvents() {
		if (!defined("ENABLE_APPLICATION_EVENTS")) {
			define("ENABLE_APPLICATION_EVENTS", true);
		}
	}
	
	/** 
	 * When the event(s) you listen for fire a method with the exact same name
	 * as the event will be called on the PageTypeController for that pagetype.
	 *
	 * @see Events::extend()
	 * @param string $ctHandle the name of the pagetype
	 * @param string|bool $event either one of the on_page_<action> events or false to listen to all events
	 * @param array $params 
	 * @return void
	 */
	public static function extendPageType($ctHandle, $event = false, $params = array()) {
		self::enableEvents();
		if ($event == false) {
			// then we're registering ALL the page type events for this particular page type
			self::extendPageType($ctHandle, 'on_page_add', $params);
			self::extendPageType($ctHandle, 'on_page_update', $params);
			self::extendPageType($ctHandle, 'on_page_duplicate', $params);
			self::extendPageType($ctHandle, 'on_page_move', $params);
			self::extendPageType($ctHandle, 'on_page_view', $params);
			self::extendPageType($ctHandle, 'on_page_version_approve', $params);
			self::extendPageType($ctHandle, 'on_page_delete', $params);
		} else {
			$class = Object::camelcase($ctHandle) . 'PageTypeController';
			$method = $event;
			$filename = Loader::pageTypeControllerPath($ctHandle);
			self::$registeredEvents[$event][] = array(
				self::EVENT_TYPE_PAGETYPE,
				$class,
				$method,
				$filename,
				$params
			);
		}
	}
	
	/**
	 * Register a callback for a certain event
	 * 
	 * <code>
	 * Events::extend('on_user_add', 'MySpecialClass', 'createSpecialUserInfo', 'models/my_special_class.php', array('foo' => 'bar'))
	 * </code>
	 * @param string $event name of the event
	 * @param string $class name of the call on which the method will be called
	 * @param string $method name of the method that will be called
	 * @param string $filename either a relative path to the DIR_BASE or an absolute path to file containing the class 
	 * @param array $params that will be appended to the arguments of the method
	 * $param int $priority
	 * @return void
	 */
	public static function extend($event, $class, $method, $filename, $params = array()) {
		self::enableEvents();
		self::$registeredEvents[$event][] = array(
			self::EVENT_TYPE_GLOBAL,
			$class,
			$method,
			$filename,
			$params,
			$priority
		);
		self::sortByPriority();
	}
	
	/** 
	 * Fires an system-wide event calling the earlier set callbacks using the extend() method
	 * Additional arguments will be passed to the callbacks
	 * 
	 * @param string $event
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @param mixed $argN and so on
	 * @return void
	 */
	public static function fire($event) {
		if ((!defined('ENABLE_APPLICATION_EVENTS')) || (ENABLE_APPLICATION_EVENTS == false)) {
			return;
		}
		
		// any additional arguments passed to the fire function will get passed FIRST to the method, with the method's own registered
		// params coming at the end. e.g. if I fire Events::fire('on_login', $userObject) it will come in with user object first
		$args = func_get_args();
		// shift the event arg off so we only have the additional args
		array_shift($args);

		$events = self::$registeredEvents[$event];
		$eventReturn = false;
		
		if (is_array($events)) {
			foreach($events as $ev) {
				$type = $ev[0];
				$proceed = true;
				if ($type == self::EVENT_TYPE_PAGETYPE) {
					// then the first argument in the event fire() method will be the page
					// that this applies to. We check to see if the page type is the right type
					$proceed = false;
					if (is_object($args[0]) && $args[0] instanceof Page && $args[0]->getCollectionTypeID() > 0) {
						if ($ev[3] == Loader::pageTypeControllerPath($args[0]->getCollectionTypeHandle())) {
							$proceed = true;
						}
					}
				}
				
				if ($proceed) {
					if ($ev[3] != false) {
						// HACK - second part is for windows and its paths
					
						if (substr($ev[3], 0, 1) == '/' || substr($ev[3], 1, 1) == ':') {
							// then this means that our path is a full one
							require_once($ev[3]);
						} else {
							require_once(DIR_BASE . '/' . $ev[3]);
						}
					}
					$params = (is_array($ev[4])) ? $ev[4] : array();
					
					// now if args has any values we put them FIRST
					$params = array_merge($args, $params);
	
					if (method_exists($ev[1], $ev[2])) {
						// Note: DO NOT DO RETURN HERE BECAUSE THEN MULTIPLE EVENTS WON'T WORK
						$eventReturn = call_user_func_array(array($ev[1], $ev[2]), $params);
					}
				}
			}
		}		
		
		// TODO only the return value returned by the last callback gets forwarded
		return $eventReturn;
	}

	/**
	 * Sorts registered events by priority
	 * @return void
	 */
	protected static function sortByPriority() {
		foreach(array_keys(self::$registeredEvents) as $event) {
			usort(self::$registeredEvents[$event], 'Events::comparePriority');
		}
	}

	/**
	 * compare function to be used with usort
	 * for sorting the events by priority
	 * @param array $a
	 * @param array $b
	 * @return number|number|number
	 */
	public static function comparePriority($a, $b) {
		if ($a['priority'] == $b['priority']) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

}



//	$controller = Loader::controller($this);
//	$ret = $controller->runTask('on_page_delete', array($this));
