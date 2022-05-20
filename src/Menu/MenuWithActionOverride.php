<?php

namespace MediaWiki\Extension\MenuEditor\Menu;

use Article;
use BlueSpice\Discovery\Hook\BlueSpiceDiscoveryTemplateDataProviderAfterInit;
use BlueSpice\Discovery\ITemplateDataProvider;
use Html;
use MediaWiki;
use MediaWiki\Extension\MenuEditor\IMenu;

use MediaWiki\Hook\MediaWikiPerformActionHook;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\HookContainer\HookContainer;
use OutputPage;
use SkinTemplate;
use Title;
use User;
use WebRequest;

abstract class MenuWithActionOverride implements
	IMenu,
	MediaWikiPerformActionHook,
	SkinTemplateNavigation__UniversalHook,
	BlueSpiceDiscoveryTemplateDataProviderAfterInit
{
	/** @var Title|null */
	private $title = null;

	/**
	 * @param HookContainer $hookContainer
	 */
	public function __construct( HookContainer $hookContainer ) {
		$hookContainer->register( 'MediaWikiPerformAction', [ $this, 'onMediaWikiPerformAction' ] );
		$hookContainer->register(
			'SkinTemplateNavigation::Universal',
			[ $this, 'onSkinTemplateNavigation__Universal' ]
		);
		$hookContainer->register(
			'BlueSpiceDiscoveryTemplateDataProviderAfterInit',
			[ $this, 'onBlueSpiceDiscoveryTemplateDataProviderAfterInit' ]
		);
	}

	/**
	 * @param OutputPage $output
	 * @param Article $article
	 * @param Title $title
	 * @param User $user
	 * @param WebRequest $request
	 * @param MediaWiki $mediaWiki
	 * @return bool|void
	 */
	public function onMediaWikiPerformAction( $output, $article, $title, $user, $request, $mediaWiki ) {
		$action = $request->getText( 'action', $request->getText( 'veaction', 'view' ) );
		if ( !in_array( $action, [ 'view', 'menueditsource', 'edit', 'create' ] ) ) {
			return true;
		}

		if ( $action === 'menueditsource' ) {
			$request->setVal( 'action', 'edit' );
			return true;
		}
		if ( !$this->appliesToTitle( $title ) ) {
			return true;
		}
		$this->title = $title;

		$output->setPageTitle( $title->getPrefixedText() );
		$output->addModules( 'ext.menuEditor.pageEditOverride' );
		$output->addHTML( Html::element( 'div', [
			'id' => 'menuEditor-container',
			'data-mode' => $action,
			'data-menu-key' => $this->getKey(),
			'data-default' => json_encode( $this->getEmptyContent() ),
		] ) );
		if ( $action === 'edit' ) {
			$request->setVal( 'action', 'menuedit' );
		}

		return false;
	}

	/**
	 * // phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		if ( !$this->title ) {
			return;
		}
		$links['views']['menueditsource'] = [
			'text' => $sktemplate->msg( "menueditor-action-menueditsource" )->text(),
			'href' => $this->title->getLocalURL( [ 'action' => 'menueditsource' ] ),
			'class' => false,
			'id' => 'ca-menueditsource',
			'position' => 12,
		];
		$links['views']['edit'] = array_merge( $links['views']['edit'], [
			'text' => $sktemplate->msg( "menueditor-action-menuedit" )->text(),
		] );
	}

	/**
	 * @param ITemplateDataProvider $registry
	 */
	public function onBlueSpiceDiscoveryTemplateDataProviderAfterInit( $registry ): void {
		if ( !$this->title ) {
			return;
		}
		$registry->register( 'panel/edit', 'ca-menueditsource' );
		$registry->unregister( 'panel/edit', 'ca-new-section' );
		$registry->unregister( 'panel/edit', 'ca-ve-edit' );
	}
}
