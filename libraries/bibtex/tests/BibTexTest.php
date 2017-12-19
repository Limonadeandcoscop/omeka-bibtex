<?php
  // Call Structures_BibTexTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "BibTexTest::main");
 }

require_once "Structures/BibTex.php";

/**
 * Test class for Structures_BibTex.
 * Generated by PHPUnit_Util_Skeleton on 2006-06-06 at 22:13:11.
 */
class BibTexTest extends PHPUnit_Framework_TestCase
{
    var $obj;


    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $this->obj = new Structures_BibTex();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
        unset($this->obj);
    }

    public function testLoadFileFileExists() {
        $ret = $this->obj->loadFile(__FILE__);
        $this->content = ''; //Erasing the loaded content again because it is senseless
        $this->assertTrue($ret);
    }

    public function testLoadFileFileDoesNotExists() {
        try { 
            $this->obj->loadFile((string)time());

            $this->fail("Expected an exception with a faux file name");
        } catch (Structures_BibTex_Exception $sbe) {
            $this->assertContains("Could not find file", $sbe->getMessage());
        }

    }

    /**
     * @todo Implement test_parseEntry().
     */
    public function test_parseEntry() {
        //Remember here that there is no closing brace!
        $test                  = "@foo{bar,john=doe";
        $shouldbe              = array();
        $shouldbe['john']      = 'doe';
        $shouldbe['cite']      = 'bar';
        $shouldbe['entryType'] = 'foo';
        $this->assertEquals($shouldbe, $this->obj->_parseEntry($test));
    }

    public function test_checkEqualSignTrue() {
        $test = "={=}";
        $this->assertTrue($this->obj->_checkEqualSign($test,0));
    }
    public function test_checkEqualSignFalse() {
        $test = "={=}";
        $this->assertFalse($this->obj->_checkEqualSign($test,2));
    }

    public function testClearWarnings() {
        $this->obj->clearWarnings();
        $this->obj->_generateWarning('type', 'entry');
        $this->obj->clearWarnings();
        $this->assertFalse($this->obj->hasWarning());
    }

    /*
     * the next tests check for the generation of the following Warnings:
     * - WARNING_AT_IN_BRACES
     * - WARNING_ESCAPED_DOUBLE_QUOTE_INSIDE_DOUBLE_QUOTES
     * - WARNING_UNBALANCED_AMOUNT_OF_BRACES
     */
    public function test_validateValueWarningAtInBraces() {
        $this->obj->clearWarnings();
        $test = '{john@doe}';
        $this->obj->_validateValue($test, '');
        $this->assertEquals('WARNING_AT_IN_BRACES', $this->obj->warnings[0]['warning']);
    }
    public function test_validateValueWarningEscapedDoubleQuoteInsideDoubleQuotes() {
        $this->obj->clearWarnings();
        $test = '"john\"doe"';
        $this->obj->_validateValue($test, '');
        $this->assertEquals('WARNING_ESCAPED_DOUBLE_QUOTE_INSIDE_DOUBLE_QUOTES', $this->obj->warnings[0]['warning']);
    }
    public function test_validateValueWarningUnbalancedAmountOfBracesOpen() {
        $this->obj->clearWarnings();
        $test = '{john{doe}';
        $this->obj->_validateValue($test, '');
        $this->assertEquals('WARNING_UNBALANCED_AMOUNT_OF_BRACES', $this->obj->warnings[0]['warning']);
    }
    public function test_validateValueWarningUnbalancedAmountOfBracesClosed() {
        $this->obj->clearWarnings();
        $test = '{john}doe}';
        $this->obj->_validateValue($test, '');
        $this->assertEquals('WARNING_UNBALANCED_AMOUNT_OF_BRACES', $this->obj->warnings[0]['warning']);
    }

    public function test_generateWarning() {
        $this->obj->clearWarnings();
        $this->obj->_generateWarning('type', 'entry');
        $ret = $this->obj->hasWarning();
        $this->obj->clearWarnings();
        $this->assertTrue($ret);
    }

    public function testHasWarning()
    {
        $this->obj->clearWarnings();
        $this->assertFalse($this->obj->hasWarning());
    }

    public function testAmount()
    {
        $teststring = "@Article {art1,author = {John Doe and Jane Doe}}@Article { art2,author = {John Doe and Jane Doe}}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertEquals(2, $this->obj->amount());
    }

    public function testGetStatistic()
    {
        $teststring = "@Article {art1,author = {John Doe and Jane Doe}}@Article { art2,author = {John Doe and Jane Doe}}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $shouldbe            = array();
        $shouldbe['article'] = 2;
        $this->assertEquals($shouldbe, $this->obj->getStatistic());
    }

    function testSingleParse()
    {
        $teststring="@Article { ppm_jon:1991,
author = {John Doe and Jane Doe}
}";
        $this->obj->content=$teststring;
        $this->obj->parse();
        $this->assertEquals(1,$this->obj->amount());
    }

    function testMultiParse()
    {
        $teststring = "@Article { art1,
author = {John Doe and Jane Doe}
}
@Article { art2,
author = {John Doe and Jane Doe}
}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertEquals(2,$this->obj->amount());
    }

    function testParse()
    {
        $teststring = "@Article { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}";
        $shouldbe = array();
        $shouldbe[0]['entryType'] = 'article';
        $shouldbe[0]['cite']      = 'art1';
        $shouldbe[0]['title']     = 'Titel1';
        $shouldbe[0]['author'][0]['first'] = 'John';
        $shouldbe[0]['author'][0]['von']   = '';
        $shouldbe[0]['author'][0]['last']  = 'Doe';
        $shouldbe[0]['author'][0]['jr']    = '';
        $shouldbe[0]['author'][1]['first'] = 'Jane';
        $shouldbe[0]['author'][1]['von']   = '';
        $shouldbe[0]['author'][1]['last']  = 'Doe';
        $shouldbe[0]['author'][1]['jr']    = '';
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertEquals($shouldbe,$this->obj->data);
    }

    function testBibTex()
    {
        $testarray = array();
        $testarray[0]['entryType'] = 'Article';
        $testarray[0]['cite']      = 'art1';
        $testarray[0]['title']     = 'Titel1';
        $testarray[0]['author'][0]['first'] = 'John';
        $testarray[0]['author'][0]['von']   = '';
        $testarray[0]['author'][0]['last']  = 'Doe';
        $testarray[0]['author'][0]['jr']    = '';
        $testarray[0]['author'][1]['first'] = 'Jane';
        $testarray[0]['author'][1]['von']   = '';
        $testarray[0]['author'][1]['last']  = 'Doe';
        $testarray[0]['author'][1]['jr']    = '';
        $shouldbe = "@article { art1,\n\ttitle = {Titel1},\n\tauthor = {Doe, , John and Doe, , Jane}\n}";
        $this->obj->data = $testarray;
        $this->assertEquals(trim($shouldbe),trim($this->obj->bibTex()));
    }

    function testAddEntry()
    {
        $addarray = array();
        $addarray['type'] = 'Article';
        $addarray['cite'] = 'art2';
        $addarray['title'] = 'Titel2';
        $addarray['author'][0] = 'John Doe';
        $addarray['author'][1] = 'Jane Doe';
        $teststring = "@Article { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->obj->addEntry($addarray);
        $this->assertEquals(2,$this->obj->amount());
    }

    function testEntryOverMoreLines()
    {
        //Entry found at http://en.wikipedia.org/wiki/BibTeX
        $teststring = "@Book{abramowitz+stegun,
  author =       \"Milton Abramowitz and Irene A. Stegun\",
  title =        \"Handbook of Mathematical Functions with
                  Formulas, Graphs, and Mathematical Tables\",
  publisher =    \"Dover\",
  year =         1964,
  address =      \"New York\",
  edition =      \"ninth Dover printing, tenth GPO printing\",
  isbn =         \"0-486-61272-4\"
}";
        $shouldbe = array();
        $shouldbe[0]['entryType']  = 'book';
        $shouldbe[0]['cite']       = 'abramowitz+stegun';
        $shouldbe[0]['title']      = "Handbook of Mathematical Functions with
                  Formulas, Graphs, and Mathematical Tables";
        $shouldbe[0]['author'][0]['first'] = 'Milton';
        $shouldbe[0]['author'][0]['von']   = '';
        $shouldbe[0]['author'][0]['last']  = 'Abramowitz';
        $shouldbe[0]['author'][0]['jr']    = '';
        $shouldbe[0]['author'][1]['first'] = 'Irene A.';
        $shouldbe[0]['author'][1]['von']   = '';
        $shouldbe[0]['author'][1]['last']  = 'Stegun';
        $shouldbe[0]['author'][1]['jr']    = '';
        $shouldbe[0]['publisher'] = 'Dover';
        $shouldbe[0]['year']      = '1964';
        $shouldbe[0]['address']   = 'New York';
        $shouldbe[0]['edition']   = 'ninth Dover printing, tenth GPO printing';
        $shouldbe[0]['isbn']      = '0-486-61272-4';
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data);
    }

    function testParsingComment()
    {
        $teststring = "@Article { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}
Here is a comment
@Article { art2,
title = {Titel2},
author = {John Doe and Jane Doe}
}";
        $shouldbe = array();
        $shouldbe[0]['entryType'] = 'article';
        $shouldbe[0]['cite']      = 'art1';
        $shouldbe[0]['title']     = 'Titel1';
        $shouldbe[0]['author'][0]['first'] = 'John';
        $shouldbe[0]['author'][0]['von']   = '';
        $shouldbe[0]['author'][0]['last']  = 'Doe';
        $shouldbe[0]['author'][0]['jr']    = '';
        $shouldbe[0]['author'][1]['first'] = 'Jane';
        $shouldbe[0]['author'][1]['von']   = '';
        $shouldbe[0]['author'][1]['last']  = 'Doe';
        $shouldbe[0]['author'][1]['jr']    = '';
        $shouldbe[1]['entryType'] = 'article';
        $shouldbe[1]['cite']      = 'art2';
        $shouldbe[1]['title']     = 'Titel2';
        $shouldbe[1]['author'][0]['first'] = 'John';
        $shouldbe[1]['author'][0]['von']   = '';
        $shouldbe[1]['author'][0]['last']  = 'Doe';
        $shouldbe[1]['author'][0]['jr']    = '';
        $shouldbe[1]['author'][1]['first'] = 'Jane';
        $shouldbe[1]['author'][1]['von']   = '';
        $shouldbe[1]['author'][1]['last']  = 'Doe';
        $shouldbe[1]['author'][1]['jr']    = '';
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data);
    }
    /*
	 function testWrongBraces1() {
	 $teststring = "@Article { art1,
	 title = {Tit}el1},
	 author = {John Doe and Jane Doe}
	 }";
        $this->obj->content = $teststring;
	 $this->assertTrue(PEAR::isError($this->obj->parse()));
	 }

	 function testWrongBraces2() {
	 $teststring = "@Article { art1,
	 title = {Titel1},
	 author = {John Doe and }Jane }Doe}
	 }";
	 $this->obj->content = $teststring;
	 $this->assertTrue(PEAR::isError($this->obj->parse()));
	 }
    */
    function testWrongBraces3() {
        $teststring = "@Article { art1,
title = {Titel1},
author = {John {Doe and {Jane Doe}
}";
        $this->obj->content = $teststring;
        try {
            $this->obj->parse();

            $this->fail("Expected an exception");
        } catch (Structures_BibTex_Exception $sbe) {
            $this->assertContains("Unbalanced parenthesis", $sbe->getMessage());
        }
    }

    function testWarningAtInBraces() {
        $teststring = "@Article { art1,
title = {Titel1},
author = {John Doe and @Jane Doe}
}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertTrue('WARNING_AT_IN_BRACES'==$this->obj->warnings[0]['warning']);
    }
    function testWarningEscapedDoubleQuote() {
        $teststring = "@Article { art1,
title = {Titel1},
author = \"John Doe and \\\"Jane Doe\"
}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertTrue('WARNING_ESCAPED_DOUBLE_QUOTE_INSIDE_DOUBLE_QUOTES'==$this->obj->warnings[0]['warning']);
    }
    /*
	 function testWarningAmountBraces() {
	 $teststring = "@Article { art1,
	 title = {Tit{el1},
	 author = {John Doe and }Jane Doe}
	 }";
	 $this->obj->content = $teststring;
	 $this->obj->parse();
	 $this->assertTrue('WARNING_UNBALANCED_AMOUNT_OF_BRACES'==$this->obj->warnings[0]['warning']);
	 }
    */
    function testWarningMultipleEntries() {
        $teststring = "@Article { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}
@Article { art2,
title = {Titel1},
author = {John Doe and Jane Doe}
}
@Article { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}
@Article { art2,
title = {Titel1},
author = {John Doe and Jane Doe}
}
@Article { art3,
title = {Titel1},
author = {John Doe and Jane Doe}
}";
        $this->obj->content = $teststring;
        $this->obj->parse();
        $this->assertTrue('WARNING_MULTIPLE_ENTRIES'==$this->obj->warnings[0]['warning']);
    }

    /* This testing suite is needed to get the Authors correct.
     for more information: http://artis.imag.fr/%7EXavier.Decoret/resources/xdkbibtex/bibtex_summary.html
     The names of the functions are build as follows:
     "test": Of course it is a unit test
     "Author": Function testing the authors
     "First": There are three different ways writing an author this is the first one
     "Simple": Description of the tes
    */
    function testAuthorFirstSimple() {
        $test     = "AA BB";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'BB';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    
    function testAuthorFirstLastCannotBeEmpty() {
        $test     = "AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = '';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'AA';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    
    function testAuthorFirstSimpleLowerCase() {
        $test     = "AA bb";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'bb';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    
    function testAuthorFirstLastCannotBeEmptyLowerCase() {
        $test     = "aa";
        $shouldbe = array();
        $shouldbe[0]['first'] = '';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'aa';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    
    function testAuthorFirstSimpleVon() {
        $test     = "AA bb CC";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstSimpleVonInnerUppercase() {
        $test     = "AA bb CC dd EE";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb CC dd';
        $shouldbe[0]['last']  = 'EE';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstDigitsArecaselessUppercase() {
        $test     = "AA 1B cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA 1B';
        $shouldbe[0]['von']   = 'cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstDigitsArecaselessLowercase() {
        $test     = "AA 1b cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '1b cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstPseudoLettersAreCaselessLowerInsideUpperOutside() {
        $test     = "AA {b}B cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA {b}B';
        $shouldbe[0]['von']   = 'cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstPseudoLettersAreCaselessLowerInsideLowerOutside() {
        $test     = "AA {b}b cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '{b}b cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstPseudoLettersAreCaselessUpperInsideUpperOutside() {
        $test     = "AA {B}B cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA {B}B';
        $shouldbe[0]['von']   = 'cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstPseudoLettersAreCaselessUpperInsideLowerOutside() {
        $test     = "AA {B}b cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '{B}b cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstNonLettersAreCaselessUpperCase() {
        $test     = "AA \BB{b} cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA \BB{b}';
        $shouldbe[0]['von']   = 'cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstNonLettersAreCaselessLowerCase() {
        $test     = "AA \bb{b} cc dd";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '\bb{b} cc';
        $shouldbe[0]['last']  = 'dd';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstGroupingCaselessOne() {
        $test     = "AA {bb} cc DD";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA {bb}';
        $shouldbe[0]['von']   = 'cc';
        $shouldbe[0]['last']  = 'DD';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstGroupingCaselessTwo() {
        $test     = "AA bb {cc} DD";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = '{cc} DD';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorFirstGroupingCaselessThree() {
        $test     = "AA {bb} CC";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA {bb}';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdSimpleCaseFirstUppercase() {
        $test     = "bb CC, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdSimpleCaseFirstLowercase() {
        $test     = "bb CC, aa";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'aa';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdSimpleVon() {
        $test     = "bb CC dd EE, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb CC dd';
        $shouldbe[0]['last']  = 'EE';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdLastPartCoannotBeEmpty() {
        $test     = "bb, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'bb';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdFirstCanBeEmptyAfterComma() {
        $test     = "BB,";
        $shouldbe = array();
        $shouldbe[0]['first'] = '';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'BB';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdSimpleJrUppercase() {
        $test     = "bb CC,XX, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = 'XX';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdSimpleJrLowercase() {
        $test     = "bb CC,xx, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = 'bb';
        $shouldbe[0]['last']  = 'CC';
        $shouldbe[0]['jr']    = 'xx';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    function testAuthorSecondAndThirdJrCanBeEmptyBetweenCommas() {
        $test     = "BB,, AA";
        $shouldbe = array();
        $shouldbe[0]['first'] = 'AA';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'BB';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    /*Testing the case determination needed for the authors*/
    function testCaseUpperSimple() {
        $test = 'AA';
        $this->assertEquals(1, $this->obj->_determineCase($test));
    }
    function testCaseLowerSimple() {
        $test = 'aa';
        $this->assertEquals(0, $this->obj->_determineCase($test));
    }
    function testCaseCaselessSimple() {
        $test = '{a}';
        $this->assertEquals(-1, $this->obj->_determineCase($test));
    }
    function testCaseUpperComplexBrace() {
        $test = '{A}A';
        $this->assertEquals(1, $this->obj->_determineCase($test));
    }
    function testCaseLowerComplexBrace() {
        $test = '{a}a';
        $this->assertEquals(0, $this->obj->_determineCase($test));
    }
    function testCaseUpperComplexNumber() {
        $test = '1A';
        $this->assertEquals(1, $this->obj->_determineCase($test));
    }
    function testCaseLowerComplexNumber() {
        $test = '1a';
        $this->assertEquals(0, $this->obj->_determineCase($test));
    }
    function testCaseUpperComplexWhitespace() {
        $test = ' A';
        $this->assertEquals(1, $this->obj->_determineCase($test));
    }
    function testCaseLowerComplexWhitespace() {
        $test = ' a';
        $this->assertEquals(0, $this->obj->_determineCase($test));
    }
    function testCaseErrorEmptyString() {
        $test = '';
        try {
            $this->obj->_determineCase($test);

            $this->fail("Expected an exception");
        } catch (Structures_BibTex_Exception $sbe) {
            $this->assertContains("Could not determine case on word", $sbe->getMessage());
        }

    }
    function testCaseErrorNonString() {
        $test = 2;
        try {
            $this->obj->_determineCase($test);

            $this->fail("Expected an exception");
        } catch (Structures_BibTex_Exception $sbe) {
            $this->assertContains("Could not determine case on word", $sbe->getMessage());
        }

    }
    
    function testAllowedTypeTrue() {
        $test = 'article';
        $this->assertTrue($this->obj->_checkAllowedEntryType($test));
    }
    function testAllowedTypeFalse() {
        $test = 'foo';
        $this->assertFalse($this->obj->_checkAllowedEntryType($test));
    }
    
    public function testAllowedTypeWarning() {
        $this->obj->clearWarnings();
        $test = "@Foo { art1,
title = {Titel1},
author = {John Doe and Jane Doe}
}";
        $this->obj->content = $test;
        $this->obj->setOption('validate', true);
        $this->obj->parse();
        $this->assertEquals('WARNING_NOT_ALLOWED_ENTRY_TYPE', $this->obj->warnings[0]['warning']);
    }
    
    public function testMissingLastBraceParsing() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo1,
school = {school1},
title = {title1},
author = {author1},
year = {year1}
}
@phdthesis{foo2,
school = {school2},
title = {title2},
author = {author2},
year = {year2}

@phdthesis{foo3,
school = {school3},
title = {title3},
author = {author3},
year = {year3}
}
@phdthesis{foo4,
school = {school4},
title = {title4},
author = {author4},
year = {year4}
}
        ';
        $this->obj->content = $test;
        $this->obj->setOption('validate', true);
        $this->obj->parse();
        $this->assertEquals($this->obj->amount(), 4);
    }

    public function testMissingLastBraceParsing2() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo1,
school = {school1},
title = {title1},
author = {author1},
year = {year1}
        ';
        $this->obj->content = $test;
        $this->obj->setOption('validate', true);
        $this->obj->parse();
        $this->assertEquals($this->obj->amount(), 1);
    }
    
    public function testMissingLastBraceWarning() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo1,
school = {school1},
title = {title1},
author = {author1},
year = {year1}
}
@phdthesis{foo2,
school = {school2},
title = {title2},
author = {author2},
year = {year2}

@phdthesis{foo3,
school = {school3},
title = {title3},
author = {author3},
year = {year3}
}
@phdthesis{foo4,
school = {school4},
title = {title4},
author = {author4},
year = {year4}
}
        ';
        $this->obj->content = $test;
        $this->obj->setOption('validate', true);
        $this->obj->parse();
        $this->assertEquals($this->obj->warnings[0]['warning'], 'WARNING_MISSING_END_BRACE');
    }

    public function testNewlineInAuthorField() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo1,
school = {school1},
title = {title1},
author = {author1 and
author2},
year = {year1}
}
        ';
        $shouldbe = array(array('first'=>'','von'=>'','last'=>'author1','jr'=>''), array('first'=>'','von'=>'','last'=>'author2','jr'=>''));
        $this->obj->content = $test;
        $this->obj->setOption('unwrap', false);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data[0]['author']);
    }

    public function testNotRemoveCurlyBraces() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
school = {school4},
title = {Do {S}omething},
author = {author4},
year = {year4}
}
        ';
        $shouldbe = 'Do {S}omething';
        $this->obj->content = $test;
        $this->obj->setOption('removeCurlyBraces', false);
        $this->obj->parse();
        //print_r($this->obj->data[0]['author']);
        $this->assertEquals($shouldbe, $this->obj->data[0]['title']);
    }
    
    public function testRemoveCurlyBraces() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
school = {school4},
title = {Do {S}omething},
author = {author4},
year = {year4}
}
        ';
        $shouldbe = 'Do Something';
        $this->obj->content = $test;
        $this->obj->setOption('removeCurlyBraces', true);
        $this->obj->parse();
        //print_r($this->obj->data[0]['author']);
        $this->assertEquals($shouldbe, $this->obj->data[0]['title']);
    }

    public function testRemoveCurlyBraces2() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
school = {school4},
title = {Do {S}o{me\}th}ing},
author = {author4},
year = {year4}
}
        ';
        $shouldbe = 'Do Some\}thing';
        $this->obj->content = $test;
        $this->obj->setOption('removeCurlyBraces', true);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data[0]['title']);
    }

    public function testRemoveCurlyBracesWithoutBraces() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
