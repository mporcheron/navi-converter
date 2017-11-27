<?php

/**
 * Abstract file writer.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

namespace Porcheron\VuiCorpusBuilder;

/**
 * Interface for corpus writers.
 */
abstract class Writer {

    /** @var       bool $suppliedFragmentMeta `true` if the meta data for the fragment has been given. */
    private $suppliedFragmentMeta;

    /**
     * Open the file to write -- a filename (without an extension) is given.
     *
     * @param      string  $path      Path to the directory to write to, including trailing slash
     * @param      string  $filename  Name of the file to write to (without extension)
     */
    public abstract function open($path, $filename);

    /**
     * Write the next fragment to the corpus in one go.
     *
     * @param      Fragment  $fragment       Add a fragment and all the requests and responses together
     */
    public final function add(Fragment $fragment) {
        $this->next(
            $fragment->getFile(),
            $fragment->getTimestamp(),
            $fragment->getDay(),
            $fragment->getDate(),
            $fragment->getTime());

        foreach ($fragment as $key => $messages) {
            $this->request($messages['request']);
            $this->request($messages['response']);
        }
    }

    /**
     * Write the next fragment to the corpus.
     *
     * @param      string  $file       Name of the file (inc. extension) of the audio fragment
     * @param      int     $timestamp  UNIX timestamp of the file
     */
    public function next($file, $timestamp) {
        $this->done();
        $this->suppliedFragmentMeta = true;
    }

    /**
     * Log a request for the last added fragment.
     *
     * @param      string  $request  Transcription of the request made
     * @throws     LogicException When a request is added before any fragment data is created with `next()`
     */
    public function request($request) {
        if (!$this->suppliedFragmentMeta) {
            throw new \LogicException('Must add fragment meta data first!');
        }
    }

    /**
     * Log a response for the last added fragment.
     *
     * @param      string  $response  Transcription of the response made
     * @throws     LogicException When a request is added before any fragment data is created with `next()`
     */
    public function response($response) {
        if (!$this->suppliedFragmentMeta) {
            throw new \LogicException('Must add fragment meta data first!');
        }
    }

    /**
     * Finish providing requests and responses for a fragment.
     * 
     * @return     bool `false` if nothing should is done
     */
    public function done() {
        if ($this->suppliedFragmentMeta === false) {
            return false;
        }

        $this->suppliedFragmentMeta = false;
        return true;
    }

    /**
     * Close the file and save the complete log.
     */
    public abstract function close();
}