/**
 * Server-side API base URL.
 * In Docker: INTERNAL_API_URL=http://api:8000
 * Local dev: defaults to http://localhost:8001
 */
export const SERVER_API_URL =
  process.env.INTERNAL_API_URL || "http://localhost:8001";

export function serverApiUrl(path: string): string {
  return `${SERVER_API_URL}${path}`;
}
