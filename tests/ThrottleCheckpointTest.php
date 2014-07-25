<?php namespace Cartalyst\Sentinel\Tests;
/**
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Sentinel
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository;
use Cartalyst\Sentinel\Checkpoints\ThrottleCheckpoint;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class ThrottleCheckpointTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testLogin()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'));

		$throttle->shouldReceive('globalDelay')->once()->andReturn(0);
		$throttle->shouldReceive('userDelay')->once()->andReturn(0);

		$checkpoint->login(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	/**
	 * @expectedException \Cartalyst\Sentinel\Checkpoints\ThrottlingException
	 */
	public function testFailedLogin()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'));

		$throttle->shouldReceive('globalDelay')->once()->andReturn(10);

		$checkpoint->login(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	public function testCheck()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'));

		$checkpoint->check(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	public function testFail()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'));

		$throttle->shouldReceive('globalDelay')->once();
		$throttle->shouldReceive('userDelay')->once();
		$throttle->shouldReceive('log')->once();

		$checkpoint->fail(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	public function testWithIpAddress()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'), '127.0.0.1');

		$throttle->shouldReceive('globalDelay')->once();
		$throttle->shouldReceive('ipDelay')->once();
		$throttle->shouldReceive('userDelay')->once();
		$throttle->shouldReceive('log')->once();

		$checkpoint->fail(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	/**
	 * @expectedException \Cartalyst\Sentinel\Checkpoints\ThrottlingException
	 */
	public function testThrowsExceptionWithIpDelay()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'), '127.0.0.1');

		$throttle->shouldReceive('globalDelay')->once();
		$throttle->shouldReceive('ipDelay')->once()->andReturn(10);

		$checkpoint->fail(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

	/**
	 * @expectedException \Cartalyst\Sentinel\Checkpoints\ThrottlingException
	 */
	public function testThrowsExceptionWithUserDelay()
	{
		$checkpoint = new ThrottleCheckpoint($throttle = m::mock('Cartalyst\Sentinel\Throttling\IlluminateThrottleRepository'), '127.0.0.1');

		$throttle->shouldReceive('globalDelay')->once();
		$throttle->shouldReceive('ipDelay')->once()->andReturn(0);
		$throttle->shouldReceive('userDelay')->once()->andReturn(10);

		$checkpoint->fail(m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
	}

}