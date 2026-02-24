/**
 * Admin API client (base URL /admin/api, with CSRF and credentials).
 */

const base = '/admin/api';

function getCsrfToken(): string {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return (meta?.getAttribute('content') ?? '') as string;
}

export async function adminFetch(
  path: string,
  options: RequestInit = {}
): Promise<Response> {
  const url = path.startsWith('http') ? path : `${base}${path.startsWith('/') ? path : `/${path}`}`;
  const headers: HeadersInit = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': getCsrfToken(),
    ...(options.headers as object),
  };
  return fetch(url, {
    ...options,
    credentials: 'same-origin',
    headers,
  });
}

export async function get<T = unknown>(path: string): Promise<T> {
  const r = await adminFetch(path, { method: 'GET' });
  if (!r.ok) throw new Error(await r.text());
  return r.json() as Promise<T>;
}

export async function put<T = unknown>(path: string, body: object): Promise<T> {
  const r = await adminFetch(path, {
    method: 'PUT',
    body: JSON.stringify(body),
  });
  if (!r.ok) throw new Error(await r.text());
  return r.json() as Promise<T>;
}

export async function post<T = unknown>(path: string, body?: object): Promise<T> {
  const r = await adminFetch(path, {
    method: 'POST',
    body: body ? JSON.stringify(body) : undefined,
  });
  if (!r.ok) throw new Error(await r.text());
  return r.json() as Promise<T>;
}
