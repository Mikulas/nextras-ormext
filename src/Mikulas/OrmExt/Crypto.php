<?php

namespace Mikulas\OrmExt;


/**
 * Requires ext-mcrypt
 */
class Crypto
{

	/** @var string */
	protected $key;

	/** @var int */
	protected $ivSize;


	/**
	 * @param string $key
	 */
	public function __construct($key)
	{
		$this->key = $key;
		$this->ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	}


	/**
	 * @param string $plain
	 * @return string garble
	 */
	public function encrypt($plain)
	{
		$iv = mcrypt_create_iv($this->ivSize, MCRYPT_DEV_URANDOM);
		$crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $plain, MCRYPT_MODE_CBC, $iv);
		$garble = base64_encode($iv . $crypt);
		return $garble;
	}


	/**
	 * @param string $garble
	 * @return string plain
	 */
	public function decrypt($garble)
	{
		$combo = base64_decode($garble);
		$iv = substr($combo, 0, $this->ivSize);
		$crypt = substr($combo, $this->ivSize, strlen($combo));
		$plain = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $crypt, MCRYPT_MODE_CBC, $iv);
		return rtrim($plain);
	}

}