school = {school4},
title = {Do Something},
author = {author4},
year = {year4}
}
        ';
        $shouldbe = 'Do Something';
        $this->obj->content = $test;
        $this->obj->setOption('removeCurlyBraces', true);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data[0]['title']);
    }
    
    public function testRtfExport() {
        $test = '
@phdthesis{foo4,
title = {title},
author = {author},
journal = {journal},
year = {year}
}
        ';
        $shouldbe = '{\rtf
author title journal year
\par
}';
        $this->obj->content      = $test;
        /*Setting formating option and saving old state*/
        $oldrtfstring            = $this->obj->rtfstring;
        $this->obj->rtfstring    = 'AUTHORS TITLE JOURNAL YEAR';
        $oldauthorstring         = $this->obj->authorstring;
        $this->obj->authorstring = 'LAST';
        $this->obj->parse();
        
        $rtf = $this->obj->rtf();
        /*Resetting old state*/
        $this->obj->rtfstring    = $oldrtfstring;
        $this->obj->authorstring = $oldauthorstring;
        
        $this->assertEquals($shouldbe, $rtf);
    }

    public function testHtmlExport() {
        $test = '
@phdthesis{foo4,
title = {title},
author = {author},
journal = {journal},
year = {year}
}
        ';
        $shouldbe = '<p>
author title journal year
</p>
';
        $this->obj->content      = $test;
        /*Setting formating option and saving old state*/
        $oldhtmlstring           = $this->obj->htmlstring;
        $this->obj->htmlstring   = 'AUTHORS TITLE JOURNAL YEAR';
        $oldauthorstring         = $this->obj->authorstring;
        $this->obj->authorstring = 'LAST';
        $this->obj->parse();
        
        $html = $this->obj->html();
        /*Resetting old state*/
        $this->obj->htmlstring   = $oldhtmlstring;
        $this->obj->authorstring = $oldauthorstring;
        
        $this->assertEquals($shouldbe, $html);
    }

    public function testFormatAuthor() {
        $test = '
@phdthesis{foo4,
author = {von Last, Jr ,First}
}
        ';
        $shouldbe = 'von Last Jr First';
        $this->obj->content      = $test;
        /*Setting formating option and saving old state*/
        $oldhtmlstring           = $this->obj->htmlstring;
        $this->obj->htmlstring   = 'AUTHORS';
        $oldauthorstring         = $this->obj->authorstring;
        $this->obj->authorstring = 'VON LAST JR FIRST';
        $this->obj->parse();
        
        $html = $this->obj->html();
        /*Dropping tags and trimming*/
        $html = trim(strip_tags($html));
        /*Resetting old state*/
        $this->obj->htmlstring   = $oldhtmlstring;
        $this->obj->authorstring = $oldauthorstring;
        
        $this->assertEquals($shouldbe, $html);
    }
    
    public function testExtractAuthorWhitespace() {
        $test = 'John Doe';
        $shouldbe = array();
        $shouldbe[0]['first'] = 'John';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'Doe';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }

    public function testExtractAuthorTilde() {
        $test = 'John~Doe';
        $shouldbe = array();
        $shouldbe[0]['first'] = 'John';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'Doe';
        $shouldbe[0]['jr']    = '';
        $this->assertEquals($shouldbe, $this->obj->_extractAuthors($test));
    }
    
    public function testNotExtractAuthors() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
