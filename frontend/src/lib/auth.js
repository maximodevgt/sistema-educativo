export const API = 'http://localhost:8000/api';

export function getToken() {
  return localStorage.getItem('token');
}

export function getUsuario() {
  const raw = localStorage.getItem('usuario');
  return raw ? JSON.parse(raw) : null;
}

export function setSesion(token, usuario) {
  localStorage.setItem('token', token);
  localStorage.setItem('usuario', JSON.stringify(usuario));
}

export function limpiarSesion() {
  localStorage.removeItem('token');
  localStorage.removeItem('usuario');
}

/** Fusiona cambios (ej. foto_url) en el usuario guardado y los persiste. */
export function actualizarUsuario(cambios) {
  const usuario = { ...(getUsuario() ?? {}), ...cambios };
  localStorage.setItem('usuario', JSON.stringify(usuario));
  return usuario;
}

/** Si no hay sesión, manda al login de inmediato. Llamar al inicio de cada pantalla protegida. */
export function requireAuth() {
  if (!getToken()) {
    window.location.href = '/';
  }
}

/** fetch hacia la API con el token de sesión. Si el servidor responde 401, cierra sesión y manda al login. */
export async function apiFetch(path, options = {}) {
  const token = getToken();
  const esFormData = options.body instanceof FormData;
  const headers = {
    Accept: 'application/json',
    ...(options.body && !esFormData ? { 'Content-Type': 'application/json' } : {}),
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
    ...(options.headers || {}),
  };

  const res = await fetch(`${API}${path}`, { ...options, headers });

  if (res.status === 401) {
    limpiarSesion();
    window.location.href = '/';
    throw new Error('No autenticado');
  }

  return res;
}

export async function cerrarSesion() {
  try {
    await apiFetch('/logout', { method: 'POST' });
  } catch {
    // si falla la llamada, igual cerramos la sesión local
  }
  limpiarSesion();
  window.location.href = '/';
}
