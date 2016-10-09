<?php

namespace Mikulas\OrmExt;

use Finite\Exception\StateException;
use Finite\Loader\ArrayLoader;
use Finite\Loader\LoaderInterface;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Finite\Transition\TransitionInterface;


/**
 * Requires yohang/finite, an optional dependency
 */
abstract class StatefulProperty extends ModifiableDataStore implements StatefulInterface
{

	/** @var mixed scalar */
	private $state;

	/** @var StateMachine */
	private $stateMachine;


	/**
	 * @param NULL|mixed $state scalar
	 */
	public function __construct($state = NULL)
	{
		$this->stateMachine = new StateMachine($this);
		$this->getLoader()->load($this->stateMachine);

		$this->setFiniteState($state ?: $this->getInitialState());
		$this->onModify();

		// intentionally set after ArrayLoader::load, as it sets symfony accessor
		$this->stateMachine->setStateAccessor(new StatefulPropertyStateAccessor);
		$this->stateMachine->initialize();
	}


	/**
	 * @return LoaderInterface
	 */
	protected function getLoader()
	{
		return new ArrayLoader([
			'class' => static::class,
			'states' => $this->getStates(),
			'transitions' => $this->getTransitions(),
		]);
	}


	/**
	 * @return mixed scalar state name
	 */
	protected function getInitialState()
	{
		$initial = [];
		foreach ($this->getStates() as $state => $attributes) {
			if (isset($attributes['type']) && $attributes['type'] === StateInterface::TYPE_INITIAL) {
				$initial[$state] = $attributes;
			}
		}
		if (count($initial) < 1) {
			throw StateDefinitionException::createNoInitialState();
		} else if (count($initial) > 1) {
			throw StateDefinitionException::createMultipleInitialStates($initial);
		}

		return array_keys($initial)[0];
	}


	/**
	 * @return array
	 * @link http://finite.readthedocs.org/en/master/examples/basic_graph.html#configure-your-graph
	 */
	abstract protected function getStates();

	/**
	 * @return array
	 * @link http://finite.readthedocs.org/en/master/examples/basic_graph.html#configure-your-graph
	 */
	abstract protected function getTransitions();


	/**
	 * @return StateMachine
	 */
	public function getStateMachine()
	{
		return $this->stateMachine;
	}


	# StateMachine facade

	/**
	 * Returns if the transition is applicable.
	 *
	 * @param string|TransitionInterface $transition
	 * @param array                      $parameters
	 *
	 * @return bool
	 */
	public function can($transition, array $parameters = [])
	{
		return $this->stateMachine->can($transition, $parameters);
	}


	/**
	 * Apply a transition.
	 *
	 * @param string $transitionName
	 * @param array  $parameters
	 *
	 * @return mixed
	 * @throws StateException
	 */
	public function apply($transitionName, array $parameters = [])
	{
		return $this->stateMachine->apply($transitionName, $parameters);
	}


	# StatefulInterface conformation

	public function getFiniteState()
	{
		return $this->state;
	}


	/**
	 * @param string $state
	 * @throws StateException
	 */
	public function setFiniteState($state)
	{
		if (!array_key_exists($state, $this->getStates())) {
			throw new StateException("State '$state' is not in defined diagram");
		}

		$this->state = $state;
		$this->onModify();
	}


	# IPropertyDataStore conformance

	public static function parse($state)
	{
		return new static($state);
	}


	public function serialize()
	{
		return $this->state;
	}

}
