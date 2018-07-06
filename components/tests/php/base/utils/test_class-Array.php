<?php
/**
 * Class CP_Tests_Class_Array
 *
 * @package Components
 */

/**
 * Test base component class Array.
 *
 * @group base
 */
class CP_Tests_Class_Array extends WP_UnitTestCase {

	/**
	 * @covers Pixelgrade_Array::insertBeforeKey
	 */
	function test_insertBeforeKey() {
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertBeforeKey( [ 2, 3, 4 ], 0, 1 )
		);
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertBeforeKey( [ 1, 3, 4 ], 1, 2 )
		);
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertBeforeKey( [ 2, 3, 4 ], 100, 1 )
		);

		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertBeforeKey( [ [ 'second' ], [ 'third' ], [ 'fourth' ] ], 0, [ [ 'first' ] ] )
		);
		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertBeforeKey( [ [ 'second' ], [ 'third' ], [ 'fourth' ] ], 100, [ [ 'first' ] ] )
		);
		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertBeforeKey( [ [ 'first' ], [ 'third' ], [ 'fourth' ] ], 1, [ [ 'second' ] ] )
		);

		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertBeforeKey( [ 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ], 'boguskey', [ 'first' => [ 'first' ] ] )
		);
		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertBeforeKey( [ 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ], 'second', [ 'first' => [ 'first' ] ] )
		);
		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertBeforeKey( [ 'first' => [ 'first'], 'third' => ['third'], 'fourth' => ['fourth'] ], 'third', [ 'second' => [ 'second' ] ] )
		);
	}

	/**
	 * @covers Pixelgrade_Array::insertAfterKey
	 */
	function test_insertAfterKey() {
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertAfterKey( [ 1, 2, 3 ], 2, 4 )
		);
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertAfterKey( [ 1, 3, 4 ], 0, 2 )
		);
		$this->assertEquals(
			[ 1, 2, 3, 4 ],
			Pixelgrade_Array::insertAfterKey( [ 1, 2, 3 ], 100, 4 )
		);

		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertAfterKey( [ [ 'first' ], [ 'second' ], [ 'third' ] ], 2, [ [ 'fourth' ] ] )
		);
		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertAfterKey( [ [ 'first' ], [ 'second' ], [ 'third' ] ], 100, [ [ 'fourth' ] ] )
		);
		$this->assertEquals(
			[ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ],
			Pixelgrade_Array::insertAfterKey( [ [ 'first' ], [ 'third' ], [ 'fourth' ] ], 0, [ [ 'second' ] ] )
		);

		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertAfterKey( [ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'] ], 'boguskey', [ 'fourth' => ['fourth'] ] )
		);
		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertAfterKey( [ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'] ], 'third', [ 'fourth' => ['fourth'] ] )
		);
		$this->assertEquals(
			[ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ],
			Pixelgrade_Array::insertAfterKey( [ 'first' => [ 'first'], 'third' => ['third'], 'fourth' => ['fourth'] ], 'first', [ 'second' => [ 'second' ] ] )
		);
	}

	/**
	 * @covers Pixelgrade_Array::findSubarrayByKeyValue
	 */
	function test_findSubarrayByKeyValue() {
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' , 'second', 'third', 'fourth' ], 0, 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' , 'second', 'third', 'fourth' ], 0, '' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' , 'second', 'third', 'fourth' ], 'bogus', 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => 'firstv' , 'second' => 'secondv', 'third' => 'thirdv', 'fourth' => 'fourthv' ], 0, 'firstv' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => 'firstv' , 'second' => 'secondv', 'third' => 'thirdv', 'fourth' => 'fourthv' ], 'bogus', 'firstv' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => 'firstv' , 'second' => 'secondv', 'third' => 'thirdv', 'fourth' => 'fourthv' ], 'bogus', '' ) );

		$this->assertEquals( 0, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ], 0, 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ], 0, 'fifth' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ], 1, 'first' ) );
		$this->assertEquals( 1, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' ], [ 'second' ], [ 'third' ], [ 'fourth' ] ], 0, 'second' ) );

		$this->assertEquals( 'first', Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ], 0, 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ], 1, 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ 'first' => [ 'first'], 'second' => ['second'], 'third' => ['third'], 'fourth' => ['fourth'] ], 0, 'fifth' ) );

		$this->assertEquals( 0, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => 'first'], ['second' => 'second'], ['third' =>'third'], ['fourth' =>'fourth'] ], 'first', 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => 'first'], ['second' => 'second'], ['third' =>'third'], ['fourth' =>'fourth'] ], 'first', 'second' ) );

		$this->assertEquals( 0, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => ['first'] ], ['second' => ['second'] ], ['third' =>['third'] ], ['fourth' =>['fourth'] ] ], 'first', ['first'] ) );
		$this->assertEquals( 1, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => ['first'] ], ['second' => ['second'] ], ['third' =>['third'] ], ['fourth' =>['fourth'] ] ], 'second', ['second'] ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => ['first'] ], ['second' => ['second'] ], ['third' =>['third'] ], ['fourth' =>['fourth'] ] ], 'first', 'first' ) );
		$this->assertEquals( false, Pixelgrade_Array::findSubarrayByKeyValue( [ [ 'first' => ['first'] ], ['second' => ['second'] ], ['third' =>['third'] ], ['fourth' =>['fourth'] ] ], 'second', ['first'] ) );
	}

	/**
	 * @covers Pixelgrade_Array::objArraySearch
	 */
	function test_objArraySearch() {
		$object1 = (object) [
			'propertyOne' => 'foo1',
			'propertyTwo' => 1,
		];
		$object2 = (object) [
			'propertyOne' => 'foo2',
			'propertyTwo' => 2,
		];
		$object3 = (object) [
			'propertyOne' => 'foo3',
			'propertyTwo' => 3,
		];
		$object_array = [ $object1, $object2, $object3 ];

		$this->assertEquals( 0, Pixelgrade_Array::objArraySearch( $object_array, 'propertyOne', 'foo1' ) );
		$this->assertEquals( 1, Pixelgrade_Array::objArraySearch( $object_array, 'propertyOne', 'foo2' ) );
		$this->assertEquals( 1, Pixelgrade_Array::objArraySearch( $object_array, 'propertyTwo', 2 ) );
		$this->assertEquals( 1, Pixelgrade_Array::objArraySearch( $object_array, 'propertyTwo', '2' ) );
		$this->assertEquals( false, Pixelgrade_Array::objArraySearch( $object_array, 'bogus', '2' ) );
		$this->assertEquals( false, Pixelgrade_Array::objArraySearch( $object_array, 'propertyOne', 'bogus' ) );
	}

	/**
	 * @covers Pixelgrade_Array::arrayDiffAssocRecursive
	 */
	function test_arrayDiffAssocRecursive() {
		// Test data integrity
		$this->assertEquals( false, Pixelgrade_Array::arrayDiffAssocRecursive( [], [] ) );
		$this->assertEquals( false, Pixelgrade_Array::arrayDiffAssocRecursive( 12, [] ) );
		$this->assertEquals( false, Pixelgrade_Array::arrayDiffAssocRecursive( '', [] ) );
		$this->assertEquals( false, Pixelgrade_Array::arrayDiffAssocRecursive( false, [] ) );
		// Test without needing recursion
		$array1 = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
		$array2 = array('a' => 'green', 'yellow', 'red');
		$this->assertEquals( [ 'b' => 'brown', 'c' => 'blue', 0 => 'red' ], Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );
		$this->assertEquals( ['123'], Pixelgrade_Array::arrayDiffAssocRecursive( ['123'], ['456'] ) );
		$this->assertEquals( false, Pixelgrade_Array::arrayDiffAssocRecursive( ['123'], ['123'] ) );

		$array1 = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => 'green',
			'yellow',
			'violet',
			'red',
		];
		$diff = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [ 'green' ],
			'yellow',
			'violet',
			'red',
		];
		$diff = [
			'a' => [
				1 => 'yellow',
				2 => 'violet',
			],
			'b' => 'brown',
			'c' => 'blue',
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [
				'r' => 'green'
			],
			'yellow',
			'violet',
			'red',
		];
		$diff = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'b' => 'brown',
			'c' => 'blue',
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [
				'y' => 'green'
			],
			'yellow',
			'violet',
			'red',
		];
		$diff = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'b' => 'brown',
			'c' => 'blue',
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'b' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [
				'y' => 'yellow'
			],
			'yellow',
			'violet',
			'red',
		];
		$diff = [
			'a' => [
				't' => 'green',
				'u' => 'violet',
			],
			'b' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'c' => 'blue',
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'c' => 'blue',
			'b' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'red',
		];
		$array2 = [
			'a' => [
				'y' => 'yellow'
			],
			'yellow',
			'b' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'c' => 'blue',
			'red',
		];
		$diff = [
			'a' => [
				't' => 'green',
				'u' => 'violet',
			],
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );

		$array1 = [
			'a' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'c' => 'blue',
			'b' => [
				't' => 'green',
				'y' => 'yellow',
				'u' => 'violet',
			],
			'red',
		];
		$array2 = [
			'a' => [
				'y' => 'yellow'
			],
			'yellow',
			'b' => [
				't' => 'green',
				'y' => 'yellow',
			],
			'c' => 'blue',
			'red',
		];
		$diff = [
			'a' => [
				't' => 'green',
				'u' => 'violet',
			],
			'b' => [
				'u' => 'violet',
			],
			0 => 'red',
		];
		$this->assertEquals( $diff, Pixelgrade_Array::arrayDiffAssocRecursive( $array1, $array2 ) );
	}

	/**
	 * @covers Pixelgrade_Array::strArraySearch
	 */
	function test_strArraySearch() {
		$needle = 'ree';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( $needle, [] ) );
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( $needle, 'red' ) );
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( '', $haystack ) );
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( false, $haystack ) );
		$this->assertEquals( 'a', Pixelgrade_Array::strArraySearch( $needle, $haystack ) );

		$needle = 'sadfasd';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( $needle, $haystack ) );

		$needle = 'red';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red', 'dred', 43 );
		$this->assertEquals( 0, Pixelgrade_Array::strArraySearch( $needle, $haystack ) );

		$needle = '4';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red', 'dred', 43 );
		$this->assertEquals( false, Pixelgrade_Array::strArraySearch( $needle, $haystack ) );
	}

	/**
	 * @covers Pixelgrade_Array::strrArraySearch
	 */
	function test_strrArraySearch() {
		$needle = 'ree';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( $needle, [] ) );
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( $needle, 'red' ) );
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( '', $haystack ) );
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( false, $haystack ) );
		$this->assertEquals( 'a', Pixelgrade_Array::strrArraySearch( $needle, $haystack ) );

		$needle = 'sadfasd';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red');
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( $needle, $haystack ) );

		$needle = 'red';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red', 'd' => 'green', 'dred', 43 );
		$this->assertEquals( 1, Pixelgrade_Array::strrArraySearch( $needle, $haystack ) );

		$needle = '4';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red', 'dred', 43 );
		$this->assertEquals( false, Pixelgrade_Array::strrArraySearch( $needle, $haystack ) );

		$needle = 'gree';
		$haystack = array('a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red', 'd' => 'green', 'dred', 43 );
		$this->assertEquals( 'd', Pixelgrade_Array::strrArraySearch( $needle, $haystack ) );
	}

	/**
	 * @covers Pixelgrade_Array::detach
	 */
	function test_detach() {
		$array = [];
		$this->assertEquals( false, Pixelgrade_Array::detach( $array, 'asd' ) );

		$array = [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red' ];
		$this->assertEquals( false, Pixelgrade_Array::detach( $array, 'asd' ) );
		$this->assertEquals( 'red', Pixelgrade_Array::detach( $array, 0 ) );
		$this->assertEquals( [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue' ], $array );

		$array = [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red' ];
		$this->assertEquals( 'brown', Pixelgrade_Array::detach( $array, 'b' ) );
		$this->assertEquals( [ 'a' => 'green', 'c' => 'blue', 'red' ], $array );
	}

	/**
	 * @covers Pixelgrade_Array::detachByValue
	 */
	function test_detachByValue() {
		$array = [];
		$this->assertEquals( false, Pixelgrade_Array::detachByValue( $array, 'asd' ) );

		$array = [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red' ];
		$this->assertEquals( false, Pixelgrade_Array::detachByValue( $array, 'asd' ) );
		$this->assertEquals( 'red', Pixelgrade_Array::detachByValue( $array, 'red' ) );
		$this->assertEquals( [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue' ], $array );

		$array = [ 'a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red' ];
		$this->assertEquals( 'brown', Pixelgrade_Array::detachByValue( $array, 'brown' ) );
		$this->assertEquals( [ 'a' => 'green', 'c' => 'blue', 'red' ], $array );
	}

	/**
	 * @covers Pixelgrade_Array::reorder
	 */
	function test_reorder() {
		$this->assertEquals( false, Pixelgrade_Array::reorder( false, 0, 1 ) );
		$this->assertEquals( false, Pixelgrade_Array::reorder( 'asdsa', 0, 1 ) );

		$this->assertEquals( [], Pixelgrade_Array::reorder( [], 0, 1 ) );

		$this->assertEquals( [ 0, 1, 2, 3 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 1, 1 ) );
		$this->assertEquals( [ 0, 1, 2, 3 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 10, 1 ) );
		$this->assertEquals( [ 0, 2, 3, 1 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 1, 10 ) );
		$this->assertEquals( [ 1, 0, 2, 3 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 0, 1 ) );
		$this->assertEquals( [ 0, 2, 1, 3 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 1, 2 ) );
		$this->assertEquals( [ 0, 1, 2, 3 ], Pixelgrade_Array::reorder( [ 0, 1, 2, 3 ], 3, 4 ) );

		$this->assertEquals( [ '0', '2', '1', '3' ], Pixelgrade_Array::reorder( [ '0', '1', '2', '3' ], 1, 2 ) );
		$this->assertEquals( [ '0', '1', '2', '3' ], Pixelgrade_Array::reorder( [ '0', '1', '2', '3' ], 3, 4 ) );
		$this->assertEquals( [ '0', '2', '3', '1' ], Pixelgrade_Array::reorder( [ '0', '1', '2', '3' ], 1, 10 ) );

		$this->assertEquals( [ 'a' => 'aa', 'c' => 'cc', 'bb', 'dd' ], Pixelgrade_Array::reorder( [ 'a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 'dd' ], 1, 2 ) );
		$this->assertEquals( false, Pixelgrade_Array::reorder( [ 'a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 'dd' ], 'b', 'c' ) );
		$this->assertEquals( [ 'a' => 'aa', 'c' => 'cc', 'd' => 'dd', 'bb' ], Pixelgrade_Array::reorder( [ 'a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 'dd' ], 1, 10 ) );
	}

	/**
	 * @covers Pixelgrade_Array::array_merge_recursive_distinct
	 */
	function test_array_merge_recursive_distinct() {
		$array1 = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => 'green',
			'yellow',
			'violet',
			'red',
		];
		$merged = [
			'a' => 'green',
			'b' => 'brown',
			'c' => 'blue',
			'yellow',
			'violet',
			'red',
		];
		$this->assertEquals( $merged, Pixelgrade_Array::array_merge_recursive_distinct( $array1, $array2 ) );

		$array1 = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => ['green'],
			'yellow',
			'violet',
			'red',
		];
		$merged = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'yellow',
			'violet',
			'red',
		];
		$this->assertEquals( $merged, Pixelgrade_Array::array_merge_recursive_distinct( $array1, $array2 ) );

		$array1 = [
			'a' => [ 'green', 'yellow', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => ['green', 'boom'],
			'yellow',
			'violet',
			'red',
		];
		$merged = [
			'a' => [ 'green', 'boom', 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'yellow',
			'violet',
			'red',
		];
		$this->assertEquals( $merged, Pixelgrade_Array::array_merge_recursive_distinct( $array1, $array2 ) );

		$array1 = [
			'a' => [ 'g' => 'green', 'y' => 'yellow', 'v' => 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [ 'g' => 'boom', 't' => 'tuuuu', 'v' => 'violet' ],
			'yellow',
			'violet',
			'red',
		];
		$merged = [
			'a' => [ 'g' => 'boom', 'y' => 'yellow', 't' => 'tuuuu', 'v' => 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'yellow',
			'violet',
			'red',
		];
		$this->assertEquals( $merged, Pixelgrade_Array::array_merge_recursive_distinct( $array1, $array2 ) );

		$array1 = [
			'a' => [ 'g' => 'green', 'y' => 'yellow', 'v' => 'violetttt' ],
			'b' => 'brsdfsdfown',
			'c' => 'blue',
			'red',
		];
		$array2 = [
			'a' => [ 'g' => 'boom', 't' => 'tuuuu', 'v' => 'violet' ],
			'b' => 'brown',
			'yellow',
			'violet',
			'red',
		];
		$merged = [
			'a' => [ 'g' => 'boom', 'y' => 'yellow', 't' => 'tuuuu', 'v' => 'violet' ],
			'b' => 'brown',
			'c' => 'blue',
			'yellow',
			'violet',
			'red',
		];
		$this->assertEquals( $merged, Pixelgrade_Array::array_merge_recursive_distinct( $array1, $array2 ) );
	}

	/**
	 * @covers Pixelgrade_Array::array_orderby
	 */
	function test_array_orderby() {
		$unordered = [];
		$ordered = [];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'priority', SORT_ASC ) );

		$unordered = [ 'b', 'd', 'a', 'c' ];
		$ordered = [ 'a', 'b', 'c', 'd' ];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered ) );

		$unordered = [ 10, 1, 100, 0 ];
		$ordered = [ 0, 1, 10, 100 ];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered ) );

		// Without a string key, the flag is ignored and the defaults are used
		$unordered = [ 10, 1, 100, 0 ];
		$ordered = [ 0, 1, 10, 100 ];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, '', SORT_DESC ) );

		$unordered = [
			[ 'first' => 3, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 4, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 2, 'second' => 'b', 'third' => 'bb' ],
		];
		$ordered = [
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 2, 'second' => 'b', 'third' => 'bb' ],
			[ 'first' => 3, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 4, 'second' => 'd', 'third' => 'dd' ],
		];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'first', SORT_ASC ) );
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'second', SORT_ASC ) );
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'third', SORT_ASC ) );

		$unordered = [
			[ 'first' => 3, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 4, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 2, 'second' => 'b', 'third' => 'bb' ],
		];
		$ordered = [
			[ 'first' => 4, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 3, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 2, 'second' => 'b', 'third' => 'bb' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
		];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'first', SORT_DESC ) );
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'second', SORT_DESC ) );
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'third', SORT_DESC ) );

		$unordered = [
			[ 'first' => 2, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 2, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 1, 'second' => 'b', 'third' => 'bb' ],
		];
		$ordered = [
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 1, 'second' => 'b', 'third' => 'bb' ],
			[ 'first' => 2, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 2, 'second' => 'd', 'third' => 'dd' ],
		];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'first', SORT_ASC, 'second', SORT_ASC ) );

		$unordered = [
			[ 'first' => 2, 'second' => 'c', 'third' => 'cc' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 2, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 1, 'second' => 'b', 'third' => 'bb' ],
		];
		$ordered = [
			[ 'first' => 1, 'second' => 'b', 'third' => 'bb' ],
			[ 'first' => 1, 'second' => 'a', 'third' => 'aa' ],
			[ 'first' => 2, 'second' => 'd', 'third' => 'dd' ],
			[ 'first' => 2, 'second' => 'c', 'third' => 'cc' ],
		];
		$this->assertEquals( $ordered, Pixelgrade_Array::array_orderby( $unordered, 'first', SORT_ASC, 'second', SORT_DESC ) );
	}
}
