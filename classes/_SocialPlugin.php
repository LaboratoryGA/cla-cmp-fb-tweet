<?php

class SocialPlugin implements ClaPluginBackground {

	/*
	 * @param Background $bg
	 */
	public function Background(Background $bg)
	{
		if ($bg->IsTimePass("Update social stream", 20)) {
			$social = new SocialStream();
			$social->Go();
		}
	}
}
