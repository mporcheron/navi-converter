<?php

/**
 * Write an XML file.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

namespace Porcheron\VuiCorpusBuilder;

/**
 * Write to a XML file. This class caches requests and responses, and writes them to the file when a new fragment is inserted.
 */
class XmlWriter extends Writer {

    /** @var        \SplFileObject  $file           XML file */
    private $file;

    /** @var        mixed[]         $currentFragment Fragment data to write (this is only written on close or the insertion of the next fragment). */
    private $currentFragment;

    /** @var        int             $writtenFragment Number of fragments written. */
    private $writtenFragments = 0;

    /** @var        string           HEADER          Header of the XML file */
    const HEADER = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<corpus><fragments>";

    /** @var        string           FOOTER          Footer of the XML file */
    const FOOTER = '</fragments></corpus>';

    /**
     * {@inheritDoc}
     */
    public function open($path, $filename) {
        $this->file = new \SplFileObject($path . $filename . '.xml', 'w');
        $this->file->fwrite(self::HEADER);
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
     * {@inheritDoc}
     */
    public function done() {
        if (parent::done() && !is_null($this->currentFragment)) {
            $this->file->fwrite('<fragment>');
            $this->file->fwrite('<file name="'. $this->currentFragment['file'] .'" />');
            $this->file->fwrite('<timestamp value="'. $this->currentFragment['timestamp'] .'" />');
            $this->file->fwrite('<messages>');

            $num = count($this->currentFragment['messages']['requests']);
            $num = max($num, count($this->currentFragment['messages']['responses']));

            for($i = 0; $i < $num; $i++) {
                $request = $this->currentFragment['messages']['requests'][$i];
                $response = $this->currentFragment['messages']['responses'][$i];

                $this->file->fwrite('<message request="'. ($request ?: 'null') .'"');
                $this->file->fwrite(' response="'. ($request ?: 'null') .'" />');
            }

            $this->file->fwrite('</messages></fragment>');
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
        $this->file->fwrite(self::FOOTER);
        $this->file = null;
    }

}