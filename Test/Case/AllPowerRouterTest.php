<?php
/**
 * All PowerRouter plugin tests
 */
class AllPowerRouterTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All PowerRouter test');

		$path = CakePlugin::path('PowerRouter') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}