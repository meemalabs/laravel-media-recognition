<?php

namespace Meema\MediaRecognition\Http\Controllers;

use Aws\Sns\Message;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Meema\MediaRecognition\Events\FacialAnalysisCompleted;
use Meema\MediaRecognition\Events\LabelAnalysisCompleted;
use Meema\MediaRecognition\Events\ModerationAnalysisCompleted;
use Meema\MediaRecognition\Events\TextAnalysisCompleted;
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
        $message = $this->ensureSubscriptionIsConfirmed();

        Log::info('incoming rekognition webhook message', $message);

        if (! array_key_exists('Status', $message)) {
            Log::alert('incoming rekognition webhook: "Status"-key does not exist');

            return;
        }

        if ($message['Status'] !== 'SUCCEEDED') {
            Log::alert('incoming rekognition webhook: "Status"-field value does not equal "SUCCEEDED"');

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
     * @param int|null $mediaId
     * @throws \Exception
     */
    public function fireEventFor(string $type, array $message, int $mediaId = null)
    {
        switch ($type) {
            case 'labels':
                Recognize::getLabelsByJobId($message['JobId'], $mediaId);
                event(new LabelAnalysisCompleted($message, $mediaId));
                break;
            case 'faces':
                Recognize::getFacesByJobId($message['JobId'], $mediaId);
                event(new FacialAnalysisCompleted($message, $mediaId));
                break;
            case 'moderation':
                Recognize::getContentModerationByJobId($message['JobId'], $mediaId);
                event(new ModerationAnalysisCompleted($message, $mediaId));
                break;
            case 'ocr':
                Recognize::getTextDetectionByJobId($message['JobId'], $mediaId);
                event(new TextAnalysisCompleted($message, $mediaId));
                break;
            default:
                throw new \Exception();
        }
    }

    /**
     * @return array
     */
    public function ensureSubscriptionIsConfirmed(): array
    {
        $message = Message::fromRawPostData()->toArray();

        if (array_key_exists('SubscribeURL', $message)) {
            Http::get($message['SubscribeURL']);
        }

        return json_decode($message['Message'], true);
    }
}
