<?php

/**
 * Build an open file format version of the corpus that is edited in an XLSX file.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

namespace Porcheron\VuiCorpusBuilder;

use Port\Excel\ExcelReader;

/**
 * This class will read a corpus XLSX file and generate the open file format
 * files for the corpus.
 */
class Reader {

    /** @var        ExcelReader $reader PortPHP Excel file reader */
    private $reader;

    /**
     * Open the Excel file
     *
     * @param      \SplFileObject  $file   Excel file to parse
     */
    public function __construct(\SplFileObject $file) {
        $this->reader = new ExcelReader($file, 0, 0);
    }

    /**
     * Read the Excel file and write to each the given writers.
     *
     * @param      string    $path      Path to the directory to write to, including trailing slash
     * @param      string    $filename  Name of the file to write to (without extension)
     * @param      Writer[]  $writers   Implemented writers to convert the file to.
     */
    public function write($path, $filename, Writer... $writers) {
        $openWriters = [];
        foreach($writers as $writer) {
            try {
                $writer->open($path, $filename);
                $openWriters[] = $writer;
            } catch(RuntimeException $e) {
                echo 'Error opening file for '. get_class($writer) . "; skipping...\n";
            }
        }

        foreach ($this->reader as $row) {
            if (!empty($row['file'])) {
                foreach($openWriters as $openWriter) {
                    $openWriter->next(
                        $row['file'],
                        $row['timestamp']);
                    $openWriter->request($row['request']);
                    $openWriter->response($row['response']);
                }
            } else {
                foreach($openWriters as $openWriter) {
                    $openWriter->request($row['request']);
                    $openWriter->response($row['response']);
                }
            }
        }

        foreach($openWriters as $openWriter) {
            $openWriter->close();
        }
    }
}