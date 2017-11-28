<?php

/**
 * Run the Corpus Builder with a given file and generate a JSON and XML file.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

require 'vendor/autoload.php';

use Porcheron\VuiCorpusBuilder\Reader;
use Porcheron\VuiCorpusBuilder\JsonWriter;
use Porcheron\VuiCorpusBuilder\XmlWriter;

$cp = new Reader(new \SplFileObject('../publiccorpus.xlsx'));
$cp->write('../', 'publiccorpus', new JsonWriter(), new XmlWriter());