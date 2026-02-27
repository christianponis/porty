'use client';

import { useEffect, useState, useCallback, FormEvent } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import * as ownerApi from '@/lib/api/owner';
import type { Assessment } from '@/lib/api/types';
import { ArrowLeftIcon, CameraIcon } from '@heroicons/react/24/outline';

interface QuestionGroup {
  group: string;
  questions: {
    key: string;
    label: string;
  }[];
}

const questionGroups: QuestionGroup[] = [
  {
    group: 'Infrastruttura',
    questions: [
      { key: 'infrastructure_dock', label: 'Stato della banchina e delle strutture di ormeggio' },
      { key: 'infrastructure_electrical', label: 'Impianto elettrico e colonnine di servizio' },
      { key: 'infrastructure_water', label: 'Impianto idrico e prese acqua' },
      { key: 'infrastructure_lighting', label: 'Illuminazione e visibilita notturna' },
    ],
  },
  {
    group: 'Servizi',
    questions: [
      { key: 'services_wifi', label: 'Connessione Wi-Fi e copertura segnale' },
      { key: 'services_waste', label: 'Gestione rifiuti e raccolta differenziata' },
      { key: 'services_fuel', label: 'Stazione rifornimento carburante' },
    ],
  },
  {
    group: 'Sicurezza',
    questions: [
      { key: 'safety_surveillance', label: 'Videosorveglianza e sistema di allarme' },
      { key: 'safety_access', label: 'Controllo accessi e recinzione' },
      { key: 'safety_firefighting', label: 'Attrezzatura antincendio' },
      { key: 'safety_first_aid', label: 'Kit di primo soccorso e defibrillatore' },
    ],
  },
];

export default function AssessmentPage() {
  const params = useParams();
  const router = useRouter();
  const berthId = Number(params.id);
  const { addToast } = useUIStore();

  const [assessment, setAssessment] = useState<Assessment | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [scores, setScores] = useState<Record<string, number>>({});
  const [photos, setPhotos] = useState<Record<string, File | null>>({});

  const fetchAssessment = useCallback(async () => {
    try {
      setLoading(true);
      const data = await ownerApi.getAssessment(berthId);
      setAssessment(data);
      // Pre-fill existing scores
      if (data.criteria?.length) {
        const existing: Record<string, number> = {};
        data.criteria.forEach((c) => {
          existing[c.key] = c.score;
        });
        setScores(existing);
      }
    } catch {
      // Assessment may not exist yet, that's OK
    } finally {
      setLoading(false);
    }
  }, [berthId]);

  useEffect(() => {
    fetchAssessment();
  }, [fetchAssessment]);

  const setScore = (key: string, value: number) => {
    setScores((prev) => ({ ...prev, [key]: value }));
  };

  const handlePhotoChange = (key: string, e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0] || null;
    setPhotos((prev) => ({ ...prev, [key]: file }));
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // Validate all questions answered
    const allKeys = questionGroups.flatMap((g) => g.questions.map((q) => q.key));
    const unanswered = allKeys.filter((key) => !scores[key]);
    if (unanswered.length > 0) {
      addToast({ type: 'warning', message: 'Rispondi a tutte le domande prima di inviare' });
      return;
    }

    try {
      setSubmitting(true);
      const criteria = Object.entries(scores).map(([key, score]) => ({ key, score }));
      await ownerApi.submitAssessment(berthId, { criteria });
      addToast({ type: 'success', message: 'Autovalutazione inviata con successo!' });
      router.push(`/owner/berths/${berthId}`);
    } catch {
      addToast({ type: 'error', message: 'Errore nell\'invio dell\'autovalutazione' });
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="mx-auto max-w-3xl space-y-6">
        <div className="h-8 w-64 animate-pulse rounded-lg bg-sky-50" />
        {[...Array(3)].map((_, i) => (
          <div key={i} className="h-64 animate-pulse rounded-xl bg-sky-50" />
        ))}
      </div>
    );
  }

  return (
    <div className="mx-auto max-w-3xl space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link
          href={`/owner/berths/${berthId}`}
          className="flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-white text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700"
        >
          <ArrowLeftIcon className="h-4 w-4" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-sky-900">Autovalutazione</h1>
          <p className="text-sm text-slate-500">
            Valuta il tuo posto barca per ottenere il punteggio Ancora
          </p>
        </div>
      </div>

      {/* Status banner */}
      {assessment && assessment.status === 'completed' && (
        <div className="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
          <p className="text-sm font-medium text-emerald-800">
            Autovalutazione completata il{' '}
            {assessment.completed_at
              ? new Date(assessment.completed_at).toLocaleDateString('it-IT')
              : '-'}
            . Punteggio: {assessment.anchor_rating}/5 ({assessment.anchor_level})
          </p>
          <p className="mt-1 text-xs text-emerald-600">
            Puoi aggiornare la tua autovalutazione in qualsiasi momento.
          </p>
        </div>
      )}

      {/* Form */}
      <form onSubmit={handleSubmit} className="space-y-6">
        {questionGroups.map((group) => (
          <div
            key={group.group}
            className="rounded-xl border border-sky-100 bg-white shadow-sm overflow-hidden"
          >
            <div className="border-b border-sky-100 bg-gradient-to-r from-sky-800 to-sky-700 px-6 py-4">
              <h2 className="text-lg font-semibold text-white">{group.group}</h2>
            </div>
            <div className="divide-y divide-sky-50">
              {group.questions.map((question) => (
                <div key={question.key} className="px-6 py-4 space-y-3">
                  <p className="text-sm font-medium text-slate-700">{question.label}</p>

                  {/* Score buttons */}
                  <div className="flex items-center gap-2">
                    {[1, 2, 3, 4, 5].map((value) => (
                      <button
                        key={value}
                        type="button"
                        onClick={() => setScore(question.key, value)}
                        className={`flex h-10 w-10 items-center justify-center rounded-lg text-sm font-semibold transition-all ${
                          scores[question.key] === value
                            ? 'bg-gradient-to-br from-sky-600 to-cyan-500 text-white shadow-md scale-110'
                            : 'border border-slate-200 bg-white text-slate-600 hover:border-sky-300 hover:bg-sky-50'
                        }`}
                      >
                        {value}
                      </button>
                    ))}
                    <span className="ml-2 text-xs text-slate-400">
                      {scores[question.key]
                        ? `${scores[question.key]}/5`
                        : 'Non valutato'}
                    </span>
                  </div>

                  {/* Photo upload */}
                  <div className="flex items-center gap-3">
                    <label className="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs text-slate-500 transition-colors hover:border-sky-300 hover:bg-sky-50 hover:text-sky-600">
                      <CameraIcon className="h-4 w-4" />
                      {photos[question.key]
                        ? photos[question.key]!.name
                        : 'Aggiungi foto (opzionale)'}
                      <input
                        type="file"
                        accept="image/*"
                        className="hidden"
                        onChange={(e) => handlePhotoChange(question.key, e)}
                      />
                    </label>
                    {photos[question.key] && (
                      <button
                        type="button"
                        onClick={() => setPhotos((prev) => ({ ...prev, [question.key]: null }))}
                        className="text-xs text-red-500 hover:text-red-700"
                      >
                        Rimuovi
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        ))}

        {/* Submit */}
        <div className="flex justify-end gap-3">
          <Link href={`/owner/berths/${berthId}`}>
            <Button variant="secondary" type="button">Annulla</Button>
          </Link>
          <Button type="submit" loading={submitting}>
            Invia autovalutazione
          </Button>
        </div>
      </form>
    </div>
  );
}
