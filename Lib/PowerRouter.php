<?php

App::uses('Router', 'Routing');
App::uses('PowerRoute', 'PowerRouter.Lib');


/**
 * The PowerRouter is the an interface that simplifies the use of the custom
 * route class `PowerRoute`, making it simple do define a route name and setting
 * up the custom route class for every route defined with it.
 *
 * @author Éber Freitas Dias <eber@tanlup.com>
 * @package plugin.PowerRouter
 */
class PowerRouter {

/**
 * This method tried to mimic the original `Router::connect` adding some
 * extra params and handling the insertion of necessary options to it.
 *
 * @param string $name A unique name for this route.
 * @param string $route A string describing the template of the route.
 * @param array $defaults An array describing the default route parameters.
 *                        These parameters will be used by default and can
 *                        supply routing parameters that are not dynamic.
 * @param array $options An array matching the named elements in the route
 *                       to regular expressions which that element should
 *                       match. Also contains additional parameters such as
 *                       which routed parameters should be shifted into the
 *                       passed arguments and supplying patterns for routing
 *                       parameters.
 * @return array Array of routes.
 */
	static public function connect($name, $route, $defaults = array(), $options = array()) {
		$extra = array('routeClass' => 'PowerRoute');

		if (!empty($name)) {
			$extra['routeName'] = $name;
		}

		return Router::connect($route, $defaults, array_merge($extra, $options));
	}
}