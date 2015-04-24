<?php
namespace Claromentis\Social\Data;

/**
 * This class represents an individual/account holder for a particular social
 * media stream. It contains the common basics between the various systems.
 *
 * @author Nathan Crause
 */
class Person {
	
	public function __construct($name, $link, $raw) {
		$this->name = $name;
		$this->link = $link;
		$this->raw = $raw;
	}
	
	private $name;
	
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	private $link;
	
	public function getLink() {
		return $this->link;
	}

	public function setLink($link) {
		$this->link = $link;
		return $this;
	}
	
	private $raw;
	
	public function getRaw() {
		return $this->raw;
	}

	public function setRaw($raw) {
		$this->raw = $raw;
		return $this;
	}
	
}
