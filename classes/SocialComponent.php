<?php
require_once __DIR__ . '/../lib/php-emoji-master/emoji.php';

use Claromentis\Social\Cache;
use Claromentis\Social\Stream;
use Claromentis\Social\Template\Factory;

/**
 * This component renders contents from various social media sources
 *
 * @author Nathan Crause
 */
class SocialComponent extends TemplaterComponentTmpl {
	
	/**
	 * Used to define the overarching template (each individual provider will
	 * still have their own templater)
	 */
	const OPT_TEMPLATE = 'template';
	
	/**
	 * Used to define a list of all the feeds to pull in
	 */
	const OPT_SOURCE_FILTER = 'source_filter';
	
	/**
	 * Suffix to each individual provider's template
	 */
	const OPT_TEMPLATE_SUFFIX = '_template';
	
	/**
	 * Used to define the overall maximum number of posts
	 */
	const OPT_LIMIT = 'limit';
	
	/**
	 * Used to define the individual source's maximum number of posts
	 */
	const OPT_LIMIT_PER = 'limit_per';
	
	/**
	 * Used to define the maximum number of characters of a post.
	 */
	const OPT_POST_LENGTH = 'post_length';
	
	/**
	 * Used to define the "target" to all links in a post
	 */
	const OPT_LINK_TARGET = 'link_target';
	
	const ARG_POSTS_DATASRC = 'posts.datasrc';
	
	private static $DEFAULTS = [
		self::OPT_TEMPLATE		=> 'social/list.html',
		self::OPT_LIMIT			=> 10,
		self::OPT_LIMIT_PER		=> 99,
		self::OPT_LINK_TARGET	=> '_blank'
	];
	
	/**
	 * 
	 * @global Configuration $cfg_social
	 * @param array $attributes
	 * @return string
	 */
	public function Show($attributes) {
		ClaApplication::Enter('social');

		global $cfg_social;
			
		try {
			
			$options = array_merge(self::$DEFAULTS, $this->generateDefaults(), $attributes);
			$sources = $this->gatherSources($options);

	//		return '<pre>' . print_r($cfg_social->getProviderInstance('facebook', 'default'), true) . '</pre>';
//			$fb = $cfg_social->getProviderInstance('facebook', 'default')->getFeed();
//
//			return '<pre>' . print_r($fb->getStream(), true) . '</pre>';
			
			$id = 'social-' . uniqid();
			$args = [
				'js_element_id.body'	=> $id,
				'html_element_id.id'	=> $id,
				'js_content_height.body'	=> $options[self::OPT_POST_LENGTH],
				self::ARG_POSTS_DATASRC	=> []
			];
			$engine = Factory::fromComponent($this);
			
			foreach ($records = $this->getRecords($sources, $options[self::OPT_LIMIT], $options[self::OPT_LIMIT_PER]) as $record) {
				$templateOption = $record->source->provider . self::OPT_TEMPLATE_SUFFIX;

				$args[self::ARG_POSTS_DATASRC][] = [
					'post.+class'		=> "social-{$record->source->provider} {$record->post->getWrapperClassCSS()}",
					'body.body_html'	=> emoji_unified_to_html($record->post->getHTML($options[$templateOption], $engine, $options))
				];
			}
			
//			return '<pre>' . print_r($args, true) . ' </pre>';
			
			return $this->CallTemplater($options[self::OPT_TEMPLATE], $args);
		}
		catch (Exception $ex) {
			error_log('Critical error: ' . $ex->getMessage());
			error_log($ex->getTraceAsString());
			
			return lmsg('social.critical_error') . ': ' . $ex->getMessage() . '<pre>' . $ex->getTraceAsString() . '</pre>';
		}
	}
	
	/**
	 * Utility method to generate some defaults, based on dynamic data, such
	 * as a list of active providers.
	 * 
	 * @global \Claromentis\Social\Configuration $cfg_social
	 */
	private function generateDefaults() {
		global $cfg_social;
			
		$defaults = [];
		
		foreach ($cfg_social->getActiveProviders() as $provider) {
			$defaults[$provider . self::OPT_TEMPLATE_SUFFIX] = 
					"social/{$provider}.html";
		}
		
		return $defaults;
	}
	
	/**
	 * This method checks the parameters to determine the providers and 
	 * instances which should be retrieved.
	 * 
	 * @param array $options
	 * @global Configuration $cfg_social
	 */
	private function gatherSources(array $options) {
		global $cfg_social;
		
		$sources = [];
		
		if (key_exists(self::OPT_SOURCE_FILTER, $options)) {
			$keys = preg_split('/\s*,\s*/', $options[self::OPT_SOURCE_FILTER]);
			
			// step through all the keys, and try to find aliases
			foreach ($keys as $key) {
				// if this is a known alias, just add all the sources associated
				// with that alias
				if ($cfg_social->aliasExists($key)) {
					$sources = array_merge($sources, $cfg_social->getAlias($alias));
				}
				// it's not an alias, so it must be a direct provider(instance?)
				// reference
				else {
					// if the key as a separator "::" then it's a provider + instance name
					if (strpos('::', $key) !== false) {
						$parts = explode('::', $key);
						$sources[] = (object) [
							'provider'	=> $parts[0],
							'name'		=> $parts[1]
						];
					}
					// no separator, so must be ALL of them
					else {
						foreach ($cfg_social->getProviderInstances($key) as $name) {
							$sources[] = (object) [
								'provider'	=> $key,
								'name'		=> $name
							];
						}
					}
				}
			}
		}
		// nothing specified, so load everything
		else {
			foreach ($cfg_social->getActiveProviders() as $provider) {
				foreach ($cfg_social->getProviderInstances($provider) as $name) {
					$sources[] = (object) compact('provider', 'name');
				}
			}
		}
		
		return $sources;
	}
	
	/**
	 * Retrieves a complete list of all source records, sorted between them
	 * by creation date.
	 * 
	 * @param array $sources[stdClass] list of sources
	 * @param integer $limit the maximum number of records to return
	 * @return array[Post]
	 */
	private function getRecords(array $sources, $limit, $limitPer) {
		$records = [];
		
		foreach ($sources as $source) {
			foreach ($this->getSourceRecords($source, $limitPer) as $post) {
				$records[] = (object) compact('source', 'post');
			}
		}
		
		usort($records, function($a, $b) {
			if ($a->post->getDate()->getTimestamp() > $b->post->getDate()->getTimestamp()) {
				return -1;
			}
			return 1;
		});
		
		return array_slice($records, 0, $limit);
	}
	
	private function getSourceRecords(\stdClass $source, $limitPer) {
		$cache = new Cache($source->provider, $source->name);
		
		// TODO: the following line should be removed to that the cache
		// will actually work
		$cache->clear();
		
		if (!$cache->exists()) {
			$stream = new Stream($source->provider, $source->name);
			
			$stream->run();
		}
		
//		die('Record: <pre>' . print_r($cache->get(), true) . '</pre>');
		
		return array_slice($cache->get(), 0, $limitPer);
	}
	
}
