{capture assign='pageTitle'}{$title}{/capture}
{capture assign='contentTitle'}{/capture}

{capture assign='headContent'}
	<link rel='stylesheet' id='options_typography_Lato-css' href='//fonts.googleapis.com/css?family=Lato&#038;subset=latin' type='text/css' media='all' />
	<link rel='stylesheet' id='cherry-lazy-load-css'  href='{WORDPRESS_URL}wp-content/plugins/cherry-lazy-load/css/lazy-load.css' type='text/css' media='all' />
	{foreach from=$additionalStylesheets item=additionalStylesheet}<link rel='stylesheet' id='{$additionalStylesheet[id]}' href='{$additionalStylesheet[src]}' type='text/css' media='{$additionalStylesheet[media]}' />
	{/foreach}
	<script data-relocate="true">var ui_init_object = { "auto_init":"false","targets":[] }; var jQuery = $;</script>
	{foreach from=$additionalScripts item=additionalScript}<script data-relocate="true" src='{$additionalScript[src]}'></script>{/foreach}
	<script data-relocate="true" src='{WORDPRESS_URL}wp-content/plugins/cherry-lazy-load/js/cherry.lazy-load.js'></script>
	<script data-relocate="true" src='{WORDPRESS_URL}wp-content/plugins/cherry-lazy-load/js/device.min.js'></script>
	{if !$css|empty}<style type="text/css">{@$css}</style>{/if}
	<style type="text/css">
		#content {
			padding-left: 0;
			padding-right: 0;
			margin-left: 0;
			margin-right: 0;
		}
		.wordpressContent {
			width: 100%;
		}
		.sidebar + .content {
			max-width: calc(100% - 200px);
		}
		.content_box:before {
			width: 100%;
		}
		#content figure.icon {
			display: block;
		}
		#content p + *, #content ul + *, #content ol + * {
			margin-top: 0;
		}
	</style>
{/capture}

{include file='header'}

<div class="wordpressContent" data-page-id="{$wpPage->ID}">
	{@$content}
</div>

{include file='footer'}
