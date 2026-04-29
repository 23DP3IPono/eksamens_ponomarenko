<template>
  <v-container class="services" fluid>
    <section class="hero">
      <div class="hero__text">
        <h1>Ceļojumi</h1>
        <p>Meklē, filtrē un plāno savu nākamo piedzīvojumu.</p>
      </div>
    </section>

    <section class="filters">
      <v-text-field
        v-model="search"
        label="Meklēt pēc nosaukuma vai galamērķa"
        variant="outlined"
        density="comfortable"
        clearable
        @update:modelValue="debouncedLoad"
      />

      <div class="filters__row">
        <v-text-field
          v-model.number="budgetMin"
          label="Budžets no (€)"
          type="number"
          variant="outlined"
          density="comfortable"
          @update:modelValue="debouncedLoad"
        />
        <v-text-field
          v-model.number="budgetMax"
          label="Budžets līdz (€)"
          type="number"
          variant="outlined"
          density="comfortable"
          @update:modelValue="debouncedLoad"
        />
        <v-text-field
          v-model="dateFrom"
          label="Datums no"
          type="date"
          variant="outlined"
          density="comfortable"
          @update:modelValue="loadTrips"
        />
        <v-text-field
          v-model="dateTo"
          label="Datums līdz"
          type="date"
          variant="outlined"
          density="comfortable"
          @update:modelValue="loadTrips"
        />
      </div>

      <div class="filters__row">
        <v-select
          v-model="sortBy"
          :items="sortOptions"
          label="Kārtot pēc"
          variant="outlined"
          density="comfortable"
          @update:modelValue="loadTrips"
        />
        <v-select
          v-model="sortDir"
          :items="[{ title: 'Augoši', value: 'asc' }, { title: 'Dilstoši', value: 'desc' }]"
          label="Secība"
          variant="outlined"
          density="comfortable"
          @update:modelValue="loadTrips"
        />
        <v-btn class="reset-btn" @click="resetFilters">Notīrīt filtrus</v-btn>
      </div>
    </section>

    <Loader v-if="loading" text="Meklē ceļojumus..." />
    <div v-else-if="error" class="state error">
      ⚠️ Neizdevās ielādēt datus. Pārbaudi savienojumu un mēģini vēlreiz.
    </div>
    <EmptyState
      v-else-if="trips.length === 0"
      icon="🔍"
      title="Neviens ceļojums neatbilst meklēšanas kritērijiem"
      subtitle="Pamēģini mainīt filtrus vai notīrīt meklēšanu."
      actionText="Notīrīt filtrus"
      @action="resetFilters"
    />

    <section v-else class="cards">
      <div
        class="card"
        v-for="t in trips"
        :key="t.celojuma_id"
      >
        <FavoriteButton :trip-id="t.celojuma_id" class="card__fav" />
        <div class="card__inner" @click="$router.push('/services/' + t.celojuma_id)">
          <div class="card__icon">🌍</div>
          <div class="card__name">{{ t.nosaukums }}</div>
          <div class="card__desc">{{ t.galamerkis }}</div>
          <div class="card__dates">
            {{ formatDate(t.sakuma_datums) }} – {{ formatDate(t.beigu_datums) }}
          </div>
          <div class="card__user" v-if="t.lietotajs">
            <small>👤 {{ t.lietotajs.name }} {{ t.lietotajs.uzvards }}</small>
          </div>
          <div class="card__price">€ {{ t.budzets }}</div>
        </div>
      </div>
    </section>
  </v-container>
</template>

<script>
import api from "@/api";
import Loader from "@/components/Loader.vue";
import EmptyState from "@/components/EmptyState.vue";
import FavoriteButton from "@/components/FavoriteButton.vue";

