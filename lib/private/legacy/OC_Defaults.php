<?php

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

use OCP\IConfig;
use OCP\Server;
use OCP\ServerVersion;

class OC_Defaults {
	private $theme;

	private $defaultEntity;
	private $defaultName;
	private $defaultTitle;
	private $defaultBaseUrl;
	private $defaultSyncClientUrl;
	private $defaultiOSClientUrl;
	private $defaultiTunesAppId;
	private $defaultAndroidClientUrl;
	private $defaultFDroidClientUrl;
	private $defaultDocBaseUrl;
	private $defaultDocVersion;
	private $defaultSlogan;
	private $defaultColorBackground;
	private $defaultColorPrimary;
	private $defaultTextColorPrimary;
	private $defaultProductName;

	public function __construct() {
		$config = Server::get(IConfig::class);
		$serverVersion = Server::get(ServerVersion::class);

		$this->defaultEntity = 'Nextcloud'; /* e.g. company name, used for footers and copyright notices */
		$this->defaultName = 'Nextcloud'; /* short name, used when referring to the software */
		$this->defaultTitle = 'Nextcloud'; /* can be a longer name, for titles */
		$this->defaultBaseUrl = 'https://nextcloud.com';
		$this->defaultSyncClientUrl = $config->getSystemValue('customclient_desktop', 'https://nextcloud.com/install/#install-clients');
		$this->defaultiOSClientUrl = $config->getSystemValue('customclient_ios', 'https://geo.itunes.apple.com/us/app/nextcloud/id1125420102?mt=8');
		$this->defaultiTunesAppId = $config->getSystemValue('customclient_ios_appid', '1125420102');
		$this->defaultAndroidClientUrl = $config->getSystemValue('customclient_android', 'https://play.google.com/store/apps/details?id=com.nextcloud.client');
		$this->defaultFDroidClientUrl = $config->getSystemValue('customclient_fdroid', 'https://f-droid.org/packages/com.nextcloud.client/');
		$this->defaultDocBaseUrl = 'https://docs.nextcloud.com';
		$this->defaultDocVersion = $serverVersion->getMajorVersion(); // used to generate doc links
		$this->defaultColorBackground = '#00679e';
		$this->defaultColorPrimary = '#00679e';
		$this->defaultTextColorPrimary = '#ffffff';
		$this->defaultProductName = 'Nextcloud';

		$themePath = OC::$SERVERROOT . '/themes/' . OC_Util::getTheme() . '/defaults.php';
		if (file_exists($themePath)) {
			// prevent defaults.php from printing output
			ob_start();
			require_once $themePath;
			ob_end_clean();
			if (class_exists('OC_Theme')) {
				$this->theme = new OC_Theme();
			}
		}
	}

