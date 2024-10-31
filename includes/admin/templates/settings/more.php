<?php
/**
 * Admin - Templates - Settings - More.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Set the promos.
$promos = array(
	array(
		'title'         => 'Freelancer Marketplace',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/freelancer-marketplace-plugin/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/freelancer-marketplace-plugin-documentation/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-freelancer-marketplace.png',
		'description'   => 'will transform your WordPress website into a freelancer marketplace. It allows customers to submit projects and freelancers to find work. The plugin includes a project submission process, bidding system, workflows for file uploads, messaging, and various other features to operate a comprehensive freelancer marketplace in WordPress.',
		'initial_link'  => true,
	),
	array(
		'title'         => 'EDD Enhanced Sales Report',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/edd-enhanced-sales-reports/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/edd-enhanced-sales-report-plugin-documentation/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-edd-enhanced-sales-reports.png',
		'description'   => 'is a free plugin, which enhances your insights into the sales data from your Easy Digital Downloads webstore. This plugin adds detailed reports of your sales data by product, ordered products, and by customer. Also, it has a special feature to enhance the standard Orders Report Page in EDD by adding a column showing the products included in each order. The aim of this plugin is to enhance the sales reporting of your Easy Digital Downloads store and offer deep insights into the drivers of earnings.',
		'initial_link'  => true,
	),
	array(
		'title'         => 'EDD Advanced Shortcodes',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/edd-advanced-shortcodes/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/edd-advanced-shortcodes-setup-documentation/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-edd-advanced-shortcodes.png',
		'description'   => 'plugin provides enhanced shortcodes to easily create lists and carousels of products, reviews, and authors for Easy Digital Downloads Webstores. These shortcodes come with an extensive number of attributes that allow presenting your products at nearly unlimited possibilities. You can use the shortcodes to insert into your blog posts, your knowledge page, or even create a rich demonstration page of all types of products your Easy Digital Downloads store contains. Furthermore, the plugin also manages to show sales notification popup messages which add credibility to your webstore.',
		'initial_link'  => true,
	),
	array(
		'title'         => 'EDD Product Versions',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/edd-product-versions/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/edd-product-versions/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-edd-product-versions.png',
		'description'   => 'EDD Product Versions Plugin introduces versioning of your download files, by saving old file versions of your download products, and offering a New Version Upgrade to existing customers, for an additional revenue flow. You can either use this plugin to offer 1) old file versions to your existing customers for convenience or 2) offer upgrades, including price discounts, to existing customers. Customers who purchased old file versions will see a button on their purchase receipts to upgrade to the latest product version. This plugin is fully integrated with EDD FES in allowing vendors to create bundles, and new bundle versions. Enhance your sales by selling updated versions of already existing products!',
		'initial_link'  => true,
	),
	array(
		'title'         => 'EDD Landing pages for Categories and Tags',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/edd-landing-pages-for-categories-and-tags/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/edd-landing-pages-for-categories-and-tags/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-edd-landing-pages.png',
		'description'   => 'EDD Landing pages for Categories and Tags Plugin turns the category and tag pages of your Easy Digital Downloads webstore into feature-rich landing pages. Download Category and Tag pages now can show a featured image, text above and below the list of products, and add images and formatting to your text. The plugin also enhances the number of columns and products to be displayed per page and adds quick sorting options. Furthermore, the plugin also includes a variety of shortcodes to show your categories in the form of product carousels or lists in blog posts or even create a directory of all your categories and tags. This is a great plugin to add if you seek to enhance the user experience and site navigation for your Easy Digital Downloads webstore.',
		'initial_link'  => true,
	),
	array(
		'title'         => 'Simple Page Access Restriction',
		'url'           => 'https://www.pluginsandsnippets.com/downloads/simple-page-access-restriction/',
		'support'       => 'https://www.pluginsandsnippets.com/support/',
		'documentation' => 'https://www.pluginsandsnippets.com/knowledge-base/simple-page-access-restriction-documentation/',
		'image'         => PSUPSELLMASTER_URL . 'assets/images/admin/promotions/plugin-simple-page-access-restriction.png',
		'description'   => 'Simple Page Access Restriction Plugin offers a simple way to restrict visits to select pages only to logged-in users and allows for page redirection to a defined (login) page of your choice. Therefore, Guest users, which are not logged-in, will be redirected to another page upon accessing the restricted pages set by the site admins.',
		'initial_link'  => true,
	),
);
?>
<?php if ( isset( $promos ) && ! empty( $promos ) ) : ?>
	<h1><?php esc_html_e( 'Plugins for your Webstore!', 'psupsellmaster' ); ?></h1>
	<p><?php esc_html_e( 'Get to know some of our best plugins that can enhance the conversion rate of your Easy Digital Downloads Webstore.' ); ?></p>
	<div class="psupsellmaster-other-plugins">
		<?php foreach ( $promos as $promo ) : ?>
			<div class="psupsellmaster-other-plugin">
				<div class="psupsellmaster-other-plugin-title">
					<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php echo esc_html( $promo['title'] ); ?></a>
				</div>
				<div class="psupsellmaster-other-plugin-links">
					<div>
						<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php esc_html_e( 'View', 'psupsellmaster' ); ?></a>
					</div>
					<?php if ( isset( $promo['documentation'] ) ) : ?>
						<div>
							<a href="<?php echo esc_url( $promo['documentation'] ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'psupsellmaster' ); ?></a>
						</div>
					<?php endif; ?>
					<?php if ( isset( $promo['support'] ) ) : ?>
						<div>
							<a href="<?php echo esc_url( $promo['support'] ); ?>" target="_blank"><?php esc_html_e( 'Support', 'psupsellmaster' ); ?></a>
						</div>
					<?php endif; ?>
				</div>
				<div class="psupsellmaster-other-plugin-image">
					<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank">
						<img src="<?php echo esc_url( $promo['image'] ); ?>" />
					</a>
				</div>
				<div class="psupsellmaster-other-plugin-desc">
					<?php if ( $promo['initial_link'] ) : ?>
						<a href="<?php echo esc_url( $promo['url'] ); ?>" target="_blank"><?php echo esc_html( $promo['title'] ); ?></a>
					<?php endif; ?>
					<?php echo esc_html( $promo['description'] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
