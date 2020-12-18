<?php

namespace Meema\MediaRecognition\Http\Controllers;

use Aws\Sns\Message;
use Illuminate\Routing\Controller;
use Meema\MediaRecognition\Events\VideoFacialAnalysisIsCompleted;
use Meema\MediaRecognition\Events\VideoLabelAnalysisIsCompleted;
use Meema\MediaRecognition\Events\VideoModerationIsCompleted;
use Meema\MediaRecognition\Events\VideoTextAnalysisIsCompleted;
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

        try {
            $this->fireEventFor($type, $message, $mediaId);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * @param string $type
     * @param array $message
     * @param int $mediaId
     * @throws \Exception
     */
    public function fireEventFor(string $type, array $message, int $mediaId)
    {
        switch ($type) {
            case 'labels':
                Recognize::getLabelsByJobId($message['JobId'], $mediaId);
                event(new VideoLabelAnalysisIsCompleted($message));
                break;
            case 'faces':
                Recognize::getFacesByJobId($message['JobId'], $mediaId);
                event(new VideoFacialAnalysisIsCompleted($message));
                break;
            case 'moderation':
                Recognize::getContentModerationByJobId($message['JobId'], $mediaId);
                event(new VideoModerationIsCompleted($message));
                break;
            case 'ocr':
                Recognize::getTextDetectionByJobId($message['JobId'], $mediaId);
                event(new VideoTextAnalysisIsCompleted($message));
                break;
            default:
                throw new \Exception();
        }
    }
}
