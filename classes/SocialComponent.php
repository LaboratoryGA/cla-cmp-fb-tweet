<?php

/*
 * <component class="SocialComponent" source_filter="Fb,Twitter" limit="2">
 * Attributes:
 *		source_filter: Comma seperated list of sources. If left blank or attribute is ommited then no filtering will occur. Values are case insensitive. 
 *		limit:	Limit number of posts to show. 
 */

class SocialComponent implements TemplaterComponent {

	public function Show($attributes)
	{
		global $APPDATA;
		$path = "{$APPDATA}/people/social_component.json";
		$limit = (isset($attributes) && (ctype_digit($attributes['limit']) || is_int($attributes['limit']))) ? $attributes['limit'] : SocialStream::$limit; 

		if(file_exists($path))
		{
			$file = file_get_contents($path);  
			$args = json_decode($file, true);

			if (isset($attributes['source_filter']) && !empty($attributes['source_filter']))
			{
				$source_filter = explode(",", $attributes['source_filter']);
				$source_filter = array_map('trim', $source_filter);
				$source_filter = array_map('strtolower', $source_filter);

				foreach ($args['posts.datasrc'] as $key => $post)
					if (!in_array(strtolower($post['post.+class']), $source_filter))
					{
						unset($args['posts.datasrc'][$key]);
					}
			}

			$args['posts.datasrc'] = array_slice($args['posts.datasrc'], 0, $limit);
			
			return process_cla_template('social/template.html', $args);
		}
		else
			return;
	}
}
