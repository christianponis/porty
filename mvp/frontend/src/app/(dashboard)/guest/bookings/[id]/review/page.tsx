'use client';

import { useState, FormEvent } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import ReviewStars from '@/components/domain/ReviewStars';
import * as guestApi from '@/lib/api/guest';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

interface Criterion {
  key: string;
  label: string;
}

const criteria: Criterion[] = [
  { key: 'mooring', label: 'Ormeggio' },
  { key: 'services', label: 'Servizi' },
  { key: 'location', label: 'Posizione' },
  { key: 'value', label: 'Qualita/Prezzo' },
  { key: 'hospitality', label: 'Accoglienza' },
];

export default function GuestBookingReviewPage() {
  const params = useParams();
  const router = useRouter();
  const bookingId = Number(params.id);
  const { addToast } = useUIStore();

  const [ratings, setRatings] = useState<Record<string, number>>({});
  const [comment, setComment] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const setRating = (key: string, value: number) => {
    setRatings((prev) => ({ ...prev, [key]: value }));
  };

  const averageRating = () => {
    const values = Object.values(ratings);
    if (values.length === 0) return 0;
    return Math.round(values.reduce((sum, v) => sum + v, 0) / values.length);
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // Validate all criteria rated
    const unrated = criteria.filter((c) => !ratings[c.key]);
    if (unrated.length > 0) {
      addToast({
        type: 'warning',
        message: `Valuta tutti i criteri prima di inviare`,
      });
      return;
    }

    if (comment.length > 2000) {
      addToast({ type: 'warning', message: 'Il commento non puo superare i 2000 caratteri' });
      return;
    }

    try {
      setSubmitting(true);
      await guestApi.submitReview(bookingId, {
        rating: averageRating(),
        comment,
      });
      addToast({ type: 'success', message: 'Recensione inviata con successo! Grazie per il tuo feedback.' });
      router.push(`/guest/bookings/${bookingId}`);
    } catch {
      addToast({ type: 'error', message: 'Errore nell\'invio della recensione' });
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link
          href={`/guest/bookings/${bookingId}`}
          className="flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-white text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700"
        >
          <ArrowLeftIcon className="h-4 w-4" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-sky-900">Lascia una recensione</h1>
          <p className="text-sm text-slate-500">
            Condividi la tua esperienza per aiutare altri ospiti
          </p>
        </div>
      </div>

      {/* Form */}
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Rating criteria */}
        <div className="rounded-xl border border-sky-100 bg-white shadow-sm overflow-hidden">
          <div className="border-b border-sky-100 bg-gradient-to-r from-sky-800 to-sky-700 px-6 py-4">
            <h2 className="text-lg font-semibold text-white">Valutazione</h2>
            <p className="text-sm text-sky-200">Seleziona da 1 a 5 stelle per ogni criterio</p>
          </div>
          <div className="divide-y divide-sky-50">
            {criteria.map((criterion) => (
              <div
                key={criterion.key}
                className="flex items-center justify-between px-6 py-4"
              >
                <p className="text-sm font-medium text-slate-700">{criterion.label}</p>
                <ReviewStars
                  value={ratings[criterion.key] || 0}
                  onChange={(value) => setRating(criterion.key, value)}
                  size="lg"
                />
              </div>
            ))}
          </div>

          {/* Average */}
          {Object.keys(ratings).length > 0 && (
            <div className="border-t border-sky-100 bg-sky-50/50 px-6 py-3 flex items-center justify-between">
              <p className="text-sm font-semibold text-sky-800">Media complessiva</p>
              <div className="flex items-center gap-2">
                <ReviewStars value={averageRating()} readonly size="md" />
                <span className="text-lg font-bold text-sky-800">{averageRating()}/5</span>
              </div>
            </div>
          )}
        </div>

        {/* Comment */}
        <div className="rounded-xl border border-sky-100 bg-white p-6 shadow-sm space-y-3">
          <div className="flex items-center justify-between">
            <label htmlFor="comment" className="text-sm font-semibold text-slate-700">
              Commento
            </label>
            <span className={`text-xs ${comment.length > 2000 ? 'text-red-500' : 'text-slate-400'}`}>
              {comment.length}/2000
            </span>
          </div>
          <textarea
            id="comment"
            rows={5}
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            maxLength={2000}
            placeholder="Racconta la tua esperienza... Cosa ti e piaciuto? Cosa potrebbe essere migliorato?"
            className="block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm transition-colors placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:ring-offset-0"
          />
        </div>

        {/* Submit */}
        <div className="flex justify-end gap-3">
          <Link href={`/guest/bookings/${bookingId}`}>
            <Button variant="secondary" type="button">Annulla</Button>
          </Link>
          <Button type="submit" loading={submitting}>
            Invia recensione
          </Button>
        </div>
      </form>
    </div>
  );
}
