<?php defined('BLUDIT') or die('Bludit CMS.');

/**
 * Latest Scheduled -- furthest scheduled date in the admin sidebar.
 *
 * The Content → Scheduled tab is the only place Bludit shows those dates, so
 * an author scheduling a queue has to leave the editor to see where it ends.
 * This plugin prints that furthest date in the admin sidebar on every screen
 * and links to content#scheduled.
 *
 * One class on purpose: buildPlugins() instantiates every class a plugin file
 * newly declares (D-145).
 */
class pluginLatestScheduled extends Plugin
{
	/**
	 * Kept in step with metadata.json, and appended to the stylesheet URL.
	 *
	 * Bludit's includeCSS() appends ?version=BLUDIT_VERSION, which changes when
	 * Bludit is upgraded and never when this plugin is.
	 */
	const VERSION = '0.1.0';

	/** Compact sidebar date. The full SCHEDULED_DATE_FORMAT is in the tooltip. */
	const SIDEBAR_DATE_FORMAT = 'j M Y';

	public function init()
	{
		$this->dbFields = array();
	}

	public function form()
	{
		return '<div class="alert alert-primary" role="alert">' . $this->description() . '</div>';
	}

	public function adminHead()
	{
		return '<link rel="stylesheet" type="text/css" href="'
			. $this->htmlPath() . 'css/latest-scheduled.css?v=' . self::VERSION . '">' . PHP_EOL;
	}

	public function adminSidebar()
	{
		global $L;

		$furthest = $this->furthestScheduled();
		$url = HTML_PATH_ADMIN_ROOT . 'content#scheduled';
		$label = Sanitize::html($L->get('ls-label'));

		if ($furthest === null) {
			$none = Sanitize::html($L->get('ls-none'));
			$title = Sanitize::html($L->get('ls-title'));
			return '<a class="nav-link js-latest-scheduled ls-empty" href="' . $url . '" title="' . $title . '">'
				. '<span class="fa fa-calendar"></span>'
				. '<span class="ls-label">' . $label . '</span>'
				. '<span class="ls-date js-latest-scheduled-date" data-date="" data-empty="1">' . $none . '</span>'
				. '</a>';
		}

		$short = Sanitize::html($this->formatDate($furthest['date'], self::SIDEBAR_DATE_FORMAT));
		$fullFormat = defined('SCHEDULED_DATE_FORMAT') ? SCHEDULED_DATE_FORMAT : 'D, j M Y, H:i';
		$full = $this->formatDate($furthest['date'], $fullFormat);
		$tip = sprintf(
			$L->get('ls-title-with-page'),
			(int) $furthest['count'],
			$full,
			$furthest['title']
		);

		return '<a class="nav-link js-latest-scheduled" href="' . $url . '" title="' . Sanitize::html($tip) . '">'
			. '<span class="fa fa-calendar"></span>'
			. '<span class="ls-label">' . $label . '</span>'
			. '<span class="ls-date js-latest-scheduled-date" data-date="'
			. Sanitize::html($furthest['date']) . '" data-empty="0">' . $short . '</span>'
			. '</a>';
	}

	/**
	 * The scheduled page with the latest date, or null if the queue is empty.
	 *
	 * Reads $pages->db (via getScheduledDB), not new Page(): the constructor
	 * opens index.txt, and the sidebar renders on every admin request (D-147).
	 */
	private function furthestScheduled()
	{
		global $pages;
		global $login;

		if (!is_object($pages) || !method_exists($pages, 'getScheduledDB')) {
			return null;
		}

		$db = $pages->getScheduledDB(false);
		if (!is_array($db) || $db === array()) {
			return null;
		}

		if (checkRole(array('author'), false) && is_object($login)) {
			$user = $login->username();
			foreach ($db as $key => $fields) {
				if (!isset($fields['username']) || $fields['username'] !== $user) {
					unset($db[$key]);
				}
			}
		}

		$bestKey = null;
		$bestDate = '';
		foreach ($db as $key => $fields) {
			if (!is_array($fields)) {
				continue;
			}
			$date = isset($fields['date']) ? (string) $fields['date'] : '';
			if ($date === '') {
				continue;
			}
			if ($bestKey === null || $date > $bestDate) {
				$bestKey = $key;
				$bestDate = $date;
			}
		}

		if ($bestKey === null) {
			return null;
		}

		$title = isset($db[$bestKey]['title']) ? Sanitize::htmlDecode((string) $db[$bestKey]['title']) : '';
		return array(
			'key'   => (string) $bestKey,
			'date'  => $bestDate,
			'title' => $title,
			'count' => count($db)
		);
	}

	private function formatDate($raw, $format)
	{
		if (class_exists('Date') && defined('DB_DATE_FORMAT')) {
			$out = Date::format($raw, DB_DATE_FORMAT, $format);
			if ($out !== false && $out !== '') {
				return $out;
			}
		}
		$ts = strtotime($raw);
		return $ts !== false ? date($format, $ts) : $raw;
	}
}
