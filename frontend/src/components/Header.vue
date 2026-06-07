<template>
  <header class="header">
    <div class="logo" @click="goHome">
      <AppLogo :size="40" :show-text="true" />
    </div>

    <button class="burger" @click="menuOpen = !menuOpen" aria-label="Menu">
      <span :class="{ open: menuOpen }"></span>
      <span :class="{ open: menuOpen }"></span>
      <span :class="{ open: menuOpen }"></span>
    </button>

    <nav class="nav" :class="{ 'nav--open': menuOpen }" @click="menuOpen = false">
      <router-link to="/">Sākums</router-link>
      <router-link to="/services">Ceļojumi</router-link>
      <router-link to="/stats">Statistika</router-link>
      <router-link to="/contact">Kontakti</router-link>

      <template v-if="!auth.isLoggedIn">
        <router-link to="/login">Pieslēgties</router-link>
        <router-link to="/register" class="register-link">Reģistrēties</router-link>
      </template>

      <template v-else>
        <router-link to="/my-trips">Mani ceļojumi</router-link>
        <router-link v-if="auth.isAdmin" to="/admin" class="admin-link">🛡 Admin</router-link>
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
import AppLogo from "@/components/AppLogo.vue";

export default {
  components: {
    AppLogo,
  },
  data() {
    return { menuOpen: false };
  },
  computed: {
    auth() {
      return useAuthStore();
    },
  },
  watch: {
    $route() {
      this.menuOpen = false;
    },
  },
  methods: {
    goHome() {
      this.menuOpen = false;
      this.$router.push("/");
    },
    async logout() {
      try {
        await api.logout();
      } catch (_) {}
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
}

.nav {
  display: flex;
  gap: 22px;
  align-items: center;
  flex-wrap: wrap;
}

.nav .admin-link {
  background: linear-gradient(135deg, #8b5cf6, #6366f1);
  color: white !important;
  padding: 8px 14px !important;
  border-radius: 999px;
}
.nav .admin-link::after {
  display: none;
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

.burger {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 28px;
  height: 22px;
  background: transparent;
  border: none;
  cursor: pointer;
  z-index: 1100;
}
.burger span {
  display: block;
  width: 100%;
  height: 3px;
  background: white;
  border-radius: 2px;
  transition: transform 0.3s, opacity 0.3s;
  transform-origin: center;
}
.burger span.open:nth-child(1) {
  transform: translateY(9.5px) rotate(45deg);
}
.burger span.open:nth-child(2) {
  opacity: 0;
}
.burger span.open:nth-child(3) {
  transform: translateY(-9.5px) rotate(-45deg);
}

@media (max-width: 1100px) {
  .header {
    padding: 15px 25px;
  }
  .logo {
    font-size: 18px;
  }
  .nav {
    gap: 16px;
  }
}

@media (max-width: 700px) {
  .header {
    padding: 12px 18px;
  }
  .burger {
    display: flex;
  }
  .nav {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    align-items: stretch;
    background: rgba(11, 15, 26, 0.97);
    backdrop-filter: blur(12px);
    padding: 20px;
    gap: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transform: translateY(-130%);
    transition: transform 0.3s ease;
  }
  .nav--open {
    transform: translateY(0);
  }
  .nav a, .nav span {
    text-align: center;
  }
  .nav a::after {
    display: none;
  }
}
</style>