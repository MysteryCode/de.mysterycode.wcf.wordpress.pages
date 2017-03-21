<?php

namespace wcf\page;

use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\StringUtil;

class WpPage extends AbstractPage {
	/**
	 * @var \WP_Post
	 */
	protected $wpPage = null;
	
	/**
	 * @var \WP_Query
	 */
	protected $wpQuery = null;
	
	/**
	 * @var string[]
	 */
	protected $stylesheets = [];
	
	/**
	 * @var string[]
	 */
	protected $javascripts = [];
	
	public function readParameters() {
		parent::readParameters();
		
		if (empty($_GET['page'])) {
			throw new IllegalLinkException();
		}
		
		define( 'ABSPATH', WORDPRESS_ABSPATH );
		
		$path = StringUtil::trim($_GET['page']);
		$pathParts = explode("/", $path);
		$path = array_pop($pathParts);
		
		// init wordpress
		include( ABSPATH . 'wp-load.php' );
		wp('pagename=' . $path);
		global $wp_query;
		$this->wpQuery = $wp_query;
		$this->wpPage = $this->wpQuery->queried_object;
		
		if ($this->wpPage === null || !$this->wpPage->ID) {
			throw new IllegalLinkException();
		}
		
		include( ABSPATH . 'wp-includes/template-loader.php' );
		
		wp_enqueue_scripts();
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'content' => str_replace(
				[
					"<script",
					'jQuery('
				],
				[
					'<script data-relocate="true"',
					'$('
				],
				apply_filters('the_content', $this->wpPage->post_content)),
			'additionalStylesheets' => $this->stylesheets,
			'additionalScripts' => $this->javascripts,
			'title' => $this->wpPage->post_title,
			'wpPage' => $this->wpPage,
		]);
	}
	
	public function show() {
		// fix 404 header from wordpress
		status_header(200);
		
		parent::show();
	}
	
	protected function loadStylesheets() {
		$this->stylesheets[] = [
			'src' => get_bloginfo('stylesheet_url', 'display'),
			'media' => 'all',
			'id' => 'theme'
		];
		
		global $wp_styles;
		$styleQueue = $wp_styles->queue;
		foreach ($styleQueue as $id => $style) {
			if (!empty($wp_styles->registered[$style])) {
				/** @var \_WP_Dependency $obj */
				$obj = $wp_styles->registered[$style];
				
				//TODO: fix regex (domain)
				if (!preg_match('/^(' . WORDPRESS_URL . '|\/\/)/', $obj->src)) {
					$src = WORDPRESS_URL . FileUtil::removeLeadingSlash($obj->src);
				}
				
				$this->stylesheets[] = [
					'src' => $src,
					'media' => $obj->args,
					'id' => $style
				];
			}
		}
	}
	
	protected function loadScripts() {
		global $wp_scripts;
		$scriptQueue = $wp_scripts->queue;
		foreach ($scriptQueue as $id => $script) {
			if (!empty($wp_scripts->registered[$script])) {
				/** @var \_WP_Dependency $obj */
				$obj = $wp_scripts->registered[$script];
				
				if (preg_match('/(jplayer)/', $obj->src)) {
					continue;
				}
				
				//TODO: fix regex (domain)
				if (!preg_match('/^(' . WORDPRESS_URL . '|\/\/)/', $obj->src)) {
					$src = WORDPRESS_URL . FileUtil::removeLeadingSlash($obj->src);
				}
				
				$this->javascripts[] = [
					'src' => $src,
					'id' => $script
				];
			}
		}
	}
}
