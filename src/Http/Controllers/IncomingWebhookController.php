<?php

namespace Meema\MediaRecognition\Http\Controllers;

use Aws\Sns\Message;
use Illuminate\Routing\Controller;
use Meema\MediaRecognition\Events\VideoFacialAnalysisCompleted;
use Meema\MediaRecognition\Events\VideoLabelAnalysisCompleted;
use Meema\MediaRecognition\Events\VideoModerationAnalysisCompleted;
use Meema\MediaRecognition\Events\VideoTextAnalysisCompleted;
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
                event(new VideoLabelAnalysisCompleted($message));
                break;
            case 'faces':
                Recognize::getFacesByJobId($message['JobId'], $mediaId);
                event(new VideoFacialAnalysisCompleted($message));
                break;
            case 'moderation':
                Recognize::getContentModerationByJobId($message['JobId'], $mediaId);
                event(new VideoModerationAnalysisCompleted($message));
                break;
            case 'ocr':
                Recognize::getTextDetectionByJobId($message['JobId'], $mediaId);
                event(new VideoTextAnalysisCompleted($message));
                break;
            default:
                throw new \Exception();
        }
    }
}
