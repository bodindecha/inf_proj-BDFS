<?php
	function getClosestCDN(): string {
		global $APP_CONST, $USER_IP;
		$SAVE_KEY = "CDN_REGION";
		$REGIONS = array(
			"TH" => array( // Bangkok
				"domain" => "cdn.TianTcl.net",
				"coord" => [13.7563, 100.5018]
			),
			"US-West" => array( // Oregon
				"domain" => "us.cdn.TianTcl.net",
				"coord" => [44.0521, -123.0868]
			),
			"UK" => array( // Newcastle upon Tyne
				"domain" => "TianTcl.infy.uk",
				"coord" => [54.9733, -1.6140]
			)
		);

		// Get saved region
		if (TianTcl::sessVar($SAVE_KEY)) {
			$save = false;
			$closest = TianTcl::sessVar($SAVE_KEY);
			goto submit_closest_CDN;
		}

		// Get user geolocation from IP
		$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$USER_IP}"), true);
		if (!isset($geo["lat"], $geo["lon"], $geo["countryCode"])) goto submit_closest_CDN;

		// Determine from exact country code
		if (isset($geo["countryCode"]) && in_array($geo["countryCode"], array_keys($REGIONS))) {
			$closest = $geo["countryCode"];
			goto submit_closest_CDN;
		}

		// Determine from coordinates using Haversine formula
		if (isset($geo["lat"], $geo["lon"])) {
			$minDist = PHP_INT_MAX;
			$closest = $APP_CONST["defaultCDN"];
			function haversine($lat, $lon): float {
				global $geo;
				$earthRadius = 6371;
				$dLat = deg2rad($lat - $geo["lat"]);
				$dLon = deg2rad($lon - $geo["lon"]);
				$a = sin($dLat / 2) ** 2 + cos(deg2rad($geo["lat"])) * cos(deg2rad($lat)) * sin($dLon/2) ** 2;
				return 2 * $earthRadius * asin(sqrt($a));
			} foreach ($REGIONS as $region => $data) {
				$dist = haversine(...$data["coord"]);
				if ($dist < $minDist) {
					$minDist = $dist;
					$closest = $region;
				}
			}
		}

		submit_closest_CDN:
		if (!isset($closest)) $closest = $APP_CONST["defaultCDN"];
		if (!isset($save) || $save) TianTcl::sessVar($SAVE_KEY, $closest);
		return $REGIONS[$closest]["domain"];
	}
	$APP_CONST["cdnURL"] = "https://".getClosestCDN()."/";
?>