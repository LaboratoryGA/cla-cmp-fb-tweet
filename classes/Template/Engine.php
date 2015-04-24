<?php
namespace Claromentis\Social\Template;

/**
 * This interface defines the single method which all templater engines must
 * expose.
 *
 * @author Nathan Crause
 */
interface Engine {
	
	public function render($template, array $args);
	
}
