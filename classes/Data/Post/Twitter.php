<?php
namespace Claromentis\Social\Data\Post;

use SocialComponent;
use Claromentis\Social\Data\Post;
use Claromentis\Social\Template\Engine;
use Claromentis\Social\Template\Factory;

/**
 * Concrete implementation of a Twitter post
 *
 * @author Nathan Crause
 */
class Twitter extends Post {
	
	protected function extractRaw($src) {
		// no extraction required for twitter - the object we get from the
		// SDK is a pure associative array
		return $src;
	}
	
	public function getHTML($template = 'social/twitter.html', Engine $engine = NULL, array $options = []) {
		$engine = $engine ?: Factory::raw();
		$target = $options[SocialComponent::OPT_LINK_TARGET] ?: '_blank';
		$args = [
//			'body.body_html'	=> $this->generateBody($target),
			'media.visible'		=> false,
			'origin.href'		=> "https://twitter.com/{$this->getRaw()['user']['screen_name']}/status/{$this->getRaw()['id_str']}",
			'origin.target'		=> $target
		];
			
		$this->generateBody($args, $target);
//		die('<pre>' . print_r($args, true) . '</pre>----<pre>' . print_r($entity, true));
		
		return $engine->render($template, $args);
	}
	
	private function generateBody(&$args, $target) {
		$body = $this->getRaw()['text'];
		// as we start inserting anchor tags, we need to adjust the index 
		// positions of subsequent 'entities'
		$indexDrift = 0;
		// re-order all the entities into an ascending order
		$entities = [];
		
		foreach (['hashtag' => 'hashtags', 'mention' => 'user_mentions', 
				'url' => 'urls', 'media' => 'media'] as $entityType => $srcKey) {
			foreach ($this->getRaw()['entities'][$srcKey] as $entity) {
				$entity['entity_type'] = $entityType;
				$entities[] = $entity;
			}
		}
		
		// sort them based on starting positions
		usort($entities, function($a, $b) {
			if ($a['indices'][0] < $b['indices'][0]) {
				return -1;
			}
			
			return 1;
		});
		
		// now, process all the entities
		foreach ($entities as $entity) {
			$start = $entity['indices'][0] + $indexDrift;
			$end = $entity['indices'][1] + $indexDrift;
			$text = substr($body, $start, $end - $start);
			$anchor = call_user_func(function() use ($entity, $text, $target, &$args) {
				switch ($entity['entity_type']) {
					case 'hashtag':
						return "<a href=\"https://twitter.com/hashtag/{$entity['text']}?src=hash\" target=\"{$target}\">{$text}</a>";
					case 'mention':
						return "<a href=\"https://twitter.com/{$entity['screen_name']}\" target=\"{$target}\">{$text}</a>";
					case 'url':
						return "<a href=\"{$entity['url']}\" target=\"{$target}\">{$text}</a>";
					case 'media':
						$args['media.visible'] = true;
						$args['picture.src'] = preg_replace('/^http[s]?:/', '', $entity['media_url']);
//						die('<pre>' . print_r($args, true) . '</pre>----<pre>' . print_r($entity, true));
						// we actually want the marked section completely
						// removed from the body
						return '';
					default:
						// if we don't know what it is (for whatever reason),
						// simply return the text unaltered - this may actually
						// mess with indexDrift, but not too sure
						return $text;
				}
			});
			
			$body = substr($body, 0, $start) . $anchor . substr($body, $end);
			// set the drift to the length difference between the origin text
			// and the resulting anchor
			$indexDrift += strlen($anchor) - strlen($text);
		}
		
		// TODO: instead of 'media' in the entites section above, should we
		// be scanning for the "extended_entities" instead? This would allow
		// us to embed videos directly, but may be too verbose in this context
		// (is linking to it sufficient?)
		
		$args['body.body_html'] = $body;
	}
}
