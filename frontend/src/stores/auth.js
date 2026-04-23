import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    fullName: (state) =>
      state.user ? `${state.user.name} ${state.user.uzvards}` : "",
  },

  actions: {
    setAuth(user, token) {
      this.user = user;
      this.token = token;
    },
    clearAuth() {
      this.user = null;
      this.token = null;
    },
  },
});