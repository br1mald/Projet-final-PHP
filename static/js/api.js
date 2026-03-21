const API_BASE = "/final_project/api";

export async function apiFetch(endpoint, options = {}) {
  const response = await fetch(`${API_BASE}/${endpoint}`, {
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

export function apiDelete(endpoint) {
  return apiFetch(endpoint, { method: "DELETE" });
}