author = {John Doe},
}
        ';
        $shouldbe = 'John Doe';
        $this->obj->content = $test;
        $this->obj->setOption('extractAuthors', false);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data[0]['author']);
    }

    public function testExtractAuthors() {
        $this->obj->clearWarnings();
        $test = '
@phdthesis{foo4,
author = {John Doe},
}
        ';
        $shouldbe = array();
        $shouldbe[0]['first'] = 'John';
        $shouldbe[0]['von']   = '';
        $shouldbe[0]['last']  = 'Doe';
        $shouldbe[0]['jr']    = '';
        $this->obj->content = $test;
        $this->obj->setOption('extractAuthors', true);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->data[0]['author']);
    }
    
    public function testNotExtractAuthorsBibtexExport() {
        $this->obj->clearWarnings();
        $test = "@phdthesis { foo4,\n\tauthor = {John Doe}\n}\n\n";
        $this->obj->content = $test;
        $this->obj->setOption('extractAuthors', false);
        $this->obj->parse();
        $this->assertEquals($test, $this->obj->bibTex());
    }

    public function testNotExtractAuthorsRtfExport() {
        $this->obj->clearWarnings();
        $test = "@phdthesis { foo4,\n\tauthor = {John Doe}\n}\n\n";
        $shouldbe = '{\rtf
John Doe, "{\b }", {\i }, 
\par
}';
        $this->obj->content = $test;
        $this->obj->setOption('extractAuthors', false);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->rtf());
    }

    public function testNotExtractAuthorsHtmlExport() {
        $this->obj->clearWarnings();
        $test = "@phdthesis { foo4,\n\tauthor = {John Doe}\n}\n\n";
        $shouldbe = '<p>
John Doe, "<strong></strong>", <em></em>, <br />
</p>
';
        $this->obj->content = $test;
        $this->obj->setOption('extractAuthors', false);
        $this->obj->parse();
        $this->assertEquals($shouldbe, $this->obj->html());
    }

    function testRemoveCurlyBracesDespiteStrangeCapitalisation()
    {
        $this->obj->setOption('removeCurlyBraces', true);

        $teststring = "@ARTICLE{FOO,
  title = {{FoOBaR}: A system with {StrAnGe} capitalization},
}";
        $this->obj->content = $teststring;
        $this->obj->parse();

        $this->assertEquals('FoOBaR: A system with StrAnGe capitalization',$this->obj->data[0]['title']);
    }

}
