<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    public function detectFaces();

    public function detectText();

}
