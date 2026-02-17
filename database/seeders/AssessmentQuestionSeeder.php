<?php

namespace Database\Seeders;

use App\Enums\AssessmentCategory;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Seeder;

class AssessmentQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // --- Infrastructure (4 domande) ---
            [
                'category' => AssessmentCategory::Infrastructure,
                'question_text' => 'Qual è lo stato di conservazione del pontile e delle passerelle di accesso?',
                'question_type' => 'scale_1_5',
                'requires_photo' => true,
                'weight' => 2.0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Infrastructure,
                'question_text' => 'Le colonnine elettriche sono funzionanti e conformi alle normative?',
                'question_type' => 'boolean',
                'requires_photo' => true,
                'weight' => 1.5,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Infrastructure,
                'question_text' => 'Il sistema di erogazione acqua è efficiente e privo di perdite?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 1.5,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Infrastructure,
                'question_text' => 'Qual è la qualità e lo stato delle bitte e degli anelli di ormeggio?',
                'question_type' => 'scale_1_5',
                'requires_photo' => true,
                'weight' => 1.0,
                'sort_order' => 4,
                'is_active' => true,
            ],

            // --- Services (3 domande) ---
            [
                'category' => AssessmentCategory::Services,
                'question_text' => 'È disponibile una connessione Wi-Fi accessibile dall\'ormeggio?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 0.8,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Services,
                'question_text' => 'Con quale frequenza e qualità viene effettuata la raccolta rifiuti?',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Services,
                'question_text' => 'Quanto è vicina e accessibile la stazione di rifornimento carburante?',
                'question_type' => 'scale_1_10',
                'requires_photo' => false,
                'weight' => 0.8,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // --- Security (3 domande) ---
            [
                'category' => AssessmentCategory::Security,
                'question_text' => 'È presente un sistema di videosorveglianza CCTV funzionante?',
                'question_type' => 'boolean',
                'requires_photo' => true,
                'weight' => 1.5,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Security,
                'question_text' => 'È previsto un servizio di guardiania notturna o presidio h24?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 1.5,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Security,
                'question_text' => 'Valutare la presenza e lo stato delle attrezzature antincendio (estintori, idranti).',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // --- Location (4 domande) ---
            [
                'category' => AssessmentCategory::Location,
                'question_text' => 'Quanto è facile l\'accesso all\'ormeggio dalla viabilità principale?',
                'question_type' => 'scale_1_10',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Location,
                'question_text' => 'Il posto barca è ben riparato dal moto ondoso e dalle correnti?',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 1.5,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Location,
                'question_text' => 'Quanto dista l\'ormeggio dal centro abitato e dai servizi essenziali?',
                'question_type' => 'scale_1_10',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Location,
                'question_text' => 'Valutare la qualità paesaggistica e la bellezza del contesto circostante.',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 0.5,
                'sort_order' => 4,
                'is_active' => true,
            ],

            // --- LandServices (3 domande) ---
            [
                'category' => AssessmentCategory::LandServices,
                'question_text' => 'È disponibile un parcheggio dedicato o nelle vicinanze dell\'ormeggio?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::LandServices,
                'question_text' => 'Qual è la qualità e la pulizia delle docce e dei servizi igienici?',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 1.2,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::LandServices,
                'question_text' => 'Sono presenti ristoranti, bar o servizi di ristorazione nelle vicinanze?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 0.5,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // --- Sustainability (3 domande) ---
            [
                'category' => AssessmentCategory::Sustainability,
                'question_text' => 'È attivo un sistema di raccolta differenziata dei rifiuti nel marina?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 1.0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Sustainability,
                'question_text' => 'Il marina utilizza fonti di energia rinnovabile (pannelli solari, eolico)?',
                'question_type' => 'boolean',
                'requires_photo' => false,
                'weight' => 0.8,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category' => AssessmentCategory::Sustainability,
                'question_text' => 'Il marina possiede certificazioni ambientali (Bandiera Blu, ISO 14001, EMAS)?',
                'question_type' => 'scale_1_5',
                'requires_photo' => false,
                'weight' => 1.2,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($questions as $question) {
            AssessmentQuestion::create($question);
        }
    }
}
