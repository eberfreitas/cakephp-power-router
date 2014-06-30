<?php

App::uses('CakeRoute', 'Routing/Route');

/**
 * PowerRoute is a custom route class with some additions to the default Router.
 * With it you can perform some different things when defining your routes. The
 * main benefits from this class are:
 *
 * - Being able to define and call a route by a name.
 * - Being able to setup a custom function to determine if the route is
 *   applicable or not.
 * - Being able to define a custom function (callback) to modify params from a
 *   matched route.
 *
 * @link http://book.cakephp.org/2.0/en/development/routing.html#custom-route-classes
 * @author Éber Freitas Dias <eber@tanlup.com>
 * @package plugin.PowerRouter
 */
class PowerRoute extends CakeRoute {

/**
 * Checks to see if the given URL can be parsed by this route.
 * If the route can be parsed an array of parameters will be returned; if
 * not `false` will be returned.
 *
 * @param string $url The url to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 */
	public function parse($url) {
		$conditionResult = $callbackFunction = null;

		if (isset($this->options['condition'])) {
			$conditionFunctions = !is_array($this->options['condition'])
				? array($this->options['condition'])
				: $this->options['condition'];

			foreach ($conditionFunctions as $conditionFunction) {
				$tempResult = call_user_func($conditionFunction, $url, $this);

				if ($tempResult == false) {
					$conditionResult = false;
					break;
				}
			}
		}

		if (isset($this->options['callback']) && is_callable($this->options['callback'])) {
			$callbackFunction = $this->options['callback'];
			unset($this->options['callback']);
		}

		$params = parent::parse($url);

		if (!$params) {
			return false;
		}

		if ($conditionResult === false) {
			return false;
		}

		if (!is_null($callbackFunction)) {
			$params = call_user_func($callbackFunction, $params);
		}

		return $params;
	}

/**
 * Attempt to match a url array. If the url matches the route parameters and
 * settings, then return a generated string url. If the url doesn't match
 * the route parameters, false will be returned. This method handles the
 * reverse routing or conversion of url arrays into string urls.
 *
 * @param array $url An array of parameters to check matching with.
 * @return mixed Either a string url for the parameters if they match or false.
 */
	public function match($url) {
		if (!empty($url['routeName']) && !empty($this->options['routeName'])) {
			if ($url['routeName'] !== $this->options['routeName']) {
				return false;
			} else {
				foreach ($this->defaults as $key => $value) {
					$url[$key] = $value;
				}

				unset($url['routeName']);
			}
		}

		return parent::match($url);
	}
}