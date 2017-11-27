<?php

/**
 * Fragment class.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

namespace Porcheron\VuiCorpusBuilder;

/**
 * Write to a JSON file. This class caches requests and responses, and writes them to the file when a new fragment is inserted.
 */
class Fragment implements \Iterator {

    /** @var        string      $file           Name of the file (inc. extension) of the audio fragment */
    private $file;

    /** @var        int         $timestamp      UNIX timestamp of the file */
    private $timestamp;

    /** @var        string[]    $requests       Array of requests made to the VUI */
    private $requests = [];

    /** @var        string[]    $responses      Array of responses from the VUI */
    private $responses = [];

    /** @var        int         $numRequests   Number of requests made to the VUI */
    private $numRequests = 0;

    /** @var        int         $numResponses   Number of responses from the VUI */
    private $numResponses = 0;

    /** @var        int         $index          Current iterable position */
    private $index = 0;

    /**
     * { function_description }
     *
     * @param      string  $file       Name of the file (inc. extension) of the audio fragment
     * @param      int     $timestamp  UNIX timestamp of the file
     */
    public function __construct($file, $timestamp) {
        $this->file = $file;
        $this->timestamp = $timestamp;
    }

    /**
     * Gets the file name including the extension of the audio fragment.
     *
     * @return     string  File name of the audio fragment.
     */
    public function getFile() {
        return $file;
    }

    /**
     * Gets the UNIX timestamp of the file.
     *
     * @return     int  UNIX timestamp of the file
     */
    public function getTimetstamp() {
        return $file->timestamp;
    }

    /**
     * Record a record that is in this fragment.
     *
     * @param      stirng  $request  Request to a VUI made by a user
     */
    public function addRequest($request) {
        $this->requests[] = $request;
        $this->numRequests++;
    }

    /**
     * Record a response that is in this fragment.
     *
     * @param      string  $response  Response from a VUI
     */
    public function addResponse($response) {
        while ($this->numRequests < ++$this->numResponses) {
            $this->responses[] = "";
        }
        $this->responses[] = $response;
    }

    /**
     * Return the current request and response as a key/value pair.
     *
     * @return     string[] Array of two strings, with two indexes of 'request' and 'response'
     */
     public function current() {
        $response = $this->responses[$this->index] ?: '';
        return [
            'request' => $this->requests[$this->index],
            'response' =>$response
        ];
     }

    /**
     * {@inheritDoc}
     */
    public function key() {
        return $this->index;
    }

    /**
     * {@inheritDoc}
     */
    public function next() {
        $this->index++;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind() {
        $this->index = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function valid() {
        return $index < $this->numRequests;
    }
}