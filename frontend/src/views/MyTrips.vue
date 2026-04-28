<template>
  <v-container class="my-trips" fluid>
    <section class="hero">
      <div>
        <h1>Mani ceļojumi</h1>
        <p>Tavi personīgi saglabātie ceļojumu plāni.</p>
      </div>
      <v-btn class="new-btn" @click="$router.push('/services/new')">
        + Jauns ceļojums
      </v-btn>
    </section>

    <Loader v-if="loading" text="Ielādē tavus ceļojumus..." />
    <div v-else-if="error" class="state error">
      ⚠️ Neizdevās ielādēt tavus ceļojumus.
    </div>

    <template v-else>
      <section v-if="favorites.length > 0" class="fav-section">
        <h2 class="section-title">⭐ Iecienītie ceļojumi</h2>
        <div class="cards">
          <div
            class="card"
            v-for="t in favorites"
            :key="'fav-' + t.celojuma_id"
          >
            <FavoriteButton
              :trip-id="t.celojuma_id"
              :initial="true"
              class="card__fav"
              @change="onFavoriteChange(t.celojuma_id, $event)"
            />
            <div class="card__main" @click="$router.push('/services/' + t.celojuma_id)">
              <div class="card__icon">⭐</div>
              <div class="card__name">{{ t.nosaukums }}</div>
              <div class="card__desc">{{ t.galamerkis }}</div>
              <div class="card__dates">
                {{ formatDate(t.sakuma_datums) }} – {{ formatDate(t.beigu_datums) }}
              </div>
              <div class="card__price">€ {{ t.budzets }}</div>
            </div>
          </div>
        </div>
      </section>

      <h2 v-if="trips.length > 0" class="section-title">📌 Mani izveidotie</h2>

      <EmptyState
        v-if="trips.length === 0 && favorites.length === 0"
        icon="✈️"
        title="Vēl nav neviena ceļojuma"
        subtitle="Sāc ceļojumu plānošanu, izveidojot savu pirmo braucienu!"
        actionText="+ Jauns ceļojums"
        @action="$router.push('/services/new')"
      />

      <section v-else-if="trips.length > 0" class="cards">
        <div
          class="card"
          v-for="t in trips"
          :key="t.celojuma_id"
        >
          <div class="card__main" @click="$router.push('/services/' + t.celojuma_id)">
            <div class="card__icon">🌍</div>
            <div class="card__name">{{ t.nosaukums }}</div>
            <div class="card__desc">{{ t.galamerkis }}</div>
            <div class="card__dates">
              {{ formatDate(t.sakuma_datums) }} – {{ formatDate(t.beigu_datums) }}
            </div>
            <div class="card__price">€ {{ t.budzets }}</div>
          </div>

          <div class="card__actions">
            <v-btn
              class="btn btn--edit"
              @click.stop="$router.push('/services/' + t.celojuma_id + '/edit')"
            >
              ✏️ Rediģēt
            </v-btn>
            <v-btn
              class="btn btn--delete"
              :loading="deletingId === t.celojuma_id"
              @click.stop="confirmDelete(t)"
            >
              🗑 Dzēst
            </v-btn>
          </div>
        </div>
      </section>
    </template>

    <v-dialog v-model="confirmDialog" max-width="420">
      <div class="dialog">
        <h3>Apstiprināt dzēšanu</h3>
        <p>
          Vai tiešām vēlies dzēst ceļojumu
          <strong>"{{ toDelete?.nosaukums }}"</strong>?
          Šī darbība ir neatgriezeniska.
        </p>
        <div class="dialog__actions">
          <v-btn class="btn" @click="confirmDialog = false">Atcelt</v-btn>
          <v-btn class="btn btn--delete" @click="doDelete">Dzēst</v-btn>
        </div>
      </div>
    </v-dialog>
  </v-container>
</template>

<script>
import api from "@/api";
import Loader from "@/components/Loader.vue";
import EmptyState from "@/components/EmptyState.vue";
import FavoriteButton from "@/components/FavoriteButton.vue";
import { useAuthStore } from "@/stores/auth";

