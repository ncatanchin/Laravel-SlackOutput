<?php

namespace NicolasMahe\SlackOutput\Command;

use Illuminate\Console\Command;
use Maknz\Slack\Client;
use Exception;

class SlackPost extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slack:post
	  {message? : The message to send}
	  {to? : The channel or person}
	  {attach? : The attachment payload}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a message to Slack';

  /**
   * Execute the console command.
   *
   * @return mixed
   * @throws Exception
   */
	public function handle()
	{
    $this->line('Processing...');

    //get command arguments
    $message = $this->argument('message');
    $to = $this->argument('to');
    $attach = $this->argument('attach');

    //init client
    $client = $this->initClient();

    //attach data
    if (is_array($attach)) {
	    if ($this->is_assoc($attach)) {
		    $attach = [$attach];
	    }

	    foreach($attach as $attachElement) {
		    $client = $client->attach($attachElement);
	    }
    }
    if (!empty($to)) {
      $client = $client->to($to);
    }

    //send
    $client->send($message);

		$this->info('Message sent');
	}

  /**
   * Init the slack client
   *
   * @return Client
   * @throws Exception
   */
  protected function initClient() {
    //get slack config
    $slack_config = config('slack-output');

    if (empty($slack_config["endpoint"])) {
      throw new Exception("The endpoint url is not set in the config");
    }

    //init client
    $client = new Client($slack_config["endpoint"], [
      "username"  => $slack_config["username"],
      "icon"      => $slack_config["icon"]
    ]);

    return $client;
  }

	/**
	 * Check if array is associative
	 *
	 * @param array $array
	 * @return bool
	 */
	public static function is_assoc(array $array)
	{
		// Keys of the array
		$keys = array_keys($array);

		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}
}
