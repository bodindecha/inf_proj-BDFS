<?php
	session_start(); ob_start();
	$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));

	// Connect
	require_once($APP_RootDir."private/config/constant.php");
	require_once($APP_RootDir."private/script/function/utility.php");
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	require_once($APP_RootDir."private/script/function/database.php");
	require_once($APP_RootDir."private/script/function/checkPermission.php");
	require_once($APP_RootDir."private/script/function/dbConfig.php");

	if ($APP_CONST["environment"] == "DEV") {
		ini_set("display_errors", "1");
		ini_set("display_startup_errors", "1");
		error_reporting(E_ALL);
	}

	/**
	 * API class for handling API requests and responses. This class provides methods for initializing the API, retrieving data from the request, setting default response structures, and adding messages and JavaScript actions to the response. It also includes methods for indicating success or error states in the response, as well as a method for adding developer information to the response for debugging purposes.
	 * 
	 * @author TianTcl
	 */
	class API {
		private static $default = [], $is = array(
			"initialized" => false
		), $return;
		public static $action, $command, $attr, $file;
		function __construct() {
			# if (self::$is["initialized"]) self::initialize();
		}
		/**
		 * Initializes by retrieving data and setting default response.
		 * This method should be called at the beginning of the API handling process to ensure that all necessary data is retrieved and the response structure is set up before any processing occurs.
		 * 
		 * @param bool $useNormalParameters If true, the method will check for "act" and "cmd" in the request and send output if they are not set.
		 * @param bool $useResponseTemplate If true, the method will initialize the response with a default template containing "success", "messages", and "jsaction" keys. If false, it will initialize an empty array for the response.
		 */
		final public static function initialize(bool $useNormalParameters=true, bool $useResponseTemplate=true): void {
			if (self::$is["initialized"]) return;
			global $APP_RootDir, $_SERVER;
			$APP_RootDir ??= str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
			self::$default["APP_RootDir"] = $APP_RootDir;
			$buffer = new self();
			$buffer -> retrieveData();
			$buffer -> setDefault($useNormalParameters, $useResponseTemplate);
			self::$is["initialized"] = true;
		}
		// Initializers
		/**
		 * Retrieves data from the request and populates the static properties of the API class accordingly. This method is responsible for extracting the action, command, attributes, and files from the incoming request and storing them in the class properties for later use in processing the API request.
		 */
		private function retrieveData(): void {
			global $_REQUEST, $_FILES;
			// Recieve
			$_REQUEST ??= [];
			self::$action	= $_REQUEST["act"] ?? null;
			self::$command	= $_REQUEST["cmd"] ?? null;
			self::$attr		= $_REQUEST["param"] ?? (json_decode(file_get_contents('php://input'), true) ?? null);
			self::$file		= $_FILES ?? null;
		}
		/**
		 * Sets the default response structure for the API. This method initializes the response with a default template containing "success", "messages", and "jsaction" keys, or an empty array if the $useResponseTemplate parameter is set to false. It also checks if the normal parameters "act" and "cmd" are set in the request, and if not, it sends the output immediately. This method should be called after retrieving data from the request to ensure that the response structure is properly initialized before any processing occurs.
		 */
		private function setDefault(bool $useNormalParameters=true, bool $useResponseTemplate=true): void {
			// Review
			self::$return = $useResponseTemplate ? array(
				"success" => false,
				"messages" => [],
				"jsaction" => []
			) : [];
			if ($useNormalParameters && (!strlen(self::$action) || !strlen(self::$command)))
				self::sendOutput();
		}

		// Helpers
		/**
		 * Adds a message to the response. This method takes a message type, an optional text, and an optional display duration, and adds it to the "messages" array in the response. The message can be added as a simple type or as an array containing the type, text, and display duration. This method is used to accumulate messages that will be sent back to the client as part of the API response.
		 * WARNING: This method does not check for the existence of the "messages" key in the response array, so it should be ensured that the response structure is properly initialized before calling this method to avoid potential errors.
		 * 
		 * @param int|string $type The type of the message. This is used to categorize the message (e.g., error, success, info). Or if only one parameter is provided, it will be treated as the message key pair for front-end lookup (better for translation).
		 * @param ?string $text The text of the message. This is the actual content of the message that will be displayed to the user. If null, the $type parameter will be used as the message key pair for front-end lookup.
		 * @param ?int $display_dur The duration (in seconds) for which the message should be displayed on the front-end. If null, the front-end will use a default duration for displaying the message. If zero, the message will be displayed indefinitely until dismissed by the user.
		 */
		private static function addMessage(int|string $type, string|null $text=null, int|null $display_dur=null): void {
			array_push(
				self::$return["messages"],
				$text == null ? $type : ($display_dur ? [$type, $text, $display_dur] : [$type, $text])
			);
		}

		// Prototypes
		/**
		 * Sets the success state of the API response. This method updates the "success" key in the response to true, indicating that the API request was processed successfully. It also provides options to clear existing messages and JavaScript actions from the response, and to include additional output information if provided. This method should be called when the API processing is completed successfully to prepare the response for sending back to the client.
		 * 
		 * @param mixed $output Optional additional information to include in the response under the "info" key.
		 * @param bool $clearMsg If true, this will clear any existing messages from the response before sending.
		 * @param bool $clearJsaction If true, this will clear any existing JavaScript actions from the response before sending.
		 */
		final public static function successState(mixed $output=null, bool $clearMsg=false, bool $clearJsaction=true): void {
			if (!self::$is["initialized"]) self::initialize();
			self::$return["success"] = true;
			if ($clearMsg) unset(self::$return["messages"]);
			if ($clearJsaction) unset(self::$return["jsaction"]);
			if ($output <> null) self::$return["info"] = $output;
		}
		/**
		 * Adds an error message to the response and prepares it for sending. This method is used to indicate that an error occurred during the processing of the API request.
		 * 
		 * @param int|string $type The type of the message. This is used to categorize the message (e.g., error, success, info). Or if only one parameter is provided, it will be treated as the message key pair for front-end lookup (better for translation).
		 * @param ?string $text The text of the message. This is the actual content of the message that will be displayed to the user. If null, the $type parameter will be used as the message key pair for front-end lookup.
		 * @param ?int $display_dur The duration (in seconds) for which the message should be displayed on the front-end. If null, the front-end will use a default duration for displaying the message. If zero, the message will be displayed indefinitely until dismissed by the user. 
		 */
		final public static function errorMessage(int|string $type, string|null $text=null, int|null $display_dur=null): void {
			if (!self::$is["initialized"]) self::initialize();
			self::addMessage($type, $text, $display_dur);
		}
		/**
		 * Adds an informational message to the response.
		 * 
		 * @param int|string $type The type of the message. This is used to categorize the message (e.g., error, success, info). Or if only one parameter is provided, it will be treated as the message key pair for front-end lookup (better for translation).
		 * @param ?string $text The text of the message. This is the actual content of the message that will be displayed to the user. If null, the $type parameter will be used as the message key pair for front-end lookup.
		 * @param ?int $display_dur The duration (in seconds) for which the message should be displayed on the front-end. If null, the front-end will use a default duration for displaying the message. If zero, the message will be displayed indefinitely until dismissed by the user.
		 */
		final public static function infoMessage(int|string $type, string|null $text=null, int|null $display_dur=null): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["messages"])) self::$return["messages"] = [];
			self::addMessage($type, $text, $display_dur);
		}
		/**
		 * Adds JavaScript actions to the response. This method allows you to specify JavaScript code that should be executed on the client side when the response is received. The JavaScript code can be added as a single string or as an array of strings, and it will be stored in the "jsaction" key of the response. This is useful for triggering specific behaviors on the front-end based on the outcome of the API request.
		 * 
		 * @param string[]|string $commands A single string of JavaScript code or an array of strings, where each string is a piece of JavaScript code to be executed on the client side.
		 */
		final public static function addJSAction(array|string $commands): void {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["jsaction"])) self::$return["jsaction"] = [];
			gettype($commands) == "string" ?
				array_push(self::$return["jsaction"], $commands) : array_push(self::$return["jsaction"], ...$commands);
		}
		/**
		 * Adds developer information to the response. The information can be added under a specific key, and the method provides options for how the data should be added (replacing existing data, appending to existing data if it's an array, or concatenating if it's a string). This allows developers to include relevant information in the API response without affecting the production environment.
		 * 
		 * @param string|int|float $key The key under which the developer information should be stored in the response. This can be a string, integer, or float.
		 * @param mixed $data The developer information to be added.
		 * @return bool Returns true if the information was added successfully, or false if there was a type mismatch when trying to append data.
		 */
		final public static function devInfo(string|int|float $key, mixed $data, string $mode="append"): bool {
			if (!self::$is["initialized"]) self::initialize();
			if (!isset(self::$return["_dev"])) self::$return["_dev"] = [];
			if ($mode == "replace" || !isset(self::$return["_dev"][$key])) self::$return["_dev"][$key] = $data;
			else if ($mode == "append") {
				if (!isset(self::$return["_dev"][$key])) self::$return["_dev"][$key] = [];
				if (is_array(self::$return["_dev"][$key]) && is_array($data)) self::$return["_dev"][$key] = array_merge(self::$return["_dev"][$key], $data);
				else if (is_string(self::$return["_dev"][$key]) && is_string($data)) self::$return["_dev"][$key] .= $data;
				else if (is_numeric(self::$return["_dev"][$key]) && is_numeric($data)) self::$return["_dev"][$key] += $data;
				else return false;
			} return true;
		}
		/**
		 * Sends the API response to the client. This method encodes the response data as JSON, sets the appropriate headers, and outputs the response. It also handles any output buffering and ensures that the database connection is closed before exiting. The method can take an optional parameter to set the HTTP response code or to format the JSON output in a more readable way.
		 * 
		 * @param false|int $resp The HTTP response code to be set. If false, no response code is set.
		 * @param bool $readable Whether the JSON output should be formatted in a readable way.
		 */
		final public static function sendOutput(false|int $resp=false, bool $readable=false): never {
			if (!self::$is["initialized"]) self::initialize();
			global $APP_CONST, $APP_DB;
			// Handle configuration
			if (gettype($resp) == "int") http_response_code($resp);
			else $readable = $resp;
			// Process data
			if (ob_get_level() && $error = ob_get_clean()) {
				if ($APP_CONST["environment"] == "DEV") API::errorMessage(3, $error);
				else self::devInfo("error", $error);
			} $outputData = json_encode(self::$return, !$readable ? 0 : JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
			header("Content-Type: application/json; charset=UTF-8");
			header("Content-Length: ".strlen($outputData));
			echo $outputData;
			$APP_DB[0] -> close();
			exit(0);
		}

		// Getters
		/**
		 * Checks if there are any messages in the response. This method returns the count of messages if there are any, or false if there are no messages.
		 */
		final public static function hasMessage(): int|false {
			if (!self::$is["initialized"]) self::initialize();
			return isset(self::$return["messages"]) && count(self::$return["messages"]) ? count(self::$return["messages"]) : false;
		}
		/**
		 * Checks if the API response indicates a successful operation.
		 */
		final public static function isSuccess(): bool {
			if (!self::$is["initialized"]) self::initialize();
			return self::$return["success"] ?? false;
		}

		// Utilities
		/**
		 * Checks if the user has the required permissions to perform a certain action. If the user does not have the necessary permissions, it adds an error message to the response and terminates the progress immediately, optionally triggers an authentication request if the user is not logged in, and sends the response back to the client. This method is used to enforce access control on API endpoints by verifying that the user has the appropriate permissions before allowing them to proceed with the requested action.
		 */
		final public static function requirePermission(string|array|null $scopes=null, bool $useAnd=true, bool $mods=true): bool {
			global $APP_USER;
			if (hasPermission($scopes, $useAnd, mods: $mods)) return true;
			self::$return["messages"] = [[2, "You don't have permission to perform this action."]];
			if (empty($APP_USER)) self::addJSAction("sys?.auth?.request();");
			self::sendOutput();
			return false;
		}
	}
?>