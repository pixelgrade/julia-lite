<?php
/**
 * Class CP_Tests_Class_Value
 *
 * @package Components
 */

/**
 * Test base component class Value.
 *
 * @group base
 */
class CP_Tests_Class_Value extends WP_UnitTestCase {

	/**
	 * @covers Pixelgrade_Value::toBool
	 */
	function test_toBool() {
		$this->assertEquals( false, Pixelgrade_Value::toBool( false ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( 0 ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( null ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( [] ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( '0' ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( 'off' ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( 'n' ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( 'no' ) );
		$this->assertEquals( false, Pixelgrade_Value::toBool( 'asdasd' ) );

		$this->assertEquals( true, Pixelgrade_Value::toBool( true ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 1 ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( -1 ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 100 ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( '1' ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 'true' ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 'yes' ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 'y' ) );
		$this->assertEquals( true, Pixelgrade_Value::toBool( 'on' ) );
	}

	/**
	 * @covers Pixelgrade_Value::maybeSplitByWhitespace
	 */
	function test_maybeSplitByWhitespace() {
		$this->assertEquals( false, Pixelgrade_Value::maybeSplitByWhitespace( false ) );
		$this->assertEquals( 100, Pixelgrade_Value::maybeSplitByWhitespace( 100 ) );

		$this->assertEquals( [ 1, 'a', 'b' => 'c' ], Pixelgrade_Value::maybeSplitByWhitespace( [ 1, 'a', 'b' => 'c' ] ) );

		$this->assertEquals( [], Pixelgrade_Value::maybeSplitByWhitespace( '' ) );
		$this->assertEquals( ['abc'], Pixelgrade_Value::maybeSplitByWhitespace( 'abc' ) );
		$this->assertEquals( ['ab-c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab-c' ) );
		$this->assertEquals( ['ab/c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab/c' ) );
		$this->assertEquals( ['ab_c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab_c' ) );

		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab c' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab    c' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab    c  ' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( 'ab    c ' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( ' ab    c ' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( '   ab    c ' ) );
		$this->assertEquals( ['ab', 'c'], Pixelgrade_Value::maybeSplitByWhitespace( "ab\n\r\tc" ) );
		$this->assertEquals( ['ab', 'c', 'd', 1], Pixelgrade_Value::maybeSplitByWhitespace( "\n\r\tab\n\r\tc\n\r\td\n\r\t1\n\r\t" ) );
	}

	/**
	 * @covers Pixelgrade_Value::maybeExplodeList
	 */
	function test_maybeExplodeList() {
		$this->assertEquals( [], Pixelgrade_Value::maybeExplodeList( false ) );
		$this->assertEquals( [1], Pixelgrade_Value::maybeExplodeList( 1 ) );
		$this->assertEquals( [], Pixelgrade_Value::maybeExplodeList( 0 ) );

		$this->assertEquals( ['a', 'b', 'c'], Pixelgrade_Value::maybeExplodeList( 'a,b,c' ) );
		$this->assertEquals( ['a', 'b', 'c'], Pixelgrade_Value::maybeExplodeList( ' a , b , c ' ) );
		$this->assertEquals( ['a', 'b|c'], Pixelgrade_Value::maybeExplodeList( 'a,b|c' ) );
		$this->assertEquals( ['a,b', 'c'], Pixelgrade_Value::maybeExplodeList( 'a,b|c', '|' ) );
		$this->assertEquals( ['a', 'b', 'c'], Pixelgrade_Value::maybeExplodeList( 'a|b|c', '|' ) );
		$this->assertEquals( ['a', '', 'b', 'c', ''], Pixelgrade_Value::maybeExplodeList( 'a||b|c|', '|' ) );
	}

	/**
	 * @covers Pixelgrade_Value::maybePrefix
	 */
	function test_maybePrefix() {
		$this->assertEquals( 0, Pixelgrade_Value::maybePrefix( 0 ) );
		$this->assertEquals( 1, Pixelgrade_Value::maybePrefix( 1 ) );
		$this->assertEquals( '', Pixelgrade_Value::maybePrefix( '' ) );

		$prefix = 'abc_';
		$this->assertEquals( 'abc_1', Pixelgrade_Value::maybePrefix( 1, $prefix ) );
		$this->assertEquals( 'abc_de', Pixelgrade_Value::maybePrefix( 'de', $prefix ) );
		$this->assertEquals( [0, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefix( [0, 1, 2, 3], $prefix ) );
		$this->assertEquals( (object)[0, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefix( (object)[0, 1, 2, 3], $prefix ) );
		$this->assertEquals( (object)[false, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefix( (object)[false, 1, 2, 3], $prefix ) );
		$this->assertEquals( [0, 'a' => 'abc_1', 'b' => 'abc_2','c' => 'abc_3'], Pixelgrade_Value::maybePrefix( [0, 'a' => 1, 'b' => 2, 'c' => 3], $prefix ) );

		$this->assertEquals( [0, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefix( [0, 'abc_1','abc_2','abc_3'], $prefix ) );
		$this->assertEquals( [0, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefix( [0, '1','abc_2','3'], $prefix ) );
		$this->assertEquals( [0, '_1','_2','_3'], Pixelgrade_Value::maybePrefix( [0, '1','_2','3'], '_' ) );
		$this->assertEquals( [0, '_____1','______2','_____3'], Pixelgrade_Value::maybePrefix( [0, '1','_2','3'], '_____' ) );
	}

	/**
	 * @covers Pixelgrade_Value::maybeSuffix
	 */
	function test_maybeSuffix() {
		$this->assertEquals( 0, Pixelgrade_Value::maybeSuffix( 0 ) );
		$this->assertEquals( 1, Pixelgrade_Value::maybeSuffix( 1 ) );
		$this->assertEquals( '', Pixelgrade_Value::maybeSuffix( '' ) );

		$suffix = '_abc';
		$this->assertEquals( '1_abc', Pixelgrade_Value::maybeSuffix( 1, $suffix ) );
		$this->assertEquals( 'de_abc', Pixelgrade_Value::maybeSuffix( 'de', $suffix ) );
		$this->assertEquals( [0, '1_abc','2_abc','3_abc'], Pixelgrade_Value::maybeSuffix( [0, 1, 2, 3], $suffix ) );
		$this->assertEquals( (object)[0, '1_abc','2_abc','3_abc'], Pixelgrade_Value::maybeSuffix( (object)[0, 1, 2, 3], $suffix ) );
		$this->assertEquals( (object)[false, '1_abc','2_abc','3_abc'], Pixelgrade_Value::maybeSuffix( (object)[false, 1, 2, 3], $suffix ) );
		$this->assertEquals( [0, 'a' => '1_abc', 'b' => '2_abc','c' => '3_abc'], Pixelgrade_Value::maybeSuffix( [0, 'a' => 1, 'b' => 2, 'c' => 3], $suffix ) );

		$this->assertEquals( [0, '1_abc','2_abc','3_abc'], Pixelgrade_Value::maybeSuffix( [0, '1_abc','2_abc','3_abc'], $suffix ) );
		$this->assertEquals( [0, '1_abc','2_abc','3_abc'], Pixelgrade_Value::maybeSuffix( [0, '1','2_abc','3'], $suffix ) );
		$this->assertEquals( [0, '1_','2_','3_'], Pixelgrade_Value::maybeSuffix( [0, '1','2_','3'], '_' ) );
		$this->assertEquals( [0, '1______','2_______','3______'], Pixelgrade_Value::maybeSuffix( [0, '1','2_','3'], '______' ) );
	}

	/**
	 * @covers Pixelgrade_Value::maybePrefixSuffix
	 */
	function test_maybePrefixSuffix() {
		$this->assertEquals( 0, Pixelgrade_Value::maybePrefixSuffix( 0 ) );
		$this->assertEquals( 1, Pixelgrade_Value::maybePrefixSuffix( 1 ) );
		$this->assertEquals( '', Pixelgrade_Value::maybePrefixSuffix( '' ) );
		$this->assertEquals( 'pre_sdfd', Pixelgrade_Value::maybePrefixSuffix( 'sdfd', 'pre_' ) );
		$this->assertEquals( 'sdfd_suf', Pixelgrade_Value::maybePrefixSuffix( 'sdfd', '', '_suf' ) );

		$prefix = 'abc_';
		$suffix = '_fgh';
		$this->assertEquals( 'abc_1_fgh', Pixelgrade_Value::maybePrefixSuffix( 1, $prefix, $suffix ) );
		$this->assertEquals( 'abc_de_fgh', Pixelgrade_Value::maybePrefixSuffix( 'de', $prefix, $suffix ) );
		$this->assertEquals( [0, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( [0, 1, 2, 3], $prefix, $suffix ) );
		$this->assertEquals( (object)[0, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( (object)[0, 1, 2, 3], $prefix, $suffix ) );
		$this->assertEquals( (object)[false, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( (object)[false, 1, 2, 3], $prefix, $suffix ) );
		$this->assertEquals( [0, 'a' => 'abc_1_fgh', 'b' => 'abc_2_fgh','c' => 'abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( [0, 'a' => 1, 'b' => 2, 'c' => 3], $prefix, $suffix ) );

		$this->assertEquals( [0, 'abc_1','abc_2','abc_3'], Pixelgrade_Value::maybePrefixSuffix( [0, 1, 2, 3], $prefix ) );
		$this->assertEquals( [0, '1_fgh','2_fgh','3_fgh'], Pixelgrade_Value::maybePrefixSuffix( [0, 1, 2, 3], '', $suffix ) );
		$this->assertEquals( [0, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( [0, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], $prefix, $suffix ) );
		$this->assertEquals( [0, 'abc_1_fgh','abc_2_fgh','abc_3_fgh'], Pixelgrade_Value::maybePrefixSuffix( [0, '1','2_fgh','abc_3'], $prefix, $suffix ) );
	}

	/**
	 * @covers Pixelgrade_Value::toLowerAscii
	 */
	function test_toLowerAscii() {
		$this->assertEquals( '', Pixelgrade_Value::toLowerAscii( '' ) );
		$this->assertEquals( 'aa', Pixelgrade_Value::toLowerAscii( 'aAÂ' ) );
		$this->assertEquals( 'abqwresff', Pixelgrade_Value::toLowerAscii( 'abqwrešđčžsff' ) );
		$this->assertEquals( '._-', Pixelgrade_Value::toLowerAscii( '._-' ) );
		$this->assertEquals( '._-', Pixelgrade_Value::toLowerAscii( '.!@#$%^&*()_+-=[]{};\':\",//<>?' ) );
		$this->assertEquals( '', Pixelgrade_Value::toLowerAscii( '∂άαáàâãªä' ) );
	}

	/**
	 * @covers Pixelgrade_Value::removeDoubles
	 */
	function test_removeDoubles() {
		$this->assertEquals( '.', Pixelgrade_Value::removeDoubles( '.....' ) );
		$this->assertEquals( '.+=-', Pixelgrade_Value::removeDoubles( '...+++===---' ) );
		$this->assertEquals( '. .', Pixelgrade_Value::removeDoubles( '... ....' ) );
	}

	/**
	 * @covers Pixelgrade_Value::translit
	 */
	function test_translit() {
		$this->assertEquals( 'Ae', Pixelgrade_Value::translit( 'Ä' ) );
		$this->assertEquals( 'Dollar', Pixelgrade_Value::translit( '$' ) );
		$this->assertEquals( 'Grad Celsius', Pixelgrade_Value::translit( '℃' ) );
		$this->assertEquals( "A-ae-Uebermensch-pa-hoyeste-niva!-I-ya-lyublyu-PHP!-ﬁ", Pixelgrade_Value::translit( "A æ Übérmensch på høyeste nivå! И я люблю PHP! ﬁ" ) );
	}
}
