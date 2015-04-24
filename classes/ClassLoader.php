<?php
namespace Claromentis\Social;

/**
 * This is a simple classloader used for loading libraries easier
 *
 * @author Nathan Crause
 */
class ClassLoader {
	
	public static function register($namespace, $path, $isFirstNamespaceImplicit = false) {
		spl_autoload_register(function($class) use ($namespace, $path, $isFirstNamespaceImplicit) {
			// if the class's FQN doesn't start with the namespace, abort
			if (strpos($class, $namespace . '\\') !== 0) {
				return false;
			}

			// if the first namespace is implicit, drop it from the directory
			if ($isFirstNamespaceImplicit) {
				$subnamespaced = substr($class, strlen($namespace) + 1);
				$path = $path . DIRECTORY_SEPARATOR
						. str_replace('\\', DIRECTORY_SEPARATOR, $subnamespaced) . '.php';
			}
			else {
				$path = $path . DIRECTORY_SEPARATOR
						. str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
			}

			// if the file doesn't exist, short-circuit
			if (!file_exists($path)) {
				return false;
			}

			require_once $path;

			return true;
		});
	}
	
}
