<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\Berth;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentAnswer;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SelfAssessmentController extends Controller
{
    public function __construct(
        private RatingService $ratingService,
    ) {}

    public function show(Berth $berth)
    {
        $this->authorizeOwner($berth);

        $assessment = $berth->selfAssessment?->load('answers.question');
        $questions = AssessmentQuestion::active()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('owner.assessments.show', compact('berth', 'assessment', 'questions'));
    }

    public function store(Request $request, Berth $berth)
    {
        $this->authorizeOwner($berth);

        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:assessment_questions,id',
            'answers.*.answer_value' => 'required|integer|min:0|max:10',
            'photos.*' => 'nullable|image|max:2048',
        ]);

        $assessment = SelfAssessment::updateOrCreate(
            ['berth_id' => $berth->id],
            ['owner_id' => Auth::id(), 'status' => 'draft'],
        );

        foreach ($request->answers as $index => $answerData) {
            $photoPath = null;

            if ($request->hasFile("photos.{$answerData['question_id']}")) {
                $photoPath = $request->file("photos.{$answerData['question_id']}")
                    ->store("assessments/{$berth->id}", 'public');
            }

            SelfAssessmentAnswer::updateOrCreate(
                [
                    'self_assessment_id' => $assessment->id,
                    'question_id' => $answerData['question_id'],
                ],
                [
                    'answer_value' => $answerData['answer_value'],
                    'photo_path' => $photoPath ?? SelfAssessmentAnswer::where('self_assessment_id', $assessment->id)
                        ->where('question_id', $answerData['question_id'])
                        ->value('photo_path'),
                ],
            );
        }

        $this->ratingService->submitSelfAssessment($assessment->fresh());

        return redirect()->route('owner.berths.show', $berth)
            ->with('success', 'Autovalutazione completata! Le tue Ancore Grigie sono state aggiornate.');
    }

    private function authorizeOwner(Berth $berth): void
    {
        if ($berth->owner_id !== Auth::id()) {
            abort(403, 'Non autorizzato.');
        }
    }
}
