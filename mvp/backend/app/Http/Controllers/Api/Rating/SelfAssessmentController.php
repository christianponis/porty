<?php

namespace App\Http\Controllers\Api\Rating;

use App\Enums\AssessmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSelfAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Berth;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentAnswer;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SelfAssessmentController extends Controller
{
    public function __construct(
        private RatingService $ratingService,
    ) {}

    /**
     * Show the current self-assessment for a berth (for owners).
     */
    public function show(Berth $berth): JsonResponse
    {
        $user = auth('api')->user();

        if ($berth->owner_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        $assessment = $berth->selfAssessment;

        if (! $assessment) {
            return response()->json([
                'data' => null,
                'message' => 'Nessuna autovalutazione presente.',
            ]);
        }

        $assessment->load('answers.question');

        return response()->json([
            'data' => new AssessmentResource($assessment),
        ]);
    }

    /**
     * Submit self-assessment answers with optional photos (for owners).
     */
    public function store(StoreSelfAssessmentRequest $request, Berth $berth): JsonResponse
    {
        $user = auth('api')->user();

        if ($berth->owner_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        // Create or get existing assessment
        $assessment = SelfAssessment::updateOrCreate(
            ['berth_id' => $berth->id],
            [
                'owner_id' => $user->id,
                'status' => AssessmentStatus::Draft,
            ]
        );

        // Delete old answers
        $assessment->answers()->delete();

        // Create new answers
        foreach ($request->answers as $index => $answerData) {
            $photoPath = null;

            if (isset($answerData['photo']) && $answerData['photo']->isValid()) {
                $photoPath = $answerData['photo']->store(
                    "assessments/{$berth->id}",
                    'public'
                );
            }

            SelfAssessmentAnswer::create([
                'self_assessment_id' => $assessment->id,
                'question_id' => $answerData['question_id'],
                'answer_value' => $answerData['answer_value'],
                'photo_path' => $photoPath,
            ]);
        }

        // Submit and calculate score
        $assessment = $this->ratingService->submitSelfAssessment($assessment);
        $assessment->load('answers.question');

        return response()->json([
            'message' => 'Autovalutazione inviata con successo.',
            'data' => new AssessmentResource($assessment),
        ], 201);
    }
}
