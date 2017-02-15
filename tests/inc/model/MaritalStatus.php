<?php

namespace Mikulas\OrmExt\Tests;

use Finite\State\StateInterface;
use Mikulas\OrmExt\StatefulProperty;


class MaritalStatus extends StatefulProperty
{

	# states
	const SINGLE = 'single';
	const MARRIED = 'married';
	const DIVORCED = 'divorced';
	const WIDOWED = 'widowed';

	# transitions
	const TR_MARRY = 'marry';
	const TR_DIVORCE = 'divorce';
	const TR_WIDOW = 'widow';

	const PR_CAN_LOOK_FOR_A_RELATIONSHIP = 'can-look-for-a-relationship';


	/**
	 * @return array
	 * @link http://finite.readthedocs.org/en/master/examples/basic_graph.html#configure-your-graph
	 */
	protected function getStates()
	{
		return [
			self::SINGLE => [
				'type' => StateInterface::TYPE_INITIAL,
				'properties' => [self::PR_CAN_LOOK_FOR_A_RELATIONSHIP => TRUE],
			],
			self::MARRIED => [
				'type' => StateInterface::TYPE_NORMAL,
				'properties' => [],
			],
			self::DIVORCED => [
				'type' => StateInterface::TYPE_NORMAL,
				'properties' => [self::PR_CAN_LOOK_FOR_A_RELATIONSHIP => TRUE],
			],
			self::WIDOWED => [
				'type' => StateInterface::TYPE_NORMAL,
				'properties' => [self::PR_CAN_LOOK_FOR_A_RELATIONSHIP => TRUE],
			],
		];
	}


	/**
	 * @return array
	 * @link http://finite.readthedocs.org/en/master/examples/basic_graph.html#configure-your-graph
	 */
	protected function getTransitions()
	{
		return [
			self::TR_MARRY => [
				'from' => [self::SINGLE, self::DIVORCED, self::WIDOWED],
				'to' => self::MARRIED,
			],
			self::TR_DIVORCE => [
				'from' => [self::MARRIED],
				'to' => self::DIVORCED,
			],
			self::TR_WIDOW => [
				'from' => [self::MARRIED],
				'to' => self::WIDOWED,
			],
		];
	}

}
