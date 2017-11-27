<?php

/**
 * Run the VuiCorpusBuilder.
 *
 * @author  Martin Porcheron <martin-vuicorpusbuilder@porcheron.uk>
 * @license MIT
 */

require 'vendor/autoload.php';

use Porcheron\VuiCorpusBuilder\Reader;
use Porcheron\VuiCorpusBuilder\JsonWriter;

$cp = new Reader(new \SplFileObject('../publiccorpus.xlsx'));
$cp->write('../', 'publiccorpus', new JsonWriter());