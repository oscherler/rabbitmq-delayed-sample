<?php

namespace RabbitMQ\SampleBundle\Producer;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class DelayedProducer
{
	protected $connection;
	protected $exchange_name;
	protected $delay;
	
	public function __construct( $connection, $exchange_name, $delay )
	{
		$this->connection = $connection;
		$this->exchange_name = $exchange_name;
		$this->delay = $delay;
	}
	
	public function publish( $msgBody, $routingKey = '', $additionalProperties = array() )
	{
		$id = 'delay-waiting-queue-' . uniqid();
	
		$producer = new Producer( $this->connection );
		
		$producer->setExchangeOptions( array(
			'name' => 'delay-exchange',
			'type' => 'direct'
		) );
		
		$producer->setQueueOptions( array(
			'name' => $id,
			'routing_keys' => array( $id ),
			'arguments' => array(
				'x-message-ttl' => array( 'I', $this->delay ),
				'x-dead-letter-exchange' => array( 'S', $this->exchange_name ),
				'x-dead-letter-routing-key' => array( 'S', $routingKey ),
				'x-expires' => array( 'I', 1.1 * $this->delay )
			)
		) );
		
		$producer->setupFabric();
		
		$producer->publish( $msgBody, $id, $additionalProperties );
	}
}
