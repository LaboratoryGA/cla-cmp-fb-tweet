<?php
namespace Claromentis\Social\Template;

use TemplaterComponentTmpl;
use Claromentis\Social\Template\Engine\Component;

/**
 * This factory class provides several utility  methods for getting a template
 * engine instance.
 *
 * @author Nathan Crause
 */
class Factory {
	
	public static function fromComponent(TemplaterComponentTmpl $component) {
		return new Component($component);
	}
	
	public static function raw() {
		
	}
	
}
