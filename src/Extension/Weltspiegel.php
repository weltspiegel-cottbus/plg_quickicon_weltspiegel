<?php

/**
 * @package     Weltspiegel\Plugin\Quickicon\Weltspiegel
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

namespace Weltspiegel\Plugin\Quickicon\Weltspiegel\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Module\Quickicon\Administrator\Event\QuickIconsEvent;

/**
 * Weltspiegel Quick Icon Plugin
 *
 * @since 1.0.0
 */
final class Weltspiegel extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * Load plugin language files automatically
     *
     * @var boolean
     * @since 1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this plugin listens to.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onGetIcons' => 'getIcons',
        ];
    }

    /**
     * Add quick icons to the dashboard.
     *
     * @param QuickIconsEvent $event The event object
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function getIcons(QuickIconsEvent $event): void
    {
        $context = $event->getContext();

        if ($context !== $this->params->get('context', 'mod_quickicon')
            && $context !== 'mod_quickicon') {
            return;
        }

        $result = $event->getArgument('result', []);

	    // Startseite (featured article) - edit only
	    $featuredId = $this->getFeaturedArticleId();
	    if ($featuredId) {
		    $result[] = [
			    [
				    'image' => 'icon-home',
				    'name'  => 'PLG_QUICKICON_WELTSPIEGEL_STARTSEITE',
				    'link'  => 'index.php?option=com_content&task=article.edit&id=' . $featuredId,
				    'group' => 'MOD_QUICKICON_SITE',
			    ],
		    ];
	    }

	    // Cinetixx - no add action (items come from API)
        $result[] = [
            [
                'image' => 'fa fa-film',
                'name'  => 'PLG_QUICKICON_WELTSPIEGEL_CINETIXX',
                'link'  => 'index.php?option=com_weltspiegel&view=movies',
                'group' => 'MOD_QUICKICON_SITE',
            ],
        ];

        // Vorschauen - with add action
        $result[] = [
            [
                'image'   => 'icon-eye',
                'name'    => 'PLG_QUICKICON_WELTSPIEGEL_VORSCHAUEN',
                'link'    => 'index.php?option=com_weltspiegel&view=vorschauen',
                'linkadd' => 'index.php?option=com_weltspiegel&task=vorschau.add',
                'group'   => 'MOD_QUICKICON_SITE',
            ],
        ];

        // Veranstaltungen - with add action
        $result[] = [
            [
                'image'   => 'icon-calendar',
                'name'    => 'PLG_QUICKICON_WELTSPIEGEL_VERANSTALTUNGEN',
                'link'    => 'index.php?option=com_weltspiegel&view=veranstaltungen',
                'linkadd' => 'index.php?option=com_weltspiegel&task=veranstaltung.add',
                'group'   => 'MOD_QUICKICON_SITE',
            ],
        ];

        $event->setArgument('result', $result);
    }

    /**
     * Get the ID of the featured article (startseite)
     *
     * @return int|null Article ID or null if not found
     *
     * @since 1.0.0
     */
    private function getFeaturedArticleId(): ?int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('featured') . ' = 1')
            ->setLimit(1);
        $db->setQuery($query);

        $id = $db->loadResult();

        return $id ? (int) $id : null;
    }
}
