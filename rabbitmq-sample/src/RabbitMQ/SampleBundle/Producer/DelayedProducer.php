<?php

namespace RabbitMQ\SampleBundle\Producer;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class DelayedProducer
{
	protected $connection;
	protected $destination_exchange;
	protected $prefix;
	
	public function __construct( $connection, $destination_exchange, $prefix )
	{
		$this->connection = $connection;
		$this->destination_exchange = $destination_exchange;

		if( ! is_string( $prefix ) || strlen( $prefix ) > 60 )
			throw new \UnexpectedValueException('Prefix should be a string of length <= 60.');

		$this->prefix = $prefix;
	}
	
	public function delayedPublish( $delay, $msgBody, $routingKey = '', $additionalProperties = array() )
	{
		if( ! is_integer( $delay ) || $delay < 0 )
			throw new \UnexpectedValueException('Publish delay should be a positive integer.');
		
		# expire the queue a little bit after the delay, but minimum 1 second
		$expiration = 1000 + floor( 1.1 * $delay );
		
		$name = sprintf( '%s-exchange', $this->prefix );
		$id = sprintf( '%s-waiting-queue-%d', $this->prefix, $delay );
	
		$producer = new Producer( $this->connection );
		
		$producer->setExchangeOptions( array(
			'name' => $name,
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
