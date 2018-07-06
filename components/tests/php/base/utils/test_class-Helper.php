<?php
/**
 * Class CP_Tests_Class_Helper
 *
 * @package Components
 */

/**
 * Test base component class Helper.
 *
 * @group base
 */
class CP_Tests_Class_Helper extends WP_UnitTestCase {

	/**
	 * @covers Pixelgrade_Helper::ob_function
	 */
	function test_ob_function() {
		$text = 'This is something';
		$this->assertEquals( $text, Pixelgrade_Helper::ob_function( array( $this, 'output_received_input' ), [ $text ] ) );

	}

	public function output_received_input( $something ) {
		echo $something;
	}
}
