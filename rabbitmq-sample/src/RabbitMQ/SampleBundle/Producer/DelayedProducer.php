<?php

namespace RabbitMQ\SampleBundle\Producer;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class DelayedProducer
{
	protected $connection;
	protected $destination_exchange;
	
	public function __construct( $connection, $destination_exchange )
	{
		$this->connection = $connection;
		$this->destination_exchange = $destination_exchange;
	}
	
	public function delayedPublish( $delay, $msgBody, $routingKey = '', $additionalProperties = array() )
	{
		$id = 'delay-waiting-queue-' . uniqid();
		if( ! is_integer( $delay ) || $delay < 0 )
			throw new \UnexpectedValueException('Publish delay should be a positive integer.');
		
		# expire the queue a little bit after the delay, but minimum 1 second
		$expiration = 1000 + floor( 1.1 * $delay );
		
		$producer = new Producer( $this->connection );
		
		$producer->setExchangeOptions( array(
			'name' => 'delay-exchange',
			'type' => 'direct'
		) );
		
		$producer->setQueueOptions( array(
			'name' => $id,
			'routing_keys' => array( $id ),
			'arguments' => array(
				'x-message-ttl' => array( 'I', $delay ),
				'x-dead-letter-exchange' => array( 'S', $this->destination_exchange ),
				'x-dead-letter-routing-key' => array( 'S', $routingKey ),
				'x-expires' => array( 'I', $expiration )
			)
		) );
		
		$producer->setupFabric();
		
		$producer->publish( $msgBody, $id, $additionalProperties );
	}
}
