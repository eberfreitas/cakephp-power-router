<?php

App::uses('PowerRouter', 'PowerRouter.Lib');

class PowerRouteTest extends CakeTestCase {

	public function testMatchRouteName() {
		Router::reload();

		$routes = PowerRouter::connect('first-test', '/controller/view/123');
		$routes = PowerRouter::connect('second-test', '/controller/view/321');
		$this->assertCount(2, $routes);

		$matchRoute = Router::url(array('routeName' => 'first-test'));
		$this->assertEqual('/controller/view/123', $matchRoute);

		$matchRoute = Router::url(array('routeName' => 'second-test'));
		$this->assertEqual('/controller/view/321', $matchRoute);
	}

	public function testParseRouteCondition() {
		Router::reload();

		Configure::write('route-test', true);

		$condition = function () {
			return Configure::read('route-test') === true;
		};

		$routes = PowerRouter::connect(
			'test',
			'/findamatch',
			array(
				'controller' => 'main',
				'action' => 'view'
			), array(
				'condition' => $condition
			)
		);

		$parsedRoute = Router::parse('/findamatch');
		$this->assertNotEmpty($parsedRoute);
		$this->assertEqual('main', $parsedRoute['controller']);
		$this->assertEqual('view', $parsedRoute['action']);

		Configure::write('route-test', false);

		$parsedRoute = Router::parse('/findamatch');
		$this->assertEmpty($parsedRoute);
	}

	public function testParseRouteCallback() {
		Router::reload();

		$callback = function ($params) {
			if (!empty($params['controller'])) {
				$params['controller'] = 'yolo_' . $params['controller'];
			}

			return $params;
		};

		$routes = PowerRouter::connect(
			'test',
			'/findamatch',
			array(
				'controller' => 'main',
				'action' => 'view'
			), array(
				'callback' => $callback
			)
		);

		$parsedRoute = Router::parse('/findamatch');
		$this->assertEqual('yolo_main', $parsedRoute['controller']);
	}
}