<?php
/**
 * @brief		GraphQL: Stream query
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @since		10 May 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\core\api\GraphQL\Queries;
use GraphQL\Type\Definition\ObjectType;
use IPS\Api\GraphQL\TypeRegistry;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Stream query for GraphQL API
 */
class _Stream
{
	/*
	 * @brief 	Query description
	 */
	public static $description = "Returns an activity stream";

	/*
	 * Query arguments
	 */
	public function args(): array
	{
		return array(
			'id' => TypeRegistry::id()
		);
	}

	/**
	 * Return the query return type
	 */
	public function type() 
	{
		return \IPS\core\api\GraphQL\TypeRegistry::stream();
	}

	/**
	 * Resolves this query
	 *
	 * @param 	mixed 	Value passed into this resolver
	 * @param 	array 	Arguments
	 * @param 	array 	Context values
	 * @return	\IPS\core\Stream
	 */
	public function resolve($val, $args, $context)
	{
		if( isset( $args['id'] ) && \intval( $args['id'] ) )
		{
			try
			{
				$stream = \IPS\core\Stream::load( $args['id'] );
			}
			catch ( \OutOfRangeException $e )
			{
				return NULL;
			}

			/* Suitable for guests? */
			if ( !\IPS\Member::loggedIn()->member_id and !( ( $stream->ownership == 'all' or $stream->ownership == 'custom' ) and $stream->read == 'all' and $stream->follow == 'all' and $stream->date_type != 'last_visit' ) )
			{
				throw new \IPS\Api\GraphQL\SafeException( 'INVALID_STREAM', 'GQL/0003/1', 403 );
			}
		}
		else
		{
			/* Start with a blank stream */
			$stream = \IPS\core\Stream::allActivityStream();
		}

		return $stream;
	}
}
