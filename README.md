somc-subpages-estachap
======================

Wordpress 3.5 plugin used to display all subpages it is placed on.

An object­oriented Wordpress 3.5+ Plugin called “somc­subpages­<yourgithubname>” that can be used as a Wordpress Widget and as a Wordpress Shortcode (naming convention: [somc­subpages­<yourgithubname>]). It should display
all subpages of the page it is placed on.

It fetchs and displays all subpages' titles, truncated after 20 characters and (if present) it shows a very small thumbnail­version of the pages/posts 'featured image' next to the title. Each level can be sorted by title in ascending and descending order by the user. It should also be possible expand and collapsed each level. All this must be possible without reconnecting to the server after the page has been loaded.

Usage
=====================

Upload the zip file to the WP site and activate (/dist/somc-subpages-estachap.zip).

You can add a shortcode [somc_subpages] to any page where you want to display a list of sub-pages of itself.
A second way to use is by adding the Subpages widget to a sidebar or another widget area that you can choose under “Appearance > Widget” menu. 

Note that in the case where both the shortcode and the widget are present on a displayed page only the shortcode area will be activated and no subpages will be displayed by the widget. Only one subpage area can be shown by a page.