	/**
	 * @param string $method
	 */
	private function themeExist($method) {
		if (isset($this->theme) && method_exists($this->theme, $method)) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the base URL
	 * @return string URL
	 */
	public function getBaseUrl() {
		if ($this->themeExist('getBaseUrl')) {
			return $this->theme->getBaseUrl();
		} else {
			return $this->defaultBaseUrl;
		}
	}

	/**
	 * Returns the URL where the sync clients are listed
	 * @return string URL
	 */
	public function getSyncClientUrl() {
		if ($this->themeExist('getSyncClientUrl')) {
			return $this->theme->getSyncClientUrl();
		} else {
			return $this->defaultSyncClientUrl;
		}
	}

	/**
	 * Returns the URL to the App Store for the iOS Client
	 * @return string URL
	 */
	public function getiOSClientUrl() {
		if ($this->themeExist('getiOSClientUrl')) {
			return $this->theme->getiOSClientUrl();
		} else {
			return $this->defaultiOSClientUrl;
		}
	}

	/**
	 * Returns the AppId for the App Store for the iOS Client
	 * @return string AppId
	 */
	public function getiTunesAppId() {
		if ($this->themeExist('getiTunesAppId')) {
			return $this->theme->getiTunesAppId();
		} else {
			return $this->defaultiTunesAppId;
		}
	}

	/**
	 * Returns the URL to Google Play for the Android Client
	 * @return string URL
	 */
	public function getAndroidClientUrl() {
		if ($this->themeExist('getAndroidClientUrl')) {
			return $this->theme->getAndroidClientUrl();
		} else {
			return $this->defaultAndroidClientUrl;
		}
	}

	/**
	 * Returns the URL to Google Play for the Android Client
	 * @return string URL
	 */
	public function getFDroidClientUrl() {
		if ($this->themeExist('getFDroidClientUrl')) {
			return $this->theme->getFDroidClientUrl();
		} else {
			return $this->defaultFDroidClientUrl;
		}
	}

	/**
	 * Returns the documentation URL
	 * @return string URL
	 */
	public function getDocBaseUrl() {
		if ($this->themeExist('getDocBaseUrl')) {
			return $this->theme->getDocBaseUrl();
		} else {
			return $this->defaultDocBaseUrl;
		}
	}

	/**
	 * Returns the title
	 * @return string title
	 */
	public function getTitle() {
		if ($this->themeExist('getTitle')) {
			return $this->theme->getTitle();
		} else {
			return $this->defaultTitle;
		}
	}

	/**
	 * Returns the short name of the software
	 * @return string title
	 */
	public function getName() {
		if ($this->themeExist('getName')) {
			return $this->theme->getName();
		} else {
			return $this->defaultName;
		}
	}

	/**
	 * Returns the short name of the software containing HTML strings
	 * @return string title
	 */
	public function getHTMLName() {
		if ($this->themeExist('getHTMLName')) {
			return $this->theme->getHTMLName();
		} else {
			return $this->defaultName;
		}
	}

	/**
	 * Returns entity (e.g. company name) - used for footer, copyright
	 * @return string entity name
	 */
	public function getEntity() {
		if ($this->themeExist('getEntity')) {
			return $this->theme->getEntity();
		} else {
			return $this->defaultEntity;
		}
	}

	/**
	 * Returns slogan
	 * @return string slogan
	 */
	public function getSlogan(?string $lang = null) {
		if ($this->themeExist('getSlogan')) {
			return $this->theme->getSlogan($lang);
		} else {
			if ($this->defaultSlogan === null) {
				$l10n = \OC::$server->getL10N('lib', $lang);
				$this->defaultSlogan = $l10n->t('a safe home for all your data');
			}
			return $this->defaultSlogan;
		}
	}

	/**
	 * Returns short version of the footer
	 * @return string short footer
	 */
	public function getShortFooter() {
		if ($this->themeExist('getShortFooter')) {
			$footer = $this->theme->getShortFooter();
		} else {
			$footer = '<a href="' . $this->getBaseUrl() . '" target="_blank"'
				. ' rel="noreferrer noopener">' . $this->getEntity() . '</a>'
				. ' – ' . $this->getSlogan();
		}

		return $footer;
	}

	/**
	 * Returns long version of the footer
	 * @return string long footer
	 */
	public function getLongFooter() {
		if ($this->themeExist('getLongFooter')) {
			$footer = $this->theme->getLongFooter();
		} else {
			$footer = $this->getShortFooter();
		}

		return $footer;
	}

	/**
	 * @param string $key
	 * @return string URL to doc with key
	 */
	public function buildDocLinkToKey($key) {
		if ($this->themeExist('buildDocLinkToKey')) {
			return $this->theme->buildDocLinkToKey($key);
		}
		return $this->getDocBaseUrl() . '/server/' . $this->defaultDocVersion . '/go.php?to=' . $key;
	}

	/**
	 * Returns primary color
	 * @return string
	 */
	public function getColorPrimary() {
		if ($this->themeExist('getColorPrimary')) {
			return $this->theme->getColorPrimary();
		}
		if ($this->themeExist('getMailHeaderColor')) {
			return $this->theme->getMailHeaderColor();
		}
		return $this->defaultColorPrimary;
	}

	/**
	 * Returns primary color
	 * @return string
	 */
	public function getColorBackground() {
		if ($this->themeExist('getColorBackground')) {
			return $this->theme->getColorBackground();
		}
		return $this->defaultColorBackground;
	}

	/**
	 * @return array scss variables to overwrite
	 */
	public function getScssVariables() {
		if ($this->themeExist('getScssVariables')) {
			return $this->theme->getScssVariables();
		}
		return [];
	}

	public function shouldReplaceIcons() {
		return false;
	}

	/**
	 * Themed logo url
	 *
	 * @param bool $useSvg Whether to point to the SVG image or a fallback
	 * @return string
	 */
	public function getLogo($useSvg = true) {
		if ($this->themeExist('getLogo')) {
			return $this->theme->getLogo($useSvg);
		}

		if ($useSvg) {
			$logo = \OC::$server->getURLGenerator()->imagePath('core', 'logo/logo.svg');
		} else {
			$logo = \OC::$server->getURLGenerator()->imagePath('core', 'logo/logo.png');
		}
		return $logo . '?v=' . hash('sha1', implode('.', \OCP\Util::getVersion()));
	}

	public function getTextColorPrimary() {
		if ($this->themeExist('getTextColorPrimary')) {
			return $this->theme->getTextColorPrimary();
		}
		return $this->defaultTextColorPrimary;
	}

	public function getProductName() {
		if ($this->themeExist('getProductName')) {
			return $this->theme->getProductName();
		}
		return $this->defaultProductName;
	}
}
