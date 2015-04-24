<?php
namespace Claromentis\Social\Data;

use DateTime;
use Serializable;
use Claromentis\Social\Template\Engine;

/**
 * Abstract class for a singular stream post. Since each provider may have a
 * different way of formatting this content (i.e. hashtags, people, links, etc.)
 * there is no generic concrete way of making this class.
 *
 * @author Nathan Crause
 */
abstract class Post implements Serializable {
	
	public function __construct(DateTime $date, $src) {
		$this->date = $date;
		$this->raw = $this->extractRaw($src);
		
//		die('<pre>constructed: ' . print_r($this, true) . '</pre>');
	}
	
	/**
	 * since the various social media SDK's can use objects to pass around data,
	 * these cannot be persisted. This is because the Claromentis autoloader
	 * won't know from where to load them. As such, we are required to try
	 * to transpose this data into a serializable format, typically either in
	 * stdClass's or in associative arrays.
	 */
	abstract protected function extractRaw($src);

	/**
	 * Holds the timestamp for the post
	 *
	 * @var DateTime
	 */
	private $date;
	
	/**
	 * Retrieves the timestamp for the post
	 * 
	 * @return DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	private $raw;
	
	/**
	 * Retrieves the raw, unprocessed platform-specific content.
	 * 
	 * @return mixed
	 */
	public function getRaw() {
		return $this->raw;
	}
	
	/**
	 * Generate HTML content for this post.
	 * 
	 * @param string $template the template file to use when rendering
	 * @param Claromentis\Social\Template\Engine $engine the engine for
	 * actually rendering
	 * @param array $options associative array of possible options (for example,
	 * those passed in during invocation of a templater component).
	 */
	abstract public function getHTML($template = 'social/item.html', Engine $engine = NULL, array $options = []);
	
	/**
	 * Override this method if you with the post implementation to actively
	 * add some addition CSS classes to the result.
	 */
	public function getWrapperClassCSS() {
		return null;
	}
	
	public function serialize() {
		return serialize([
			'date'	=> $this->date,
			'raw'	=> $this->raw
		]);
	}

	public function unserialize($serialized) {
		$parts = unserialize($serialized);
		
		$this->date = $parts['date'];
		$this->raw = $parts['raw'];
//		echo "<pre>Unserializing from: $serialized</pre>";
//		list($this->date, $this->raw) = unserialize($serialized);
	}

	
}
