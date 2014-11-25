<?php

namespace RabbitMQ\SampleBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class SampleConsumer implements ConsumerInterface
{
	public function execute( AMQPMessage $msg )
	{
		$payload = json_decode( $msg->body, true );
		
		if( is_array( $payload ) && isset( $payload['time'] ) && isset( $payload['delay'] ) )
		{
			$duration = ( microtime( true ) - $payload['time'] ) * 1000;
			printf( "Sent %d ms ago with delay %d.\n", $duration, $payload['delay'] );
		}
		else
		{
			echo $msg->body . "\n";
		}	
	}
}
