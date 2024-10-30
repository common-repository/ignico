<?php

namespace IgnicoWordPress\Api\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 * The class responsible for handling raw curl message
 *
 * Class Response
 *
 * @package IgnicoWordPress\Api\Http\Message
 */
class Stream implements StreamInterface {

	/**
	 * PSR-7 require message body to be a string. Curl can not return stream
	 * so we must create string from the curl string response.
	 *
	 * Stream will be created like that:
	 *
	 * <code>
	 * $stream = fopen('php://memory','r+');
	 *
	 * $this->fwrite($string);
	 * $this->rewind();
	 * </code>
	 *
	 * @var resource
	 */
	private $stream;

	/**
	 * Stream constructor.
	 *
	 * Create stream from string
	 *
	 * @param string $string
	 */
	public function __construct( $string ) {
		$this->stream = fopen( 'php://memory', 'r+' );

		$this->write( $string );
		$this->rewind();
	}

	/**
	 * Reads all data from the stream into a string, from the beginning to end.
	 *
	 * This method MUST attempt to seek to the beginning of the stream before
	 * reading data and read the stream until the end is reached.
	 *
	 * Warning: This could attempt to load a large amount of data into memory.
	 *
	 * This method MUST NOT raise an exception in order to conform with PHP's
	 * string casting operations.
	 *
	 * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @return string
	 */
	public function __toString() {
		return $this->getContents();
	}

	/**
	 * Closes the stream and any underlying resources.
	 *
	 * @return void
	 */
	public function close() {
		fclose( $this->stream );
	}

	/**
	 * Separates any underlying resources from the stream.
	 *
	 * After the stream has been detached, the stream is in an unusable state.
	 *
	 * @return resource|null Underlying PHP stream, if any
	 */
	public function detach() {
		fclose( $this->stream );

		unset( $this->stream );
	}

	/**
	 * Get the size of the stream if known.
	 *
	 * @return int|null Returns the size in bytes if known, or null if unknown.
	 */
	public function getSize() {
		$fstats = fstat( $this->stream );

		if ( isset( $fstats['size'] ) ) {
			return $fstats['size'];
		}

		return null;
	}

	/**
	 * Returns the current position of the file read/write pointer
	 *
	 * @return int Position of the file pointer
	 * @throws \RuntimeException on error.
	 */
	public function tell() {
		return ftell( $this->stream );
	}

	/**
	 * Returns true if the stream is at the end of the stream.
	 *
	 * @return bool
	 */
	public function eof() {
		return feof( $this->stream );
	}

	/**
	 * Returns whether or not the stream is seekable.
	 *
	 * @return bool
	 */
	public function isSeekable() {
		$seekable = $this->getMetadata( 'seekable' );

		if ( is_bool( $seekable ) ) {
			return $seekable;
		}

		return false;
	}

	/**
	 * Seek to a position in the stream.
	 *
	 * @link http://www.php.net/manual/en/function.fseek.php
	 * @param int $offset Stream offset
	 * @param int $whence Specifies how the cursor position will be calculated
	 *     based on the seek offset. Valid values are identical to the built-in
	 *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
	 *     offset bytes SEEK_CUR: Set position to current location plus offset
	 *     SEEK_END: Set position to end-of-stream plus offset.
	 * @throws \RuntimeException on failure.
	 */
	public function seek( $offset, $whence = SEEK_SET ) {
		fseek( $this->stream, $offset, $whence );
	}

	/**
	 * Seek to the beginning of the stream.
	 *
	 * If the stream is not seekable, this method will raise an exception;
	 * otherwise, it will perform a seek(0).
	 *
	 * @see seek()
	 * @link http://www.php.net/manual/en/function.fseek.php
	 * @throws \RuntimeException on failure.
	 */
	public function rewind() {
		rewind( $this->stream );
	}

	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @return bool
	 */
	public function isWritable() {
		$mode = $this->getMetadata( 'mode' );

		$writableModes = array(
			'r+',
			'w',
			'w+',
			'a',
			'a+',
			'x',
			'x+',
		);

		if ( in_array( $mode, $writableModes ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Write data to the stream.
	 *
	 * @param string $string The string that is to be written.
	 * @return int Returns the number of bytes written to the stream.
	 * @throws \RuntimeException on failure.
	 */
	public function write( $string ) {
		fwrite( $this->stream, $string );
	}

	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @return bool
	 */
	public function isReadable() {
		$mode = $this->getMetadata( 'mode' );

		$readableModes = array(
			'r',
			'r+',
			'w+',
			'a+',
			'x+',
		);

		if ( in_array( $mode, $readableModes ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Read data from the stream.
	 *
	 * @param int $length Read up to $length bytes from the object and return
	 *     them. Fewer than $length bytes may be returned if underlying stream
	 *     call returns fewer bytes.
	 * @return string Returns the data read from the stream, or an empty string
	 *     if no bytes are available.
	 * @throws \RuntimeException if an error occurs.
	 */
	public function read( $length ) {
		return fread( $this->stream, $length );
	}

	/**
	 * Returns the remaining contents in a string
	 *
	 * @return string
	 * @throws \RuntimeException if unable to read or an error occurs while
	 *     reading.
	 */
	public function getContents() {
		return stream_get_contents( $this->stream );
	}

	/**
	 * Get stream metadata as an associative array or retrieve a specific key.
	 *
	 * The keys returned are identical to the keys returned from PHP's
	 * stream_get_meta_data() function.
	 *
	 * @link http://php.net/manual/en/function.stream-get-meta-data.php
	 * @param string $key Specific metadata to retrieve.
	 * @return array|mixed|null Returns an associative array if no key is
	 *     provided. Returns a specific key value if a key is provided and the
	 *     value is found, or null if the key is not found.
	 */
	public function getMetadata( $key = null ) {
		$data = stream_get_meta_data( $this->stream );

		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ];
		}

		return null;
	}
}
