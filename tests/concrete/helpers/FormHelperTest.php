<?php

require_once dirname(__FILE__) . '/../../../web/concrete/helpers/form.php';

/**
 * Test class for FormHelper.
 * Generated by PHPUnit on 2011-07-04 at 17:52:53.
 */
class FormHelperTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var FormHelper
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new FormHelper;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {

	}

	public function testSubmit() {
		$html = $this->object->submit('element', 'value', array('class' => 'test-class-1', 'arbitrary' => 'arbitrary'), 'test-class-2');
		$this->assertAttributes($html, array('class' => array('test-class-1', 'test-class-2', 'ccm-input-submit'), 'value' => 'value'));
	}

	public function testButton() {
		$html = $this->object->button('element', 'value', array('class' => 'test-class-1', 'arbitrary' => 'arbitrary'), 'test-class-2');
		$this->assertAttributes($html, array('class' => array('test-class-1', 'test-class-2', 'ccm-input-button'), 'value' => 'value'));
	}

	public function testCheckbox() {
		$html = $this->object->checkbox('element', 'value', true, array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-checkbox'), 'arbitrary' => 'arbitrary'));
	}

	public function testTextarea() {
		$html = $this->object->textarea('element', 'inner text', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-textarea'), 'arbitrary' => 'arbitrary'));
	}

	public function testRadio() {
		//test where valueOrArray is array
		$html = $this->object->radio('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-radio'), 'arbitrary' => 'arbitrary'));

		//test where valueOrArray is value
		$html = $this->object->radio('element', 'value', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-radio'), 'arbitrary' => 'arbitrary', 'checked' => 'checked'));
	}

	public function testText() {
		//test where valueOrArray is array
		$html = $this->object->text('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-text'), 'arbitrary' => 'arbitrary', 'type' => 'text'));

		//test where valueOrArray is value
		$html = $this->object->text('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-text'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'text'));
	}

	public function testEmail() {
		//test where valueOrArray is array
		$html = $this->object->email('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-email'), 'arbitrary' => 'arbitrary', 'type' => 'email'));

		//test where valueOrArray is value
		$html = $this->object->email('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-email'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'email'));
	}

	public function testTelephone() {
		//test where valueOrArray is array
		$html = $this->object->telephone('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-tel'), 'arbitrary' => 'arbitrary', 'type' => 'tel'));

		//test where valueOrArray is value
		$html = $this->object->telephone('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-tel'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'tel'));
	}

	public function testUrl() {
		//test where valueOrArray is array
		$html = $this->object->url('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-url'), 'arbitrary' => 'arbitrary', 'type' => 'url'));

		//test where valueOrArray is value
		$html = $this->object->url('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-url'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'url'));
	}

	public function testSearch() {
		//test where valueOrArray is array
		$html = $this->object->search('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-search'), 'arbitrary' => 'arbitrary', 'type' => 'search'));

		//test where valueOrArray is value
		$html = $this->object->search('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-search'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'search'));
	}

	public function testSelect() {
		$options = array('1' => 'One', '2' => 'Two', '3' => 'Three');

		//test where valueOrArray is array
		$html = $this->object->select('element', $options, array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes(substr($html, 0, strpos($html, '<option')), array('class' => array('test-class', 'ccm-input-select'), 'arbitrary' => 'arbitrary'));

		//test where valueOrArray is value
		$html = $this->object->select('element', $options, '3', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes(substr($html, 0, strpos($html, '<option')), array('class' => array('test-class', 'ccm-input-select'), 'arbitrary' => 'arbitrary', 'ccm-passed-value' => '3'));

		$html = substr($html, strpos($html, 'option value="3"'));
		$html = substr($html, 0, strpos($html, '</option'));
		$this->assertContains('selected', $html);
	}

	public function testSelectMultiple() {
		$options = array('1' => 'One', '2' => 'Two', '3' => 'Three');

		$html = $this->object->selectMultiple('element', $options, array(1,3), array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes(substr($html, 0, strpos($html, '<option')), array('class' => array('test-class', 'ccm-input-select'), 'arbitrary' => 'arbitrary'));

		$test = substr($html, strpos($html, 'option value="1"'));
		$test = substr($html, 0, strpos($html, '</option'));
		$this->assertContains('selected', $test);

		$test = substr($html, strpos($html, 'option value="3"'));
		$test = substr($html, 0, strpos($html, '</option'));
		$this->assertContains('selected', $test);
	}

	public function testPassword() {
		//test where valueOrArray is array
		$html = $this->object->password('element', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-password'), 'arbitrary' => 'arbitrary', 'type' => 'password'));

		//test where valueOrArray is value
		$html = $this->object->password('element', 'value', array('arbitrary' => 'arbitrary', 'class' => 'test-class'));
		$this->assertAttributes($html, array('class' => array('test-class', 'ccm-input-password'), 'value' => 'value', 'arbitrary' => 'arbitrary', 'type' => 'password'));
	}


	private function assertAttributes($tag, $assertedAttributes) {
		preg_match_all("/\s+([\w\-]+)=\"([^\"]+)\"/", $tag, $matches);

		$this->assertEquals(count($matches[1]), count(array_unique($matches[1])), 'Element has duplicate attributes');
		
		$attributes = array_combine($matches[1], $matches[2]);

		$this->assertRegexp('/element[0-9]?/', $attributes['id']);
		$this->assertContains('element', $attributes['name']);

		foreach ($assertedAttributes as $k => $v) {
			if ($k == 'class') {
			    sort($v);
				$assClass = explode(' ', $attributes['class']);
				sort($assClass);
				$this->assertEquals($v, $assClass);
			} else {
				$this->assertEquals($v, $attributes[$k]);
			}
		}
	}
}

?>
