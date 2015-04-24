<?php
namespace Claromentis\Social\Template\Engine;

use TemplaterComponentTmpl;

/**
 * This class provides a means of accessing the "CallTemplater" method of a
 * given component.
 *
 * @author Nathan Crause
 */
class ComponentProxy extends TemplaterComponentTmpl {
	
	private $component;
	
	public function __construct(TemplaterComponentTmpl $component) {
		$this->component = $component;
	}
	
	public function Show($attributes) {
		return 'This component is not intended to be invoked.';
	}
	
	public function invokeTemplater($template, array $args) {
		return $this->component->CallTemplater($template, $args);
	}

}
