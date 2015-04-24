<?php
/**
 * This background task runs every 15 minutes to update the feeds
 *
 * @author Nathan Crause
 */
class SocialPlugin implements ClaPluginBackground {
	
	public function Background(Background $bg) {
		if ($bg->IsTimePass('Update social stream', Stream::TTL)) {
			\Claromentis\Social\Stream::all();
		}
	}

}
