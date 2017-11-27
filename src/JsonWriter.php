<?php

/**
 * Write a JSON file.
 *
 * @author  Martin Porcheron <martin-corpusbuilder@porcheron.uk>
 * @license MIT
 */

namespace Porcheron\VuiCorpusBuilder;

/**
 * Write to a JSON file. This class caches requests and responses, and writes them to the file when a new fragment is inserted.
 */
class JsonWriter extends Writer {

    /** @var        \SplFileObject $file JSON file */
    private $file;

    /** @var        mixed[] $currentFragment Fragment data to write (this is only written on close or the insertion of the next fragment). */
    private $currentFragment;

    /** @var        int $writtenFragment Number of fragments written. */
    private $writtenFragments = 0;

    /**
     * {@inheritDoc}
     */
    public function open($path, $filename) {
        $this->file = new \SplFileObject($path . $filename . '.json', 'w');
        $this->file->fwrite('[');
    }

    /**
     * {@inheritDoc}
     */
    public function next($file, $timestamp) {
        parent::next($file, $timestamp);

        $this->currentFragment = [
            'file' => $file,
            'timestamp' => $timestamp,
            'messages' => ['requests' => [], 'responses' => []]];
    }

    /**
     * {@inheritDoc}
     */
    public function request($request) {
        parent::request($request);
        $this->currentFragment['messages']['requests'][] = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function response($response) {
        parent::response($response);
        $this->currentFragment['messages']['responses'][] = $response;
    }

    /**
     * Write the current fragment in the buffer to the file.
     */
    public function done() {
        if (parent::done() && !is_null($this->currentFragment)) {
            $data = ($this->writtenFragments > 0 ? ',' : '') . json_encode($this->currentFragment);
            $this->file->fwrite($data);
            $this->writtenFragments++;
            $this->currentFragment = null;
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function close() {
        $this->done();
        $this->file->fwrite(']');
        $this->file = null;
    }

}