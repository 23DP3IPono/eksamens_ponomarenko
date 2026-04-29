import { useAuthStore } from "@/stores/auth";

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

async function request(path, options = {}) {
  const auth = useAuthStore();

  const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    ...(options.headers || {}),
  };

  if (auth.token) {
    headers.Authorization = `Bearer ${auth.token}`;
  }

  const res = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers,
  });

  const data = await res.json().catch(() => null);

  if (!res.ok) {
    const err = new Error(data?.message || "Kļūda pieprasījumā");
    err.status = res.status;
    err.data = data;
    throw err;
  }

  return data;
}

export const api = {
  // === Auth ===
  register: (data) =>
    request(`/register`, { method: "POST", body: JSON.stringify(data) }),
  login: (data) =>
    request(`/login`, { method: "POST", body: JSON.stringify(data) }),
  logout: () => request(`/logout`, { method: "POST" }),
  me: () => request(`/me`),

  // === Trips ===
  getTrips: (params = {}) => {
    const qs = new URLSearchParams(params).toString();
    return request(`/celojumi${qs ? "?" + qs : ""}`);
  },
  getMyTrips: (params = {}) => {
    const qs = new URLSearchParams({ ...params, mine: 1 }).toString();
    return request(`/celojumi?${qs}`);
  },
  getTrip: (id) => request(`/celojumi/${id}`),
  createTrip: (data) =>
    request(`/celojumi`, { method: "POST", body: JSON.stringify(data) }),
  updateTrip: (id, data) =>
    request(`/celojumi/${id}`, { method: "PUT", body: JSON.stringify(data) }),
  deleteTrip: (id) => request(`/celojumi/${id}`, { method: "DELETE" }),
  getTripStats: () => request(`/celojumi/stats`),

  // === Contact messages ===
  sendMessage: (data) =>
    request(`/messages`, { method: "POST", body: JSON.stringify(data) }),

  // === Expenses ===
  createExpense: (data) =>
    request(`/izdevumi`, { method: "POST", body: JSON.stringify(data) }),
  updateExpense: (id, data) =>
    request(`/izdevumi/${id}`, { method: "PUT", body: JSON.stringify(data) }),
  deleteExpense: (id) => request(`/izdevumi/${id}`, { method: "DELETE" }),

  // === Reservations ===
  createReservation: (data) =>
    request(`/rezervacijas`, { method: "POST", body: JSON.stringify(data) }),
  updateReservation: (id, data) =>
    request(`/rezervacijas/${id}`, { method: "PUT", body: JSON.stringify(data) }),
  deleteReservation: (id) => request(`/rezervacijas/${id}`, { method: "DELETE" }),

  // === Places ===
  getPlaces: (params = {}) => {
    const qs = new URLSearchParams(params).toString();
    return request(`/vietas${qs ? "?" + qs : ""}`);
  },
  getPlacesByCountry: (valsts) =>
    request(`/vietas?valsts=${encodeURIComponent(valsts)}`),

  createPlace: (data) =>
    request(`/vietas`, { method: "POST", body: JSON.stringify(data) }),

  // === Day points ===
  createDayPoint: (data) =>
    request(`/dienas-punkti`, { method: "POST", body: JSON.stringify(data) }),
  updateDayPoint: (id, data) =>
    request(`/dienas-punkti/${id}`, { method: "PUT", body: JSON.stringify(data) }),
  deleteDayPoint: (id) => request(`/dienas-punkti/${id}`, { method: "DELETE" }),

  // === Favorites ===
  getFavorites: () => request(`/favorites`),
  checkFavorite: (tripId) => request(`/favorites/check/${tripId}`),
  addFavorite: (tripId) =>
    request(`/favorites/${tripId}`, { method: "POST" }),
  removeFavorite: (tripId) =>
    request(`/favorites/${tripId}`, { method: "DELETE" }),

  // === Admin ===
  adminStats: () => request(`/admin/stats`),
  adminUsers: () => request(`/admin/users`),
  adminDeleteUser: (id) => request(`/admin/users/${id}`, { method: "DELETE" }),
  adminTrips: () => request(`/admin/trips`),
  adminDeleteTrip: (id) => request(`/admin/trips/${id}`, { method: "DELETE" }),
  adminMessages: () => request(`/admin/messages`),
  adminDeleteMessage: (id) => request(`/admin/messages/${id}`, { method: "DELETE" }),

};

export default api;