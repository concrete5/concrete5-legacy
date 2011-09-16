<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with text.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class TextHelper { 
	
	/** 
	 * Takes text and returns it in the "lowercase-and-dashed-with-no-punctuation" format
	 * @param string $handle
	 * @param bool $leaveSlashes
	 * @return string $handle
	 */
	function sanitizeFileSystem($handle, $leaveSlashes=false) {
		$handle = trim($handle);
		$handle = str_replace(PAGE_PATH_SEPARATOR, '-', $handle);
		$searchMulti = array(
			"ä",
			"ö",
			"ß",
			"ü",
			"æ",
			"ø",
			"å",
			"é",
			"è"	
		);

		$replaceMulti = array(
			'ae',
			'oe',
			'ss',
			'ue',
			'ae',
			'oe',
			'aa',
			'e',
			'e'
		);
		
		$handle = str_replace($searchMulti, $replaceMulti, $handle);

		$searchNormal = array("/[&]/", "/[\s]+/", "/[^0-9A-Za-z-_.]/", "/-+/");
		$searchSlashes = array("/[&]/", "/[\s]+/", "/[^0-9A-Za-z-_.\/]/", "/-+/");
		$replace = array("and", "-", "", "-");
		
		$search = $searchNormal;
		if ($leaveSlashes) {
			$search = $searchSlashes;
		}

		$handle = preg_replace($search, $replace, $handle);
		if (function_exists('mb_strtolower')) {
			$handle = mb_strtolower($handle, APP_CHARSET);
		} else {
			$handle = strtolower($handle);
		}
		$handle = trim($handle, '-');
		$handle = str_replace('-', PAGE_PATH_SEPARATOR, $handle);
		return $handle;
	}

	/** 
	 * Strips tags and optionally reduces string to specified length.
	 * @param string $string
	 * @param int $maxlength
	 * @param string $allowed
	 * @return string
	 */
	function sanitize($string, $maxlength = 0, $allowed = '') {	
            $text = trim(strip_tags($string, $allowed));
		if ($maxlength > 0) {
			if (function_exists('mb_substr')) {
				$text = mb_substr($text, 0, $maxlength, APP_CHARSET);
			} else {
				$text = substr($text, 0, $maxlength);
			}
		}
		if ($text == null) {
			return ""; // we need to explicitly return a string otherwise some DB functions might insert this as a ZERO.
		}
		return $text;
	}

	/**
	 * always use in place of htmlentites(), so it works with different langugages
	 * @param string $v
	 * @return string
	 */
	public function entities($v){
		return htmlentities( $v, ENT_COMPAT, APP_CHARSET); 
	}
	 
	 
	/**
	 * An alias for shorten()
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string
	 */
	public function shorten($textStr, $numChars = 255, $tail = '…') {
		return $this->shortText($textStr, $numChars, $tail);
	}
	
	/** 
	 * Like sanitize, but requiring a certain number characters, and assuming a tail
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string $textStr
	 */	
	function shortText($textStr, $numChars=255, $tail='…') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr') && function_exists('mb_strlen')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr = mb_substr($textStr, 0, $numChars, APP_CHARSET) . $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = substr($textStr, 0, $numChars) . $tail;
			}
		}
		return $textStr;			
	}
        
        /**
        * Shortens and sanitizes a string but only cuts at word boundaries
	* @param string $textStr
	* @param int $numChars
	* @param string $tail
        */
        function shortenTextWord($textStr, $numChars=255, $tail='…') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr=preg_replace('/\s+?(\S+)?$/', '', mb_substr($textStr, 0, $numChars + 1, APP_CHARSET)) . $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = preg_replace('/\s+?(\S+)?$/', '', substr($textStr, 0, $numChars + 1)) . $tail;
			}
		}
		return $textStr;		
	}

	
	
	/**
	 * Takes a string and turns it into the CamelCase or StudlyCaps version
	 * @param string $string
	 * @return string
	 */
	public function camelcase($string) {
		return Object::camelcase($string);
	}
	
	/** 
	 * Scans passed text and automatically hyperlinks any URL inside it
	 * @param string $input
	 * @param int $newWindow
	 * @return string $output
	 */
	public function autolink($input,$newWindow=0) {
		$target=($newWindow)?' target="_blank" ':'';
		$output = preg_replace("/(http:\/\/|https:\/\/|(www\.))(([^\s<]{4,80})[^\s<]*)/", '<a href="http://$2$3" '.$target.' rel="nofollow">http://$2$4</a>', $input);
		return ($output);
	}
	
	/** 
	 * automatically add hyperlinks to any twitter style @usernames in a string
	 * @param string $input
	 * @param int $newWindow
	 * @param int $withSearch
	 * @return string $output
	 */	
	public function twitterAutolink($input,$newWindow=0,$withSearch=0) {
		$target=($newWindow)?' target="_blank" ':'';
    	$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" ".$target." class=\"twitter-username\">@$2</a>$3 ", $input);
		if($withSearch) 
			$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://search.twitter.com/search?q=%23$2\" ".$target." class=\"twitter-search\">#$2</a>$3 ", $input);		
    	return $output;
	}  
	
	/**
	 * Runs a number of text functions, including autolink, nl2br, strip_tags. Assumes that you want simple
	 * text comments but witih a few niceties.
	 * @param string $input
	 * @return string $output
	 */
	public function makenice($input) {
		$output = strip_tags($input);
		$output = $this->autolink($output);
		$output = nl2br($output);
		return $output;
	}
	
	/** 
	 * A wrapper for PHP's fnmatch() function, which some installations don't have.
	 * @param string $pattern
	 * @param string $string
	 * @return bool
	 */
	public function fnmatch($pattern, $string) {
		if(!function_exists('fnmatch')) {
			return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
		} else {
			return fnmatch($pattern, $string);
		}
	}
	
	
	/** 
	 * Takes a CamelCase string and turns it into camel_case
	 * @param string $string
	 * @return string
	 */
	public function uncamelcase($string) {
		$v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
		$a = array();
		array_shift($v);
		for($i = 0; $i < count($v); $i++) {
			if ($i % 2) {
				if (function_exists('mb_strtolower')) {
					$a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
				} else {
					$a[] = strtolower($v[$i - 1] . $v[$i]);
				}
			}
		}
		return implode('_', $a);
	}
	
	/**
	 * Takes a handle-based string like "blah_blah" or "blah-blah" or "blah/blah" and turns it into "Blah Blah"
	 * @param string $string
	 * @return string $r1
	 */
	public function unhandle($string) {
		$r1 = ucwords(str_replace(array('_', '-', '/'), ' ', $string));
		return $r1;
	}

	/**
	 * An alias for sanitizeFileSystem()
	 * @param string $handle
	 * @param bool $leaveSlashes
	 * @return string $handle
	 */
	public function handle($handle, $leaveSlashes=false) {
		return $this->sanitizeFileSystem($handle, $leaveSlashes=false);
	}
	
	/**
	 * Strips out non-alpha-numeric characters
	 * @param string $val
	 * @return string
	 */
	public function filterNonAlphaNum($val){ 
		return preg_replace('/[^[:alnum:]]/', '', $val);
	}
	
	/** 
	 * Highlights a string within a string with the class ccm-hightlight-search
	 * @param string $value
	 * @param string $searchString
	 * @return string
	 */
	 
	public function highlightSearch($value, $searchString) {
		return str_ireplace($searchString, '<em class="ccm-highlight-search">' . $searchString . '</em>', $value);
	}
}

?>
