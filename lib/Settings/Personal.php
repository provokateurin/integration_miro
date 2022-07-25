<?php
namespace OCA\Miro\Settings;

use OCA\Miro\Service\MiroAPIService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;

use OCA\Miro\AppInfo\Application;

class Personal implements ISettings {

	/**
	 * @var IConfig
	 */
	private $config;
	/**
	 * @var IInitialState
	 */
	private $initialStateService;
	/**
	 * @var string|null
	 */
	private $userId;
	/**
	 * @var MiroAPIService
	 */
	private $miroAPIService;

	public function __construct(IConfig $config,
								IInitialState $initialStateService,
								MiroAPIService $miroAPIService,
								?string $userId) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
		$this->miroAPIService = $miroAPIService;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$token = $this->config->getUserValue($this->userId, Application::APP_ID, 'token');
		$searchBoardsEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'search_boards_enabled', '0');
		$miroUserId = $this->config->getUserValue($this->userId, Application::APP_ID, 'user_id');
		$miroUserName = $this->config->getUserValue($this->userId, Application::APP_ID, 'user_name');

		// for OAuth
		$clientID = $this->config->getAppValue(Application::APP_ID, 'client_id');
		// don't expose the client secret to users
		$clientSecret = ($this->config->getAppValue(Application::APP_ID, 'client_secret') !== '');
		$usePopup = $this->config->getAppValue(Application::APP_ID, 'use_popup', '0');

		$userConfig = [
			'token' => $token ? 'dummyTokenContent' : '',
			'client_id' => $clientID,
			'client_secret' => $clientSecret,
			'use_popup' => ($usePopup === '1'),
			'user_id' => $miroUserId,
			'user_name' => $miroUserName,
			'search_boards_enabled' => ($searchBoardsEnabled === '1'),
		];
		$this->initialStateService->provideInitialState('user-config', $userConfig);
		return new TemplateResponse(Application::APP_ID, 'personalSettings');
	}

	public function getSection(): string {
		return 'connected-accounts';
	}

	public function getPriority(): int {
		return 10;
	}
}
