<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JsonException;

class SentryTunnelController extends Controller {
	/**
	 * @throws JsonException
	 */
	public function index(Request $request): void {
		$host = "sentry.io";
		// Set $known_project_ids to an array with your Sentry project IDs which you
		// want to accept through this proxy.
		$known_project_ids = [
			4504527483305984
		];


		$envelope = $request->getContent();
		$pieces = explode("\n", $envelope, 2);
		$header = json_decode($pieces[0], true, 512);
		if (isset($header["dsn"])) {
			$dsn = parse_url($header["dsn"]);
			$project_id = (int)trim($dsn["path"], "/");
			if (in_array($project_id, $known_project_ids, true)) {
				$options = [
					'http' => [
						'header' => "Content-type: application/x-sentry-envelope\r\n",
						'method' => 'POST',
						'content' => $envelope
					]
				];
				echo file_get_contents(
					"https://$host/api/$project_id/envelope/",
					false,
					stream_context_create($options));
			}
		}
	}
}