export default {
  components: { Loader, EmptyState, FavoriteButton },
  data() {
    return {
      trips: [],
      loading: false,
      error: null,

      search: "",
      budgetMin: null,
      budgetMax: null,
      dateFrom: "",
      dateTo: "",

      sortBy: "celojuma_id",
      sortDir: "asc",
      sortOptions: [
        { title: "ID", value: "celojuma_id" },
        { title: "Nosaukums", value: "nosaukums" },
        { title: "Galamērķis", value: "galamerkis" },
        { title: "Sākuma datums", value: "sakuma_datums" },
        { title: "Budžets", value: "budzets" },
      ],

      debounceTimer: null,
    };
  },

  mounted() {
    this.loadTrips();
  },

  methods: {
    async loadTrips() {
      this.loading = true;
      this.error = null;
      try {
        const params = {
          sort_by: this.sortBy,
          sort_dir: this.sortDir,
        };
        if (this.search) params.search = this.search;
        if (this.budgetMin !== null && this.budgetMin !== "") params.budget_min = this.budgetMin;
        if (this.budgetMax !== null && this.budgetMax !== "") params.budget_max = this.budgetMax;
        if (this.dateFrom) params.date_from = this.dateFrom;
        if (this.dateTo) params.date_to = this.dateTo;

        this.trips = await api.getTrips(params);
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    },

    debouncedLoad() {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => this.loadTrips(), 300);
    },

    resetFilters() {
      this.search = "";
      this.budgetMin = null;
      this.budgetMax = null;
      this.dateFrom = "";
      this.dateTo = "";
      this.sortBy = "celojuma_id";
      this.sortDir = "asc";
      this.loadTrips();
    },

    formatDate(d) {
      if (!d) return "";
      return new Date(d).toLocaleDateString("lv-LV");
    },
  },
};
</script>

<style scoped>
.services {
  padding: 40px 20px;
}

.hero {
  background: linear-gradient(135deg, #111827, #0f172a);
  padding: 60px 20px;
  border-radius: 20px;
  color: white;
  text-align: center;
  margin-bottom: 30px;
}

.hero h1 {
  font-size: 44px;
  font-weight: 900;
}

.hero p {
  opacity: 0.8;
  margin-top: 10px;
}

.filters {
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  padding: 20px;
  border-radius: 20px;
  margin-bottom: 25px;
  color: white;
}

.filters__row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
  margin-top: 12px;
}

.filters__row .reset-btn {
  grid-column: span 1;
  align-self: end;
  background: #ef4444;
  color: white;
  border-radius: 999px;
}

.state {
  text-align: center;
  padding: 40px;
  color: #aaa;
  font-size: 18px;
}
.state.error {
  color: #ff6b6b;
}

.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.card {
  position: relative;
  background: #0b0f1a;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 20px;
  padding: 25px;
  text-align: center;
  transition: transform 0.35s ease, box-shadow 0.35s ease, background 0.35s ease;
  color: white;
}

.card:hover {
  transform: translateY(-12px) scale(1.03);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
  background: rgba(255, 255, 255, 0.08);
}

.card__fav {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
}

.card__inner {
  cursor: pointer;
}

.card__icon {
  font-size: 30px;
  margin-bottom: 15px;
}

.card__name {
  font-weight: 900;
  font-size: 20px;
}

.card__desc {
  opacity: 0.8;
  margin: 10px 0;
}

.card__dates {
  font-size: 13px;
  opacity: 0.7;
  margin-bottom: 8px;
}

.card__user {
  opacity: 0.7;
  margin-bottom: 10px;
}

.card__price {
  font-weight: 900;
  color: #f59e0b;
  margin-bottom: 15px;
  font-size: 20px;
}

@media (max-width: 1100px) {
  .cards {
    grid-template-columns: 1fr 1fr;
  }
  .filters__row {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 700px) {
  .services {
    padding: 25px 15px;
  }
  .hero {
    padding: 40px 20px;
  }
  .hero h1 {
    font-size: 32px;
  }
  .filters {
    padding: 16px;
  }
  .filters__row {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  .cards {
    grid-template-columns: 1fr;
  }
  .reset-btn {
    width: 100%;
  }
}
</style>