<?php
namespace Claromentis\Social\Template\Engine;

use Claromentis\Social\Template\Engine;

/**
 * More rudimentary renderer, invoking the templaring subsystem directly.
 *
 * @author Nathan Crause
 */
class Raw implements Engine {
	
	public function render($template, array $args) {
		return process_cla_template($template, $args);
	}

}
