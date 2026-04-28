<template>
  <header class="header">
    <div class="logo" @click="$router.push('/')">
      🌍 Ceļojumu plānotājs
    </div>

    <nav class="nav">
      <router-link to="/">Sākums</router-link>
      <router-link to="/services">Ceļojumi</router-link>
      <router-link to="/contact">Kontakti</router-link>
      <router-link to="/stats">Statistika</router-link>

      <template v-if="!auth.isLoggedIn">
        <router-link to="/login">Pieslēgties</router-link>
        <router-link to="/register" class="register-link">Reģistrēties</router-link>
      </template>

      <template v-else>
        <router-link to="/my-trips">Mani ceļojumi</router-link>
        <router-link to="/services/new" class="register-link">+ Jauns</router-link>
        <span class="user">👤 {{ auth.fullName }}</span>
        <a href="#" @click.prevent="logout">Iziet</a>
      </template>
    </nav>
  </header>
</template>

<script>
import { useAuthStore } from "@/stores/auth";
import api from "@/api";

export default {
  computed: {
    auth() {
      return useAuthStore();
    },
  },
  methods: {
    async logout() {
      try {
        await api.logout();
      } catch (_) {
      }
      this.auth.clearAuth();
      this.$router.push("/");
    },
  },
};
</script>

<style>
.header {
  position: sticky;
  top: 0;
  z-index: 1000;

  backdrop-filter: blur(12px);
  background: rgba(15, 23, 42, 0.85);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);

  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 50px;
  transition: background 0.3s ease, box-shadow 0.3s ease;
}

.header:hover {
  background: rgba(15, 23, 42, 0.95);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.logo {
  font-weight: 900;
  font-size: 22px;
  color: white;
  letter-spacing: 1px;
  cursor: pointer;
  transition: color 0.3s ease, transform 0.3s ease;
}

.logo:hover {
  color: #f59e0b;
  transform: scale(1.05);
}

.nav {
  display: flex;
  gap: 25px;
  align-items: center;
}

.nav a {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-weight: 600;
  position: relative;
  transition: 0.3s ease;
  padding: 5px 0;
}

.nav a:hover {
  color: #f59e0b;
}

.nav a.router-link-active {
  color: #f59e0b;
}

.nav a::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  height: 2px;
  width: 0%;
  background: #f59e0b;
  border-radius: 2px;
  transition: 0.3s ease;
}

.nav a:hover::after,
.nav a.router-link-active::after {
  width: 100%;
}

.nav .register-link {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111 !important;
  padding: 8px 16px !important;
  border-radius: 999px;
}

.nav .register-link::after {
  display: none;
}

.nav .user {
  color: #f59e0b;
  font-weight: 700;
}
</style>