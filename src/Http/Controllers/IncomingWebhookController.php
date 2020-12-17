<?php

namespace Meema\MediaRecognition\Http\Controllers;

use Aws\Sns\Message;
use Illuminate\Routing\Controller;
use Meema\MediaRecognition\Events\VideoFacesAreAnalyzed;
use Meema\MediaRecognition\Events\VideoLabelsAreAnalyzed;
use Meema\MediaRecognition\Events\VideoModerationComplete;
use Meema\MediaRecognition\Facades\Recognize;

class IncomingWebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('verify-signature');
    }

    /**
     * @throws \Exception
     */
    public function __invoke()
    {
        $message = json_decode(Message::fromRawPostData()['Message'], true);

        if ($message['Status'] !== 'SUCCEEDED') {
            return;
        }

        $arr = explode('_', $message['JobTag']);
        $type = $arr[0];
        $mediaId = (int) $arr[1];

        if ($type === 'labels') {
            Recognize::getLabelsByJobId($message['JobId'], $mediaId);

            event(new VideoLabelsAreAnalyzed($message));

            return;
        }

        if ($type === 'faces') {
            Recognize::getFacesByJobId($message['JobId'], $mediaId);

            event(new VideoFacesAreAnalyzed($message));

            return;
        }

        if ($type === 'moderation') {
            Recognize::getModerationByJobId($message['JobId'], $mediaId);

            event(new VideoModerationComplete($message));
        }
    }
}
