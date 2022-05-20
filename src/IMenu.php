<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\MenuEditor;

use MWStake\MediaWiki\Component\Wikitext\INode;
use Title;

interface IMenu {
	/**
	 * @param Title $title
	 * @return bool
	 */
	public function appliesToTitle( Title $title ): bool;

	/**
	 * @return string
	 */
	public function getRLModule(): string;

	/**
	 * @return string
	 */
	public function getJSClassname(): string;

	/**
	 * @return string
	 */
	public function getKey(): string;

	/**
	 * @return INode[]
	 */
	public function getEmptyContent(): array;
}