export default {
  components: { Loader, EmptyState, FavoriteButton },
  data() {
    return {
      trips: [],
      favorites: [],
      loading: true,
      error: null,
      deletingId: null,
      confirmDialog: false,
      toDelete: null,
    };
  },
  async mounted() {
    const auth = useAuthStore();
    if (!auth.isLoggedIn) {
      this.$router.push("/login");
      return;
    }

    await Promise.all([this.load(), this.loadFavorites()]);
    this.loading = false;
  },
  methods: {
    async load() {
      try {
        this.trips = await api.getMyTrips({ sort_by: "sakuma_datums", sort_dir: "desc" });
      } catch (err) {
        this.error = err.message;
      }
    },
    async loadFavorites() {
      try {
        this.favorites = await api.getFavorites();
      } catch (err) {
        console.error("Favorites load failed:", err);
      }
    },
    onFavoriteChange(tripId, isFav) {
      if (!isFav) {
        this.favorites = this.favorites.filter(t => t.celojuma_id !== tripId);
      }
    },
    confirmDelete(trip) {
      this.toDelete = trip;
      this.confirmDialog = true;
    },
    async doDelete() {
      if (!this.toDelete) return;
      this.deletingId = this.toDelete.celojuma_id;
      this.confirmDialog = false;
      try {
        await api.deleteTrip(this.toDelete.celojuma_id);
        this.trips = this.trips.filter(t => t.celojuma_id !== this.toDelete.celojuma_id);
        this.favorites = this.favorites.filter(t => t.celojuma_id !== this.toDelete.celojuma_id);
      } catch (err) {
        alert("Kļūda dzēšot: " + err.message);
      } finally {
        this.deletingId = null;
        this.toDelete = null;
      }
    },
    formatDate(d) {
      if (!d) return "";
      return new Date(d).toLocaleDateString("lv-LV");
    },
  },
};
</script>

<style scoped>
.my-trips {
  padding: 40px 20px;
  color: white;
}
.hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  padding: 40px;
  border-radius: 20px;
  margin-bottom: 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}
.hero h1 {
  font-size: 36px;
  font-weight: 900;
}
.hero p {
  opacity: 0.8;
}
.new-btn {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #111;
  font-weight: 800;
  border-radius: 999px;
  padding: 14px 24px;
}
.state {
  text-align: center;
  padding: 60px 20px;
  color: #aaa;
}
.state.error {
  color: #ff6b6b;
}
.fav-section {
  margin-bottom: 30px;
}
.section-title {
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 15px;
  color: white;
}
.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}
.card {
  position: relative;
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.card__fav {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
}
.card__main {
  cursor: pointer;
  text-align: center;
  transition: transform 0.35s ease;
}
.card__main:hover {
  transform: translateY(-6px);
}
.card__icon {
  font-size: 30px;
  margin-bottom: 10px;
}
.card__name {
  font-weight: 900;
  font-size: 20px;
}
.card__desc {
  opacity: 0.8;
  margin: 6px 0;
}
.card__dates {
  font-size: 13px;
  opacity: 0.7;
  margin-bottom: 6px;
}
.card__price {
  color: #f59e0b;
  font-weight: 900;
  font-size: 18px;
}
.card__actions {
  display: flex;
  gap: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 12px;
}
.btn {
  flex: 1;
  border-radius: 999px;
  font-weight: 700;
}
.btn--edit {
  background: rgba(255, 255, 255, 0.08);
  color: white;
}
.btn--delete {
  background: #ef4444;
  color: white;
}
.dialog {
  background: #0b0f1a;
  color: white;
  padding: 30px;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.12);
}
.dialog h3 {
  font-size: 22px;
  font-weight: 900;
  margin-bottom: 10px;
}
.dialog p {
  opacity: 0.85;
  margin-bottom: 20px;
}
.dialog__actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
@media (max-width: 900px) {
  .cards {
    grid-template-columns: 1fr;
  }
}
</style>