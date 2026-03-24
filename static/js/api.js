const API_BASE = window.API_BASE || "/Projet-final-PHP/api";

export async function apiFetch(endpoint, options = {}) {
  const base = API_BASE.replace(/\/$/, '');
  const url = endpoint.startsWith('http') ? endpoint : `${base}/${endpoint.replace(/^\//, '')}`;
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json" },
    ...options,
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.error || "Une erreur est survenue");
  }

  return data;
}

export function apiGet(endpoint) {
  return apiFetch(endpoint);
}

export function apiPost(endpoint, body) {
  return apiFetch(endpoint, {
    method: "POST",
    body: JSON.stringify(body),
  });
}

export function apiPut(endpoint, body) {
  return apiFetch(endpoint, {
    method: "PUT",
    body: JSON.stringify(body),
  });
}

export function apiDelete(endpoint, body) {
  return apiFetch(endpoint, {
    method: "DELETE",
    body: JSON.stringify(body),
  });
}
