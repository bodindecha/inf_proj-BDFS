<?php
	class LINE {
		private $token;

		public function __construct(string $bearer="") {
			$this -> token = trim($bearer);
		}

		public function setToken(string $bearer): void {
			$this -> token = trim($bearer);
		}
		public function notify(string $message): string {
			$queryData = http_build_query(array("message" => $message), "", "&");
			$headerOptions = array("http" => array(
				"method" => "POST",
				"header" => "Content-Type: application/x-www-form-urlencoded\r\n"
					."Authorization: Bearer ".($this -> token)."\r\n"
					."Content-Length: ".strlen($queryData)."\r\n",
				"content" => $queryData
			) ); $sendNoti = file_get_contents("https://notify-api.line.me/api/notify", false, stream_context_create($headerOptions));
			return $sendNoti;
		}
	} $LINE = new LINE();
?>