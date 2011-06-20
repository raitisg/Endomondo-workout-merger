<?php

	/**
	 * Convenience function for debugging
	 */
	function pr($data) {
		echo '<pre>'.print_r($data, true).'</pre>';
	}

	/**
	 * Get file extension
	 *
	 * @param string $filename Filename
	 * @return string
	 */
	function getExtension($filename) {
		if (strpos($filename, '.') === false) {
			return '';
		}
		return end(explode('.', strtolower($filename)));
	}


	/**
	 * Merge 2 or more workouts
	 *
	 * Because this is just a quick and dirty prototype, I won't work with XML as, well... XML, but
	 * instead just as with regular strings.
	 *
	 * @param array $files List of files to merge
	 * @return mixed string XML content or boolean false
	 */
	function mergeWorkouts($files) {
		if (count($files) < 2) {
			return false;
		}

		$xml = file_get_contents(array_shift($files));

		foreach ($files as $path) {
			$file = file_get_contents($path);

			if (preg_match('/<trkseg>(.*)<\/trkseg>/s', $file, $matches, PREG_OFFSET_CAPTURE)) {
				// last occurance of </trkseg>
				$pos = strrpos($xml, '</trkseg>');
				if ($pos !== false) {
					// insert just before </trkseg>
					$xml = substr_replace($xml, $matches[1][0], $pos, 0);
				}
			} else {
				return false;
			}
		}

		return $xml;
	}


?>