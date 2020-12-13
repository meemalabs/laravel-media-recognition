<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    /**
     * Cancels an active job.
     *
     * @param string $id
     * @return \Aws\Result
     */
    public function testing(string $id);

}
