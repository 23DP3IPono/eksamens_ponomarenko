<!-- Login.vue -->
<template>
  <v-container class="auth" fluid>
    <div class="card">
      <h1>Pieslēgties</h1>
      <p class="sub">Ievadi e-pastu un paroli, lai turpinātu</p>

      <v-text-field
        v-model="email"
        label="E-pasts"
        type="email"
        variant="outlined"
        :error-messages="errors.email"
      />

      <v-text-field
        v-model="password"
        label="Parole"
        type="password"
        variant="outlined"
        :error-messages="errors.password"
      />

      <div v-if="serverError" class="error">{{ serverError }}</div>

      <v-btn
        class="submit"
        :loading="loading"
        @click="submit"
      >
        Pieslēgties
      </v-btn>

      <div class="link">
        Nav konta?
        <router-link to="/register">Reģistrēties</router-link>
      </div>
    </div>
  </v-container>
</template>

<script>
import api from "@/api";
import { useAuthStore } from "@/stores/auth";

export default {
  data() {
    return {
      email: "",
      password: "",
      errors: {},
      serverError: "",
      loading: false,
    };
  },
  methods: {
    async submit() {
      this.errors = {};
      this.serverError = "";

      // Client-side validation
      if (!this.email) this.errors.email = "E-pasts ir obligāts";
      else if (!/^\S+@\S+\.\S+$/.test(this.email))
        this.errors.email = "E-pasta formāts nav pareizs";
      if (!this.password) this.errors.password = "Parole ir obligāta";
      if (Object.keys(this.errors).length) return;

      this.loading = true;
      try {
        const data = await api.login({
          email: this.email,
          password: this.password,
        });
        const auth = useAuthStore();
        auth.setAuth(data.user, data.token);
        this.$router.push("/");
      } catch (err) {
        if (err.status === 422 && err.data?.errors) {
          this.errors = {};
          for (const k of Object.keys(err.data.errors)) {
            this.errors[k] = err.data.errors[k][0];
          }
        } else {
          this.serverError = err.message;
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.auth {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}
.card {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 45px;
  max-width: 440px;
  width: 100%;
  color: white;
}
.card h1 {
  font-size: 32px;
  font-weight: 900;
  margin-bottom: 6px;
}
.sub {
  opacity: 0.7;
  margin-bottom: 25px;
}
.error {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}
.submit {
  width: 100%;
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 14px;
  margin-top: 10px;
}
.link {
  margin-top: 20px;
  text-align: center;
  opacity: 0.8;
}
.link a {
  color: #f59e0b;
  margin-left: 5px;
  font-weight: 700;
}
</style>