<?php
namespace Claromentis\Social\Template\Engine;

use TemplaterComponentTmpl;
use Claromentis\Social\Template\Engine;

/**
 * This class provides a template engine based on a templater component object
 *
 * @author Nathan Crause
 */
class Component implements Engine {
	
	/**
	 *
	 * @var ComponentProxy 
	 */
	private $component;
	
	public function __construct(TemplaterComponentTmpl $component) {
		$this->component = new ComponentProxy($component);
	}

	public function render($template, array $args) {
		return $this->component->invokeTemplater($template, $args);
	}

}
