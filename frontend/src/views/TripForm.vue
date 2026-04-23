<!-- TripForm.vue-->
<template>
  <v-container class="form-page" fluid>
    <v-btn class="back-btn" @click="$router.back()">← Atpakaļ</v-btn>

    <div class="card">
      <h1>{{ isEdit ? "Rediģēt ceļojumu" : "Jauns ceļojums" }}</h1>
      <p class="sub">Aizpildi visus laukus, lai {{ isEdit ? "saglabātu izmaiņas" : "izveidotu jaunu ceļojumu" }}.</p>

      <div v-if="loading" class="state">Ielādē...</div>

      <template v-else>
        <v-text-field
          v-model="form.nosaukums"
          label="Nosaukums"
          variant="outlined"
          :error-messages="errors.nosaukums"
          maxlength="100"
          counter
        />

        <v-text-field
          v-model="form.galamerkis"
          label="Galamērķis"
          variant="outlined"
          :error-messages="errors.galamerkis"
          maxlength="100"
          counter
        />

        <div class="row">
          <v-text-field
            v-model="form.sakuma_datums"
            label="Sākuma datums"
            type="date"
            variant="outlined"
            :error-messages="errors.sakuma_datums"
          />
          <v-text-field
            v-model="form.beigu_datums"
            label="Beigu datums"
            type="date"
            variant="outlined"
            :error-messages="errors.beigu_datums"
          />
        </div>

        <v-text-field
          v-model.number="form.budzets"
          label="Budžets (€)"
          type="number"
          min="0"
          step="0.01"
          variant="outlined"
          :error-messages="errors.budzets"
        />

        <div v-if="serverError" class="error">{{ serverError }}</div>

        <div class="actions">
          <v-btn class="submit" :loading="saving" @click="submit">
            {{ isEdit ? "Saglabāt" : "Izveidot ceļojumu" }}
          </v-btn>
          <v-btn class="cancel" @click="$router.back()">Atcelt</v-btn>
        </div>
      </template>
    </div>
  </v-container>
</template>

<script>
import api from "@/api";
import { useAuthStore } from "@/stores/auth";

export default {
  data() {
    return {
      form: {
        nosaukums: "",
        galamerkis: "",
        sakuma_datums: "",
        beigu_datums: "",
        budzets: null,
      },
      errors: {},
      serverError: "",
      loading: false,
      saving: false,
    };
  },
  computed: {
    isEdit() {
      return !!this.$route.params.id;
    },
  },
  async mounted() {
    // Redirect guests away — only registered users should see this page
    const auth = useAuthStore();
    if (!auth.isLoggedIn) {
      this.$router.push("/login");
      return;
    }

    // If editing, load the existing trip data
    if (this.isEdit) {
      this.loading = true;
      try {
        const trip = await api.getTrip(this.$route.params.id);
        // Check ownership on frontend too (backend will also enforce)
        if (trip.lietotajs_id !== auth.user.id) {
          alert("Nav tiesību rediģēt šo ceļojumu");
          this.$router.push("/services");
          return;
        }
        this.form.nosaukums = trip.nosaukums;
        this.form.galamerkis = trip.galamerkis;
        this.form.sakuma_datums = trip.sakuma_datums;
        this.form.beigu_datums = trip.beigu_datums;
        this.form.budzets = Number(trip.budzets);
      } catch (err) {
        this.serverError = err.message;
      } finally {
        this.loading = false;
      }
    }
  },
  methods: {
    validate() {
      this.errors = {};

      if (!this.form.nosaukums) this.errors.nosaukums = "Nosaukums ir obligāts";
      else if (this.form.nosaukums.length > 100)
        this.errors.nosaukums = "Nosaukums pārāk garš";

      if (!this.form.galamerkis) this.errors.galamerkis = "Galamērķis ir obligāts";

      if (!this.form.sakuma_datums)
        this.errors.sakuma_datums = "Sākuma datums ir obligāts";
      else if (!this.isEdit) {
        // For new trips, start date must be today or later
        const today = new Date().toISOString().slice(0, 10);
        if (this.form.sakuma_datums < today)
          this.errors.sakuma_datums = "Sākuma datumam jābūt šodien vai vēlāk";
      }

      if (!this.form.beigu_datums)
        this.errors.beigu_datums = "Beigu datums ir obligāts";
      else if (
        this.form.sakuma_datums &&
        this.form.beigu_datums < this.form.sakuma_datums
      )
        this.errors.beigu_datums = "Beigu datumam jābūt pēc sākuma datuma";

      if (this.form.budzets === null || this.form.budzets === "")
        this.errors.budzets = "Budžets ir obligāts";
      else if (this.form.budzets < 0)
        this.errors.budzets = "Budžets nevar būt negatīvs";

      return Object.keys(this.errors).length === 0;
    },
    async submit() {
      this.serverError = "";
      if (!this.validate()) return;

      this.saving = true;
      try {
        let trip;
        if (this.isEdit) {
          trip = await api.updateTrip(this.$route.params.id, this.form);
        } else {
          trip = await api.createTrip(this.form);
        }
        this.$router.push(`/services/${trip.celojuma_id}`);
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
        this.saving = false;
      }
    },
  },
};
</script>

<style scoped>
.form-page {
  padding: 40px 20px;
  color: white;
}
.back-btn {
  margin-bottom: 20px;
  border-radius: 999px;
}
.card {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 45px;
  max-width: 700px;
  margin: 0 auto;
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
.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}
.state {
  text-align: center;
  padding: 40px;
  color: #aaa;
}
.error {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 15px;
}
.actions {
  display: flex;
  gap: 15px;
  margin-top: 15px;
}
.submit {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 14px 30px;
  flex: 1;
}
.cancel {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  border-radius: 999px;
  padding: 14px 30px;
}
@media (max-width: 700px) {
  .row {
    grid-template-columns: 1fr;
  }
}
</style>